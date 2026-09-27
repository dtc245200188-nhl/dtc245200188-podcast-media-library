---
title: BÁO CÁO MÔN TRIỂN KHAI VÀ QUẢN TRỊ HỆ THỐNG PHẦN MỀM
author: 
  - Nông Hoàng Long - dtc245200188
date: \today
---

# BÁO CÁO MÔN TRIỂN KHAI VÀ QUẢN TRỊ HỆ THỐNG PHẦN MỀM
**Đề tài:** Website Podcast/Media Library (Đề 37)

**Thông tin sinh viên:**
- Họ tên: Nông Hoàng Long
- Lớp: CNTT K23G
- MSSV: dtc245200188

---

## MỤC LỤC
1. Giới thiệu đề tài & Sơ đồ kiến trúc hệ thống
2. Source Code & GitHub
3. Triển khai Ứng dụng & Cơ sở dữ liệu (App + DB)
4. Cấu hình Nginx Reverse Proxy
5. Giám sát hệ thống (Prometheus + Grafana)
6. Quản lý Log tập trung (Loki + Promtail)
7. Tăng cường bảo mật (Hardening)
8. Kết luận & Hướng phát triển

---

## Phần 1: Giới thiệu đề tài & Sơ đồ kiến trúc

### 1.1 Giới thiệu
Dự án "Website Podcast/Media Library" là một nền tảng nghe podcast trực tuyến được xây dựng bằng PHP và MySQL, đóng gói hoàn toàn bằng Docker Compose. Hệ thống cung cấp các chức năng quản lý danh mục, tập podcast (audio, cover image), trình phát nhạc HTML5 tùy chỉnh, tìm kiếm và phân trang, cùng trang quản trị (Admin) an toàn.

### 1.2 Sơ đồ kiến trúc hệ thống
Kiến trúc hệ thống được chia thành 3 mạng (networks) riêng biệt để đảm bảo tính cô lập và bảo mật:
- **frontend-net**: `nginx`, `app`, `phpmyadmin`
- **backend-net**: `app`, `mysql`, `phpmyadmin`, `prometheus`, `mysqld-exporter`
- **monitoring-net**: `prometheus`, `grafana`, `node-exporter`, `cadvisor`, `loki`, `promtail`

![Kiến trúc hệ thống](docs/screenshots/01-architecture.png)

---

## Phần 2: Source Code & GitHub
Dự án được quản lý phiên bản qua Git và tuân thủ các quy tắc commit rõ ràng theo từng giai đoạn phát triển.
- Các file nhạy cảm như `.env`, thư mục `uploads/`, và thư mục chứa logs/metrics được loại trừ qua file `.gitignore`.
- Thay vì đẩy `password` lên GitHub, hệ thống sử dụng biến môi trường (Environment Variables) quản lý thông qua file `.env.example`.

---

## Phần 3: Triển khai Ứng dụng & Cơ sở dữ liệu (App + DB)

### 3.1 Ứng dụng PHP-FPM
- Sử dụng base image `php:8.2-fpm-alpine` nhằm tối ưu dung lượng.
- Cài đặt các extension cần thiết như PDO MySQL, GD, intl, mbstring.
- File `php.ini` được tùy chỉnh để tăng giới hạn upload file (audio/cover) lên 100MB và set Timezone `Asia/Ho_Chi_Minh`.
- Container chạy với quyền non-root (user `appuser`, UID 1000).

### 3.2 Cơ sở dữ liệu MySQL & phpMyAdmin
- Sử dụng MySQL 8.0 với các biến môi trường cấu hình tài khoản (`MYSQL_ROOT_PASSWORD`, `MYSQL_USER`, v.v.).
- Tự động khởi tạo database thông qua script `init.sql` (Schema, Seed data).
- Healthcheck được tích hợp để đảm bảo DB sẵn sàng trước khi App và Exporter kết nối.
- Cung cấp phpMyAdmin phục vụ nhu cầu quản trị database với giới hạn upload 100MB.

![Trang web Podcast](docs/screenshots/02-website.png)
![Giao diện phpMyAdmin](docs/screenshots/03-phpmyadmin.png)

---

## Phần 4: Cấu hình Nginx Reverse Proxy
- Nginx được cấu hình như một Reverse Proxy cho cả PHP-FPM (App) và phpMyAdmin.
- Sử dụng SSL tự ký (Self-signed certificate) cho giao thức HTTPS trên cổng 443. Hệ thống tự động chuyển hướng HTTP (80) sang HTTPS (443).
- FastCGI được cấu hình tinh chỉnh cache size.
- Ẩn phiên bản Nginx (`server_tokens off`).
- Container chạy dưới quyền non-root (`nginxuser`, UID 1001).

---

## Phần 5: Giám sát hệ thống (Prometheus + Grafana)

### 5.1 Prometheus & Exporters
- Cài đặt Prometheus để thu thập metrics của toàn hệ thống mỗi 15s.
- Tích hợp 3 exporters chính:
  - **Node Exporter**: Giám sát tài nguyên máy chủ.
  - **cAdvisor**: Giám sát tài nguyên tiêu thụ của các Docker containers.
  - **mysqld-exporter**: Giám sát hiệu năng và kết nối của MySQL database.

### 5.2 Grafana Dashboards
- Grafana (chạy ở cổng 3000) được kết nối sẵn với Prometheus và Loki thông qua cơ chế Auto-provisioning.
- Triển khai Dashboard tổng quan hiển thị mức độ sử dụng CPU/Memory của toàn hệ thống.

![Grafana Dashboard](docs/screenshots/04-grafana-dashboard.png)

---

## Phần 6: Quản lý Log tập trung (Loki + Promtail)
- **Loki**: Hệ thống lưu trữ và truy vấn log dạng chuỗi thời gian, chạy trên cổng 3100.
- **Promtail**: Agent thu thập toàn bộ log từ `docker.sock` của tất cả các container và gửi về Loki.

### 6.1 LogQL Queries minh họa
1. **Tìm lỗi (ERROR/500)** từ Nginx/App: `{container=~"podcast-(nginx|app)"} |= "error" or |= " 50"`
2. **Thống kê IP truy cập mỗi phút**: `sum by (ip) (rate({container="podcast-nginx"} |~ "^(?P<ip>\\S+)" [1m]))`
3. **Tìm request chậm (>1s)**: `{container="podcast-app"} |= "GET" | regexp "(?P<req_time>\\d+\\.\\d+)s$" | req_time > 1`

![Loki Explore Query](docs/screenshots/05-loki-query.png)

---

## Phần 7: Tăng cường bảo mật (Hardening)

- **Non-root Containers**: `app` và `nginx` đều chạy dưới custom UID thay vì root.
- **Network Isolation**: Giới hạn tối đa phạm vi truy cập (App không thấy Monitoring, Nginx không kết nối trực tiếp DB).
- **Security Headers**:
  - `Strict-Transport-Security (HSTS)`
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Content-Security-Policy`

![Security Headers/HTTPS](docs/screenshots/06-security-headers.png)

---

## Phần 8: Kết luận & Hướng phát triển
**Kết quả đạt được:** Hoàn thành đầy đủ các yêu cầu môn học. Đóng gói toàn bộ vòng đời phát triển từ Web App tới Monitoring, Logging và Security bằng Docker Compose.

**Hướng phát triển:** Tích hợp Alertmanager để gửi cảnh báo lỗi tự động qua Telegram/Email; Sử dụng SSL hợp lệ (Let's Encrypt) nếu triển khai production.
