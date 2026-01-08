# Docker Development Environment

This Docker setup provides a complete development environment for the PAE ERP CodeIgniter 3 application.

## Prerequisites

- Docker Desktop (Windows/Mac) or Docker Engine + Docker Compose (Linux)
- At least 4GB of available RAM
- 10GB+ free disk space (for database and images)

## Quick Start

1. **Start the containers:**
   
   **For Docker Compose V2 (newer Docker Desktop):**
   ```bash
   docker compose up -d
   ```
   
   **For Docker Compose V1 (older installations):**
   ```bash
   docker-compose up -d
   ```
   
   **Note:** If you get "command not found", try:
   - Restart PowerShell/Command Prompt after installing Docker Desktop
   - Use full path: `C:\Program Files\Docker\Docker\resources\bin\docker compose up -d`
   - Or use Docker Desktop GUI: Open Docker Desktop → Use Compose

2. **Wait for database initialization:**
   The first startup will take several minutes as SQL files are imported. Monitor progress with:
   ```bash
   docker compose logs -f mysql
   # or
   docker-compose logs -f mysql
   ```

3. **Configure database connection for Docker:**
   ```bash
   cp docker/database.docker.php application/config/database.php
   ```

4. **Access the application:**
   - Web: http://localhost:8080
   - MySQL: localhost:3307

## Services

### PHP-FPM (php)
- **Container:** `erp_php`
- **Image:** Custom PHP 8.3-FPM with required extensions
- **Extensions:** mysqli, gd, mbstring, zip, xml, dom, intl, opcache
- **Port:** 9000 (internal)

### Nginx (nginx)
- **Container:** `erp_nginx`
- **Image:** nginx:alpine
- **Port:** 8080 (host) → 80 (container)
- **Config:** `nginx/nginx.conf`

### MySQL (mysql)
- **Container:** `erp_mysql`
- **Image:** mysql:8.0
- **Port:** 3307 (host) → 3306 (container)
- **Databases:**
  - `uub4rmw23inpzxn9_erp` (main)
  - `uub4rmw23inpzxn9_erp_audit` (audit)
- **User:** `uub4rmw23inpzxn9_pae_root`
- **Password:** `959@M+U1GOat`

## Common Commands

### Start services
```bash
# Docker Compose V2 (recommended)
docker compose up -d

# Docker Compose V1 (legacy)
docker-compose up -d
```

### Stop services
```bash
docker compose stop
# or
docker-compose stop
```

### Stop and remove containers
```bash
docker compose down
# or
docker-compose down
```

### Stop and remove containers + volumes (⚠️ deletes database)
```bash
docker compose down -v
# or
docker-compose down -v
```

### View logs
```bash
# All services
docker compose logs -f
# or
docker-compose logs -f

# Specific service
docker compose logs -f php
docker compose logs -f nginx
docker compose logs -f mysql
```

### Execute commands in containers
```bash
# PHP container
docker compose exec php bash
docker compose exec php php -v

# MySQL container
docker compose exec mysql bash
docker compose exec mysql mysql -u root -proot_password_2024
```

### Rebuild containers
```bash
# Rebuild PHP container (after Dockerfile changes)
docker compose build php

# Rebuild all containers
docker compose build
```

### Check container status
```bash
docker compose ps
# or
docker-compose ps
```

## Database Management

### Connect to MySQL
```bash
docker compose exec mysql mysql -u uub4rmw23inpzxn9_pae_root -p959@M+U1GOat uub4rmw23inpzxn9_erp
# or
docker-compose exec mysql mysql -u uub4rmw23inpzxn9_pae_root -p959@M+U1GOat uub4rmw23inpzxn9_erp
```

### Import SQL manually
```bash
# Copy SQL file to container
docker cp db/erp.sql erp_mysql:/tmp/erp.sql

# Import
docker compose exec mysql mysql -u root -proot_password_2024 uub4rmw23inpzxn9_erp < /tmp/erp.sql
```

### Export database
```bash
docker compose exec mysql mysqldump -u root -proot_password_2024 uub4rmw23inpzxn9_erp > backup.sql
```

### Reset database (⚠️ deletes all data)
```bash
docker compose down -v
docker compose up -d
```

