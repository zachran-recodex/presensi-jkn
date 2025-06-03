#!/bin/bash

# Main deployment script untuk Website Presensi Laravel
# Author: Deploy Script for PT. Jaka Kuasa Nusantara

set -e  # Exit on any error

echo "🚀 Starting deployment for Website Presensi Laravel..."
echo "📅 $(date)"
echo "==========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please don't run this script as root. Use a user with sudo privileges."
    exit 1
fi

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

print_status "Checking current directory..."
if [ ! -f "docker-compose.yml" ]; then
    print_error "docker-compose.yml not found. Please run this script from the project root."
    exit 1
fi

print_status "Stopping existing containers..."
docker-compose down --remove-orphans || true

print_status "Removing old images..."
docker system prune -f

print_status "Building and starting containers..."
docker-compose up -d --build

print_status "Waiting for database to be ready..."
sleep 30

print_status "Installing Laravel dependencies..."
docker-compose exec app composer install --optimize-autoloader --no-dev

print_status "Setting up Laravel..."
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

print_status "Running database migrations..."
docker-compose exec app php artisan migrate --force

print_status "Seeding database..."
docker-compose exec app php artisan db:seed --force

print_status "Creating storage link..."
docker-compose exec app php artisan storage:link

print_status "Setting up Face Recognition API..."
docker-compose exec app php artisan face:setup --test

print_status "Setting permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache

print_status "Checking container status..."
docker-compose ps

# Check if all containers are running
if docker-compose ps | grep -q "Exit"; then
    print_error "Some containers failed to start. Check logs with: docker-compose logs"
    exit 1
fi

print_success "Deployment completed successfully! 🎉"
echo ""
echo "==========================================="
echo "📋 DEPLOYMENT SUMMARY"
echo "==========================================="
echo "🌐 Website: https://jakakuasanusantara.web.id"
echo "📊 PhpMyAdmin: http://your-server-ip:8080"
echo "🔐 Database: presensi_jkn"
echo "👤 Database User: jkn_user"
echo ""
echo "📝 Next Steps:"
echo "1. Setup SSL certificate: docker-compose run --rm certbot"
echo "2. Update Nginx SSL configuration"
echo "3. Test the application"
echo "4. Setup monitoring and backups"
echo ""
echo "🔧 Useful Commands:"
echo "- View logs: docker-compose logs -f [service]"
echo "- Access app container: docker-compose exec app bash"
echo "- Restart services: docker-compose restart"
echo "- Stop all: docker-compose down"
echo ""
echo "🎯 Default Admin Login:"
echo "Email: admin@jakakuasanusantara.web.id"
echo "Password: admin123456"
echo ""
print_warning "Remember to change default passwords after first login!"
