import os
import whisper
import json
import re

model = whisper.load_model("base") # small takes too long

slugs = ['tuong-lai-cua-ai-2025', 'docker-kubernetes-nguoi-moi', 'khoi-nghiep-tu-so-0', 'marketing-so-thoi-dai-moi', 'bi-an-cua-vu-tru', 'mindfulness-thien-dinh']
audio_dir = 'app/public/uploads/audio'

results = {}

for slug in slugs:
    mp3_path = os.path.join(audio_dir, f'{slug}.mp3')
    if not os.path.exists(mp3_path):
        results[slug] = "FILE NOT FOUND"
        continue
    
    print(f"Transcribing {slug}...")
    result = model.transcribe(mp3_path, language="vi")
    text = result["text"]
    
    sentences = [s.strip() for s in re.split(r'(?<=[.!?]) +|\n+', text) if s.strip()]
    first_3 = " ".join(sentences[:3])
    
    keywords = []
    lower_text = text.lower()
    
    if slug == 'bien-doi-khi-hau':
        keys = ['biến đổi khí hậu', 'nhân loại', 'tương lai', 'nhiệt độ', 'băng', 'nước biển', 'năng lượng']
    elif slug == 'mindfulness-thien-dinh':
        keys = ['mindfulness', 'chánh niệm', 'tĩnh lặng', 'thiền định', 'bình yên', 'hơi thở']
    elif slug == 'hoc-lap-trinh-2025':
        keys = ['lập trình', '2025', 'công nghệ', 'ngôn ngữ', 'javascript', 'python', 'ai']
    elif slug == 'web3-blockchain-hype-hay-revolution':
        keys = ['web ba', 'blockchain', 'cách mạng', 'tài chính', 'phi tập trung', 'tiền điện tử']
    elif slug == 'quan-ly-tai-chinh-genz':
        keys = ['tài chính', 'gen z', 'ngân sách', 'tiết kiệm', 'đầu tư', '50 30 20']
    elif slug == 'tuong-lai-cua-ai-2025':
        keys = ['ai', 'trí tuệ nhân tạo', 'tương lai', 'công việc', 'thay đổi', '2025']
    elif slug == 'docker-kubernetes-nguoi-moi':
        keys = ['docker', 'kubernetes', 'container', 'triển khai', 'người mới']
    elif slug == 'khoi-nghiep-tu-so-0':
        keys = ['khởi nghiệp', 'số không', 'kinh nghiệm', 'thất bại', 'bài học']
    elif slug == 'marketing-so-thoi-dai-moi':
        keys = ['marketing số', 'thời đại mới', 'chiến lược', 'khách hàng', 'công cụ']
    elif slug == 'bi-an-cua-vu-tru':
        keys = ['bí ẩn', 'vũ trụ', 'khoa học', 'hố đen', 'khám phá']
        
    for k in keys:
        if k in lower_text:
            keywords.append(k)
            
    results[slug] = {
        'first_3_sentences': first_3,
        'keywords_found': keywords
    }
    print(f"[{slug}] First 3: {first_3}")
    print(f"[{slug}] Keywords: {keywords}")

with open('whisper_results.json', 'w', encoding='utf-8') as f:
    json.dump(results, f, ensure_ascii=False, indent=2)
