#!/bin/bash

# Domain Setup Script untuk jakakuasanusantara.web.id
# PT. Jaka Kuasa Nusantara - Website Presensi

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Domain configuration
DOMAIN="jakakuasanusantara.web.id"
WWW_DOMAIN="www.jakakuasanusantara.web.id"
EMAIL="admin@jakakuasanusantara.web.id"

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

# Check if running as non-root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root for security reasons!"
   exit 1
fi

print_header "Domain Setup untuk jakakuasanusantara.web.id"

# Step 1: Verify DNS Configuration
print_status "Step 1: Verifying DNS configuration..."

# Check if domain points to this server
SERVER_IP=$(curl -s ifconfig.me)
DOMAIN_IP=$(dig +short $DOMAIN)
WWW_DOMAIN_IP=$(dig +short $WWW_DOMAIN)

print_status "Server IP: $SERVER_IP"
print_status "Domain IP ($DOMAIN): $DOMAIN_IP"
print_status "WWW Domain IP ($WWW_DOMAIN): $WWW_DOMAIN_IP"

if [[ "$DOMAIN_IP" != "$SERVER_IP" ]]; then
    print_warning "Domain $DOMAIN does not point to this server!"
    print_warning "Please update your DNS records:"
    echo "  A Record: $DOMAIN -> $SERVER_IP"
    echo "  CNAME Record: www -> $DOMAIN"
    echo
    read -p "Continue anyway? (y/N): " confirm
    if [[ $confirm != [yY] ]]; then
        print_error "DNS setup required. Exiting."
        exit 1
    fi
fi

# Step 2: Update Environment Configuration
print_status "Step 2: Updating environment configuration..."

# Backup existing .env
if [ -f .env ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    print_status "Backed up existing .env file"
fi

# Update APP_URL in .env
if [ -f .env ]; then
    sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|g" .env
    print_status "Updated APP_URL to https://$DOMAIN"
else
    print_error ".env file not found! Please copy from .env.example"
    exit 1
fi

# Step 3: Restart containers without SSL first
print_status "Step 3: Starting containers without SSL..."
docker-compose down || true
docker-compose up -d

# Wait for services to be ready
print_status "Waiting for services to be ready..."
sleep 30

# Step 4: Test HTTP access
print_status "Step 4: Testing HTTP access..."
if curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN/health | grep -q "200"; then
    print_status "✅ HTTP access working!"
else
    print_warning "HTTP access test failed. Continuing anyway..."
fi

# Step 5: Install Certbot
print_status "Step 5: Installing Certbot..."
if ! command -v certbot &> /dev/null; then
    sudo apt update
    sudo apt install -y certbot python3-certbot-nginx
    print_status "Certbot installed successfully"
else
    print_status "Certbot already installed"
fi

# Step 6: Generate SSL Certificate
print_status "Step 6: Generating SSL certificate..."

# Stop nginx temporarily
docker-compose exec app supervisorctl stop nginx

# Generate certificate using standalone mode
print_status "Requesting SSL certificate from Let's Encrypt..."
sudo certbot certonly \
    --standalone \
    --agree-tos \
    --no-eff-email \
    --email $EMAIL \
    -d $DOMAIN \
    -d $WWW_DOMAIN

if [ $? -eq 0 ]; then
    print_status "✅ SSL certificate generated successfully!"

    # Copy certificates to docker volumes
    sudo cp -r /etc/letsencrypt/* docker/ssl/ 2>/dev/null || true

    # Set permissions
    sudo chown -R $USER:$USER docker/ssl/ 2>/dev/null || true

else
    print_error "SSL certificate generation failed!"
    print_warning "Will start nginx without SSL..."
fi

# Step 7: Start nginx with SSL configuration
print_status "Step 7: Starting nginx with SSL configuration..."
docker-compose exec app supervisorctl start nginx

# Step 8: Setup SSL auto-renewal
print_status "Step 8: Setting up SSL auto-renewal..."

# Create renewal script
sudo tee /etc/cron.d/certbot-renewal > /dev/null << EOF
# Renew SSL certificates twice daily
0 */12 * * * root test -x /usr/bin/certbot && perl -e 'sleep int(rand(43200))' && /usr/bin/certbot renew --quiet --deploy-hook "docker-compose -f $(pwd)/docker-compose.yml exec app supervisorctl restart nginx"
EOF

print_status "SSL auto-renewal configured"

# Step 9: Configure firewall
print_status "Step 9: Configuring firewall..."
if command -v ufw &> /dev/null; then
    sudo ufw allow 80/tcp   # HTTP
    sudo ufw allow 443/tcp  # HTTPS
    sudo ufw --force enable
    print_status "Firewall configured for HTTP/HTTPS"
fi

# Step 10: Update Laravel configuration
print_status "Step 10: Updating Laravel configuration..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Step 11: Test HTTPS access
print_status "Step 11: Testing HTTPS access..."
sleep 10

if curl -s -o /dev/null -w "%{http_code}" https://$DOMAIN/health | grep -q "200"; then
    print_status "✅ HTTPS access working!"
    HTTPS_WORKING=true
else
    print_warning "HTTPS access test failed"
    HTTPS_WORKING=false
fi

# Step 12: Create domain monitoring script
print_status "Step 12: Creating domain monitoring script..."

cat > scripts/check-domain.sh << 'EOF'
#!/bin/bash

# Domain Health Check Script
DOMAIN="jakakuasanusantara.web.id"
LOG_FILE="/var/log/domain-check.log"

# Function to log with timestamp
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> $LOG_FILE
}

