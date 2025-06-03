# 🌐 DNS Setup Guide
## Domain: jakakuasanusantara.web.id

Panduan lengkap konfigurasi DNS untuk Website Presensi PT. Jaka Kuasa Nusantara.

---

## 📋 DNS Records Required

### 1. **A Record (Primary Domain)**
```
Name/Host: @  atau  jakakuasanusantara.web.id
Type: A
Value: [YOUR_SERVER_IP]
TTL: 3600 (atau Auto)
```

### 2. **CNAME Record (WWW Subdomain)**
```
Name/Host: www
Type: CNAME
Value: jakakuasanusantara.web.id
TTL: 3600 (atau Auto)
```

### 3. **Optional Records**

#### Mail Records (untuk email notifikasi)
```
Name/Host: @
Type: MX
Priority: 10
Value: mail.jakakuasanusantara.web.id

Name/Host: mail
Type: A
Value: [YOUR_SERVER_IP]
```

#### SPF Record (untuk email security)
```
Name/Host: @
Type: TXT
Value: v=spf1 a mx ip4:[YOUR_SERVER_IP] ~all
```

---

## 🔧 DNS Configuration Steps

### Step 1: Get Your Server IP
```bash
# Di server VPS, jalankan:
curl ifconfig.me

# Atau gunakan:
dig +short myip.opendns.com @resolver1.opendns.com
```

### Step 2: Access Domain Management Panel
Login ke panel domain Anda (Cloudflare, Namecheap, GoDaddy, dll.)

### Step 3: Configure DNS Records

#### Cloudflare Setup
1. Login ke Cloudflare Dashboard
2. Select domain `jakakuasanusantara.web.id`
3. Go to **DNS** section
4. Add records:

| Type | Name | Content | Proxy Status |
|------|------|---------|--------------|
| A | @ | YOUR_SERVER_IP | ⚪ DNS only |
| CNAME | www | jakakuasanusantara.web.id | ⚪ DNS only |

#### Generic DNS Panel Setup
1. Login to your DNS provider
2. Find DNS management section
3. Add the records as specified above
4. Save changes

### Step 4: Verify DNS Propagation
```bash
# Check A record
dig jakakuasanusantara.web.id

# Check CNAME record
dig www.jakakuasanusantara.web.id

# Check from different locations
nslookup jakakuasanusantara.web.id 8.8.8.8
```

---

## ⏱️ DNS Propagation Timeline

| Location | Typical Time |
|----------|--------------|
| **Local ISP** | 5-30 minutes |
| **Regional** | 30 minutes - 2 hours |
| **Global** | 2-48 hours |

### Check Propagation Status
- **DNSChecker**: https://dnschecker.org
- **WhatsMyDNS**: https://whatsmydns.net
- **DNS Propagation**: https://dnspropagation.net

---

## 🚀 Quick Setup Commands

### 1. **Check Current DNS**
```bash
# Check current A record
dig +short jakakuasanusantara.web.id

# Check current CNAME
dig +short www.jakakuasanusantara.web.id

# Detailed DNS info
nslookup jakakuasanusantara.web.id
```

### 2. **Verify DNS After Configuration**
```bash
# Get your server IP
SERVER_IP=$(curl -s ifconfig.me)
echo "Server IP: $SERVER_IP"

# Check if domain points to server
DOMAIN_IP=$(dig +short jakakuasanusantara.web.id)
echo "Domain IP: $DOMAIN_IP"

if [[ "$DOMAIN_IP" == "$SERVER_IP" ]]; then
    echo "✅ DNS configured correctly!"
else
    echo "❌ DNS not pointing to server yet"
fi
```

### 3. **Monitor DNS Propagation**
```bash
#!/bin/bash
# Save as check-dns.sh

DOMAIN="jakakuasanusantara.web.id"
SERVER_IP=$(curl -s ifconfig.me)

echo "Checking DNS propagation for $DOMAIN"
echo "Expected IP: $SERVER_IP"
echo "----------------------------------------"

# Check multiple DNS servers
SERVERS=("8.8.8.8" "1.1.1.1" "9.9.9.9" "208.67.222.222")

for server in "${SERVERS[@]}"; do
    RESOLVED_IP=$(dig +short @$server $DOMAIN | head -n1)
    if [[ "$RESOLVED_IP" == "$SERVER_IP" ]]; then
        echo "✅ $server: $RESOLVED_IP"
    else
        echo "❌ $server: $RESOLVED_IP"
    fi
done
```

---

## 🔍 Troubleshooting DNS Issues

### Common Problems & Solutions

#### **Problem 1: DNS Not Propagating**
```bash
# Solution: Clear local DNS cache
sudo systemctl flush-dns  # Ubuntu
sudo dscacheutil -flushcache  # macOS
ipconfig /flushdns  # Windows
```

#### **Problem 2: Wrong IP Returned**
```bash
# Check if you're checking the right domain
dig jakakuasanusantara.web.id +trace

# Verify nameservers
dig NS jakakuasanusantara.web.id
```

#### **Problem 3: SSL Issues After DNS**
```bash
# Clear SSL cache and retry
openssl s_client -servername jakakuasanusantara.web.id -connect jakakuasanusantara.web.id:443
```

