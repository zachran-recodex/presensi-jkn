# 🐳 Docker Deployment Guide
## Website Presensi - PT. Jaka Kuasa Nusantara

Panduan lengkap deployment menggunakan Docker untuk VPS Ubuntu 20.04 dengan domain **jakakuasanusantara.web.id**.

---

## 📋 Requirements

### Server Requirements
- **OS**: Ubuntu 20.04 LTS
- **RAM**: Minimum 2GB, Recommended 4GB
- **Storage**: Minimum 20GB SSD
- **CPU**: 2 vCPU cores
- **Network**: Public IP dengan akses internet
- **Domain**: jakakuasanusantara.web.id (DNS configured)

### Prerequisites
- Root atau sudo access
- Git installed
- Domain DNS pointing ke server IP
- SSL certificate support (Let's Encrypt)

---

## 🚀 Quick Start (Domain Setup)

### 1. **DNS Configuration First!**
```bash
# Check your server IP
curl ifconfig.me

# Configure these DNS records:
# A Record: jakakuasanusantara.web.id → YOUR_SERVER_IP
# CNAME: www.jakakuasanusantara.web.id → jakakuasanusantara.web.id
```

📖 **Detailed DNS Guide**: [DNS-SETUP-GUIDE.md](DNS-SETUP-GUIDE.md)

### 2. **One-Click Domain Setup**
```bash
# Clone repository
git clone https://github.com/zachran-recodex/presensi-jkn.git
cd presensi-jkn

# Run domain setup (includes everything!)
chmod +x setup-jakakuasa-domain.sh
./setup-jakakuasa-domain.sh
```

### 3. **Access Your Website**
- **🌐 Live Website**: https://jakakuasanusantara.web.id
- **🎛️ Admin Panel**: https://jakakuasanusantara.web.id/dashboard
- **📱 Employee Portal**: https://jakakuasanusantara.web.id/attendance

---

## 🎯 What The Domain Setup Does

The `setup-jakakuasa-domain.sh` script automatically:

✅ **Verifies DNS configuration**  
✅ **Installs Docker & Docker Compose**  
✅ **Configures environment for production**  
✅ **Builds and starts all containers**  
✅ **Generates SSL certificate (Let's Encrypt)**  
✅ **Configures HTTPS with security headers**  
✅ **Sets up automatic SSL renewal**  
✅ **Runs database migrations & seeders**  
✅ **Configures Face Recognition API**  
✅ **Sets up monitoring & health checks**  
✅ **Configures firewall rules**  
✅ **Creates systemd auto-start service**

---

## 🌐 Domain-Specific Configuration

### **Production URL Configuration**
```bash
# Environment automatically configured for:
APP_URL=https://jakakuasanusantara.web.id
APP_ENV=production
APP_DEBUG=false

# SSL Certificate paths:
/etc/letsencrypt/live/jakakuasanusantara.web.id/fullchain.pem
/etc/letsencrypt/live/jakakuasanusantara.web.id/privkey.pem
```

### **Nginx Virtual Host**
The setup automatically configures:
- **HTTP → HTTPS redirect** for SEO
- **WWW subdomain support**
- **Security headers** (HSTS, CSP, etc.)
- **Rate limiting** for API endpoints
- **Gzip compression** for performance
- **SSL optimization** (TLS 1.2+)

### **Automatic SSL Management**
- **Certificate generation**: Let's Encrypt integration
- **Auto-renewal**: Daily cron job at 3 AM
- **Health monitoring**: SSL expiry alerts
- **Zero-downtime renewal**: Nginx reload without restart

---

## 🔧 Manual Setup (Advanced Users)

If you prefer manual control:

### 1. **DNS Setup** (Required First!)
Follow the [DNS Setup Guide](DNS-SETUP-GUIDE.md) to configure:
```
A Record: jakakuasanusantara.web.id → YOUR_SERVER_IP
CNAME: www → jakakuasanusantara.web.id
```

### 2. **Install Dependencies**
```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### 3. **Environment Configuration**
```bash
# Copy domain-specific environment
cp .env.production .env

# Update for your domain
sed -i 's|APP_URL=.*|APP_URL=https://jakakuasanusantara.web.id|g' .env
```

### 4. **SSL Certificate Setup**
```bash
# Make SSL script executable
chmod +x scripts/ssl-setup.sh

# Generate SSL certificate
./scripts/ssl-setup.sh generate

# Setup auto-renewal
./scripts/ssl-setup.sh auto-renew
```

### 5. **Start Services**
```bash
# Build and start containers
docker-compose up -d --build

# Wait for database
sleep 30

# Run Laravel setup
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan config:cache
```

---

## 🐳 Docker Architecture

### Container Services

| Service | Description | Port | Domain Access |
|---------|-------------|------|---------------|
| **app** | Laravel + Nginx + PHP-FPM | 80, 443 | jakakuasanusantara.web.id |
| **database** | MySQL 8.0 | 3306 | Internal only |
| **redis** | Cache & Queue | 6379 | Internal only |
| **queue** | Background Jobs | - | Internal only |
| **scheduler** | Cron Tasks | - | Internal only |
| **certbot** | SSL Management | - | SSL certificates |

### Volume Mapping
```
./:/var/www/html              # Application code
certbot_certs:/etc/letsencrypt # SSL certificates
certbot_www:/var/www/certbot   # SSL challenges
mysql_data:/var/lib/mysql      # Database persistence
redis_data:/data               # Cache persistence
```

### Network & Security
- **Internal Network**: `presensi_network` (bridge)
- **SSL Termination**: Nginx with Let's Encrypt
- **Security Headers**: HSTS, CSP, X-Frame-Options
- **Rate Limiting**: API and login endpoints
- **Firewall**: UFW configured for 80, 443, 22

---

## 🛠️ Domain Management Commands

### **Quick Site Check**
```bash
# Check overall site status
./check-site.sh

# Domain-specific health check
./scripts/monitor-dns.sh

# SSL certificate status
./scripts/ssl-setup.sh check
```

### **SSL Certificate Management**
```bash
# Check SSL status
./scripts/ssl-setup.sh check

# Renew SSL certificate
./scripts/ssl-setup.sh renew

# Test SSL configuration
./scripts/ssl-setup.sh test

# Setup auto-renewal
./scripts/ssl-setup.sh auto-renew
```

### **Application Management**
```bash
# View live logs
docker-compose logs -f app

# Restart application
docker-compose restart app

# Update application
git pull origin main
docker-compose up -d --build

# Clear all caches
docker-compose exec app php artisan optimize:clear
```

### **Database Operations**
```bash
# Create backup
./scripts/maintenance.sh backup

# View recent backups
ls -la /var/backups/presensi/

# Database shell
docker-compose exec database mysql -u jkn_user -p presensi_jkn
```

---

## 📊 Monitoring & Health Checks

### **Automated Monitoring**
The domain setup includes automated monitoring for:

- **Website Availability** (every 5 minutes)
- **SSL Certificate Expiry** (daily check)
- **DNS Resolution** (every 15 minutes)
- **Database Health** (every hour)
- **API Quota Usage** (weekly check)

### **Health Check Endpoints**
```bash
# Application health
curl https://jakakuasanusantara.web.id/health

# Detailed system status
docker-compose exec app php artisan system:health

# Face Recognition API status
docker-compose exec app php artisan face:debug
```

### **Log Monitoring**
```bash
# Application logs
docker-compose logs -f app

# Nginx access logs
docker-compose exec app tail -f /var/log/nginx/access.log

# SSL renewal logs
sudo tail -f /var/log/ssl-renewal.log

# Domain monitoring logs
tail -f /var/log/domain-check.log
```

### **Performance Monitoring**
```bash
# Container resource usage
docker stats

# Website response time
curl -o /dev/null -s -w "Time: %{time_total}s\n" https://jakakuasanusantara.web.id

# Database performance
docker-compose exec app php artisan db:monitor
```

---

## 🔒 Security & SSL Configuration

### **SSL Security Features**
- **TLS 1.2 & 1.3** only
- **HSTS** (HTTP Strict Transport Security)
- **Perfect Forward Secrecy**
- **OCSP Stapling**
- **Secure cipher suites**

### **Security Headers Configured**
```nginx
# Security headers automatically applied:
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
X-Content-Type-Options: nosniff
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: default-src 'self' ...
```

### **Firewall Configuration**
```bash
# Check firewall status
sudo ufw status

# Configured ports:
# 22/tcp  - SSH
# 80/tcp  - HTTP (redirects to HTTPS)
# 443/tcp - HTTPS
```

### **SSL Certificate Details**
```bash
# Certificate information
openssl x509 -in /etc/letsencrypt/live/jakakuasanusantara.web.id/cert.pem -noout -text

# Certificate chain
openssl crl2pkcs7 -nocrl -certfile /etc/letsencrypt/live/jakakuasanusantara.web.id/fullchain.pem | openssl pkcs7 -print_certs -noout

# Test SSL configuration
openssl s_client -servername jakakuasanusantara.web.id -connect jakakuasanusantara.web.id:443
```

---

## 🗄️ Backup & Disaster Recovery

### **Automated Backup Strategy**
```bash
# Daily database backup (1 AM)
mysqldump presensi_jkn > /var/backups/presensi_$(date +%Y%m%d).sql

# Weekly file backup (Sunday 2 AM)
tar -czf /var/backups/files_$(date +%Y%m%d).tar.gz ./storage/app/public

# SSL certificate backup
cp -r /etc/letsencrypt /var/backups/ssl_backup_$(date +%Y%m%d)
```

### **Manual Backup Operations**
```bash
# Full backup
./scripts/maintenance.sh backup

# Backup with compression
docker-compose exec database mysqldump -u jkn_user -p'password' presensi_jkn | gzip > backup_$(date +%Y%m%d).sql.gz

# Application files backup
tar --exclude='./node_modules' --exclude='./.git' -czf app_backup_$(date +%Y%m%d).tar.gz .
```

### **Recovery Procedures**
```bash
# Restore database
./scripts/maintenance.sh restore

# Restore SSL certificates
sudo cp -r /var/backups/ssl_backup_YYYYMMDD/* /etc/letsencrypt/

# Full system restore
# 1. Restore application files
# 2. Restore database
# 3. Restore SSL certificates
# 4. Restart all services
```

---

## 🔄 Updates & Maintenance

### **Application Updates**
```bash
# Standard update process
git pull origin main
docker-compose up -d --build
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan optimize:clear

# Zero-downtime update (advanced)
./scripts/maintenance.sh update
```

### **System Maintenance**
```bash
# Weekly maintenance
./scripts/maintenance.sh cleanup

# Monthly maintenance
sudo apt update && sudo apt upgrade -y
docker system prune -a
./scripts/ssl-setup.sh check
```

### **Container Updates**
```bash
# Update base images
docker-compose pull
docker-compose up -d --build

# Update specific service
docker-compose up -d --build app
```

---

## 🎯 Production Optimization

### **Performance Tuning**
```bash
# Enable OPcache
docker-compose exec app php -m | grep -i opcache

# Database optimization
docker-compose exec database mysql -e "OPTIMIZE TABLE presensi_jkn.*;"

# Clear all caches
docker-compose exec app php artisan optimize
```

### **CDN Integration** (Optional)
```bash
# Configure Cloudflare proxy
# 1. Update DNS to enable Cloudflare proxy
# 2. Configure SSL/TLS to "Full (strict)"
# 3. Enable security features
# 4. Setup page rules for static assets
```

---

## 🐛 Troubleshooting Domain Issues

### **Common Domain Problems**

#### **Problem: HTTPS Not Working**
```bash
# Check SSL certificate
./scripts/ssl-setup.sh check

# Check nginx configuration
docker-compose exec app nginx -t

# View SSL logs
sudo journalctl -u certbot

# Manual SSL generation
sudo certbot certonly --standalone -d jakakuasanusantara.web.id -d www.jakakuasanusantara.web.id
```

#### **Problem: DNS Not Resolving**
```bash
# Check DNS propagation
dig jakakuasanusantara.web.id
nslookup jakakuasanusantara.web.id 8.8.8.8

# Monitor DNS from multiple locations
./scripts/monitor-dns.sh

# Flush DNS cache
sudo systemctl flush-dns
```

#### **Problem: Website Timeout**
```bash
# Check container status
docker-compose ps

# Check resource usage
docker stats

# View error logs
docker-compose logs app | grep -i error

# Check disk space
df -h
```

#### **Problem: SSL Certificate Expired**
```bash
# Check expiry date
openssl x509 -in /etc/letsencrypt/live/jakakuasanusantara.web.id/cert.pem -noout -dates

# Force renewal
sudo certbot renew --force-renewal

# Restart nginx
docker-compose exec app supervisorctl restart nginx
```

### **Emergency Recovery**
```bash
# If SSL is broken, access via HTTP
curl -I http://jakakuasanusantara.web.id

# If domain is down, access via IP
curl -I http://YOUR_SERVER_IP:8080

# Emergency container restart
docker-compose down && docker-compose up -d

# Complete system recovery
./setup-jakakuasa-domain.sh
```

---

## 🔑 Login Credentials & Access

### **Admin Access**
```
URL: https://jakakuasanusantara.web.id/dashboard
Email: admin@jakakuasanusantara.web.id
Password: admin123456
```

### **Employee Demo Access**
```
URL: https://jakakuasanusantara.web.id/attendance
Email: budi.santoso@jakakuasanusantara.web.id
Password: employee123
```

### **Additional Demo Accounts**
```
HR Manager:
Email: hr@jakakuasanusantara.web.id
Password: hr123456

Marketing Staff:
Email: siti.nurhaliza@jakakuasanusantara.web.id
Password: employee123

Finance Staff:
Email: ahmad.fauzi@jakakuasanusantara.web.id
Password: employee123
```

⚠️ **CRITICAL**: Change all default passwords immediately after first login!

---

## 🎯 Post-Deployment Checklist

### **Immediate Tasks** (First 30 minutes)
- [ ] ✅ Verify website loads: https://jakakuasanusantara.web.id
- [ ] ✅ Test admin login and dashboard access
- [ ] ✅ Check SSL certificate status (A+ rating)
- [ ] 🔒 **Change all default passwords**
- [ ] 📧 Update admin email addresses
- [ ] 🔧 Configure SMTP for email notifications
- [ ] 📱 Test employee attendance flow

### **Security Setup** (First day)
- [ ] 🔑 Setup 2FA for admin accounts
- [ ] 🛡️ Review firewall settings
- [ ] 📊 Setup monitoring alerts
- [ ] 🗂️ Configure log retention
- [ ] 📄 Review security headers
- [ ] 🔍 Run security scan

### **Business Configuration** (First week)
- [ ] 👥 **Enroll employee faces** via admin panel
- [ ] 📍 Configure office locations and GPS radius
- [ ] ⏰ Set working hours for departments
- [ ] 📊 Setup attendance reports schedule
- [ ] 🔄 Test backup and restore procedures
- [ ] 📈 Configure monitoring dashboards

### **Production Readiness** (Ongoing)
- [ ] 📱 Train employees on attendance system
- [ ] 📋 Create user documentation
- [ ] 🚨 Setup emergency procedures
- [ ] 💾 Configure off-site backups
- [ ] 📞 Establish support procedures
- [ ] 🔄 Plan update procedures

---

## 📈 Business Intelligence & Analytics

### **Built-in Reports**
- **Daily Attendance**: Real-time dashboard
- **Monthly Summary**: Department-wise analysis
- **Late Arrival Trends**: Performance insights
- **Location Analytics**: Office utilization
- **Face Recognition Stats**: System effectiveness

### **Export Capabilities**
```bash
# Generate monthly report
docker-compose exec app php artisan attendance:report monthly --export=csv

# Export employee data
curl "https://jakakuasanusantara.web.id/admin/reports/export" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🌟 Advanced Features

### **API Integration**
```bash
# Face Recognition API monitoring
docker-compose exec app php artisan face:debug

# API usage statistics
curl https://jakakuasanusantara.web.id/api/stats \
  -H "Accept: application/json"
```

### **Mobile Optimization**
- **Progressive Web App** (PWA) ready
- **Touch-optimized** attendance interface
- **Camera integration** for selfie capture
- **Offline capability** for basic functions
- **Push notifications** for attendance reminders

### **Integration Possibilities**
- **Payroll Systems**: Export attendance data
- **HR Systems**: Employee data synchronization
- **Slack/Teams**: Attendance notifications
- **Email**: Daily/weekly reports
- **WhatsApp**: Attendance alerts (via API)

---

## 📞 Support & Resources

### **Technical Documentation**
- **API Documentation**: https://jakakuasanusantara.web.id/docs
- **User Manual**: Available in admin panel
- **Video Tutorials**: Face enrollment process
- **Troubleshooting Guide**: Common issues and solutions

### **Support Channels**
- **Email**: admin@jakakuasanusantara.web.id
- **Technical**: dev@jakakuasanusantara.web.id
- **Emergency**: Check server monitoring dashboard

### **Useful Links**
- **SSL Test**: https://ssllabs.com/ssltest/analyze.html?d=jakakuasanusantara.web.id
- **DNS Check**: https://dnschecker.org
- **Speed Test**: https://pagespeed.web.dev
- **Security Scan**: https://securityheaders.com

---

## 🎊 Success Metrics

### **System Performance Targets**
- **Uptime**: 99.9% availability
- **Response Time**: < 2 seconds page load
- **SSL Grade**: A+ rating
- **Security**: No critical vulnerabilities
- **Face Recognition**: > 95% accuracy

### **Business Impact**
- **Time Savings**: Automated attendance tracking
- **Accuracy**: GPS + Face verification
- **Compliance**: Audit trail and reports
- **Productivity**: Real-time monitoring
- **Cost Reduction**: Paperless system

---

## 🚀 What's Next?

### **Immediate Opportunities**
1. **Mobile App**: Native iOS/Android application
2. **Analytics Dashboard**: Advanced BI reporting
3. **API Expansion**: Third-party integrations
4. **AI Features**: Attendance pattern analysis
5. **Scaling**: Multi-location support

### **Getting Started Resources**
```bash
# Quick health check
./check-site.sh

# View system status
./scripts/maintenance.sh status

# Monitor real-time
./scripts/maintenance.sh monitor

# Check Face API status
docker-compose exec app php artisan face:debug --test
```

---

## 🎉 Congratulations!

**Website Presensi PT. Jaka Kuasa Nusantara** is now live at:

🌐 **https://jakakuasanusantara.web.id**

### **What You've Achieved:**
✅ **Enterprise-grade attendance system**  
✅ **SSL-secured domain with auto-renewal**  
✅ **Face recognition with GPS validation**  
✅ **Real-time monitoring and alerting**  
✅ **Automated backups and maintenance**  
✅ **Mobile-optimized interface**  
✅ **Production-ready infrastructure**

### **Ready for Business:**
- 👥 **Employee self-service** attendance portal
- 📊 **Admin dashboard** with real-time analytics
- 📱 **Mobile-first design** for smartphone usage
- 🔒 **Bank-level security** with SSL and encryption
- ⚡ **High performance** with Redis caching
- 🎯 **Face + GPS verification** for accuracy

---

**Happy managing your modern attendance system! 🎊**

*Developed with ❤️ for PT. Jaka Kuasa Nusantara*  
*Powered by Laravel 10, Docker, and Biznet Face Recognition*
