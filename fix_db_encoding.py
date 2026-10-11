import pymysql
import os

connection = pymysql.connect(
    host='127.0.0.1',
    user='root',
    password='SuperStr0ng!RootP@ss2024',
    database='podcast_db',
    charset='utf8mb4'
)

slugs = ['tuong-lai-cua-ai-2025', 'docker-kubernetes-nguoi-moi', 'khoi-nghiep-tu-so-0', 'marketing-so-thoi-dai-moi', 'bi-an-cua-vu-tru', 'bien-doi-khi-hau', 'mindfulness-thien-dinh', 'hoc-lap-trinh-2025', 'web3-blockchain-hype-hay-revolution', 'quan-ly-tai-chinh-genz']

with connection.cursor() as cursor:
    for slug in slugs:
        script_path = f"scripts/episode-{slug}.txt"
        if os.path.exists(script_path):
            with open(script_path, 'r', encoding='utf-8') as f:
                desc = f.read().strip()
            
            # Keep original title and update description
            cursor.execute("UPDATE episodes SET description = %s WHERE slug = %s", (desc, slug))

connection.commit()
print("Successfully fixed database encodings!")
