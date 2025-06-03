#!/bin/bash

# SSL Certificate Setup Script
# PT. Jaka Kuasa Nusantara - Website Presensi

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
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

show_usage() {
    echo "Usage: $0 [COMMAND]"
    echo
    echo "Commands:"
    echo "  generate    - Generate new SSL certificate"
    echo "  renew       - Renew existing certificate"
    echo "  check       - Check certificate status"
    echo "  install     - Install certificate to nginx"
    echo "  test        - Test SSL configuration"
    echo "  auto-renew  - Setup automatic renewal"
    echo
}

# Generate SSL Certificate
generate_ssl() {
    print_header "Generating SSL Certificate"

    # Check if certbot is installed
    if ! command -v certbot &> /dev/null; then
        print_status "Installing Certbot..."
        sudo apt update
        sudo apt install -y certbot python3-certbot-nginx
    fi

    # Check DNS first
    print_status "Checking DNS configuration..."
    SERVER_IP=$(curl -s ifconfig.me)
    DOMAIN_IP=$(dig +short $DOMAIN)

    if [[ "$DOMAIN_IP" != "$SERVER_IP" ]]; then
        print_error "DNS not configured correctly!"
        print_error "Domain $DOMAIN points to $DOMAIN_IP but server IP is $SERVER_IP"
        exit 1
    fi

    # Stop nginx temporarily
    print_status "Stopping nginx temporarily..."
    docker-compose exec app supervisorctl stop nginx || true

    # Generate certificate
    print_status "Requesting SSL certificate from Let's Encrypt..."
    sudo certbot certonly \
        --standalone \
        --agree-tos \
        --no-eff-email \
        --email $EMAIL \
        -d $DOMAIN \
        -d $WWW_DOMAIN \
        --non-interactive

    if [ $? -eq 0 ]; then
        print_status "✅ SSL certificate generated successfully!"
        install_ssl
    else
        print_error "SSL certificate generation failed!"
        exit 1
    fi
}

