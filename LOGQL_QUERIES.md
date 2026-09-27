# Các câu lệnh truy vấn LogQL mẫu

Để demo trong báo cáo, bạn hãy truy cập vào Grafana (http://localhost:3000), mở menu bên trái chọn **Explore**, sau đó chọn Data source là **Loki**. 

Bạn có thể copy/paste các câu truy vấn (LogQL) sau vào thanh tìm kiếm và bấm **Run query** để xem kết quả:

### 1. Lọc log lỗi (level=error hoặc HTTP status >= 500) của Nginx
Câu lệnh này sẽ lọc các log từ container nginx và chỉ hiển thị các dòng log có chứa "error" hoặc có mã trạng thái HTTP từ 500 trở lên.
```logql
{container="/podcast-nginx"} |= "error" or {container="/podcast-nginx"} |~ "HTTP/1.[01]\" 50[0-9]"
```

### 2. Đếm số request theo container trong 5 phút gần nhất
Câu lệnh này sẽ tính toán tổng số dòng log (tương đương số lượng request) phát sinh từ các container trong khoảng thời gian 5 phút gần nhất, giúp bạn xem tải của hệ thống.
```logql
sum(count_over_time({container=~".+"}[5m])) by (container)
```

### 3. Lọc log của PHP App có từ khóa "Exception" hoặc "Error"
Câu lệnh này lọc riêng log của container ứng dụng chính (podcast-app) để tìm các thông báo lỗi nghiêm trọng trong code PHP.
```logql
{container="/podcast-app"} |~ "(?i)(Exception|Error)"
```
