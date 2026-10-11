import os
from mutagen.mp3 import MP3
from gtts import gTTS
import time

audio_dir = 'app/public/uploads/audio'

episodes = {
    'bien-doi-khi-hau': 'Xin chào các thính giả của PodcastHub. Trong tập hôm nay, chúng ta sẽ đề cập đến một vấn đề mang tính sống còn đối với toàn nhân loại: Biến đổi khí hậu. Không còn là câu chuyện của tương lai xa xôi, biến đổi khí hậu đang diễn ra ngay lúc này và tác động trực tiếp đến cuộc sống của mỗi chúng ta. Thực trạng hiện nay đáng báo động hơn bao giờ hết. Nhiệt độ trung bình toàn cầu liên tục phá vỡ các kỷ lục. Băng ở hai cực đang tan chảy với tốc độ chóng mặt, làm mực nước biển dâng cao, đe dọa trực tiếp đến các vùng đồng bằng ven biển. Nguyên nhân chính? Hàng tỷ tấn khí nhà kính, chủ yếu là CO2, do con người thải ra môi trường mỗi năm từ việc đốt nhiên liệu hóa thạch, phá rừng và các hoạt động công nghiệp. Giải pháp quan trọng nhất và cấp bách nhất là chuyển đổi sang sử dụng năng lượng tái tạo. Năng lượng mặt trời, năng lượng gió và năng lượng địa nhiệt đang ngày càng trở nên rẻ hơn và dễ tiếp cận hơn. Việc dần loại bỏ than đá và dầu mỏ khỏi lưới điện là bước đi bắt buộc. Mỗi cá nhân chúng ta cũng phải là một phần của giải pháp. Bằng cách thay đổi những thói quen nhỏ hàng ngày: hạn chế rác thải nhựa, tiết kiệm điện nước, ưu tiên sử dụng sản phẩm tái chế. Cảm ơn các bạn đã theo dõi.',
    
    'mindfulness-thien-dinh': 'Chào mừng bạn đến với không gian tĩnh lặng của PodcastHub. Trong một thế giới ồn ào, vội vã, nơi chúng ta liên tục bị ném bom bởi hàng ngàn thông báo từ điện thoại và những áp lực công việc không tên, làm sao để tìm lại sự bình yên trong tâm hồn? Đó là lúc chúng ta cần đến Mindfulness - hay còn gọi là Chánh niệm. Mindfulness không phải là một tôn giáo, cũng không phải là việc cố gắng dập tắt mọi suy nghĩ. Đơn giản, nó là trạng thái nhận biết và tập trung vào những gì đang diễn ra trong giây phút hiện tại, mà không phán xét. Khi bạn uống một tách cà phê, bạn chỉ hoàn toàn cảm nhận hương vị và độ ấm của nó, thay vì lo lắng về buổi họp sắp tới. Bạn có thể thực hành chánh niệm trong các hoạt động hàng ngày. Khi đi bộ, hãy cảm nhận bàn chân chạm đất. Khi rửa bát, hãy chú tâm vào dòng nước và bọt xà phòng. Duy trì thói quen này, bạn sẽ nhận thấy những sự thay đổi kỳ diệu. Mức độ căng thẳng giảm đi, khả năng tập trung tăng lên, và quan trọng nhất, bạn học được cách đối diện với những cảm xúc tiêu cực một cách bình tĩnh. Cảm ơn bạn đã lắng nghe.',
    
    'hoc-lap-trinh-2025': 'Xin chào các bạn thính giả đam mê công nghệ. Chào mừng quay trở lại với PodcastHub. Chủ đề hôm nay chắc chắn sẽ rất hữu ích cho những ai đang muốn bước chân vào ngành IT: Học lập trình từ đâu trong năm 2025? Thế giới lập trình luôn vận động và thay đổi không ngừng. Năm 2025, với sự hỗ trợ mạnh mẽ của trí tuệ nhân tạo như GitHub Copilot hay ChatGPT, cách chúng ta học và làm việc đã hoàn toàn khác. Câu hỏi đầu tiên và quan trọng nhất: Nên bắt đầu học ngôn ngữ nào? Câu trả lời phụ thuộc vào mục tiêu của bạn. Nếu bạn muốn trở thành Web Developer, phát triển các trang web và ứng dụng trên trình duyệt, thì JavaScript vẫn là vị vua không thể lật đổ. Hãy bắt đầu với nền tảng HTML, CSS, sau đó đào sâu vào JavaScript. Nếu bạn đam mê Phân tích dữ liệu, Trí tuệ nhân tạo hoặc muốn làm Backend, Python là sự lựa chọn số một. Cú pháp của Python rất trong sáng, dễ đọc và dễ học đối với người mới. Hơn nữa, hệ sinh thái thư viện của Python cho dữ liệu và Trí tuệ nhân tạo là vô đối. Một lời khuyên quan trọng từ các senior developer trong năm 2025: Đừng học vẹt cú pháp. Công nghệ có thể viết code tốt hơn và nhanh hơn bạn. Thay vào đó, hãy rèn luyện tư duy logic, kỹ năng phân tích vấn đề và thiết kế hệ thống. Chúc các bạn có một khởi đầu suôn sẻ và thành công trên con đường trở thành những kỹ sư phần mềm xuất sắc. Xin chào và hẹn gặp lại!',
    
    'web3-blockchain-hype-hay-revolution': 'Chào mừng quý thính giả đến với PodcastHub. Hôm nay, chúng ta sẽ cùng mổ xẻ một chủ đề đã từng làm mưa làm gió trên các diễn đàn công nghệ và tài chính: Web ba và Blockchain. Trước hết, hãy nói về ý tưởng cốt lõi của Web ba. Nếu Web một là mạng lưới thông tin một chiều, Web hai là kỷ nguyên của mạng xã hội và nền tảng tương tác nơi dữ liệu của chúng ta bị kiểm soát bởi các ông lớn công nghệ. Thì Web ba hứa hẹn một internet phi tập trung. Ở đó, quyền lực và dữ liệu được trả lại cho người dùng thông qua công nghệ Blockchain. Đứng ở góc độ một cuộc cách mạng, Blockchain thực sự mang lại những giá trị đột phá. Công nghệ sổ cái phân tán giúp dữ liệu minh bạch, không thể bị giả mạo. Tài chính phi tập trung cho phép bất kỳ ai có kết nối internet đều có thể tiếp cận các dịch vụ vay, mượn, đầu tư mà không cần thông qua ngân hàng truyền thống. Hợp đồng thông minh tự động hóa các thỏa thuận một cách an toàn và chính xác. Tuy nhiên, ở góc độ thổi phồng, chúng ta phải thừa nhận rằng có rất nhiều dự án Web ba không mang lại giá trị thực tế. Trong giai đoạn bùng nổ, hàng ngàn dự án được tạo ra chỉ với mục đích đầu cơ, vẽ ra những viễn cảnh hão huyền để hút tiền của các nhà đầu tư. Tóm lại, Web ba và Blockchain không phải là một trò lừa đảo, nhưng cũng chưa phải là một phép màu. Nó là một công nghệ cốt lõi đang trong giai đoạn phát triển. Cảm ơn các bạn đã lắng nghe!',
    
    'quan-ly-tai-chinh-genz': 'Xin chào những người trẻ năng động của thế hệ Gen Z! Các bạn đang nghe PodcastHub. Trong tập hôm nay, chúng ta sẽ nói về một chủ đề tưởng chừng khô khan nhưng lại quyết định chất lượng cuộc sống của bạn trong tương lai: Quản lý tài chính cá nhân. Thế hệ Gen Z lớn lên trong một kỷ nguyên bùng nổ thông tin và sự cám dỗ tiêu dùng. Bước đầu tiên và quan trọng nhất: Hãy lập ngân sách chi tiêu, hay còn gọi là Budgeting. Bạn không thể kiểm soát tiền nếu không biết nó đi về đâu. Hãy thử áp dụng quy tắc 50 30 20. Trong đó, 50 phần trăm thu nhập dành cho các nhu cầu thiết yếu như nhà ở, ăn uống, đi lại. 30 phần trăm dành cho sở thích cá nhân, giải trí. Và 20 phần trăm bắt buộc phải dành cho tiết kiệm và trả nợ. Bước thứ hai, xây dựng quỹ khẩn cấp. Cuộc sống luôn có những rủi ro bất ngờ: ốm đau, hỏng xe, hay mất việc. Hãy luôn dự trữ một khoản tiền mặt đủ để bạn sống từ 3 đến 6 tháng mà không có thu nhập. Quỹ khẩn cấp chính là chiếc phao cứu sinh giúp bạn không bị chìm trong khủng hoảng. Sau khi đã có quỹ khẩn cấp, bước thứ ba là Đầu tư. Gửi tiết kiệm ngân hàng tuy an toàn nhưng lãi suất khó có thể đánh bại lạm phát trong dài hạn. Hãy bắt đầu tìm hiểu về các kênh đầu tư chứng khoán hoặc chứng chỉ quỹ. Lãi kép sẽ biến những khoản đầu tư nhỏ đều đặn hàng tháng thành một khối tài sản khổng lồ sau mười, hai mươi năm. Chúc các bạn sớm đạt được tự do tài chính! Cảm ơn đã lắng nghe!'
}

