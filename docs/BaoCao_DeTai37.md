# BÁO CÁO BÀI TẬP LỚN TRIỂN KHAI VÀ QUẢN TRỊ HỆ THỐNG PHẦN MỀM
**Đề tài 37: Website Podcast / Media Library**

**Sinh viên thực hiện:** Nông Hoàng Long  
**Mã số sinh viên (MSSV):** dtc245200188  
**Lớp / Khóa:** CNTT K23G  
**Giảng viên:** [TÊN GIẢNG VIÊN]  
**Thái Nguyên, 2026**

---

## Phần 1 - Giới thiệu & Kiến trúc hệ thống
### 1.1 Giới thiệu bài toán và mục tiêu
Đồ án môn học Triển khai và Quản trị Hệ thống Phần mềm (Đề 37) yêu cầu xây dựng và triển khai một Website Podcast/Media Library.

### 1.2 Sơ đồ và luồng dữ liệu kiến trúc hệ thống
Hệ thống được thiết kế dựa trên nguyên tắc tách biệt mạng (Network Isolation) để tối đa hóa bảo mật. Có 3 mạng (network) riêng biệt được khai báo trong Docker Compose:
1. frontend-net: Mạng lưới mà Nginx (Reverse Proxy), ứng dụng PHP (app) và phpMyAdmin tham gia để tiếp nhận yêu cầu từ người dùng (User).
2. backend-net: Nơi chứa PHP-FPM (App), MySQL, phpMyAdmin, Prometheus và mysqld-exporter. MySQL bị ẩn hoàn toàn khỏi internet.
3. monitoring-net: Dành riêng cho Prometheus, Grafana, Loki, Promtail và các exporters. Sự tách biệt này giúp ngăn chặn kẻ tấn công lợi dụng các lỗ hổng (nếu có) ở ứng dụng để truy cập vào hệ thống giám sát.

![Hình 1: Sơ đồ kiến trúc hệ thống PodcastHub](docs/screenshots/01-architecture.png)

## Phần 2 - Triển khai ứng dụng & Database
### 2.1 Chức năng website Podcast
![Hình 2: Giao diện chính của Website Podcast](docs/screenshots/02-website.png)

### 2.2 Cấu trúc cơ sở dữ liệu
![Hình 3: Quản lý cấu trúc Database qua phpMyAdmin](docs/screenshots/03-phpmyadmin.png)

### 2.3 Cấu trúc Dockerfile
Dockerfile của ứng dụng web kế thừa từ base image php:8.2-fpm-alpine. Lớp image này rất nhẹ nhưng vẫn mạnh mẽ. Trong Dockerfile, chúng ta cài đặt bổ sung extension PDO MySQL, GD, intl, mbstring để PHP có thể kết nối với MySQL và xử lý hình ảnh. Để tăng tính bảo mật, chúng ta tạo và phân quyền mã nguồn cho user appuser UID 1000, tránh tình trạng chạy mã với quyền root. Nginx cũng sử dụng image nginx 1.25-alpine và tạo user nginxuser UID 1001 để đảm bảo an toàn.

## Phần 3 - Prometheus + Grafana
![Hình 4: Dashboard Grafana giám sát CPU/Memory](docs/screenshots/04-grafana-dashboard.png)

## Phần 4 - Loki + Promtail + LogQL
### 4.1 Các truy vấn LogQL thực tế
Dưới đây là một số câu truy vấn LogQL đã được chạy thử nghiệm trên Grafana:

```logql
{container="podcast-nginx"} |= "error"
```
Ý nghĩa: Lọc tất cả các dòng log có chứa chữ error từ container Nginx.

```logql
{container="podcast-app"} |~ "(?i)(Exception|Error)"
```
Ý nghĩa: Lọc các log nghiêm trọng mang tính ngoại lệ hoặc lỗi.

```logql
sum(count_over_time({container="podcast-nginx"}[1m]))
```
Ý nghĩa: Đếm số lượng log phát sinh.

![Hình 5: Truy vấn LogQL và hiển thị kết quả trên Grafana](docs/screenshots/05-loki-query.png)

## Phần 5 - Hardening bảo mật
![Hình 6: Phản hồi Security Headers của Nginx](docs/screenshots/06-security-headers.png)

## Phần 6 - Khó khăn & Giải pháp
- Lỗi UTF-8 Encoding: dữ liệu bị double-encode khi import bằng client latin1; sửa bằng UPDATE ... SET col = CONVERT(CAST(CONVERT(col USING latin1) AS BINARY) USING utf8mb4) cho các cột bị lỗi.
- Lỗi mysqld-exporter restart liên tục ("no user specified in section or parent"): bản 0.20.0 không dùng DATA_SOURCE_NAME nữa; sửa bằng biến MYSQLD_EXPORTER_PASSWORD + flag --mysqld.username và --mysqld.address.
- Lỗi Grafana crash lặp: hai file datasource cùng đặt isDefault: true ("Only one datasource per organization can be marked as default"); sửa bằng xóa một file rồi restart.
- Lỗi chứng chỉ tự ký trên trình duyệt: dùng Advanced → Continue; kiểm tra bằng curl.exe -k.
