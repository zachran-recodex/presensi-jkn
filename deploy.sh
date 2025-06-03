#!/bin/bash

# Deploy Script untuk Website Presensi - PT. Jaka Kuasa Nusantara
# Ubuntu 20.04 VPS dengan Docker

set -e

echo "🚀 Starting deployment for PT. Jaka Kuasa Nusantara - Website Presensi"
echo "=================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_error "This script should not be run as root for security reasons!"
   exit 1
fi

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_warning "Docker is not installed. Installing Docker..."

    # Update system
    sudo apt-get update

    # Install prerequisites
    sudo apt-get install -y \
        apt-transport-https \
        ca-certificates \
        curl \
        gnupg \
        lsb-release

    # Add Docker GPG key
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

    # Add Docker repository
    echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

    # Install Docker
    sudo apt-get update
    sudo apt-get install -y docker-ce docker-ce-cli containerd.io

    # Add user to docker group
    sudo usermod -aG docker $USER

    print_status "Docker installed successfully!"
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    print_warning "Docker Compose is not installed. Installing Docker Compose..."

    # Download Docker Compose
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

    # Make executable
    sudo chmod +x /usr/local/bin/docker-compose

    print_status "Docker Compose installed successfully!"
fi

# Create necessary directories
print_status "Creating necessary directories..."
mkdir -p docker/{nginx/logs,supervisor/logs,mysql/init,ssl}
mkdir -p storage/{app/public/attendance,framework/{cache,sessions,views},logs}
mkdir -p bootstrap/cache

# Set proper permissions
print_status "Setting proper permissions..."
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public
chmod 644 .env.production

# Copy environment file
if [ ! -f .env ]; then
    print_status "Copying production environment file..."
    cp .env.production .env
else
    print_warning ".env file already exists. Please verify configuration."
fi

# Create MySQL init script
print_status "Creating MySQL initialization script..."
cat > docker/mysql/init/01-init.sql << 'EOF'
-- Initialize database for Presensi App
CREATE DATABASE IF NOT EXISTS presensi_jkn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'jkn_user'@'%' IDENTIFIED BY 'Zachran#recodex15';
GRANT ALL PRIVILEGES ON presensi_jkn.* TO 'jkn_user'@'%';
FLUSH PRIVILEGES;
EOF

# Generate Laravel application key if not exists
if ! grep -q "APP_KEY=base64:" .env; then
    print_status "Generating Laravel application key..."
    # We'll do this after containers are up
    GENERATE_KEY=true
else
    GENERATE_KEY=false
fi

# Stop existing containers if running
print_status "Stopping existing containers..."
docker-compose down --remove-orphans || true

# Build and start containers
print_status "Building and starting Docker containers..."
docker-compose up -d --build

# Wait for database to be ready
print_status "Waiting for database to be ready..."
sleep 30

# Run Laravel setup commands
print_status "Running Laravel setup commands..."

if [ "$GENERATE_KEY" = true ]; then
    docker-compose exec app php artisan key:generate --force
fi

# Run migrations and seeders
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

# Create storage link
docker-compose exec app php artisan storage:link

# Cache configuration
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set up Face Recognition
print_status "Setting up Face Recognition..."
docker-compose exec app php artisan face:setup --test

# Create backup directory
sudo mkdir -p /var/backups
sudo chown $USER:$USER /var/backups

# Setup firewall (optional)
if command -v ufw &> /dev/null; then
    print_status "Configuring firewall..."
    sudo ufw allow 22/tcp  # SSH
    sudo ufw allow 80/tcp  # HTTP
    sudo ufw allow 443/tcp # HTTPS
    sudo ufw --force enable
fi

# Create systemd service for auto-start
print_status "Creating systemd service for auto-start..."
sudo tee /etc/systemd/system/presensi-app.service > /dev/null << EOF
[Unit]
Description=Presensi App Docker Compose
Requires=docker.service
After=docker.service

[Service]
Type=oneshot
RemainAfterExit=yes
WorkingDirectory=$(pwd)
ExecStart=/usr/local/bin/docker-compose up -d
ExecStop=/usr/local/bin/docker-compose down
TimeoutStartSec=0

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl enable presensi-app.service

# Display status
print_status "Checking container status..."
docker-compose ps

print_status "Deployment completed successfully! 🎉"
echo
echo "=================================================="
echo -e "${GREEN}🌟 PT. Jaka Kuasa Nusantara - Website Presensi${NC}"
echo "=================================================="
echo -e "🌐 Website: ${BLUE}http://$(curl -s ifconfig.me)${NC}"
echo -e "📊 Admin Panel: ${BLUE}http://$(curl -s ifconfig.me)/dashboard${NC}"
echo
echo -e "${YELLOW}📋 Default Login Credentials:${NC}"
echo -e "Admin: ${BLUE}admin@jakakuasanusantara.web.id${NC} / admin123456"
echo -e "Employee: ${BLUE}budi.santoso@jakakuasanusantara.web.id${NC} / employee123"
echo
echo -e "${RED}⚠️  IMPORTANT: Change default passwords after first login!${NC}"
echo
echo -e "${GREEN}📱 Next Steps:${NC}"
echo "1. Configure SSL certificate for HTTPS"
echo "2. Set up domain DNS to point to this server"
echo "3. Configure backup strategy"
echo "4. Enroll employee faces through admin panel"
echo "5. Test attendance system"
echo
echo -e "${BLUE}🛠️  Useful Commands:${NC}"
echo "View logs: docker-compose logs -f"
echo "Restart app: docker-compose restart app"
echo "Enter container: docker-compose exec app bash"
echo "Update app: git pull && docker-compose up -d --build"
echo
print_status "Happy coding! 💻"