sql_statements = []

for slug, content in episodes.items():
    final_mp3 = os.path.join(audio_dir, f'{slug}.mp3')
    
    try:
        print(f"Generating {slug} via gTTS...")
        tts = gTTS(text=content, lang='vi', slow=False)
        tts.save(final_mp3)
        
        audio = MP3(final_mp3)
        mins = int(audio.info.length) // 60
        secs = int(audio.info.length) % 60
        duration_str = f"{mins:02d}:{secs:02d}"
        sql = f"UPDATE episodes SET audio_file = '/uploads/audio/{slug}.mp3', duration = '{duration_str}' WHERE slug = '{slug}';"
        sql_statements.append(sql)
        print(f"-> Success: {duration_str}")
    except Exception as e:
        print(f"Error for {slug}: {e}")

# Append the remaining 5 files to update.sql
for already_done in ['tuong-lai-cua-ai-2025', 'docker-kubernetes-nguoi-moi', 'khoi-nghiep-tu-so-0', 'marketing-so-thoi-dai-moi', 'bi-an-cua-vu-tru']:
    p = os.path.join(audio_dir, f'{already_done}.mp3')
    if os.path.exists(p):
        a = MP3(p)
        mins = int(a.info.length) // 60
        secs = int(a.info.length) % 60
        d_str = f"{mins:02d}:{secs:02d}"
        sql_statements.append(f"UPDATE episodes SET audio_file = '/uploads/audio/{already_done}.mp3', duration = '{d_str}' WHERE slug = '{already_done}';")

with open('update2.sql', 'w', encoding='utf-8') as f:
    f.write('SET NAMES utf8mb4;\n')
    for sql in sql_statements:
        f.write(sql + '\n')
