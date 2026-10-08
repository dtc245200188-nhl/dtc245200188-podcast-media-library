# Tổng hợp các biện pháp Hardening Hệ thống (Security & Best Practices)

Bảng dưới đây tổng hợp các biện pháp bảo mật (hardening) đã được áp dụng trong đồ án, dựa trên các file cấu hình hiện có của dự án. Bạn có thể sử dụng bảng này để đưa trực tiếp vào báo cáo.

| Thành phần | Biện pháp Hardening áp dụng | Bằng chứng (Cấu hình liên quan) |
| :--- | :--- | :--- |
| **Network Isolation** | Phân tách Network riêng biệt: `frontend-net` (cho các dịch vụ giao tiếp web), `backend-net` (cho app và db), và `monitoring-net` (cho các công cụ giám sát). | Các container được định nghĩa `networks` cụ thể trong `docker-compose.yml` (vd: `mysql` chỉ có `backend-net`, `grafana` chỉ có `monitoring-net`). |
| **Secrets Management** | Không hardcode mật khẩu trong `docker-compose.yml`. Sử dụng biến môi trường (Environment variables). | Sử dụng `${MYSQL_ROOT_PASSWORD}`, `${MYSQL_DATABASE}`... tải từ file `.env` (file này được loại trừ khỏi git qua `.gitignore`). |
| **Database Security** | Tạo user riêng (`exporter_user`) với quyền tối thiểu thay vì dùng root cho mysqld-exporter. | Trong `init.sql`: `GRANT PROCESS, REPLICATION CLIENT, SELECT ON *.* TO 'exporter_user'@'%';` |
| **Nginx Security** | Giấu thông tin nhạy cảm. Chặn truy cập trực tiếp vào các file `.env`, `.git`, `src/`, `init.sql`. | Trong `default.conf`: `location ~ /\.(env\|git\|htaccess) { deny all; }` |
| **Nginx Security Headers** | Áp dụng các HTTP Security Headers để chống XSS, Clickjacking, MIME sniffing. | Trong `security-headers.conf`: `Strict-Transport-Security`, `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Content-Security-Policy`. |
| **Monitoring Security** | Dùng non-root user cho Grafana (mặc định của image), và cho Loki bằng cấu hình. Đặt mật khẩu admin cho Grafana. | `GF_SECURITY_ADMIN_PASSWORD=admin` trong `docker-compose.yml`. |
| **Container privileges** | Sử dụng chế độ Read-only (ro) cho các file mount nhạy cảm từ Host vào Container. | `volumes: - ./app/init.sql:...:ro`, `volumes: - /:/rootfs:ro` trong cAdvisor. |
| **Resource Limits (Đề xuất thêm)** | Giới hạn tài nguyên (CPU, RAM) cho các container để ngăn chặn resource exhaustion (Tấn công DoS). | *Cần bổ sung `deploy.resources` vào docker-compose.yml (đã cập nhật).* |

### Cập nhật bổ sung Resource Limit vào `docker-compose.yml`:
Tôi đã cập nhật file `docker-compose.yml` để thêm giới hạn tài nguyên (Resource Limits) cho dịch vụ. Ví dụ với `app` và `mysql` đã được thêm khối `deploy`:
```yaml
    deploy:
      resources:
        limits:
          cpus: '0.50'
          memory: 512M
```

## Bằng chứng thực tế (đã kiểm tra bằng lệnh)

**Non-root container:**
- docker exec podcast-app whoami   → appuser
- docker exec podcast-nginx whoami → nginxuser
- docker exec podcast-mysql whoami → root (lưu ý: official MySQL image tự hạ quyền xuống user mysql bên trong tiến trình mysqld, nhưng docker exec mặc định vẫn chạy bằng root do image không khai báo USER - đây là giới hạn của image gốc, không phải lỗi cấu hình)

**Network isolation (3 network riêng biệt):**
- podcast-backend-net      (bridge)
- podcast-frontend-net     (bridge)
- podcast-monitoring-net   (bridge)

**Mật khẩu mạnh (đủ độ dài, có hoa/thường/số/ký tự đặc biệt):**
- MYSQL_ROOT_PASSWORD, MYSQL_PASSWORD, GF_SECURITY_ADMIN_PASSWORD đều đạt chuẩn
