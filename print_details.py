import pymysql
import re
import os

connection = pymysql.connect(
    host='127.0.0.1',
    user='root',
    password='SuperStr0ng!RootP@ss2024',
    database='podcast_db',
    charset='utf8mb4',
    cursorclass=pymysql.cursors.DictCursor
)

slugs = ['tuong-lai-cua-ai-2025', 'docker-kubernetes-nguoi-moi', 'khoi-nghiep-tu-so-0', 'marketing-so-thoi-dai-moi', 'bi-an-cua-vu-tru', 'bien-doi-khi-hau', 'mindfulness-thien-dinh', 'hoc-lap-trinh-2025', 'web3-blockchain-hype-hay-revolution', 'quan-ly-tai-chinh-genz']

with connection.cursor() as cursor:
    for slug in slugs:
        cursor.execute("SELECT title, description FROM episodes WHERE slug=%s", (slug,))
        row = cursor.fetchone()
        if not row: continue
        print(f"=== {slug} ===")
        print(f"Title: {row['title']}")
        # print(f"Description: {row['description'][:100]}...")
        
        script_path = f"scripts/episode-{slug}.txt"
        if os.path.exists(script_path):
            with open(script_path, 'r', encoding='utf-8') as f:
                text = f.read()
            sentences = [s.strip() for s in re.split(r'(?<=[.!?]) +|\n+', text) if s.strip()]
            first_2 = " ".join(sentences[:2])
            last_2 = " ".join(sentences[-2:])
            print(f"Script First 2: {first_2}")
            print(f"Script Last 2: {last_2}")
