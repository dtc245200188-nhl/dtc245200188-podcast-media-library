#!/bin/sh
# =============================================
# Generate Self-Signed SSL Certificate
# =============================================
# This script generates a self-signed SSL certificate for development
# Run: chmod +x generate-ssl.sh && ./generate-ssl.sh

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout "${SCRIPT_DIR}/server.key" \
    -out "${SCRIPT_DIR}/server.crt" \
    -subj "/C=VN/ST=HCM/L=HoChiMinh/O=PodcastHub/CN=localhost"

echo "✅ SSL certificate generated successfully!"
echo "   Certificate: ${SCRIPT_DIR}/server.crt"
echo "   Private Key: ${SCRIPT_DIR}/server.key"
