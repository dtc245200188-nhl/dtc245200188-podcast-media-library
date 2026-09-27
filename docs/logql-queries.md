# LogQL Queries cho Hệ thống Podcast

Đây là 3 câu truy vấn LogQL mẫu để kiểm tra log hệ thống qua Loki/Grafana:

## 1. Tìm các lỗi (ERROR/50x) từ Nginx và PHP
Truy vấn này lọc ra các log có chứa từ khoá error hoặc mã lỗi HTTP 500 từ các container Nginx và App.

```logql
{container=~"podcast-(nginx|app)"} |= "error" or |= " 50"
```

## 2. Thống kê số lượng request mỗi phút theo IP
Truy vấn này đếm số lượng HTTP request đến Nginx từ các IP khác nhau, tính trong khoảng thời gian 1 phút. Hữu ích để phát hiện DDoS hoặc scraping.

```logql
sum by (ip) (rate({container="podcast-nginx"} | json | line_format "{{.remote_addr}}" [1m]))
```
*(Lưu ý: Nginx log cần được cấu hình dạng JSON hoặc dùng regex extract `remote_addr` nếu dùng log format mặc định)*
Ví dụ dùng regex với log mặc định:
```logql
sum by (ip) (rate({container="podcast-nginx"} |~ "^(?P<ip>\\S+)" [1m]))
```

## 3. Xem log chậm (Slow Queries/Requests > 1s)
Phân tích log của PHP-FPM hoặc MySQL để tìm các request xử lý lâu hơn 1 giây.

```logql
{container="podcast-app"} |= "GET" | regexp "(?P<req_time>\\d+\\.\\d+)s$" | req_time > 1
```