## Configuration

### Database Connection

The application is configured to connect to:
- **Host:** `mysql` (Docker service name)
- **Port:** `3306` (internal)
- **User:** `uub4rmw23inpzxn9_pae_root`
- **Password:** `959@M+U1GOat`

**Important:** The production `application/config/database.php` uses `localhost` as the hostname. For Docker, you need to either:

1. **Option 1 (Recommended):** Copy the Docker-specific config:
   ```bash
   cp docker/database.docker.php application/config/database.php
   ```

2. **Option 2:** Manually edit `application/config/database.php` and change `localhost` to `mysql` in the `$db_config['host_server']` and `$db_config['audit_server']` variables.

The Docker config file (`docker/database.docker.php`) is already set up with `mysql` as the hostname.

### PHP Configuration

PHP settings can be customized in `docker/php.ini` (create if needed) or modify the Dockerfile.

### Nginx Configuration

Nginx configuration is in `nginx/nginx.conf`. Modify as needed for your requirements.

## Troubleshooting

### Docker command not found

If you get "docker: command not found" or "docker-compose: command not found":

1. **Restart your terminal** after installing Docker Desktop
2. **Check Docker Desktop is running** (should show in system tray)
3. **Use full path** (Windows):
   ```powershell
   & "C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose up -d
   ```
4. **Add Docker to PATH** (if needed):
   - Docker Desktop usually adds itself automatically
   - Check: `$env:PATH` in PowerShell should include Docker path
5. **Use Docker Desktop GUI**: Open Docker Desktop → Use Compose feature

### Port already in use

If port 8080 or 3307 is already in use, modify `docker-compose.yml`:
```yaml
nginx:
  ports:
    - "8081:80"  # Change 8080 to 8081

mysql:
  ports:
    - "3308:3306"  # Change 3307 to 3308
```

### Database initialization fails

1. Check MySQL logs:
   ```bash
   docker compose logs mysql
   # or
   docker-compose logs mysql
   ```

2. Verify SQL files exist:
   ```bash
   ls -lh db/*.sql
   ```

3. Check init script permissions:
   ```bash
   chmod +x docker/init-db.sh
   ```

4. Reinitialize:
   ```bash
   docker-compose down -v
   docker-compose up -d
   ```

### PHP errors

1. Check PHP logs:
   ```bash
   docker compose logs php
   ```

2. Check PHP-FPM status:
   ```bash
   docker compose exec php php-fpm -t
   ```

3. Verify PHP extensions:
   ```bash
   docker compose exec php php -m
   ```

### Application not loading

1. Check Nginx logs:
   ```bash
   docker compose logs nginx
   ```

2. Verify file permissions:
   ```bash
   docker-compose exec php ls -la /var/www/html
   ```

3. Test PHP-FPM connection:
   ```bash
   docker-compose exec nginx wget -O- http://php:9000
   ```

### Slow performance

- Increase Docker memory allocation (Docker Desktop → Settings → Resources)
- Use `docker-compose up --build` to ensure latest images
- Check container resource usage:
  ```bash
  docker stats
  ```

## File Permissions

If you encounter permission issues:

```bash
# Fix ownership
docker-compose exec php chown -R www-data:www-data /var/www/html

# Fix permissions
docker-compose exec php chmod -R 755 /var/www/html
docker-compose exec php chmod -R 777 /var/www/html/application/cache
docker-compose exec php chmod -R 777 /var/www/html/application/logs
```

## Development Workflow

1. **Make code changes** in your local files (they're mounted as volumes)
2. **Changes are immediately reflected** (no rebuild needed)
3. **Database changes** require container restart or manual SQL execution
4. **Commit changes** to git as normal
5. **CI/CD** will deploy to production (configured separately)

## Notes

- The database is persisted in a Docker volume (`mysql_data`)
- SQL files are imported only on first initialization
- External database connections (PECO, TVI) won't work in Docker
- Session storage uses database tables (ensure they exist)
- Large SQL imports may take 5-10 minutes on first run

## Support

For issues or questions, check:
- Docker logs: `docker-compose logs`
- Container status: `docker-compose ps`
- Application logs: `application/logs/`

