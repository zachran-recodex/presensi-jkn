#!/bin/bash

# Script untuk setup struktur directory dan file konfigurasi
# Jalankan dari /home/deploy/presensi-jkn

echo "🚀 Setting up directory structure..."

# Create directories
mkdir -p {nginx,mysql,storage/{app,logs,framework/{cache,sessions,views}}}
mkdir -p laravel-app/docker/{apache,supervisor}

echo "📁 Creating configuration files..."

# Create nginx.conf
cat > nginx/nginx.conf << 'EOF'
user nginx;
worker_processes auto;
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
    use epoll;
    multi_accept on;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logging format
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';

    # Basic settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;

    # Gzip settings
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Security headers
    add_header X-Frame-Options DENY always;
    add_header X-Content-Type-Options nosniff always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Include server configurations
    include /etc/nginx/conf.d/*.conf;
}
EOF

# Set permissions
sudo chown -R $USER:$USER .
chmod +x setup-structure.sh

echo "✅ Directory structure created successfully!"
echo "📝 Next steps:"
echo "   1. Copy your Laravel application to laravel-app/"
echo "   2. Create the configuration files shown in the tutorial"
echo "   3. Run docker-compose up -d"
