import os
import subprocess
import time
import re
from mutagen.mp3 import MP3

os.makedirs('scripts', exist_ok=True)
audio_dir = 'app/public/uploads/audio'

episodes_text = {
    'bien-doi-khi-hau': '''Xin chào các thính giả của PodcastHub. Trong tập hôm nay, chúng ta sẽ đề cập đến một vấn đề mang tính sống còn đối với toàn nhân loại: Biến đổi khí hậu. Không còn là câu chuyện của tương lai xa xôi, biến đổi khí hậu đang diễn ra ngay lúc này và tác động trực tiếp đến cuộc sống của mỗi chúng ta.

Thực trạng hiện nay đáng báo động hơn bao giờ hết. Nhiệt độ trung bình toàn cầu liên tục phá vỡ các kỷ lục. Băng ở hai cực đang tan chảy với tốc độ chóng mặt, làm mực nước biển dâng cao, đe dọa trực tiếp đến các vùng đồng bằng ven biển, trong đó có Đồng bằng sông Cửu Long của Việt Nam. Các hiện tượng thời tiết cực đoan như siêu bão, hạn hán kéo dài, lũ lụt tàn khốc ngày càng xuất hiện với tần suất dày đặc và mức độ tàn phá khủng khiếp. Nguyên nhân chính? Hàng tỷ tấn khí nhà kính, chủ yếu là CO2, do con người thải ra môi trường mỗi năm từ việc đốt nhiên liệu hóa thạch, phá rừng và các hoạt động công nghiệp.

Đứng trước thảm họa này, chúng ta không thể khoanh tay đứng nhìn. Giải pháp quan trọng nhất và cấp bách nhất là chuyển đổi sang sử dụng năng lượng tái tạo. Năng lượng mặt trời, năng lượng gió và năng lượng địa nhiệt đang ngày càng trở nên rẻ hơn và dễ tiếp cận hơn. Việc dần loại bỏ than đá và dầu mỏ khỏi lưới điện là bước đi bắt buộc.

Bên cạnh năng lượng, giao thông vận tải cũng là một lĩnh vực cần cách mạng. Sự phát triển mạnh mẽ của xe điện, cùng với việc nâng cấp hệ thống giao thông công cộng, sẽ giúp giảm thiểu đáng kể lượng khí thải. 

Đồng thời, công nghệ xanh đang đóng vai trò then chốt. Từ các vật liệu xây dựng thân thiện với môi trường, hệ thống nông nghiệp thông minh tiết kiệm nước, đến các công nghệ thu giữ và lưu trữ carbon ngay tại các nhà máy. 

Tuy nhiên, công nghệ và chính sách là chưa đủ. Mỗi cá nhân chúng ta cũng phải là một phần của giải pháp. Bằng cách thay đổi những thói quen nhỏ hàng ngày: hạn chế rác thải nhựa, tiết kiệm điện nước, ưu tiên sử dụng sản phẩm tái chế và trồng thêm nhiều cây xanh. Cuộc chiến chống biến đổi khí hậu là cuộc chiến của tất cả mọi người, vì tương lai của chính con em chúng ta. Cảm ơn các bạn đã theo dõi tập podcast này.''',

    'mindfulness-thien-dinh': '''Chào mừng bạn đến với không gian tĩnh lặng của PodcastHub. Trong một thế giới ồn ào, vội vã, nơi chúng ta liên tục bị ném bom bởi hàng ngàn thông báo từ điện thoại và những áp lực công việc không tên, làm sao để tìm lại sự bình yên trong tâm hồn? Đó là lúc chúng ta cần đến Mindfulness - hay còn gọi là Chánh niệm.

Mindfulness không phải là một tôn giáo, cũng không phải là việc cố gắng dập tắt mọi suy nghĩ. Đơn giản, nó là trạng thái nhận biết và tập trung vào những gì đang diễn ra trong giây phút hiện tại, mà không phán xét. Khi bạn uống một tách cà phê, bạn chỉ hoàn toàn cảm nhận hương vị và độ ấm của nó, thay vì lo lắng về buổi họp sắp tới.

Trong thời đại số, sự chú ý của chúng ta bị chia năm xẻ bảy. Điều này dẫn đến tình trạng kiệt sức, lo âu và mất ngủ ngày càng gia tăng. Việc thực hành thiền định và chánh niệm giống như một liều thuốc giải độc cho tâm trí. 

Vậy làm thế nào để bắt đầu, đặc biệt là khi bạn quá bận rộn? Rất đơn giản. Bạn không cần phải ngồi vắt chéo chân trên đỉnh núi suốt hàng giờ. Hãy bắt đầu với 5 phút mỗi ngày. Tìm một chỗ ngồi yên tĩnh, nhắm mắt lại và tập trung vào hơi thở của mình. Cảm nhận không khí đi vào và đi ra qua cánh mũi. 

Tất nhiên, tâm trí bạn sẽ liên tục đi lang thang. Bạn sẽ nghĩ về hóa đơn tiền điện, về câu nói khó nghe của đồng nghiệp. Đừng bực tức. Việc tâm trí lang thang là hoàn toàn tự nhiên. Khi nhận ra điều đó, hãy nhẹ nhàng, rất nhẹ nhàng, kéo sự chú ý của bạn quay trở lại với hơi thở. Đó chính là cốt lõi của thiền định.

Bạn cũng có thể thực hành chánh niệm trong các hoạt động hàng ngày. Khi đi bộ, hãy cảm nhận bàn chân chạm đất. Khi rửa bát, hãy chú tâm vào dòng nước và bọt xà phòng. 

Duy trì thói quen này, bạn sẽ nhận thấy những sự thay đổi kỳ diệu. Mức độ căng thẳng giảm đi, khả năng tập trung tăng lên, và quan trọng nhất, bạn học được cách đối diện với những cảm xúc tiêu cực một cách bình tĩnh và sáng suốt hơn. Hãy hít một hơi thật sâu, và mỉm cười với giây phút hiện tại. Cảm ơn bạn đã lắng nghe.''',

    'hoc-lap-trinh-2025': '''Xin chào các bạn thính giả đam mê công nghệ. Chào mừng quay trở lại với PodcastHub. Chủ đề hôm nay chắc chắn sẽ rất hữu ích cho những ai đang muốn bước chân vào ngành IT: Học lập trình từ đâu trong năm 2025?

Thế giới lập trình luôn vận động và thay đổi không ngừng. Nếu như cách đây vài năm, việc học code chỉ xoay quanh việc thuộc cú pháp, thì năm 2025, với sự hỗ trợ mạnh mẽ của trí tuệ nhân tạo như GitHub Copilot hay ChatGPT, cách chúng ta học và làm việc đã hoàn toàn khác. 

Câu hỏi đầu tiên và quan trọng nhất: Nên bắt đầu học ngôn ngữ nào? Câu trả lời phụ thuộc vào mục tiêu của bạn. 

Nếu bạn muốn trở thành Web Developer, phát triển các trang web và ứng dụng trên trình duyệt, thì JavaScript vẫn là vị vua không thể lật đổ. Hãy bắt đầu với nền tảng HTML, CSS, sau đó đào sâu vào JavaScript. Tiếp theo, hãy làm quen với các framework hiện đại như React hay Vue.js. 

Nếu bạn đam mê Phân tích dữ liệu, Trí tuệ nhân tạo hoặc muốn làm Backend, Python là sự lựa chọn số một. Cú pháp của Python rất trong sáng, dễ đọc và dễ học đối với người mới. Hơn nữa, hệ sinh thái thư viện của Python cho dữ liệu và Trí tuệ nhân tạo là vô đối.

Nếu bạn muốn tạo ra các ứng dụng di động, hãy tìm hiểu về Flutter hoặc React Native. Đây là những công nghệ cho phép bạn viết code một lần mà có thể chạy được trên cả iOS và Android.

Một lời khuyên quan trọng từ các senior developer trong năm 2025: Đừng học vẹt cú pháp. Công nghệ có thể viết code tốt hơn và nhanh hơn bạn. Thay vào đó, hãy rèn luyện tư duy logic, kỹ năng phân tích vấn đề và thiết kế hệ thống. Hãy hiểu rõ CÁCH mọi thứ hoạt động bên dưới, thay vì chỉ biết dùng framework một cách máy móc.

Cuối cùng, học lập trình là một cuộc đua marathon, không phải chạy nước rút. Hãy giữ vững sự kiên trì, liên tục làm các dự án thực tế và đừng ngại hỏi khi gặp khó khăn. Cộng đồng lập trình viên luôn sẵn sàng giúp đỡ bạn. Chúc các bạn có một khởi đầu suôn sẻ và thành công trên con đường trở thành những kỹ sư phần mềm xuất sắc. Xin chào và hẹn gặp lại!''',

    'web3-blockchain-hype-hay-revolution': '''Chào mừng quý thính giả đến với PodcastHub. Hôm nay, chúng ta sẽ cùng mổ xẻ một chủ đề đã từng làm mưa làm gió trên các diễn đàn công nghệ và tài chính: oép ba và Blockchain. Sau những cơn sốt giá của tiền điện tử, những bức tranh ảnh ảo bán với giá triệu đô, giờ đây, khi thị trường đã bình tâm trở lại, chúng ta hãy nhìn nhận một cách khách quan: oép ba là một cuộc cách mạng thực sự, hay chỉ là một trào lưu thổi phồng?

Trước hết, hãy nói về ý tưởng cốt lõi của oép ba. Nếu Web một là mạng lưới thông tin một chiều, Web hai là kỷ nguyên của mạng xã hội và nền tảng tương tác nơi dữ liệu của chúng ta bị kiểm soát bởi các ông lớn công nghệ như Google, Facebook. Thì oép ba hứa hẹn một internet phi tập trung. Ở đó, quyền lực và dữ liệu được trả lại cho người dùng thông qua công nghệ Blockchain.

Đứng ở góc độ một cuộc cách mạng, Blockchain thực sự mang lại những giá trị đột phá. Công nghệ sổ cái phân tán giúp dữ liệu minh bạch, không thể bị giả mạo. Tài chính phi tập trung cho phép bất kỳ ai có kết nối internet đều có thể tiếp cận các dịch vụ vay, mượn, đầu tư mà không cần thông qua ngân hàng truyền thống. Hợp đồng thông minh tự động hóa các thỏa thuận một cách an toàn và chính xác. Đây là những nền tảng vững chắc có khả năng thay đổi cấu trúc tài chính và quản lý dữ liệu trong tương lai.

Tuy nhiên, ở góc độ thổi phồng, chúng ta phải thừa nhận rằng có rất nhiều dự án oép ba không mang lại giá trị thực tế. Trong giai đoạn bùng nổ, hàng ngàn đồng tiền ảo và dự án được tạo ra chỉ với mục đích đầu cơ, vẽ ra những viễn cảnh hão huyền để hút tiền của các nhà đầu tư thiếu kiến thức. Trải nghiệm người dùng của các ứng dụng oép ba hiện tại vẫn còn quá phức tạp, khó tiếp cận đối với đại đa số công chúng, và rủi ro về bảo mật, hack, lừa đảo vẫn luôn rình rập.

Tóm lại, oép ba và Blockchain không phải là một trò lừa đảo, nhưng cũng chưa phải là một phép màu giải quyết mọi vấn đề. Nó là một công nghệ cốt lõi đang trong giai đoạn phát triển sơ khai. Những bong bóng đầu cơ đã vỡ, để lại không gian cho những dự án thực sự có giá trị, giải quyết bài toán thực tế tiếp tục xây dựng và phát triển. Cảm ơn các bạn đã lắng nghe và cùng suy ngẫm!''',

    'quan-ly-tai-chinh-genz': '''Xin chào những người trẻ năng động của thế hệ Gen Z! Các bạn đang nghe PodcastHub. Trong tập hôm nay, chúng ta sẽ nói về một chủ đề tưởng chừng khô khan nhưng lại quyết định chất lượng cuộc sống của bạn trong tương lai: Quản lý tài chính cá nhân.

Thế hệ Gen Z lớn lên trong một kỷ nguyên bùng nổ thông tin và sự cám dỗ tiêu dùng. Từ trà sữa, mua sắm qua mạng, săn giảm giá trên các sàn thương mại điện tử, cho đến những chuyến du lịch đăng ảnh sang chảnh. Áp lực đồng trang lứa khiến nhiều bạn trẻ rơi vào vòng xoáy kiếm tiền để tiêu xài, thậm chí lạm dụng thẻ tín dụng và mắc nợ khi tuổi đời còn rất trẻ. Vậy làm sao để làm chủ đồng tiền, thay vì làm nô lệ cho nó?

Bước đầu tiên và quan trọng nhất: Hãy lập ngân sách chi tiêu, hay còn gọi là Budgeting. Bạn không thể kiểm soát tiền nếu không biết nó đi về đâu. Hãy thử áp dụng quy tắc 50 30 20. Trong đó, 50 phần trăm thu nhập dành cho các nhu cầu thiết yếu như nhà ở, ăn uống, đi lại. 30 phần trăm dành cho sở thích cá nhân, giải trí. Và 20 phần trăm bắt buộc phải dành cho tiết kiệm và trả nợ. Hãy ghi chép lại mọi khoản chi tiêu dù là nhỏ nhất, bạn sẽ bất ngờ khi thấy những khoản tiền rò rỉ mà mình không hề hay biết.

Bước thứ hai, xây dựng quỹ khẩn cấp. Cuộc sống luôn có những rủi ro bất ngờ: ốm đau, hỏng xe, hay mất việc. Hãy luôn dự trữ một khoản tiền mặt đủ để bạn sống từ ba đến sáu tháng mà không có thu nhập. Quỹ khẩn cấp chính là chiếc phao cứu sinh giúp bạn không bị chìm trong khủng hoảng và không phải đi vay mượn với lãi suất cao.

Sau khi đã có quỹ khẩn cấp, bước thứ ba là Đầu tư. Gửi tiết kiệm ngân hàng tuy an toàn nhưng lãi suất khó có thể đánh bại lạm phát trong dài hạn. Hãy bắt đầu tìm hiểu về các kênh đầu tư như chứng khoán hoặc chứng chỉ quỹ. Đối với các bạn trẻ, lợi thế lớn nhất chính là thời gian. Lãi kép sẽ biến những khoản đầu tư nhỏ đặn hàng tháng thành một khối tài sản khổng lồ sau mười, hai mươi năm. Đừng chờ đợi đến khi có nhiều tiền mới đầu tư, hãy bắt đầu ngay từ những số tiền nhỏ nhất.

Cuối cùng, đầu tư vào chính bản thân bạn là khoản đầu tư lợi nhuận cao nhất. Hãy học thêm kỹ năng mới, nâng cao sức khỏe và xây dựng các mối quan hệ chất lượng. Quản lý tài chính cá nhân là một thói quen, cần sự kỷ luật và kiên nhẫn. Chúc các bạn sớm đạt được tự do tài chính! Cảm ơn đã lắng nghe!'''
}

