#!/bin/bash

# Maintenance Scripts untuk Website Presensi - PT. Jaka Kuasa Nusantara
# Author: Development Team
# Description: Collection of maintenance and troubleshooting scripts

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

# Function to show usage
show_usage() {
    echo "Usage: $0 [COMMAND]"
    echo
    echo "Available commands:"
    echo "  status       - Show system status"
    echo "  logs         - Show application logs"
    echo "  backup       - Create database backup"
    echo "  restore      - Restore database from backup"
    echo "  update       - Update application"
    echo "  restart      - Restart all services"
    echo "  cleanup      - Clean up old files and logs"
    echo "  health       - Run health checks"
    echo "  ssl          - Setup SSL certificate"
    echo "  monitor      - Real-time monitoring"
    echo
}

# Check system status
check_status() {
    print_header "System Status Check"

    echo "Docker Status:"
    docker --version
    docker-compose --version
    echo

    echo "Container Status:"
    docker-compose ps
    echo

    echo "Disk Usage:"
    df -h
    echo

    echo "Memory Usage:"
    free -h
    echo

    echo "System Load:"
    uptime
}

# Show application logs
show_logs() {
    print_header "Application Logs"
    echo "Select log type:"
    echo "1. Application logs"
    echo "2. Nginx logs"
    echo "3. Queue logs"
    echo "4. Error logs"
    echo "5. All logs"

    read -p "Enter choice (1-5): " choice

    case $choice in
        1)
            docker-compose logs -f app
            ;;
        2)
            docker-compose exec app tail -f /var/log/nginx/access.log
            ;;
        3)
            docker-compose logs -f queue
            ;;
        4)
            docker-compose exec app tail -f storage/logs/laravel.log
            ;;
        5)
            docker-compose logs -f
            ;;
        *)
            print_error "Invalid choice"
            ;;
    esac
}

# Create database backup
create_backup() {
    print_header "Database Backup"

    BACKUP_DIR="/var/backups/presensi"
    BACKUP_FILE="presensi_$(date +%Y%m%d_%H%M%S).sql"

    mkdir -p $BACKUP_DIR

    print_status "Creating backup: $BACKUP_FILE"

    docker-compose exec database mysqldump \
        -u jkn_user \
        -p'Zachran#recodex15' \
        presensi_jkn > "$BACKUP_DIR/$BACKUP_FILE"

    # Compress backup
    gzip "$BACKUP_DIR/$BACKUP_FILE"

    print_status "Backup created: $BACKUP_DIR/$BACKUP_FILE.gz"

    # Keep only last 7 backups
    cd $BACKUP_DIR
    ls -t *.gz | tail -n +8 | xargs -r rm

    print_status "Old backups cleaned up"
}

# Restore database from backup
restore_backup() {
    print_header "Database Restore"

    BACKUP_DIR="/var/backups/presensi"

    if [ ! -d "$BACKUP_DIR" ]; then
        print_error "Backup directory not found: $BACKUP_DIR"
        exit 1
    fi

    echo "Available backups:"
    ls -la $BACKUP_DIR/*.gz 2>/dev/null || { print_error "No backups found"; exit 1; }

    read -p "Enter backup filename: " backup_file

    if [ ! -f "$BACKUP_DIR/$backup_file" ]; then
        print_error "Backup file not found: $backup_file"
        exit 1
    fi

    print_warning "This will overwrite the current database!"
    read -p "Are you sure? (y/N): " confirm

    if [[ $confirm == [yY] ]]; then
        print_status "Restoring database from: $backup_file"

        # Decompress and restore
        gunzip -c "$BACKUP_DIR/$backup_file" | \
        docker-compose exec -T database mysql \
            -u jkn_user \
            -p'Zachran#recodex15' \
            presensi_jkn

        print_status "Database restored successfully"
    else
        print_status "Restore cancelled"
    fi
}

# Update application
update_app() {
    print_header "Application Update"

    print_status "Pulling latest code..."
    git pull origin main

    print_status "Rebuilding containers..."
    docker-compose up -d --build

    print_status "Running migrations..."
    docker-compose exec app php artisan migrate --force

    print_status "Clearing caches..."
    docker-compose exec app php artisan cache:clear
    docker-compose exec app php artisan config:cache
    docker-compose exec app php artisan route:cache
    docker-compose exec app php artisan view:cache

    print_status "Restarting queue workers..."
    docker-compose restart queue

    print_status "Update completed successfully!"
}

# Restart services
restart_services() {
    print_header "Restarting Services"

    print_status "Restarting all containers..."
    docker-compose restart

    print_status "Waiting for services to be ready..."
    sleep 10

    print_status "Checking container status..."
    docker-compose ps

    print_status "Services restarted successfully!"
}

# Cleanup old files
cleanup_files() {
    print_header "Cleanup Old Files"

    print_status "Cleaning up old logs..."
    docker-compose exec app find storage/logs -name "*.log" -mtime +30 -delete

    print_status "Cleaning up old attendance photos..."
    docker-compose exec app php artisan attendance:cleanup-photos --days=90

    print_status "Cleaning up Docker system..."
    docker system prune -f

    print_status "Cleaning up old backups (keeping last 7)..."
    BACKUP_DIR="/var/backups/presensi"
    if [ -d "$BACKUP_DIR" ]; then
        cd $BACKUP_DIR
        ls -t *.gz 2>/dev/null | tail -n +8 | xargs -r rm
    fi

    print_status "Cleanup completed!"
}

# Run health checks
run_health_check() {
    print_header "System Health Check"

    print_status "Running application health check..."
    docker-compose exec app php artisan system:health --detailed

    print_status "Checking database connection..."
    docker-compose exec app php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';"

    print_status "Checking Redis connection..."
    docker-compose exec redis redis-cli ping

    print_status "Checking Face Recognition API..."
    docker-compose exec app php artisan face:debug

    print_status "Health check completed!"
}

# Setup SSL certificate
setup_ssl() {
    print_header "SSL Certificate Setup"

    print_warning "This will install Certbot and setup SSL certificate"
    read -p "Continue? (y/N): " confirm

    if [[ $confirm == [yY] ]]; then
        # Install Certbot
        sudo apt update
        sudo apt install -y certbot python3-certbot-nginx

        # Get certificate
        read -p "Enter domain name: " domain
        sudo certbot --nginx -d $domain

        # Auto-renewal
        sudo crontab -l | { cat; echo "0 12 * * * /usr/bin/certbot renew --quiet"; } | sudo crontab -

        print_status "SSL certificate setup completed!"
    fi
}

# Real-time monitoring
monitor_system() {
    print_header "Real-time System Monitoring"

    echo "Press Ctrl+C to stop monitoring"
    echo

    while true; do
        clear
        echo "=== System Monitor - $(date) ==="
        echo

        echo "Container Status:"
        docker-compose ps
        echo

        echo "Resource Usage:"
        docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}"
        echo

        echo "Recent Logs (last 5 lines):"
        docker-compose logs --tail=5 app | tail -5
        echo

        sleep 5
    done
}

# Main script logic
case "${1:-}" in
    "status")
        check_status
        ;;
    "logs")
        show_logs
        ;;
    "backup")
        create_backup
        ;;
    "restore")
        restore_backup
        ;;
    "update")
        update_app
        ;;
    "restart")
        restart_services
        ;;
    "cleanup")
        cleanup_files
        ;;
    "health")
        run_health_check
        ;;
    "ssl")
        setup_ssl
        ;;
    "monitor")
        monitor_system
        ;;
    *)
        show_usage
        ;;
esac
