import os
import subprocess
import time
import re
from mutagen.mp3 import MP3

os.makedirs('scripts', exist_ok=True)
audio_dir = 'app/public/uploads/audio'
os.makedirs(audio_dir, exist_ok=True)

# Delete existing mp3s (except sample.mp3)
for f in os.listdir(audio_dir):
    if f.endswith('.mp3') and f != 'sample.mp3':
        path = os.path.join(audio_dir, f)
        if os.path.getsize(path) < 100000:
            os.remove(path)
            print(f"Deleted broken/small file: {f}")

def clean_text(text):
    text = re.sub(r'[*#_]', '', text)
    # Basic replacements for difficult words
    text = text.replace('AI', 'ây ai')
    text = text.replace('NFT', 'en ép ti')
    text = text.replace('DeFi', 'đi phai')
    text = text.replace('Web3', 'oép ba')
    text = text.replace('Web2', 'oép hai')
    text = text.replace('Web1', 'oép một')
    text = text.replace('DevOps', 'đép ốp')
    text = text.replace('K8s', 'ca tám ét')
    text = text.replace('Container', 'cần tay nơ')
    text = text.replace('Dockerfile', 'đốc cơ phai')
    text = text.replace('Smart Contract', 'xờ mát con trắc')
    text = text.replace('Copilot', 'cô pai lót')
    text = text.replace('ChatGPT', 'chát gờ bi ti')
    text = text.replace('JavaScript', 'gia va sờ cờ ríp')
    text = text.replace('Python', 'pai thòn')
    text = text.replace('React', 'ri ắc')
    text = text.replace('Vue.js', 'viu di ét')
    text = text.replace('Flutter', 'phờ lút tơ')
    text = text.replace('Backend', 'bách en')
    text = text.replace('Front-end', 'phrôn en')
    text = text.replace('GenZ', 'gien dét')
    return text

def chunk_text(text, max_len=800):
    sentences = re.split(r'(?<=[.!?]) +', text)
    chunks = []
    curr_chunk = ""
    for s in sentences:
        if len(curr_chunk) + len(s) < max_len:
            curr_chunk += s + " "
        else:
            chunks.append(curr_chunk.strip())
            curr_chunk = s + " "
    if curr_chunk:
        chunks.append(curr_chunk.strip())
    return chunks

slugs = [
    'tuong-lai-cua-ai-2025', 'docker-kubernetes-nguoi-moi', 'khoi-nghiep-tu-so-0',
    'marketing-so-thoi-dai-moi', 'bi-an-cua-vu-tru', 'bien-doi-khi-hau',
    'mindfulness-thien-dinh', 'hoc-lap-trinh-2025', 'web3-blockchain-hype-hay-revolution',
    'quan-ly-tai-chinh-genz'
]

sql_statements = []

for slug in slugs:
    txt_path = os.path.join('scripts', f'episode-{slug}.txt')
    if not os.path.exists(txt_path):
        print(f"Skipping {slug}, no script found.")
        continue
        
    with open(txt_path, 'r', encoding='utf-8') as f:
        raw_text = f.read()
        
    final_mp3 = os.path.join(audio_dir, f'{slug}.mp3')
    
    # Check if already done and valid
    if os.path.exists(final_mp3):
        try:
            audio = MP3(final_mp3)
            if audio.info.length > 90:
                print(f"Skipping {slug}, already perfectly generated ({audio.info.length:.1f}s)")
                # still generate sql
                mins = int(audio.info.length) // 60
                secs = int(audio.info.length) % 60
                duration_str = f"{mins:02d}:{secs:02d}"
                sql = f"UPDATE episodes SET audio_file = '/uploads/audio/{slug}.mp3', duration = '{duration_str}' WHERE slug = '{slug}';"
                sql_statements.append(sql)
                continue
        except Exception:
            pass # invalid mp3, regenerate
            
    print(f"Generating {slug}...")
    cleaned = clean_text(raw_text)
    chunks = chunk_text(cleaned)
    
    chunk_files = []
    success = True
    for idx, chunk in enumerate(chunks):
        if not chunk.strip(): continue
        c_file = os.path.join(audio_dir, f'{slug}_chunk_{idx}.mp3')
        chunk_files.append(c_file)
        
        # Retry loop
        for attempt in range(3):
            try:
                print(f"  -> Chunk {idx}/{len(chunks)-1} (attempt {attempt+1})")
                subprocess.run(['edge-tts', '--voice', 'vi-VN-HoaiMyNeural', '--text', chunk, '--write-media', c_file], check=True, timeout=60)
                if os.path.exists(c_file) and os.path.getsize(c_file) > 0:
                    break
            except Exception as e:
                print(f"  -> Error in subprocess")
                time.sleep(3)
        else:
            print(f"Failed to generate chunk {idx} for {slug} after 3 attempts.")
            success = False
            break
            
    if success:
        # Merge chunks
        with open(final_mp3, 'wb') as outfile:
            for c_file in chunk_files:
                with open(c_file, 'rb') as infile:
                    outfile.write(infile.read())
        
        # Validate
        try:
            audio = MP3(final_mp3)
            length = audio.info.length
            if length > 90:
                print(f"Generated {final_mp3} successfully ({length:.1f}s)")
                mins = int(length) // 60
                secs = int(length) % 60
                duration_str = f"{mins:02d}:{secs:02d}"
                sql = f"UPDATE episodes SET audio_file = '/uploads/audio/{slug}.mp3', duration = '{duration_str}' WHERE slug = '{slug}';"
                sql_statements.append(sql)
            else:
                print(f"Warning: {final_mp3} is only {length:.1f}s long.")
        except Exception as e:
            print(f"Error reading MP3 {final_mp3}: {e}")
            
    # Cleanup chunks
    for c_file in chunk_files:
        if os.path.exists(c_file):
            os.remove(c_file)

with open('update.sql', 'w', encoding='utf-8') as f:
    f.write('SET NAMES utf8mb4;\n')
    for sql in sql_statements:
        f.write(sql + '\n')

print("All done generating MP3s and update.sql.")
