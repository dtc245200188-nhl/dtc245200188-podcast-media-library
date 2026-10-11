import wave
import math
import struct
import random
import subprocess
import os

# Create audio dir
out_dir = os.path.join('app', 'public', 'uploads', 'audio')
os.makedirs(out_dir, exist_ok=True)

# Fetch slugs
result = subprocess.run(
    ['docker', 'exec', 'podcast-mysql', 'mysql', '-uroot', '-pSuperStr0ng!RootP@ss2024', 'podcast_db', '-s', '-e', 'SELECT slug FROM episodes;'],
    capture_output=True, text=True
)

slugs = [line.strip() for line in result.stdout.split('\n') if line.strip()]
if not slugs:
    print("No slugs found.")
    exit(1)

sample_rate = 22050
duration = 40.0
fade_duration = 2.0
num_samples = int(sample_rate * duration)
fade_samples = int(sample_rate * fade_duration)
max_amp = 0.3 * 32767

chords = [
    [261.63, 329.63, 392.00], # C major
    [293.66, 349.23, 440.00], # D minor
    [329.63, 392.00, 493.88], # E minor
    [349.23, 440.00, 523.25], # F major
    [392.00, 493.88, 587.33], # G major
    [440.00, 523.25, 659.25], # A minor
]

for idx, slug in enumerate(slugs):
    filename = os.path.join(out_dir, f"{slug}.wav")
    chord = chords[idx % len(chords)]
    
    with wave.open(filename, 'w') as wav_file:
        wav_file.setnchannels(1)
        wav_file.setsampwidth(2)
        wav_file.setframerate(sample_rate)
        
        for i in range(num_samples):
            t = float(i) / sample_rate
            
            # Combine frequencies
            sample = 0.0
            for freq in chord:
                sample += math.sin(2.0 * math.pi * freq * t)
            sample /= len(chord)
            
            # Fade in and fade out
            envelope = 1.0
            if i < fade_samples:
                envelope = i / float(fade_samples)
            elif i > num_samples - fade_samples:
                envelope = (num_samples - i) / float(fade_samples)
                
            val = int(sample * envelope * max_amp)
            
            # Pack as 16-bit signed integer
            wav_file.writeframesraw(struct.pack('<h', val))

    print(f"Generated {filename}")