# Check HTTP
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN/health)
if [ "$HTTP_STATUS" = "200" ] || [ "$HTTP_STATUS" = "301" ] || [ "$HTTP_STATUS" = "302" ]; then
    log_message "HTTP OK ($HTTP_STATUS)"
else
    log_message "HTTP FAILED ($HTTP_STATUS)"
fi

# Check HTTPS
HTTPS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://$DOMAIN/health)
if [ "$HTTPS_STATUS" = "200" ]; then
    log_message "HTTPS OK ($HTTPS_STATUS)"
else
    log_message "HTTPS FAILED ($HTTPS_STATUS)"
fi

# Check SSL certificate expiry
SSL_EXPIRY=$(echo | openssl s_client -servername $DOMAIN -connect $DOMAIN:443 2>/dev/null | openssl x509 -noout -dates | grep notAfter | cut -d= -f2)
if [ -n "$SSL_EXPIRY" ]; then
    EXPIRY_DATE=$(date -d "$SSL_EXPIRY" +%s)
    CURRENT_DATE=$(date +%s)
    DAYS_LEFT=$(( (EXPIRY_DATE - CURRENT_DATE) / 86400 ))

    if [ $DAYS_LEFT -lt 30 ]; then
        log_message "SSL WARNING: Certificate expires in $DAYS_LEFT days"
    else
        log_message "SSL OK: Certificate valid for $DAYS_LEFT days"
    fi
fi

# Check if all services are running
SERVICES_STATUS=$(docker-compose ps --services --filter "status=running" | wc -l)
TOTAL_SERVICES=$(docker-compose ps --services | wc -l)

if [ "$SERVICES_STATUS" = "$TOTAL_SERVICES" ]; then
    log_message "SERVICES OK: All $TOTAL_SERVICES services running"
else
    log_message "SERVICES WARNING: Only $SERVICES_STATUS/$TOTAL_SERVICES services running"
fi
EOF

chmod +x scripts/check-domain.sh

# Add to crontab for monitoring
(crontab -l 2>/dev/null; echo "*/5 * * * * $(pwd)/scripts/check-domain.sh") | crontab -

print_status "Domain monitoring configured (every 5 minutes)"

# Step 13: Display results
print_header "🎉 Domain Setup Completed!"

echo
echo "=================================================="
echo -e "${GREEN}✅ Domain Configuration Summary${NC}"
echo "=================================================="
echo -e "🌐 Primary Domain: ${BLUE}https://$DOMAIN${NC}"
echo -e "🌐 WWW Domain: ${BLUE}https://$WWW_DOMAIN${NC}"
echo -e "📧 Admin Email: ${BLUE}$EMAIL${NC}"
echo -e "🔒 SSL Certificate: ${GREEN}$([ -f /etc/letsencrypt/live/$DOMAIN/fullchain.pem ] && echo "Active" || echo "Not Found")${NC}"
echo -e "🔥 Firewall: ${GREEN}Configured${NC}"
echo -e "🔄 Auto-renewal: ${GREEN}Enabled${NC}"
echo

if [ "$HTTPS_WORKING" = true ]; then
    echo -e "${GREEN}🎊 SUCCESS! Website is accessible at:${NC}"
    echo -e "   👉 ${BLUE}https://$DOMAIN${NC}"
    echo -e "   👉 ${BLUE}https://$DOMAIN/dashboard${NC} (Admin Panel)"
else
    echo -e "${YELLOW}⚠️  HTTPS not working yet. You can access via:${NC}"
    echo -e "   👉 ${BLUE}http://$DOMAIN${NC}"
    echo -e "   👉 ${BLUE}http://$DOMAIN/dashboard${NC} (Admin Panel)"
    echo
    echo -e "${YELLOW}Troubleshooting HTTPS:${NC}"
    echo "   1. Check DNS: dig $DOMAIN"
    echo "   2. Check SSL: openssl s_client -servername $DOMAIN -connect $DOMAIN:443"
    echo "   3. Check logs: docker-compose logs app"
fi

echo
echo -e "${BLUE}📋 Default Login Credentials:${NC}"
echo -e "   Admin: ${GREEN}admin@jakakuasanusantara.web.id${NC} / admin123456"
echo -e "   Employee: ${GREEN}budi.santoso@jakakuasanusantara.web.id${NC} / employee123"
echo
echo -e "${RED}⚠️  IMPORTANT:${NC}"
echo "   1. Change default passwords immediately!"
echo "   2. Test SSL certificate: ssllabs.com/ssltest/"
echo "   3. Setup monitoring alerts"
echo "   4. Configure backup off-site storage"
echo
echo -e "${BLUE}🛠️  Management Commands:${NC}"
echo "   Domain check: ./scripts/check-domain.sh"
echo "   View logs: tail -f /var/log/domain-check.log"
echo "   SSL renewal: sudo certbot renew --dry-run"
echo "   Restart app: docker-compose restart app"
echo

print_status "Domain setup completed! 🚀"
