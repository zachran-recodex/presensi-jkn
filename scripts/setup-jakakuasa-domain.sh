#!/bin/bash

# Quick Domain Setup Script untuk jakakuasanusantara.web.id
# PT. Jaka Kuasa Nusantara - Website Presensi

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_header() {
    echo
    echo "=================================================="
    echo -e "${GREEN}$1${NC}"
    echo "=================================================="
}

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header "🌐 Setup Domain jakakuasanusantara.web.id"

echo -e "${BLUE}Panduan ini akan membantu Anda setup domain untuk Website Presensi${NC}"
echo -e "${BLUE}PT. Jaka Kuasa Nusantara dengan SSL certificate otomatis.${NC}"
echo

# Check prerequisites
print_status "Checking prerequisites..."

if [ ! -f "docker-compose.yml" ]; then
    print_error "docker-compose.yml not found! Run this script from project root."
    exit 1
fi

if ! command -v docker &> /dev/null; then
    print_error "Docker not installed! Please run ./deploy.sh first."
    exit 1
fi

# Get server information
SERVER_IP=$(curl -s ifconfig.me || echo "Unable to detect")
print_status "Server IP: $SERVER_IP"

# DNS Check
print_status "Checking DNS configuration..."
DOMAIN_IP=$(dig +short jakakuasanusantara.web.id | head -n1)
WWW_DOMAIN_IP=$(dig +short www.jakakuasanusantara.web.id | head -n1)

echo -e "Domain IP: ${BLUE}$DOMAIN_IP${NC}"
echo -e "WWW Domain IP: ${BLUE}$WWW_DOMAIN_IP${NC}"

if [[ "$DOMAIN_IP" != "$SERVER_IP" ]]; then
    print_warning "⚠️  DNS Configuration Required!"
    echo
    echo -e "${YELLOW}Please configure these DNS records in your domain panel:${NC}"
    echo -e "  ${GREEN}A Record:${NC}     jakakuasanusantara.web.id     → $SERVER_IP"
    echo -e "  ${GREEN}CNAME Record:${NC} www.jakakuasanusantara.web.id → jakakuasanusantara.web.id"
    echo
    echo -e "${BLUE}DNS propagation can take up to 48 hours worldwide.${NC}"
    echo -e "${BLUE}You can check propagation at: https://dnschecker.org${NC}"
    echo

    read -p "Continue with setup anyway? (y/N): " continue_setup
    if [[ $continue_setup != [yY] ]]; then
        print_status "Setup cancelled. Configure DNS first, then run this script again."
        exit 0
    fi

    DNS_OK=false
else
    print_status "✅ DNS configuration looks good!"
    DNS_OK=true
fi

# Phase 1: Initial setup without SSL
print_header "Phase 1: Initial Setup (HTTP)"

print_status "Updating environment configuration..."
if [ -f .env ]; then
    # Backup existing .env
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

    # Update APP_URL
    sed -i 's|APP_URL=.*|APP_URL=https://jakakuasanusantara.web.id|g' .env
    print_status "Updated APP_URL in .env"
else
    print_status "Copying environment template..."
    cp .env.production .env

    # Generate app key
    print_status "Generating application key..."
    docker-compose exec app php artisan key:generate --force || true
fi

print_status "Restarting containers..."
docker-compose down || true
docker-compose up -d

print_status "Waiting for services to be ready..."
sleep 30

# Test HTTP access
print_status "Testing HTTP access..."
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://jakakuasanusantara.web.id/health || echo "000")

if [ "$HTTP_STATUS" = "200" ]; then
    print_status "✅ HTTP access working!"
    HTTP_OK=true
elif [ "$HTTP_STATUS" = "000" ]; then
    print_warning "Could not connect to domain (DNS might not be ready)"
    HTTP_OK=false
else
    print_warning "HTTP returned status: $HTTP_STATUS"
    HTTP_OK=false
fi

# Phase 2: SSL Setup
if [ "$DNS_OK" = true ] && [ "$HTTP_OK" = true ]; then
    print_header "Phase 2: SSL Certificate Setup"

    read -p "Setup SSL certificate now? (Y/n): " setup_ssl
    if [[ $setup_ssl != [nN] ]]; then

        # Make SSL setup script executable
        chmod +x scripts/ssl-setup.sh

        print_status "Generating SSL certificate..."
        ./scripts/ssl-setup.sh generate

        if [ $? -eq 0 ]; then
            print_status "✅ SSL certificate installed!"

            # Setup auto-renewal
            ./scripts/ssl-setup.sh auto-renew

            SSL_OK=true
        else
            print_warning "SSL setup failed, but website is accessible via HTTP"
            SSL_OK=false
        fi
    else
        print_status "SSL setup skipped"
        SSL_OK=false
    fi
