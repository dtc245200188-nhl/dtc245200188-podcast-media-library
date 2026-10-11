import os
import subprocess
from mutagen.mp3 import MP3

os.makedirs('scripts', exist_ok=True)
os.makedirs('app/public/uploads/audio', exist_ok=True)

episodes = {
    'tuong-lai-cua-ai-2025': {
        'title': 'Tương lai của AI trong 2025',
        'desc': 'Khám phá những xu hướng AI mới nhất và tác động của chúng đến cuộc sống hàng ngày. Từ ChatGPT đến các mô hình ngôn ngữ lớn, chúng ta sẽ thảo luận về tương lai của trí tuệ nhân tạo và cách nó đang thay đổi mọi ngành nghề.',
        'content': '''Chào mừng các bạn đến với PodcastHub! Hôm nay, chúng ta sẽ cùng khám phá một chủ đề cực kỳ nóng hổi: Tương lai của Trí tuệ Nhân tạo trong năm 2025. 

Nhìn lại vài năm qua, AI đã có những bước tiến thần tốc. Không chỉ còn là những cỗ máy vô tri, các mô hình ngôn ngữ lớn như ChatGPT, Claude hay Gemini đã trở thành người trợ lý đắc lực trong công việc và học tập. Năm 2025 đánh dấu một kỷ nguyên mới, nơi AI không chỉ xử lý văn bản, mà còn có khả năng hiểu và tổng hợp đa phương tiện một cách mượt mà: từ giọng nói, hình ảnh, đến cảm xúc của con người.

Ý chính thứ nhất chúng ta cần bàn đến là sự cá nhân hóa. AI của năm 2025 không còn là một hệ thống chung chung. Nó học hỏi từ thói quen, sở thích, và phong cách làm việc của chính bạn để đưa ra những gợi ý phù hợp nhất. Từ việc viết email, lên lịch trình, đến thiết kế đồ họa, mọi thứ đều được tinh chỉnh cho riêng bạn.

Thứ hai, là sự thay đổi trong cấu trúc công việc. Nhiều người lo sợ AI sẽ cướp đi công việc của con người. Nhưng thực tế, nó đang loại bỏ những công việc lặp đi lặp lại, giải phóng con người để tập trung vào tư duy sáng tạo và giải quyết vấn đề phức tạp. Những kỹ năng mềm như sự đồng cảm, giao tiếp và khả năng thích nghi trở nên quan trọng hơn bao giờ hết.

Cuối cùng, vấn đề đạo đức và bảo mật. Khi AI ngày càng thông minh, việc kiểm soát dữ liệu cá nhân và đảm bảo tính minh bạch trong các quyết định của AI là một thách thức lớn. Các chính phủ và tổ chức đang nỗ lực thiết lập những quy chuẩn khắt khe để đảm bảo AI phục vụ lợi ích chung của nhân loại.

Tóm lại, tương lai của AI trong năm 2025 không phải là một viễn cảnh đáng sợ, mà là cơ hội để chúng ta vươn lên và làm chủ công nghệ. Hãy luôn học hỏi và sẵn sàng đón nhận những thay đổi. Cảm ơn các bạn đã lắng nghe tập podcast hôm nay!'''
    },
    'docker-kubernetes-nguoi-moi': {
        'title': 'Docker và Kubernetes cho người mới bắt đầu',
        'desc': 'Hướng dẫn cơ bản về containerization với Docker và orchestration với Kubernetes. Từ việc viết Dockerfile đầu tiên đến deploy ứng dụng trên K8s cluster, bắt đầu hành trình DevOps của bạn.',
        'content': '''Xin chào các bạn thính giả của PodcastHub. Trong tập hôm nay, chúng ta sẽ bước vào thế giới của DevOps với hai công nghệ cốt lõi: Docker và Kubernetes, dành riêng cho những bạn mới bắt đầu.

Nếu bạn là một lập trình viên, chắc hẳn bạn đã từng gặp tình huống: "Code chạy ngon lành trên máy mình, nhưng lại lỗi tưng bừng trên server". Đó là lúc Docker xuất hiện như một vị cứu tinh. Docker cho phép bạn đóng gói toàn bộ ứng dụng, cùng với tất cả các thư viện và môi trường cần thiết, vào một hộp chứa gọi là Container. Bạn có thể mang chiếc hộp này đi bất cứ đâu, từ máy Mac, Windows đến Linux server, mà ứng dụng vẫn chạy y hệt như cũ. 

Để bắt đầu với Docker, bạn chỉ cần học cách viết một file có tên là Dockerfile. Trong đó, bạn khai báo hệ điều hành gốc, copy code vào, cài đặt thư viện và định nghĩa lệnh khởi chạy. Chỉ với vài dòng lệnh đơn giản, bạn đã tạo ra được một Docker Image và sẵn sàng biến nó thành Container.

Tuy nhiên, khi ứng dụng của bạn lớn lên, bạn không chỉ chạy một hay hai Container, mà có thể là hàng trăm, hàng nghìn Container cùng lúc. Làm sao để quản lý chúng? Làm sao để tự động khởi động lại một Container bị sập? Đó là lúc Kubernetes, hay còn gọi là K8s, ra tay.

Kubernetes là một hệ thống điều phối Container. Nó giống như một người nhạc trưởng, chỉ huy một dàn nhạc giao hưởng gồm vô số Container. Bạn chỉ cần nói với Kubernetes rằng: "Tôi muốn ứng dụng này luôn có 3 bản sao đang chạy". Kubernetes sẽ tự động sắp xếp, phân bổ tài nguyên, và đảm bảo yêu cầu của bạn luôn được đáp ứng, kể cả khi có một server vật lý bị hỏng.

Tất nhiên, Kubernetes khá phức tạp và có một đường cong học tập khá dốc. Nhưng đừng nản lòng. Hãy bắt đầu từ việc thành thạo Docker, sau đó làm quen với các khái niệm cơ bản của K8s như Pod, Service, và Deployment. Chúc các bạn có một hành trình DevOps thú vị và thành công. Hẹn gặp lại trong các tập tiếp theo!'''
    },
    'khoi-nghiep-tu-so-0': {
        'title': 'Khởi nghiệp từ số 0 - Bài học thực tế',
        'desc': 'Chia sẻ kinh nghiệm xây dựng startup từ con số 0 với những bài học xương máu. Founder của nhiều startup thành công tại Việt Nam chia sẻ về gọi vốn, xây dựng team, và vượt qua thất bại.',
        'content': '''Chào mừng các bạn trở lại với PodcastHub. Tập podcast hôm nay dành riêng cho những ai đang ấp ủ giấc mơ khởi nghiệp, hoặc đang chật vật những bước đầu tiên trên con đường xây dựng doanh nghiệp từ con số không.

Khởi nghiệp chưa bao giờ là một con đường trải đầy hoa hồng. Nhìn từ bên ngoài, người ta thường chỉ thấy ánh hào quang của những vòng gọi vốn triệu đô hay những thương vụ mua lại đình đám. Nhưng đằng sau đó là những đêm thức trắng, những giọt nước mắt và vô số lần thất bại. 

Bài học đầu tiên và quan trọng nhất khi khởi nghiệp từ số không là: Hãy bắt đầu với một vấn đề thực tế, chứ không phải một giải pháp viển vông. Rất nhiều founder tạo ra một sản phẩm tuyệt vời, nhưng lại không có ai cần đến nó. Hãy ra ngoài, nói chuyện với khách hàng, lắng nghe nỗi đau của họ, và đảm bảo rằng giải pháp của bạn thực sự giải quyết được vấn đề đó.

Bài học thứ hai liên quan đến việc xây dựng đội ngũ. Người ta thường nói: "Muốn đi nhanh thì đi một mình, muốn đi xa thì đi cùng nhau". Trong giai đoạn đầu, bạn cần những co-founder và những nhân viên đầu tiên có cùng chung tầm nhìn, chịu được áp lực cao và sẵn sàng làm mọi việc từ chiến lược đến dọn rác. Đừng vội vàng tuyển dụng những người có bản lý lịch hoành tráng nhưng không phù hợp với văn hóa công ty.

Vấn đề thứ ba là tài chính. Khi khởi nghiệp từ số không, tiền luôn là một bài toán khó. Lời khuyên là hãy tiết kiệm từng đồng, tối ưu hóa chi phí và cố gắng tạo ra doanh thu càng sớm càng tốt. Đừng quá phụ thuộc vào tiền của nhà đầu tư. Hãy chứng minh mô hình kinh doanh của bạn có thể tự nuôi sống nó.

Và cuối cùng, hãy học cách chấp nhận và vượt qua thất bại. Thất bại là một phần tất yếu của khởi nghiệp. Quan trọng là bạn học được gì từ nó và đứng lên như thế nào. Giữ vững niềm tin, kiên trì theo đuổi đam mê, và luôn linh hoạt điều chỉnh chiến lược. Chúc các bạn chân cứng đá mềm trên con đường khởi nghiệp của mình. Cảm ơn đã lắng nghe!'''
    },
    'marketing-so-thoi-dai-moi': {
        'title': 'Marketing số thời đại mới',
        'desc': 'Chiến lược marketing digital hiệu quả trong năm 2025. Từ social media marketing, content marketing, đến SEO và paid advertising - những trend mới nhất và cách áp dụng cho doanh nghiệp Việt.',
        'content': '''Xin chào các marketer và những người yêu thích kinh doanh. Chào mừng đến với PodcastHub. Hôm nay, chúng ta sẽ cùng phân tích bức tranh Marketing Số thời đại mới, đặc biệt là những xu hướng đang định hình năm 2025.

Thế giới digital đang thay đổi với tốc độ chóng mặt. Những chiến lược từng mang lại thành công vang dội cách đây vài năm giờ có thể đã lỗi thời. Người tiêu dùng ngày càng thông minh hơn, khó tính hơn và có hàng ngàn sự lựa chọn mỗi ngày.

Xu hướng đầu tiên định hình năm nay chính là Content chân thực hay còn gọi là Authentic Content. Người dùng đang dần chán ngán với những video quảng cáo bóng bẩy, dàn dựng công phu. Họ tìm kiếm sự kết nối thật sự. Những video ngắn mang tính hậu trường, chia sẻ câu chuyện cá nhân, hay review chân thật từ người dùng thực tế đang mang lại tỷ lệ chuyển đổi cao hơn hẳn. Định dạng video ngắn trên TikTok, Reels hay Shorts vẫn thống trị, nhưng nội dung phải đi thẳng vào vấn đề trong 3 giây đầu tiên.

Thứ hai là sự lên ngôi của AI trong Marketing. Không chỉ là viết bài hay tạo ảnh, AI đang giúp cá nhân hóa trải nghiệm khách hàng ở một cấp độ hoàn toàn mới. Từ việc phân tích dữ liệu lớn để dự đoán hành vi mua sắm, đến việc tạo ra các chiến dịch quảng cáo tự động thay đổi nội dung dựa trên từng phân khúc khách hàng. Nếu bạn chưa áp dụng AI vào quy trình làm việc, bạn đang tụt lại phía sau.

Thứ ba là xu hướng Social Commerce - Mua sắm trực tiếp trên mạng xã hội. Khách hàng không muốn bị gián đoạn trải nghiệm bằng việc chuyển hướng sang một website khác. Họ muốn xem livestream, xem video và bấm mua ngay lập tức. Việc tối ưu hóa gian hàng trên TikTok Shop hay Instagram là điều kiện bắt buộc đối với các nhà bán lẻ.

Cuối cùng, đừng quên xây dựng một cộng đồng trung thành. Thay vì chỉ tập trung vào việc thu hút khách hàng mới, hãy đầu tư vào việc chăm sóc và tạo giá trị cho những người đã mua hàng. Một cộng đồng mạnh mẽ sẽ là những đại sứ thương hiệu tốt nhất cho bạn. 

Marketing số thời đại mới đòi hỏi sự linh hoạt, sáng tạo và cập nhật không ngừng. Hãy thử nghiệm những điều mới mẻ và tìm ra công thức phù hợp nhất cho doanh nghiệp của mình. Cảm ơn các bạn đã lắng nghe tập podcast hôm nay!'''
    },
    'bi-an-cua-vu-tru': {
        'title': 'Bí ẩn của vũ trụ - Hố đen và Vật chất tối',
        'desc': 'Khám phá những bí ẩn chưa được giải đáp của vũ trụ bao la. Từ hố đen siêu khối lượng đến vật chất tối bí ẩn, hành trình tìm hiểu cosmos cùng các nhà khoa học hàng đầu Việt Nam.',
        'content': '''Chào mừng quý thính giả đến với PodcastHub. Hôm nay, chúng ta sẽ tạm gác lại những bộn bề của cuộc sống thường nhật, để cùng nhau ngước nhìn lên bầu trời đêm và khám phá những bí ẩn vĩ đại nhất của vũ trụ bao la. Chủ đề của chúng ta hôm nay: Hố đen và Vật chất tối.

Từ thuở sơ khai, con người đã luôn khao khát hiểu được vũ trụ rộng lớn ngoài kia. Và một trong những hiện tượng kỳ bí nhất, khiến các nhà khoa học đau đầu suốt nhiều thập kỷ, chính là Hố Đen. 

Hố đen không phải là một cái lỗ tĩnh lặng không có gì cả. Thực chất, nó là một vùng không gian có lực hấp dẫn mạnh đến mức không một vật chất nào, kể cả ánh sáng, có thể thoát ra được. Sự hình thành của hố đen thường bắt nguồn từ cái chết của một ngôi sao khổng lồ, khi nó sụp đổ vào chính lõi của mình dưới tác dụng của trọng lực. Mặc dù chúng ta không thể nhìn thấy hố đen một cách trực tiếp, nhưng thông qua sóng hấp dẫn và bức xạ từ các vật chất xung quanh đang bị nó nuốt chửng, chúng ta biết chắc chắn rằng chúng tồn tại. Hố đen không chỉ là "kẻ hủy diệt", mà còn đóng vai trò quan trọng trong việc định hình các thiên hà.

Bí ẩn thứ hai, còn mơ hồ và bao trùm hơn cả hố đen, chính là Vật chất tối. Theo các tính toán của vật lý thiên văn, tất cả những gì chúng ta nhìn thấy được - từ các vì sao, hành tinh, đến khí và bụi vũ trụ - chỉ chiếm khoảng 5% khối lượng của vũ trụ. Khoảng 27% là Vật chất tối, và phần còn lại là Năng lượng tối. 

Vật chất tối là gì? Chúng ta hoàn toàn không biết. Nó không phát ra ánh sáng, không tương tác với bức xạ điện từ, do đó hoàn toàn vô hình. Tuy nhiên, các nhà khoa học biết nó ở đó vì ảnh hưởng hấp dẫn của nó lên các chuyển động của các thiên hà. Nếu không có lực kéo hấp dẫn khổng lồ của vật chất tối, các thiên hà đang quay với tốc độ chóng mặt sẽ bị xé toạc và văng ra khắp nơi.

Nghiên cứu về hố đen và vật chất tối không chỉ là để thỏa mãn trí tò mò, mà nó còn giúp chúng ta hiểu được nguồn gốc, cấu trúc và định mệnh cuối cùng của vũ trụ mà chúng ta đang sống. Dù hành trình khám phá còn rất dài, nhưng mỗi phát hiện mới lại đưa chúng ta đến gần hơn với chân lý. Cảm ơn quý vị đã đồng hành cùng chúng tôi trong chuyến du hành không gian hôm nay!'''
    },
    'bien-doi-khi-hau': {
        'title': 'Biến đổi khí hậu - Thực trạng và giải pháp',
        'desc': 'Phân tích tình trạng biến đổi khí hậu hiện tại và các giải pháp tiềm năng. Vai trò của công nghệ xanh, năng lượng tái tạo trong cuộc chiến chống biến đổi khí hậu toàn cầu.',
        'content': '''Xin chào các thính giả của PodcastHub. Trong tập hôm nay, chúng ta sẽ đề cập đến một vấn đề mang tính sống còn đối với toàn nhân loại: Biến đổi khí hậu. Không còn là câu chuyện của tương lai xa xôi, biến đổi khí hậu đang diễn ra ngay lúc này và tác động trực tiếp đến cuộc sống của mỗi chúng ta.

Thực trạng hiện nay đáng báo động hơn bao giờ hết. Nhiệt độ trung bình toàn cầu liên tục phá vỡ các kỷ lục. Băng ở hai cực đang tan chảy với tốc độ chóng mặt, làm mực nước biển dâng cao, đe dọa trực tiếp đến các vùng đồng bằng ven biển, trong đó có Đồng bằng sông Cửu Long của Việt Nam. Các hiện tượng thời tiết cực đoan như siêu bão, hạn hán kéo dài, lũ lụt tàn khốc ngày càng xuất hiện với tần suất dày đặc và mức độ tàn phá khủng khiếp. Nguyên nhân chính? Hàng tỷ tấn khí nhà kính, chủ yếu là CO2, do con người thải ra môi trường mỗi năm từ việc đốt nhiên liệu hóa thạch, phá rừng và các hoạt động công nghiệp.

Đứng trước thảm họa này, chúng ta không thể khoanh tay đứng nhìn. Giải pháp quan trọng nhất và cấp bách nhất là chuyển đổi sang sử dụng năng lượng tái tạo. Năng lượng mặt trời, năng lượng gió và năng lượng địa nhiệt đang ngày càng trở nên rẻ hơn và dễ tiếp cận hơn. Việc dần loại bỏ than đá và dầu mỏ khỏi lưới điện là bước đi bắt buộc.

Bên cạnh năng lượng, giao thông vận tải cũng là một lĩnh vực cần cách mạng. Sự phát triển mạnh mẽ của xe điện, cùng với việc nâng cấp hệ thống giao thông công cộng, sẽ giúp giảm thiểu đáng kể lượng khí thải. 

Đồng thời, công nghệ xanh đang đóng vai trò then chốt. Từ các vật liệu xây dựng thân thiện với môi trường, hệ thống nông nghiệp thông minh tiết kiệm nước, đến các công nghệ thu giữ và lưu trữ carbon ngay tại các nhà máy. 

Tuy nhiên, công nghệ và chính sách là chưa đủ. Mỗi cá nhân chúng ta cũng phải là một phần của giải pháp. Bằng cách thay đổi những thói quen nhỏ hàng ngày: hạn chế rác thải nhựa, tiết kiệm điện nước, ưu tiên sử dụng sản phẩm tái chế và trồng thêm nhiều cây xanh. Cuộc chiến chống biến đổi khí hậu là cuộc chiến của tất cả mọi người, vì tương lai của chính con em chúng ta. Cảm ơn các bạn đã theo dõi tập podcast này.'''
    },
    'mindfulness-thien-dinh': {
        'title': 'Mindfulness - Thiền định trong thời đại số',
        'desc': 'Hướng dẫn thực hành thiền định và mindfulness cho người bận rộn. Cách giảm stress, tăng tập trung và cải thiện sức khỏe tinh thần trong cuộc sống hiện đại đầy áp lực.',
        'content': '''Chào mừng bạn đến với không gian tĩnh lặng của PodcastHub. Trong một thế giới ồn ào, vội vã, nơi chúng ta liên tục bị ném bom bởi hàng ngàn thông báo từ điện thoại và những áp lực công việc không tên, làm sao để tìm lại sự bình yên trong tâm hồn? Đó là lúc chúng ta cần đến Mindfulness - hay còn gọi là Chánh niệm.

Mindfulness không phải là một tôn giáo, cũng không phải là việc cố gắng dập tắt mọi suy nghĩ. Đơn giản, nó là trạng thái nhận biết và tập trung vào những gì đang diễn ra trong giây phút hiện tại, mà không phán xét. Khi bạn uống một tách cà phê, bạn chỉ hoàn toàn cảm nhận hương vị và độ ấm của nó, thay vì lo lắng về buổi họp sắp tới.

Trong thời đại số, sự chú ý của chúng ta bị chia năm xẻ bảy. Điều này dẫn đến tình trạng kiệt sức, lo âu và mất ngủ ngày càng gia tăng. Việc thực hành thiền định và chánh niệm giống như một liều thuốc giải độc cho tâm trí. 

Vậy làm thế nào để bắt đầu, đặc biệt là khi bạn quá bận rộn? Rất đơn giản. Bạn không cần phải ngồi vắt chéo chân trên đỉnh núi suốt hàng giờ. Hãy bắt đầu với 5 phút mỗi ngày. Tìm một chỗ ngồi yên tĩnh, nhắm mắt lại và tập trung vào hơi thở của mình. Cảm nhận không khí đi vào và đi ra qua cánh mũi. 

Tất nhiên, tâm trí bạn sẽ liên tục đi lang thang. Bạn sẽ nghĩ về hóa đơn tiền điện, về câu nói khó nghe của đồng nghiệp. Đừng bực tức. Việc tâm trí lang thang là hoàn toàn tự nhiên. Khi nhận ra điều đó, hãy nhẹ nhàng, rất nhẹ nhàng, kéo sự chú ý của bạn quay trở lại với hơi thở. Đó chính là cốt lõi của thiền định.

Bạn cũng có thể thực hành chánh niệm trong các hoạt động hàng ngày. Khi đi bộ, hãy cảm nhận bàn chân chạm đất. Khi rửa bát, hãy chú tâm vào dòng nước và bọt xà phòng. 

Duy trì thói quen này, bạn sẽ nhận thấy những sự thay đổi kỳ diệu. Mức độ căng thẳng giảm đi, khả năng tập trung tăng lên, và quan trọng nhất, bạn học được cách đối diện với những cảm xúc tiêu cực một cách bình tĩnh và sáng suốt hơn. Hãy hít một hơi thật sâu, và mỉm cười với giây phút hiện tại. Cảm ơn bạn đã lắng nghe.'''
    },
    'hoc-lap-trinh-2025': {
        'title': 'Học lập trình từ đâu trong 2025?',
        'desc': 'Hướng dẫn lộ trình học lập trình hiệu quả cho người mới bắt đầu trong năm 2025. So sánh các ngôn ngữ, framework phổ biến và lời khuyên từ các developer senior.',
        'content': '''Xin chào các bạn thính giả đam mê công nghệ. Chào mừng quay trở lại với PodcastHub. Chủ đề hôm nay chắc chắn sẽ rất hữu ích cho những ai đang muốn bước chân vào ngành IT: Học lập trình từ đâu trong năm 2025?

Thế giới lập trình luôn vận động và thay đổi không ngừng. Nếu như cách đây vài năm, việc học code chỉ xoay quanh việc thuộc cú pháp, thì năm 2025, với sự hỗ trợ mạnh mẽ của AI như GitHub Copilot hay ChatGPT, cách chúng ta học và làm việc đã hoàn toàn khác. 

Câu hỏi đầu tiên và quan trọng nhất: Nên bắt đầu học ngôn ngữ nào? Câu trả lời phụ thuộc vào mục tiêu của bạn. 

Nếu bạn muốn trở thành Web Developer, phát triển các trang web và ứng dụng trên trình duyệt, thì JavaScript vẫn là vị vua không thể lật đổ. Hãy bắt đầu với nền tảng HTML, CSS, sau đó đào sâu vào JavaScript. Tiếp theo, hãy làm quen với các framework hiện đại như React hay Vue.js. 

Nếu bạn đam mê Phân tích dữ liệu, Trí tuệ nhân tạo hoặc muốn làm Backend, Python là sự lựa chọn số một. Cú pháp của Python rất trong sáng, dễ đọc và dễ học đối với người mới. Hơn nữa, hệ sinh thái thư viện của Python cho AI và Data Science là vô đối.

Nếu bạn muốn tạo ra các ứng dụng di động, hãy tìm hiểu về Flutter hoặc React Native. Đây là những công nghệ cho phép bạn viết code một lần mà có thể chạy được trên cả iOS và Android.

Một lời khuyên quan trọng từ các senior developer trong năm 2025: Đừng học vẹt cú pháp. AI có thể viết code tốt hơn và nhanh hơn bạn. Thay vào đó, hãy rèn luyện tư duy logic, kỹ năng phân tích vấn đề và thiết kế hệ thống. Hãy hiểu rõ CÁCH mọi thứ hoạt động bên dưới, thay vì chỉ biết dùng framework một cách máy móc.

Cuối cùng, học lập trình là một cuộc đua marathon, không phải chạy nước rút. Hãy giữ vững sự kiên trì, liên tục làm các dự án thực tế (project-based learning) và đừng ngại hỏi khi gặp khó khăn. Cộng đồng lập trình viên luôn sẵn sàng giúp đỡ bạn. Chúc các bạn có một khởi đầu suôn sẻ và thành công trên con đường trở thành những kỹ sư phần mềm xuất sắc. Xin chào và hẹn gặp lại!'''
    },
    'web3-blockchain-hype-hay-revolution': {
        'title': 'Web3 và Blockchain - Hype hay Revolution?',
        'desc': 'Phân tích khách quan về Web3, blockchain, NFT và DeFi. Đâu là tiềm năng thực sự và đâu chỉ là hype? Góc nhìn từ cả developer lẫn nhà đầu tư.',
        'content': '''Chào mừng quý thính giả đến với PodcastHub. Hôm nay, chúng ta sẽ cùng mổ xẻ một chủ đề đã từng làm mưa làm gió trên các diễn đàn công nghệ và tài chính: Web3 và Blockchain. Sau những cơn sốt giá của tiền điện tử, những bức tranh NFT bán với giá triệu đô, giờ đây, khi thị trường đã bình tâm trở lại, chúng ta hãy nhìn nhận một cách khách quan: Web3 là một cuộc cách mạng thực sự, hay chỉ là một trào lưu thổi phồng?

Trước hết, hãy nói về ý tưởng cốt lõi của Web3. Nếu Web1 là mạng lưới thông tin một chiều, Web2 là kỷ nguyên của mạng xã hội và nền tảng tương tác nơi dữ liệu của chúng ta bị kiểm soát bởi các ông lớn công nghệ như Google, Facebook. Thì Web3 hứa hẹn một internet phi tập trung. Ở đó, quyền lực và dữ liệu được trả lại cho người dùng thông qua công nghệ Blockchain.

Đứng ở góc độ một cuộc cách mạng (Revolution), Blockchain thực sự mang lại những giá trị đột phá. Công nghệ sổ cái phân tán giúp dữ liệu minh bạch, không thể bị giả mạo. Tài chính phi tập trung (DeFi) cho phép bất kỳ ai có kết nối internet đều có thể tiếp cận các dịch vụ vay, mượn, đầu tư mà không cần thông qua ngân hàng truyền thống. Hợp đồng thông minh (Smart Contract) tự động hóa các thỏa thuận một cách an toàn và chính xác. Đây là những nền tảng vững chắc có khả năng thay đổi cấu trúc tài chính và quản lý dữ liệu trong tương lai.

Tuy nhiên, ở góc độ thổi phồng (Hype), chúng ta phải thừa nhận rằng có rất nhiều dự án Web3 không mang lại giá trị thực tế. Trong giai đoạn bùng nổ, hàng ngàn token và dự án NFT được tạo ra chỉ với mục đích đầu cơ, vẽ ra những viễn cảnh hão huyền để hút tiền của các nhà đầu tư thiếu kiến thức. Trải nghiệm người dùng của các ứng dụng Web3 hiện tại vẫn còn quá phức tạp, khó tiếp cận đối với đại đa số công chúng, và rủi ro về bảo mật, hack, lừa đảo vẫn luôn rình rập.

Tóm lại, Web3 và Blockchain không phải là một trò lừa đảo, nhưng cũng chưa phải là một phép màu giải quyết mọi vấn đề. Nó là một công nghệ cốt lõi đang trong giai đoạn phát triển sơ khai. Những bong bóng đầu cơ đã vỡ, để lại không gian cho những dự án thực sự có giá trị, giải quyết bài toán thực tế tiếp tục xây dựng và phát triển. Cảm ơn các bạn đã lắng nghe và cùng suy ngẫm!'''
    },
    'quan-ly-tai-chinh-genz': {
        'title': 'Quản lý tài chính cá nhân cho GenZ',
        'desc': 'Hướng dẫn quản lý tài chính, tiết kiệm và đầu tư thông minh cho thế hệ GenZ. Từ budgeting cơ bản đến xây dựng portfolio đầu tư đầu tiên.',
        'content': '''Xin chào những người trẻ năng động của thế hệ GenZ! Các bạn đang nghe PodcastHub. Trong tập hôm nay, chúng ta sẽ nói về một chủ đề tưởng chừng khô khan nhưng lại quyết định chất lượng cuộc sống của bạn trong tương lai: Quản lý tài chính cá nhân.

Thế hệ GenZ lớn lên trong một kỷ nguyên bùng nổ thông tin và sự cám dỗ tiêu dùng. Từ trà sữa, mua sắm online, săn sale trên các sàn thương mại điện tử, cho đến những chuyến du lịch check-in sang chảnh. Áp lực đồng trang lứa (Peer pressure) khiến nhiều bạn trẻ rơi vào vòng xoáy kiếm tiền để tiêu xài, thậm chí lạm dụng thẻ tín dụng và mắc nợ khi tuổi đời còn rất trẻ. Vậy làm sao để làm chủ đồng tiền, thay vì làm nô lệ cho nó?

Bước đầu tiên và quan trọng nhất: Hãy lập ngân sách chi tiêu, hay còn gọi là Budgeting. Bạn không thể kiểm soát tiền nếu không biết nó đi về đâu. Hãy thử áp dụng quy tắc 50/30/20. Trong đó, 50% thu nhập dành cho các nhu cầu thiết yếu như nhà ở, ăn uống, đi lại. 30% dành cho sở thích cá nhân, giải trí. Và 20% bắt buộc phải dành cho tiết kiệm và trả nợ. Hãy ghi chép lại mọi khoản chi tiêu dù là nhỏ nhất, bạn sẽ bất ngờ khi thấy những khoản tiền rò rỉ mà mình không hề hay biết.

Bước thứ hai, xây dựng quỹ khẩn cấp. Cuộc sống luôn có những rủi ro bất ngờ: ốm đau, hỏng xe, hay mất việc. Hãy luôn dự trữ một khoản tiền mặt đủ để bạn sống từ 3 đến 6 tháng mà không có thu nhập. Quỹ khẩn cấp chính là chiếc phao cứu sinh giúp bạn không bị chìm trong khủng hoảng và không phải đi vay mượn với lãi suất cao.

Sau khi đã có quỹ khẩn cấp, bước thứ ba là Đầu tư. Gửi tiết kiệm ngân hàng tuy an toàn nhưng lãi suất khó có thể đánh bại lạm phát trong dài hạn. Hãy bắt đầu tìm hiểu về các kênh đầu tư như chứng khoán, chứng chỉ quỹ, hoặc ETF. Đối với các bạn trẻ, lợi thế lớn nhất chính là thời gian. Lãi kép sẽ biến những khoản đầu tư nhỏ đặn hàng tháng thành một khối tài sản khổng lồ sau 10, 20 năm. Đừng chờ đợi đến khi có nhiều tiền mới đầu tư, hãy bắt đầu ngay từ những số tiền nhỏ nhất.

Cuối cùng, đầu tư vào chính bản thân bạn là khoản đầu tư lợi nhuận cao nhất. Hãy học thêm kỹ năng mới, nâng cao sức khỏe và xây dựng các mối quan hệ chất lượng. Quản lý tài chính cá nhân là một thói quen, cần sự kỷ luật và kiên nhẫn. Chúc các bạn sớm đạt được tự do tài chính! Cảm ơn đã lắng nghe!'''
    }
}