#### **Problem 4: Subdomain Not Working**
```bash
# Check CNAME configuration
dig CNAME www.jakakuasanusantara.web.id

# Verify in browser
curl -I http://www.jakakuasanusantara.web.id
```

---

## 📊 DNS Monitoring Script

Create this script to monitor DNS health:

```bash
#!/bin/bash
# Save as scripts/monitor-dns.sh

DOMAIN="jakakuasanusantara.web.id"
SERVER_IP=$(curl -s ifconfig.me)
LOG_FILE="/var/log/dns-monitor.log"

# Function to log with timestamp
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a $LOG_FILE
}

# Check A record
A_RECORD=$(dig +short $DOMAIN | head -n1)
if [[ "$A_RECORD" == "$SERVER_IP" ]]; then
    log_message "✅ A Record OK: $A_RECORD"
else
    log_message "❌ A Record FAIL: Expected $SERVER_IP, got $A_RECORD"
fi

# Check CNAME record
CNAME_IP=$(dig +short www.$DOMAIN | head -n1)
if [[ "$CNAME_IP" == "$SERVER_IP" ]]; then
    log_message "✅ CNAME OK: www.$DOMAIN -> $CNAME_IP"
else
    log_message "❌ CNAME FAIL: www.$DOMAIN -> $CNAME_IP"
fi

# Check HTTP connectivity
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN/health)
if [[ "$HTTP_STATUS" == "200" ]]; then
    log_message "✅ HTTP OK: $HTTP_STATUS"
else
    log_message "❌ HTTP FAIL: $HTTP_STATUS"
fi

# Check HTTPS connectivity
HTTPS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://$DOMAIN/health)
if [[ "$HTTPS_STATUS" == "200" ]]; then
    log_message "✅ HTTPS OK: $HTTPS_STATUS"
else
    log_message "⚠️ HTTPS WARNING: $HTTPS_STATUS"
fi
```

### Setup Monitoring
```bash
# Make executable
chmod +x scripts/monitor-dns.sh

# Add to crontab (check every 15 minutes)
(crontab -l 2>/dev/null; echo "*/15 * * * * $(pwd)/scripts/monitor-dns.sh") | crontab -

# View monitoring logs
tail -f /var/log/dns-monitor.log
```

---

## 🌍 Regional DNS Servers

Test DNS from different regions:

### **Indonesia**
```bash
dig @202.134.0.155 jakakuasanusantara.web.id  # Indonesia
dig @203.130.196.5 jakakuasanusantara.web.id  # Telkom Indonesia
```

### **Global**
```bash
dig @8.8.8.8 jakakuasanusantara.web.id        # Google
dig @1.1.1.1 jakakuasanusantara.web.id        # Cloudflare
dig @9.9.9.9 jakakuasanusantara.web.id        # Quad9
dig @208.67.222.222 jakakuasanusantara.web.id # OpenDNS
```

---

## 🔒 Security Considerations

### **DNS Security Best Practices**

1. **Enable DNSSEC** (if supported by provider)
```bash
# Check DNSSEC status
dig +dnssec jakakuasanusantara.web.id
```

2. **Use Cloudflare Proxy** (optional)
    - Pros: DDoS protection, CDN, WAF
    - Cons: Additional complexity, potential IP masking

3. **Monitor DNS Changes**
    - Set up alerts for unauthorized DNS changes
    - Use DNS monitoring services

### **CAA Records** (for SSL security)
```
Name/Host: @
Type: CAA
Value: 0 issue "letsencrypt.org"
```

---

## 📞 Support & Troubleshooting

### **Quick Diagnostic Commands**
```bash
# Complete DNS check
./scripts/monitor-dns.sh

# Site accessibility test
curl -I http://jakakuasanusantara.web.id
curl -I https://jakakuasanusantara.web.id

# SSL certificate check
echo | openssl s_client -servername jakakuasanusantara.web.id -connect jakakuasanusantara.web.id:443 2>/dev/null | openssl x509 -noout -dates
```

### **When to Contact Support**
- DNS not propagating after 48 hours
- Unable to access domain management panel
- SSL certificate issues after DNS is working
- Persistent DNS resolution failures

### **Contact Information**
- **Technical Support**: dev@jakakuasanusantara.web.id
- **Domain Issues**: admin@jakakuasanusantara.web.id
- **Emergency**: Check server logs and DNS monitoring

---

## ✅ DNS Setup Checklist

Before proceeding with domain setup:

- [ ] Server is running and accessible
- [ ] Server IP address obtained
- [ ] Domain management panel access confirmed
- [ ] A record configured (@ → server IP)
- [ ] CNAME record configured (www → domain)
- [ ] DNS propagation verified
- [ ] HTTP access tested
- [ ] Ready for SSL certificate generation

After DNS setup:

- [ ] Run domain setup script: `./setup-jakakuasa-domain.sh`
- [ ] SSL certificate generated and installed
- [ ] HTTPS access verified
- [ ] Website fully functional
- [ ] Monitoring scripts configured

---

**DNS Setup Guide untuk jakakuasanusantara.web.id Complete! 🌐✅**

*PT. Jaka Kuasa Nusantara - Development Team*
