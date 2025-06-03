#!/bin/bash

# SSL Certificate setup untuk Website Presensi Laravel
# Menggunakan Let's Encrypt dan Certbot

echo "🔒 Setting up SSL Certificate with Let's Encrypt..."

# Domain configuration
DOMAIN="jakakuasanusantara.web.id"
WWW_DOMAIN="www.jakakuasanusantara.web.id"
EMAIL="admin@jakakuasanusantara.web.id"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if domain is pointing to this server
echo "🔍 Checking DNS configuration..."
CURRENT_IP=$(curl -s ifconfig.me)
DOMAIN_IP=$(dig +short $DOMAIN)

if [ "$CURRENT_IP" != "$DOMAIN_IP" ]; then
    print_warning "Domain $DOMAIN is not pointing to this server ($CURRENT_IP vs $DOMAIN_IP)"
    print_warning "Please update your DNS settings before continuing"
    read -p "Do you want to continue anyway? (y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Check if containers are running
if ! docker-compose ps | grep -q "Up"; then
    print_error "Docker containers are not running. Please start them first with: docker-compose up -d"
    exit 1
fi

echo "📝 Requesting SSL certificate for:"
echo "   - $DOMAIN"
echo "   - $WWW_DOMAIN"
echo "   - Email: $EMAIL"
echo ""

# Create SSL directory
mkdir -p nginx/ssl

# Request SSL certificate
echo "🚀 Requesting SSL certificate..."
docker-compose run --rm certbot certonly \
    --webroot \
    --webroot-path=/var/www/html/public \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email \
    -d $DOMAIN \
    -d $WWW_DOMAIN

if [ $? -eq 0 ]; then
    print_success "SSL certificate obtained successfully!"

    # Update Nginx configuration to use HTTPS
    echo "🔧 Updating Nginx configuration for HTTPS..."

    # Restart Nginx to load new configuration
    docker-compose restart nginx

    print_success "SSL setup completed!"
    echo ""
    echo "🌐 Your website is now available at:"
    echo "   https://$DOMAIN"
    echo "   https://$WWW_DOMAIN"
    echo ""
    echo "🔄 Setting up automatic certificate renewal..."

    # Create renewal script
    cat > /home/deploy/renew-ssl.sh << 'EOF'
#!/bin/bash
cd /home/deploy/presensi-jkn
docker-compose run --rm certbot renew --quiet
docker-compose restart nginx
EOF

    chmod +x /home/deploy/renew-ssl.sh

    # Add to crontab for automatic renewal
    (crontab -l 2>/dev/null; echo "0 3 * * 1 /home/deploy/renew-ssl.sh > /var/log/ssl-renewal.log 2>&1") | crontab -

    print_success "Automatic SSL renewal set up (runs weekly)"

else
    print_error "Failed to obtain SSL certificate"
    echo ""
    echo "🔧 Troubleshooting steps:"
    echo "1. Check if domain is pointing to this server"
    echo "2. Ensure ports 80 and 443 are open"
    echo "3. Check if website is accessible via HTTP first"
    echo "4. Verify DNS propagation: https://dnschecker.org"
    echo ""
    echo "You can retry with: ./setup-ssl.sh"
    exit 1
fi

echo ""
echo "🔒 SSL Certificate Information:"
docker-compose run --rm certbot certificates

print_success "SSL setup completed successfully! 🎉"