sql_statements = []

for slug, data in episodes.items():
    # 1. Write text script
    txt_path = os.path.join('scripts', f'episode-{slug}.txt')
    with open(txt_path, 'w', encoding='utf-8') as f:
        f.write(data['content'])
        
    print(f"Generating audio for {slug}...")
    
    # 2. Run edge-tts
    mp3_path = os.path.join('app/public/uploads/audio', f'{slug}.mp3')
    if not os.path.exists(mp3_path) or os.path.getsize(mp3_path) < 100000:
        subprocess.run([
            'edge-tts',
            '--voice', 'vi-VN-HoaiMyNeural',
            '--file', txt_path,
            '--write-media', mp3_path
        ], check=True)
    else:
        print(f"Skipping {slug}, already exists.")
    
    # 3. Read duration using mutagen
    audio = MP3(mp3_path)
    total_seconds = int(audio.info.length)
    mins = total_seconds // 60
    secs = total_seconds % 60
    duration_str = f"{mins:02d}:{secs:02d}"
    print(f"-> Created {mp3_path} ({duration_str})")
    
    # 4. Generate SQL statement
    db_path = f"/uploads/audio/{slug}.mp3"
    desc = data['desc'].replace("'", "''")
    sql = f"UPDATE episodes SET audio_file = '{db_path}', duration = '{duration_str}', description = '{desc}' WHERE slug = '{slug}';"
    sql_statements.append(sql)

# Write SQL to update.sql
with open('update.sql', 'w', encoding='utf-8') as f:
    f.write('SET NAMES utf8mb4;\n')
    for sql in sql_statements:
        f.write(sql + '\n')

print("All done. Run 'docker exec -i podcast-mysql mysql -uroot -pSuperStr0ng!RootP@ss2024 podcast_db < update.sql' to update DB.")