for slug, text in episodes_text.items():
    with open(os.path.join('scripts', f'episode-{slug}.txt'), 'w', encoding='utf-8') as f:
        f.write(text)

def clean_text(text):
    text = re.sub(r'[*#_]', '', text)
    return text

def chunk_text(text, max_len=800):
    sentences = re.split(r'(?<=[.!?]) +|\n+', text)
    chunks = []
    curr_chunk = ""
    for s in sentences:
        if not s.strip(): continue
        if len(curr_chunk) + len(s) < max_len:
            curr_chunk += s + " "
        else:
            if curr_chunk:
                chunks.append(curr_chunk.strip())
            curr_chunk = s + " "
    if curr_chunk:
        chunks.append(curr_chunk.strip())
    return chunks

slugs = ['bien-doi-khi-hau', 'hoc-lap-trinh-2025', 'web3-blockchain-hype-hay-revolution', 'quan-ly-tai-chinh-genz']

sql_statements = []

for slug in slugs:
    final_mp3 = os.path.join(audio_dir, f'{slug}.mp3')
    txt_path = os.path.join('scripts', f'episode-{slug}.txt')
    
    with open(txt_path, 'r', encoding='utf-8') as f:
        raw_text = f.read()
        
    cleaned = clean_text(raw_text)
    chunks = chunk_text(cleaned)
    
    print(f"Generating {slug} using edge-tts HoaiMyNeural, chunks: {len(chunks)}...")
    chunk_files = []
    success = True
    
    for idx, chunk in enumerate(chunks):
        c_file = os.path.join(audio_dir, f'{slug}_c{idx}.mp3')
        chunk_files.append(c_file)
        
        attempt = 1
        while True:
            try:
                print(f"  -> Chunk {idx} attempt {attempt}")
                subprocess.run(['edge-tts', '--voice', 'vi-VN-HoaiMyNeural', '--text', chunk, '--write-media', c_file], check=True)
                if os.path.exists(c_file) and os.path.getsize(c_file) > 0:
                    break
            except Exception:
                time.sleep(10)
                attempt += 1
            
    if success:
        with open(final_mp3, 'wb') as outfile:
            for c_file in chunk_files:
                with open(c_file, 'rb') as infile:
                    outfile.write(infile.read())
        audio = MP3(final_mp3)
        length = audio.info.length
        mins = int(length) // 60
        secs = int(length) % 60
        duration_str = f"{mins:02d}:{secs:02d}"
        
        desc = raw_text.replace("'", "''")
        sql = f"UPDATE episodes SET audio_file = '/uploads/audio/{slug}.mp3', duration = '{duration_str}', description = '{desc}' WHERE slug = '{slug}';"
        sql_statements.append(sql)
        print(f"Generated {slug}: {duration_str}")
        
    for c_file in chunk_files:
        if os.path.exists(c_file):
            os.remove(c_file)

with open('update3.sql', 'w', encoding='utf-8') as f:
    f.write('SET NAMES utf8mb4;\n')
    for sql in sql_statements:
        f.write(sql + '\n')

print("All done generating.")