else
    print_header "Phase 2: SSL Setup (Skipped)"
    print_warning "DNS or HTTP not working, skipping SSL setup"
    print_status "You can setup SSL later with: ./scripts/ssl-setup.sh generate"
    SSL_OK=false
fi

# Phase 3: Application Configuration
print_header "Phase 3: Application Configuration"

print_status "Configuring Laravel for production..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

print_status "Running database migrations..."
docker-compose exec app php artisan migrate --force

print_status "Creating storage link..."
docker-compose exec app php artisan storage:link

print_status "Testing Face Recognition API..."
docker-compose exec app php artisan face:setup --test || print_warning "Face API test failed"

# Phase 4: Security & Monitoring
print_header "Phase 4: Security & Monitoring"

print_status "Configuring firewall..."
if command -v ufw &> /dev/null; then
    sudo ufw allow 22/tcp   # SSH
    sudo ufw allow 80/tcp   # HTTP
    sudo ufw allow 443/tcp  # HTTPS
    sudo ufw --force enable > /dev/null 2>&1
    print_status "✅ Firewall configured"
fi

print_status "Setting up domain monitoring..."
chmod +x scripts/setup-domain.sh
./scripts/setup-domain.sh > /dev/null 2>&1 || true

# Create quick monitoring script
cat > check-site.sh << 'EOF'
#!/bin/bash
echo "=== Website Status Check ==="
echo "HTTP Status: $(curl -s -o /dev/null -w "%{http_code}" http://jakakuasanusantara.web.id/health)"
echo "HTTPS Status: $(curl -s -o /dev/null -w "%{http_code}" https://jakakuasanusantara.web.id/health)"
echo "Services: $(docker-compose ps --filter status=running | wc -l) running"
echo "Last check: $(date)"
EOF
chmod +x check-site.sh

# Final Results
print_header "🎉 Setup Complete!"

echo -e "${GREEN}✅ Domain Setup Results:${NC}"
echo

if [ "$DNS_OK" = true ]; then
    echo -e "🌐 DNS Configuration: ${GREEN}✅ Working${NC}"
else
    echo -e "🌐 DNS Configuration: ${YELLOW}⚠️  Needs attention${NC}"
fi

if [ "$HTTP_OK" = true ]; then
    echo -e "🔗 HTTP Access: ${GREEN}✅ Working${NC}"
else
    echo -e "🔗 HTTP Access: ${YELLOW}⚠️  Check connection${NC}"
fi

if [ "$SSL_OK" = true ]; then
    echo -e "🔒 HTTPS/SSL: ${GREEN}✅ Active${NC}"
    echo -e "🎊 ${GREEN}Website is live at: https://jakakuasanusantara.web.id${NC}"
    echo -e "🎛️  ${GREEN}Admin Panel: https://jakakuasanusantara.web.id/dashboard${NC}"
else
    echo -e "🔒 HTTPS/SSL: ${YELLOW}⚠️  Not configured${NC}"
    echo -e "🌐 ${BLUE}Website accessible at: http://jakakuasanusantara.web.id${NC}"
    echo -e "🎛️  ${BLUE}Admin Panel: http://jakakuasanusantara.web.id/dashboard${NC}"
fi

echo
echo -e "${BLUE}📋 Default Login Credentials:${NC}"
echo -e "   👤 Admin: ${GREEN}admin@jakakuasanusantara.web.id${NC} / admin123456"
echo -e "   👤 Employee Demo: ${GREEN}budi.santoso@jakakuasanusantara.web.id${NC} / employee123"

echo
echo -e "${YELLOW}⚠️  Important Next Steps:${NC}"
echo "   1. Change default passwords immediately"
echo "   2. Enroll employee faces in admin panel"
echo "   3. Test attendance functionality"
echo "   4. Configure backup strategy"

if [ "$SSL_OK" = false ] && [ "$DNS_OK" = true ]; then
    echo "   5. Setup SSL: ./scripts/ssl-setup.sh generate"
fi

echo
echo -e "${BLUE}🛠️  Useful Commands:${NC}"
echo "   Check site status: ./check-site.sh"
echo "   View logs: docker-compose logs -f app"
echo "   SSL management: ./scripts/ssl-setup.sh"
echo "   Full maintenance: ./scripts/maintenance.sh"

echo
echo -e "${GREEN}🎊 Happy managing your attendance system!${NC}"
echo -e "${BLUE}Website Presensi PT. Jaka Kuasa Nusantara is ready! 🚀${NC}"

# Log the setup completion
echo "$(date): Domain setup completed for jakakuasanusantara.web.id" >> /var/log/domain-setup.log 2>/dev/null || true
