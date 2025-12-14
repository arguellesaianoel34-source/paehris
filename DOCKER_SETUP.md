# Docker Setup Guide for PAE ERP

This project has been dockerized for easier local development and deployment.

## Prerequisites

- Docker Desktop installed on your Mac
- Docker Compose (included with Docker Desktop)

## Quick Start

1. **Start the containers:**
   ```bash
   docker-compose up -d
   ```

2. **Access the application:**
   - Web application: http://localhost:8080
   - MySQL (main): localhost:3307
   - MySQL (audit): localhost:3308

3. **Stop the containers:**
   ```bash
   docker-compose down
   ```

## Database Setup

The Docker setup includes two MySQL containers:
- **Main Database** (`mysql`): `pae_erp`
- **Audit Database** (`mysql_audit`): `pae_erp_audit`

### Default Credentials (Matching Live/Production Config)

**Main Database:**
- Host: `mysql` (from within Docker) or `localhost:3307` (from host)
- User: `uub4rmw23inpzxn9_pae_root`
- Password: `959@M+U1GOat`
- Database: `uub4rmw23inpzxn9_erp`

**Audit Database:**
- Host: `mysql_audit` (from within Docker) or `localhost:3308` (from host)
- User: `uub4rmw23inpzxn9_pae_root`
- Password: `959@M+U1GOat`
- Database: `uub4rmw23inpzxn9_erp_audit`

### Import Database Schema

If you have SQL files in the `./db` directory, they will be automatically imported on first startup. Otherwise, you can manually import:

```bash
# Connect to main database
docker exec -i pae_erp_mysql mysql -uuub4rmw23inpzxn9_pae_root -p'959@M+U1GOat' uub4rmw23inpzxn9_erp < db/your_schema.sql

# Connect to audit database
docker exec -i pae_erp_mysql_audit mysql -uuub4rmw23inpzxn9_pae_root -p'959@M+U1GOat' uub4rmw23inpzxn9_erp_audit < db/your_audit_schema.sql
```

## Configuration

The application uses the **live/production configuration** (Turbify) for all database connections. The `database.php` file automatically detects if it's running in Docker:

- **In Docker**: Uses container hostnames (`mysql`, `mysql_audit`)
- **Local/MacOS**: Uses `localhost` with the same live credentials

This ensures your local Docker environment matches the production setup exactly.

## Volumes

The following directories are mounted as volumes for persistence:
- `./uploads` - Uploaded files
- `./application/logs` - Application logs
- `./application/cache` - Cache files

## Useful Commands

### View logs
```bash
docker-compose logs -f web
docker-compose logs -f mysql
```

### Access MySQL CLI
```bash
# Main database
docker exec -it pae_erp_mysql mysql -uuub4rmw23inpzxn9_pae_root -p'959@M+U1GOat' uub4rmw23inpzxn9_erp

# Audit database
docker exec -it pae_erp_mysql_audit mysql -uuub4rmw23inpzxn9_pae_root -p'959@M+U1GOat' uub4rmw23inpzxn9_erp_audit
```

### Rebuild containers
```bash
docker-compose build --no-cache
docker-compose up -d
```

### Remove all data (fresh start)
```bash
docker-compose down -v
docker-compose up -d
```

## Troubleshooting

### Port conflicts
If ports 8080, 3307, or 3308 are already in use, modify `docker-compose.yml` to use different ports.

### Permission issues
If you encounter permission issues with uploads/logs/cache:
```bash
docker exec -it pae_erp_web chmod -R 777 /var/www/html/uploads
docker exec -it pae_erp_web chmod -R 777 /var/www/html/application/logs
docker exec -it pae_erp_web chmod -R 777 /var/www/html/application/cache
```

### Database connection issues
Check if MySQL containers are running:
```bash
docker-compose ps
```

Check database logs:
```bash
docker-compose logs mysql
```

## Development Notes

- The `./php` directory has been removed as it was a Windows-specific PHP installation
- PHP 7.4 is used in the Docker container (matching the original setup)
- Apache mod_rewrite is enabled for clean URLs
- All required PHP extensions are installed in the container