# Install SSL certificate to nginx
install_ssl() {
    print_header "Installing SSL Certificate"

    # Check if certificate exists
    if [ ! -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
        print_error "SSL certificate not found!"
        exit 1
    fi

    # Create SSL directory in docker
    mkdir -p docker/ssl

    # Copy certificates to docker volume
    print_status "Copying certificates..."
    sudo cp -r /etc/letsencrypt docker/ssl/ || true
    sudo chown -R $USER:$USER docker/ssl/ || true

    # Update nginx configuration to use SSL
    print_status "Updating nginx configuration..."

    # Restart nginx with SSL
    docker-compose exec app supervisorctl start nginx

    # Test SSL
    sleep 5
    test_ssl
}

# Renew SSL Certificate
renew_ssl() {
    print_header "Renewing SSL Certificate"

    print_status "Checking certificate expiry..."

    # Check days until expiry
    EXPIRY_DATE=$(sudo openssl x509 -in /etc/letsencrypt/live/$DOMAIN/cert.pem -noout -enddate | cut -d= -f2)
    EXPIRY_TIMESTAMP=$(date -d "$EXPIRY_DATE" +%s)
    CURRENT_TIMESTAMP=$(date +%s)
    DAYS_LEFT=$(( (EXPIRY_TIMESTAMP - CURRENT_TIMESTAMP) / 86400 ))

    print_status "Certificate expires in $DAYS_LEFT days"

    if [ $DAYS_LEFT -gt 30 ]; then
        print_warning "Certificate is still valid for $DAYS_LEFT days"
        read -p "Force renewal? (y/N): " confirm
        if [[ $confirm != [yY] ]]; then
            print_status "Renewal cancelled"
            return
        fi
    fi

    # Renew certificate
    print_status "Renewing certificate..."
    sudo certbot renew --force-renewal

    if [ $? -eq 0 ]; then
        print_status "✅ Certificate renewed successfully!"

        # Restart nginx
        docker-compose exec app supervisorctl restart nginx

        print_status "Nginx restarted with new certificate"
    else
        print_error "Certificate renewal failed!"
        exit 1
    fi
}

# Check SSL Certificate Status
check_ssl() {
    print_header "SSL Certificate Status"

    if [ ! -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
        print_error "❌ SSL certificate not found!"
        return
    fi

    # Certificate info
    print_status "Certificate Information:"
    sudo openssl x509 -in /etc/letsencrypt/live/$DOMAIN/cert.pem -noout -text | grep -E "(Subject:|Issuer:|Not Before|Not After)"

    # Check expiry
    EXPIRY_DATE=$(sudo openssl x509 -in /etc/letsencrypt/live/$DOMAIN/cert.pem -noout -enddate | cut -d= -f2)
    EXPIRY_TIMESTAMP=$(date -d "$EXPIRY_DATE" +%s)
    CURRENT_TIMESTAMP=$(date +%s)
    DAYS_LEFT=$(( (EXPIRY_TIMESTAMP - CURRENT_TIMESTAMP) / 86400 ))

    if [ $DAYS_LEFT -gt 30 ]; then
        print_status "✅ Certificate is valid for $DAYS_LEFT days"
    elif [ $DAYS_LEFT -gt 0 ]; then
        print_warning "⚠️  Certificate expires in $DAYS_LEFT days - renewal recommended"
    else
        print_error "❌ Certificate has expired!"
    fi

    # Test SSL connection
    print_status "Testing SSL connection..."
    if echo | timeout 10 openssl s_client -servername $DOMAIN -connect $DOMAIN:443 2>/dev/null | grep -q "Verify return code: 0"; then
        print_status "✅ SSL connection test passed"
    else
        print_error "❌ SSL connection test failed"
    fi
}

# Test SSL Configuration
test_ssl() {
    print_header "Testing SSL Configuration"

    # Test HTTPS access
    print_status "Testing HTTPS access..."

    HTTPS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://$DOMAIN/health)
    if [ "$HTTPS_STATUS" = "200" ]; then
        print_status "✅ HTTPS access working (Status: $HTTPS_STATUS)"
    else
        print_error "❌ HTTPS access failed (Status: $HTTPS_STATUS)"
    fi

    # Test WWW redirect
    print_status "Testing WWW redirect..."
    WWW_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://$WWW_DOMAIN/health)
    if [ "$WWW_STATUS" = "200" ]; then
        print_status "✅ WWW redirect working (Status: $WWW_STATUS)"
    else
        print_warning "⚠️  WWW redirect issue (Status: $WWW_STATUS)"
    fi

    # Test HTTP to HTTPS redirect
    print_status "Testing HTTP to HTTPS redirect..."
    HTTP_REDIRECT=$(curl -s -o /dev/null -w "%{http_code}" -L http://$DOMAIN/health)
    if [ "$HTTP_REDIRECT" = "200" ]; then
        print_status "✅ HTTP to HTTPS redirect working"
    else
        print_warning "⚠️  HTTP to HTTPS redirect issue"
    fi

    # SSL Labs test (optional)
    print_status "For comprehensive SSL test, visit:"
    echo "   👉 https://www.ssllabs.com/ssltest/analyze.html?d=$DOMAIN"
}

# Setup Automatic Renewal
setup_auto_renew() {
    print_header "Setting up Automatic SSL Renewal"

    # Create renewal script
    cat > /tmp/ssl-renew.sh << EOF
#!/bin/bash
/usr/bin/certbot renew --quiet --deploy-hook "cd $(pwd) && docker-compose exec app supervisorctl restart nginx"
EOF

    sudo mv /tmp/ssl-renew.sh /usr/local/bin/ssl-renew.sh
    sudo chmod +x /usr/local/bin/ssl-renew.sh

    # Add to crontab
    CRON_JOB="0 3 * * * /usr/local/bin/ssl-renew.sh >> /var/log/ssl-renewal.log 2>&1"

    # Check if cron job already exists
    if sudo crontab -l 2>/dev/null | grep -q "ssl-renew.sh"; then
        print_status "Auto-renewal already configured"
    else
        (sudo crontab -l 2>/dev/null; echo "$CRON_JOB") | sudo crontab -
        print_status "✅ Auto-renewal configured (daily at 3 AM)"
    fi

    # Test auto-renewal
    print_status "Testing auto-renewal (dry run)..."
    sudo certbot renew --dry-run

    if [ $? -eq 0 ]; then
        print_status "✅ Auto-renewal test passed"
    else
        print_error "❌ Auto-renewal test failed"
    fi
}

# Main script logic
case "${1:-}" in
    "generate")
        generate_ssl
        ;;
    "renew")
        renew_ssl
        ;;
    "check")
        check_ssl
        ;;
    "install")
        install_ssl
        ;;
    "test")
        test_ssl
        ;;
    "auto-renew")
        setup_auto_renew
        ;;
    *)
        show_usage
        ;;
esac
