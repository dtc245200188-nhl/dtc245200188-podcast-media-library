import os
from docx import Document
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_BREAK
from docx.oxml.ns import qn
from docx.oxml import OxmlElement

def add_heading(doc, text, level):
    heading = doc.add_heading(text, level=level)
    for run in heading.runs:
        run.font.name = 'Times New Roman'
        if level == 1:
            run.font.size = Pt(16)
        else:
            run.font.size = Pt(14)
        run.font.color.rgb = RGBColor(0, 0, 0)
        run.font.bold = True

def add_paragraph(doc, text, bold=False, italic=False, align=None):
    p = doc.add_paragraph()
    if align:
        p.alignment = align
    run = p.add_run(text)
    run.font.name = 'Times New Roman'
    run.font.size = Pt(13)
    run.bold = bold
    run.italic = italic
    return p

def add_code_block(doc, text):
    p = doc.add_paragraph()
    # Add light gray background
    p_format = p.paragraph_format
    p_format.left_indent = Inches(0.5)
    run = p.add_run(text)
    run.font.name = 'Courier New'
    run.font.size = Pt(11)

def main():
    doc = Document()
    
    # -----------------------------
    # 1. TRANG BÌA
    # -----------------------------
    # Setup margins for cover
    sections = doc.sections
    for section in sections:
        section.top_margin = Inches(1)
        section.bottom_margin = Inches(1)
        section.left_margin = Inches(1.25)
        section.right_margin = Inches(1)

    p = add_paragraph(doc, "TRƯỜNG ĐẠI HỌC CÔNG NGHỆ THÔNG TIN VÀ TRUYỀN THÔNG\nKHOA CÔNG NGHỆ THÔNG TIN\n\n\n\n\n\n", align=WD_ALIGN_PARAGRAPH.CENTER)
    for run in p.runs:
        run.font.size = Pt(14)
        run.bold = True

    p = add_paragraph(doc, "BÁO CÁO MÔN HỌC\nTRIỂN KHAI VÀ QUẢN TRỊ HỆ THỐNG PHẦN MỀM", align=WD_ALIGN_PARAGRAPH.CENTER)
    for run in p.runs:
        run.font.size = Pt(20)
        run.bold = True

    p = add_paragraph(doc, "\n\nWebsite Podcast/Media Library (Đề 37)\n\n\n", align=WD_ALIGN_PARAGRAPH.CENTER)
    for run in p.runs:
        run.font.size = Pt(18)
        run.bold = True

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("Sinh viên thực hiện:\n")
    run.font.name = 'Times New Roman'
    run.font.size = Pt(14)
    run.bold = True
    
    run = p.add_run("Họ tên: Nông Hoàng Long\nMSSV: dtc245200188\nLớp: CNTT K23G\n\n")
    run.font.name = 'Times New Roman'
    run.font.size = Pt(14)

    run = p.add_run("Giảng viên hướng dẫn: .......................................\n\n\n\n\n\n\n")
    run.font.name = 'Times New Roman'
    run.font.size = Pt(14)
    run.bold = True

    p = add_paragraph(doc, "Năm học 2026", align=WD_ALIGN_PARAGRAPH.CENTER)
    for run in p.runs:
        run.font.size = Pt(14)
        run.bold = True

    doc.add_page_break()

    # -----------------------------
    # 2. MỤC LỤC
    # -----------------------------
    add_heading(doc, "MỤC LỤC", level=1)
    
    # Add actual TOC field code via XML
    paragraph = doc.add_paragraph()
    run = paragraph.add_run()
    fldChar = OxmlElement('w:fldChar')
    fldChar.set(qn('w:fldCharType'), 'begin')
    instrText = OxmlElement('w:instrText')
    instrText.set(qn('xml:space'), 'preserve')
    instrText.text = 'TOC \\o "1-3" \\h \\z \\u'
    fldChar2 = OxmlElement('w:fldChar')
    fldChar2.set(qn('w:fldCharType'), 'separate')
    fldChar3 = OxmlElement('w:fldChar')
    fldChar3.set(qn('w:fldCharType'), 'end')
    
    run._r.append(fldChar)
    run._r.append(instrText)
    run._r.append(fldChar2)
    run._r.append(fldChar3)
    
    add_paragraph(doc, "(Khi mở file Word, hãy bấm Ctrl+A rồi nhấn F9, chọn 'Update entire table' để cập nhật mục lục tự động)", italic=True)
    doc.add_page_break()

    # -----------------------------
    # 3. NỘI DUNG MỞ RỘNG ĐẦY ĐỦ
    # -----------------------------
    # Phần 1
    add_heading(doc, "Phần 1 - Giới thiệu & Kiến trúc hệ thống", level=1)
    
    add_heading(doc, "1.1 Giới thiệu bài toán và mục tiêu", level=2)
    text = (
        "Đồ án môn học Triển khai và Quản trị Hệ thống Phần mềm (Đề 37) yêu cầu xây dựng và triển khai một Website "
        "Podcast/Media Library. Mục tiêu của đồ án không chỉ là phát triển một ứng dụng web cơ bản với các tính năng "
        "nghe podcast, quản lý danh mục, tìm kiếm và trang quản trị, mà còn tập trung vào việc áp dụng các công nghệ "
        "tiên tiến trong việc đóng gói (containerization), triển khai tự động, cấu hình web server bảo mật, và xây dựng "
        "hệ thống giám sát (monitoring) toàn diện."
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "1.2 Công nghệ sử dụng", level=2)
    text = (
        "Dự án sử dụng một stack công nghệ hiện đại, đảm bảo tính mở rộng và bảo mật:\n"
        "- PHP 8.2: Ngôn ngữ lập trình chính cho ứng dụng web (chạy trên PHP-FPM).\n"
        "- MySQL 8.0: Hệ quản trị cơ sở dữ liệu quan hệ lưu trữ thông tin podcast, danh mục và người dùng quản trị.\n"
        "- Nginx: Đóng vai trò là Web Server và Reverse Proxy, xử lý HTTPS, cung cấp các security headers và điều hướng "
        "lưu lượng đến PHP-FPM.\n"
        "- Docker & Docker Compose: Công cụ đóng gói và quản lý các container, đảm bảo môi trường đồng nhất từ phát triển "
        "đến triển khai.\n"
        "- Prometheus: Thu thập các thông số giám sát (metrics) từ hệ thống và ứng dụng.\n"
        "- Grafana: Nền tảng hiển thị trực quan các metrics qua biểu đồ (Dashboards).\n"
        "- Loki & Promtail: Giải pháp thu thập và quản lý log tập trung. Promtail thu thập log từ Docker, Loki lưu trữ "
        "và Grafana dùng để truy vấn log.\n"
        "- phpMyAdmin: Giao diện quản trị cơ sở dữ liệu trực quan."
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "1.3 Sơ đồ và luồng dữ liệu kiến trúc hệ thống", level=2)
    text = (
        "Hệ thống được thiết kế dựa trên nguyên tắc tách biệt mạng (Network Isolation) để tối đa hóa bảo mật. Có 3 mạng "
        "(network) riêng biệt được khai báo trong Docker Compose:\n"
        "1. frontend-net: Mạng lưới duy nhất mà Nginx (Reverse Proxy) tham gia để tiếp nhận yêu cầu từ người dùng (User).\n"
        "2. backend-net: Nơi chứa PHP-FPM (App) và MySQL. Chỉ có Nginx mới có thể giao tiếp với PHP qua mạng này. MySQL "
        "bị ẩn hoàn toàn khỏi internet.\n"
        "3. monitoring-net: Dành riêng cho Prometheus, Grafana, Loki, Promtail và các exporters. Sự tách biệt này giúp "
        "ngăn chặn kẻ tấn công lợi dụng các lỗ hổng (nếu có) ở ứng dụng để truy cập vào hệ thống giám sát."
    )
    add_paragraph(doc, text)
    
    try:
        doc.add_picture("docs/screenshots/01-architecture.png", width=Inches(6.0))
        add_paragraph(doc, "Hình 1: Sơ đồ kiến trúc hệ thống PodcastHub", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)
    except:
        add_paragraph(doc, "[Lỗi: Không tìm thấy ảnh docs/screenshots/01-architecture.png]")
        
    text = (
        "Luồng dữ liệu xử lý yêu cầu: User/Browser kết nối tới Nginx qua cổng 8888 (HTTP) hoặc 8889 (HTTPS). Nginx sẽ "
        "kiểm tra bảo mật và điều hướng các request xử lý logic (như .php) sang container PHP-FPM thông qua backend-net. "
        "PHP-FPM sau đó truy vấn CSDL MySQL để lấy dữ liệu (các bài podcast, tài khoản) và trả về HTML/JSON cho Nginx, "
        "từ đó gửi lại cho User. Đồng thời, mọi hoạt động log và metrics sẽ được Promtail và các Exporter tự động "
        "thu thập qua monitoring-net."
    )
    add_paragraph(doc, text)
    
    # Phần 2
    add_heading(doc, "Phần 2 - Quản lý mã nguồn GitHub", level=1)
    text = (
        "Dự án tuân thủ quy trình quản lý mã nguồn qua Git và GitHub, giúp kiểm soát phiên bản và cộng tác hiệu quả.\n"
        "URL Repository: https://github.com/dtc245200188-nhl/dtc245200188-podcast-media-library"
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "2.1 Cấu trúc thư mục", level=2)
    text = (
        "Cây thư mục của dự án được tổ chức rõ ràng theo chức năng:\n"
        "- app/: Chứa mã nguồn PHP của ứng dụng web, file init.sql, Dockerfile cho PHP, và cấu hình php.ini.\n"
        "- nginx/: Chứa cấu hình Nginx (nginx.conf, default.conf, security-headers.conf), SSL certificates và Dockerfile Nginx.\n"
        "- monitoring/: Chứa thư mục prometheus/ và grafana/, với các file cấu hình scraping, provisioning datasources và dashboards.\n"
        "- loki/: Chứa cấu hình loki-config.yml và promtail-config.yml cho hệ thống quản lý log.\n"
        "- docs/: Nơi lưu trữ tài liệu báo cáo, screenshot và các file markdown tài liệu."
    )
    add_paragraph(doc, text)

    add_heading(doc, "2.2 Quản lý cấu hình và Gitignore", level=2)
    text = (
        "Để bảo mật thông tin nhạy cảm, dự án sử dụng tệp .env (Môi trường ảo) để lưu trữ mật khẩu root MySQL, user MySQL, "
        "và mật khẩu admin Grafana. File .env này được định nghĩa rõ ràng trong file .gitignore nhằm ngăn chặn việc rò rỉ "
        "credentials lên GitHub. Thay vào đó, tệp .env.example được cung cấp với các giá trị mẫu, người dùng chỉ cần copy "
        "sang .env khi bắt đầu triển khai."
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "2.3 Quy trình Commit", level=2)
    text = (
        "Trong suốt quá trình phát triển, các commit được chia nhỏ theo từng chức năng (feature) và cấu hình cụ thể. "
        "Ví dụ: Các mốc commit cho việc khởi tạo dự án, xây dựng giao diện web, tích hợp Docker, thiết lập Nginx, "
        "bổ sung cấu trúc giám sát (Prometheus, Grafana), và hoàn thiện báo cáo hardening. Tổng số commit thể hiện "
        "sự phát triển liên tục và có tính lịch sử."
    )
    add_paragraph(doc, text)

    # Phần 3
    add_heading(doc, "Phần 3 - Triển khai ứng dụng & Database", level=1)
    add_heading(doc, "3.1 Chức năng website Podcast", level=2)
    text = (
        "Website Podcast được xây dựng với đầy đủ tính năng cho một nền tảng Media Library. Phía người dùng (Client) "
        "có Trang chủ hiển thị danh sách podcast mới nhất, trang Danh mục phân loại các podcast (Công nghệ, Khoa học...), "
        "trang Chi tiết tập cho phép nghe audio trực tiếp qua Custom HTML5 Audio Player, và thanh tìm kiếm podcast nhanh. "
        "Phía Quản trị (Admin) hỗ trợ tính năng CRUD (Thêm, Sửa, Xóa) cho tập podcast và danh mục, sau khi đăng nhập thành công."
    )
    add_paragraph(doc, text)
    try:
        doc.add_picture("docs/screenshots/02-website.png", width=Inches(6.0))
        add_paragraph(doc, "Hình 2: Giao diện chính của Website Podcast", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)
    except:
        add_paragraph(doc, "[Lỗi: Không tìm thấy ảnh docs/screenshots/02-website.png]")

    add_heading(doc, "3.2 Cấu trúc cơ sở dữ liệu", level=2)
    text = (
        "Cơ sở dữ liệu MySQL gồm 3 bảng chính có quan hệ mật thiết:\n"
        "- categories: Chứa danh sách các chuyên mục podcast.\n"
        "- episodes: Bảng trung tâm lưu trữ thông tin tập podcast (tiêu đề, mô tả, link audio, link ảnh bìa, ngày đăng). "
        "Bảng này liên kết với bảng categories thông qua khóa ngoại category_id.\n"
        "- admin_users: Lưu trữ tài khoản quản trị (với mật khẩu được băm bảo mật)."
    )
    add_paragraph(doc, text)
    try:
        doc.add_picture("docs/screenshots/03-phpmyadmin.png", width=Inches(6.0))
        add_paragraph(doc, "Hình 3: Quản lý cấu trúc Database qua phpMyAdmin", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)
    except:
        add_paragraph(doc, "[Lỗi: Không tìm thấy ảnh docs/screenshots/03-phpmyadmin.png]")

    add_heading(doc, "3.3 Cấu trúc Dockerfile", level=2)
    text = (
        "Dockerfile của ứng dụng web kế thừa từ base image php:8.2-fpm. Lớp image này rất nhẹ nhưng vẫn mạnh mẽ. "
        "Trong Dockerfile, chúng ta cài đặt bổ sung extension mysqli (thông qua docker-php-ext-install mysqli) để PHP "
        "có thể kết nối với MySQL. Để tăng tính bảo mật, chúng ta phân quyền mã nguồn (chown) cho user non-root mặc định "
        "của image (www-data), tránh tình trạng chạy mã với quyền cao nhất."
    )
    add_paragraph(doc, text)

    # Phần 4
    add_heading(doc, "Phần 4 - Nginx Reverse Proxy", level=1)
    text = (
        "Nginx được triển khai làm cửa ngõ (Reverse Proxy) duy nhất giao tiếp với môi trường ngoài. Mọi truy cập vào web, "
        "Dù là HTTP hay HTTPS, đều phải đi qua Nginx. Nginx xử lý việc phân giải SSL (chứng chỉ tự ký) và điều hướng "
        "đúng route tới backend. Các request tới cổng 8888 (HTTP) sẽ được tự động redirect vĩnh viễn (301) sang cổng 8889 (HTTPS)."
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "4.1 HTTP Security Headers", level=2)
    text = (
        "Để chống lại các hình thức tấn công phổ biến trên môi trường web, file security-headers.conf đã được tích hợp "
        "với các cơ chế:\n"
        "- Strict-Transport-Security (HSTS): Yêu cầu trình duyệt luôn dùng HTTPS trong tương lai, ngăn chặn tấn công downgrade.\n"
        "- X-Frame-Options: (SAMEORIGIN) Chống tấn công Clickjacking bằng cách không cho phép nhúng web vào iFrame của tên miền khác.\n"
        "- X-Content-Type-Options: (nosniff) Buộc trình duyệt tuân thủ MIME type của server, chống lỗi sniffing type.\n"
        "- X-XSS-Protection: Bật bộ lọc XSS tích hợp của trình duyệt cũ.\n"
        "- Content-Security-Policy (CSP): Hạn chế nghiêm ngặt nguồn tài nguyên tĩnh (script, style, img) được tải về, "
        "ngăn chặn mã độc XSS từ bên ngoài."
    )
    add_paragraph(doc, text)

    # Phần 5
    add_heading(doc, "Phần 5 - Prometheus + Grafana", level=1)
    text = (
        "Hệ thống giám sát (Monitoring) là phần không thể thiếu để đảm bảo dịch vụ hoạt động ổn định. Prometheus đóng "
        "vai trò trung tâm, hoạt động theo cơ chế pull (scrape metrics). Với chu kỳ 15 giây (scrape_interval: 15s), "
        "Prometheus sẽ gọi API tới các exporter để thu thập dữ liệu."
    )
    add_paragraph(doc, text)
    
    text = (
        "Các Exporters được triển khai:\n"
        "- cAdvisor: Giám sát toàn bộ container đang chạy trên host (CPU, Memory, Network của từng container).\n"
        "- node-exporter: Thu thập các số liệu của hệ điều hành host (được ánh xạ volume /proc, /sys).\n"
        "- mysqld-exporter: Giám sát số liệu nội bộ của MySQL (số query, cache, connection) với user giới hạn."
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "5.1 Grafana Provisioning", level=2)
    text = (
        "Grafana được cấu hình Provisioning tự động. File datasources.yml định nghĩa trước các kết nối tới Prometheus "
        "và Loki mà không cần thao tác tay trên UI. File dashboards/dashboard.yml trỏ tới file JSON để tự khởi tạo "
        "bảng điều khiển ngay khi container khởi động."
    )
    add_paragraph(doc, text)
    try:
        doc.add_picture("docs/screenshots/04-grafana-dashboard.png", width=Inches(6.0))
        add_paragraph(doc, "Hình 4: Dashboard Grafana giám sát CPU/Memory", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)
    except:
        add_paragraph(doc, "[Lỗi: Không tìm thấy ảnh docs/screenshots/04-grafana-dashboard.png]")
    
    text = (
        "Ý nghĩa biểu đồ: Biểu đồ ở Hình 4 thể hiện chính xác mức tiêu thụ CPU và Memory của hệ thống. Nhờ đó, người "
        "quản trị có thể phát hiện sớm các hiện tượng tràn bộ nhớ (memory leak) hoặc nghẽn cổ chai CPU để nâng cấp kịp thời."
    )
    add_paragraph(doc, text)

    # Phần 6
    add_heading(doc, "Phần 6 - Loki + Promtail + LogQL", level=1)
    text = (
        "Loki là công cụ quản lý log được thiết kế tối ưu với Prometheus, và Promtail là agent gửi log. Promtail "
        "được cấu hình mount với /var/run/docker.sock, cho phép nó lấy trực tiếp mọi log sinh ra từ standard output (stdout) "
        "của tất cả các container. Nó tự động gắn nhãn (label) container_name để dễ truy xuất."
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "6.1 Các truy vấn LogQL thực tế", level=2)
    text = "Dưới đây là một số câu truy vấn LogQL đã được chạy thử nghiệm trên Grafana:"
    add_paragraph(doc, text)
    
    add_code_block(doc, '{container="/podcast-nginx"} |= "error" or {container="/podcast-nginx"} |~ "HTTP/1.[01]\\" 50[0-9]"')
    add_paragraph(doc, "Ý nghĩa: Lọc tất cả các dòng log có chứa chữ 'error' hoặc báo lỗi server HTTP 5xx từ container Nginx.")
    
    add_code_block(doc, 'sum(count_over_time({container=~".+"}[5m])) by (container)')
    add_paragraph(doc, "Ý nghĩa: Tính tổng số lượng log phát sinh từ tất cả các container trong vòng 5 phút qua, dùng để đo lường lượng traffic/hoạt động của từng service.")
    
    add_code_block(doc, '{container="/podcast-app"} |~ "(?i)(Exception|Error)"')
    add_paragraph(doc, "Ý nghĩa: Lọc các log nghiêm trọng mang tính ngoại lệ (Exception) hoặc lỗi (Error) từ mã nguồn PHP của ứng dụng.")
    
    try:
        doc.add_picture("docs/screenshots/05-loki-query.png", width=Inches(6.0))
        add_paragraph(doc, "Hình 5: Truy vấn LogQL và hiển thị kết quả trên Grafana", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)
    except:
        add_paragraph(doc, "[Lỗi: Không tìm thấy ảnh docs/screenshots/05-loki-query.png]")

    # Phần 7
    add_heading(doc, "Phần 7 - Hardening bảo mật", level=1)
    text = (
        "Bảo mật hệ thống (Hardening) đã được chú trọng bằng nhiều lớp bảo vệ ở tầng Network, Container và Ứng dụng. "
        "Bảng dưới đây tóm tắt các biện pháp:"
    )
    add_paragraph(doc, text)
    
    # Bảng Hardening
    table = doc.add_table(rows=1, cols=3, style='Table Grid')
    hdr_cells = table.rows[0].cells
    hdr_cells[0].text = 'Thành phần'
    hdr_cells[1].text = 'Biện pháp Hardening áp dụng'
    hdr_cells[2].text = 'Bằng chứng (Cấu hình liên quan)'
    
    row_cells = table.add_row().cells
    row_cells[0].text = 'Network Isolation'
    row_cells[1].text = 'Phân tách Network riêng biệt: frontend-net, backend-net, và monitoring-net.'
    row_cells[2].text = 'Định nghĩa networks cụ thể trong docker-compose.yml'
    
    row_cells = table.add_row().cells
    row_cells[0].text = 'Secrets Management'
    row_cells[1].text = 'Không hardcode mật khẩu, dùng Environment variables.'
    row_cells[2].text = 'Sử dụng .env cho MYSQL_ROOT_PASSWORD, GF_SECURITY_ADMIN_PASSWORD'
    
    row_cells = table.add_row().cells
    row_cells[0].text = 'Database Security'
    row_cells[1].text = 'Tạo user riêng (exporter_user) quyền tối thiểu cho mysqld-exporter.'
    row_cells[2].text = 'Trong init.sql: GRANT PROCESS, REPLICATION CLIENT...'
    
    row_cells = table.add_row().cells
    row_cells[0].text = 'Nginx Security Headers'
    row_cells[1].text = 'Chống XSS, Clickjacking, MIME sniffing.'
    row_cells[2].text = 'Trong security-headers.conf (HSTS, CSP...)'

    row_cells = table.add_row().cells
    row_cells[0].text = 'Resource Limits'
    row_cells[1].text = 'Giới hạn tài nguyên (CPU, RAM) để chặn tấn công DoS (exhaustion).'
    row_cells[2].text = 'deploy.resources.limits.cpus trong docker-compose'
    
    add_paragraph(doc, "\nBằng chứng thực tế qua quá trình kiểm tra:", bold=True)
    text = (
        "- Non-root container: Các lệnh 'docker exec podcast-app whoami' đều trả về non-root (appuser/nginxuser). Riêng "
        "đối với MySQL, do giới hạn của image chính thức (official image) không khai báo chỉ thị USER, nên khi vào container "
        "bằng docker exec sẽ thấy quyền root, nhưng tiến trình mysqld bên trong đã tự hạ quyền xuống user mysql. Đây là sự thật "
        "kỹ thuật cần được chấp nhận.\n"
        "- Mật khẩu mạnh: Mật khẩu thiết lập trong file .env đều là chuỗi ký tự phức tạp.\n"
        "- Các Headers bảo mật thực sự hoạt động khi test bằng lệnh curl -I."
    )
    add_paragraph(doc, text)
    try:
        doc.add_picture("docs/screenshots/06-security-headers.png", width=Inches(6.0))
        add_paragraph(doc, "Hình 6: Phản hồi Security Headers của Nginx", italic=True, align=WD_ALIGN_PARAGRAPH.CENTER)
    except:
        add_paragraph(doc, "[Lỗi: Không tìm thấy ảnh docs/screenshots/06-security-headers.png]")

    # Phần 8
    add_heading(doc, "Phần 8 - Kết luận", level=1)
    text = (
        "Đồ án Website Podcast/Media Library đã hoàn thành đầy đủ 7 yêu cầu đề ra. Hệ thống không chỉ đáp ứng về "
        "mặt tính năng phục vụ nội dung âm thanh số mà còn đạt chuẩn rất cao về độ tin cậy trong quá trình triển khai."
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "Khó khăn & Giải pháp:", level=2)
    text = (
        "- Lỗi UTF-8 Encoding: Dữ liệu tiếng Việt lưu vào DB bị lỗi double-encode hoặc dấu hỏi. Giải pháp là thiết lập charset "
        "utf8mb4 trực tiếp ở DSN kết nối PDO trong PHP và charset mặc định của container MySQL.\n"
        "- Lỗi mysqld-exporter DATA_SOURCE_NAME deprecated: Phiên bản exporter mới đổi cú pháp. Giải pháp là cấu hình "
        "lại command line cờ kết nối đúng chuẩn DSN mới của Prometheus.\n"
        "- Lỗi Grafana crash do trùng lặp Datasource Provisioning: Việc khởi chạy container gặp xung đột nếu provisioning "
        "có uid trùng lặp. Giải quyết bằng cách dọn dẹp volume cũ (docker-compose down -v) và cấu hình chính xác file datasources.yml."
    )
    add_paragraph(doc, text)
    
    add_heading(doc, "Hướng phát triển tương lai:", level=2)
    text = (
        "Trong tương lai, dự án có thể mở rộng bằng cách tích hợp hệ thống lưu trữ đối tượng (S3-compatible như MinIO) "
        "để lưu file audio dung lượng lớn thay vì ổ cứng server. Đồng thời, có thể triển khai hệ thống CI/CD (GitHub Actions) "
        "để tự động test code, build image và cập nhật lên server mỗi khi có thay đổi."
    )
    add_paragraph(doc, text)
    
    # Setup footer page numbers
    for section in doc.sections:
        footer = section.footer
        p = footer.paragraphs[0] if footer.paragraphs else footer.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        
        # Adding page number via XML
        fldChar1 = OxmlElement('w:fldChar')
        fldChar1.set(qn('w:fldCharType'), 'begin')
        instrText = OxmlElement('w:instrText')
        instrText.set(qn('xml:space'), 'preserve')
        instrText.text = "PAGE"
        fldChar2 = OxmlElement('w:fldChar')
        fldChar2.set(qn('w:fldCharType'), 'separate')
        fldChar3 = OxmlElement('w:fldChar')
        fldChar3.set(qn('w:fldCharType'), 'end')
        
        run = p.add_run()
        run._r.append(fldChar1)
        run._r.append(instrText)
        run._r.append(fldChar2)
        run._r.append(fldChar3)
    
    # Save document
    doc.save('docs/BaoCao_DeTai37_Full.docx')

if __name__ == '__main__':
    main()
