FROM nginx:1.25-alpine

# Install openssl for SSL certificate generation
RUN apk add --no-cache openssl

# Generate self-signed SSL certificate
RUN mkdir -p /etc/nginx/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/nginx/ssl/server.key \
    -out /etc/nginx/ssl/server.crt \
    -subj "/C=VN/ST=HCM/L=HoChiMinh/O=PodcastHub/CN=localhost"

# Create non-root user
RUN addgroup -g 1001 nginxgroup && \
    adduser -u 1001 -G nginxgroup -s /bin/sh -D nginxuser

# Create required directories and set permissions
RUN mkdir -p /var/cache/nginx/client_temp \
             /var/cache/nginx/proxy_temp \
             /var/cache/nginx/fastcgi_temp \
             /var/cache/nginx/uwsgi_temp \
             /var/cache/nginx/scgi_temp \
             /var/log/nginx \
             /var/run \
    && chown -R nginxuser:nginxgroup /var/cache/nginx \
    && chown -R nginxuser:nginxgroup /var/log/nginx \
    && chown -R nginxuser:nginxgroup /etc/nginx/ssl \
    && chown -R nginxuser:nginxgroup /etc/nginx/conf.d \
    && touch /var/run/nginx.pid \
    && chown nginxuser:nginxgroup /var/run/nginx.pid

# Copy configuration files
COPY nginx.conf /etc/nginx/nginx.conf
COPY conf.d/ /etc/nginx/conf.d/
COPY security-headers.conf /etc/nginx/security-headers.conf

# Set ownership of config files
RUN chown -R nginxuser:nginxgroup /etc/nginx

# Switch to non-root user
USER nginxuser

EXPOSE 8080 8443

CMD ["nginx", "-g", "daemon off;"]
