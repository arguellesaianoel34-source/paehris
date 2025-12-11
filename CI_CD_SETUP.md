# CI/CD Setup Guide

This document explains how the PAE ERP system works with GitLab CI/CD.

## Overview

The project is structured to work in two environments:
- **Development**: Uses `docker-compose.yml` with local MySQL containers and SQL imports
- **Production**: Uses GitLab CI/CD (`.gitlab-ci.yml`) to upload files via FTP to web server

## File Structure for CI/CD

### Files Required in Git Repository

✅ **Include in Git:**
- All PHP application files (`application/`, `system/`, `assets/`, etc.)
- `Dockerfile` (for local dev)
- `docker-compose.yml` (for local dev)
- `.dockerignore`
- `.gitlab-ci.yml` (CI/CD configuration)
- `index.php`
- Configuration files (except `database.php` which should be environment-specific)

❌ **Exclude from Git:**
- `db/main/*.sql` - Large database dumps (107MB+)
- `db/audit/*.sql` - Audit database dumps
- `application/config/database.php` - Contains environment-specific credentials
- `.env` files
- `uploads/` - User-uploaded files
- `application/logs/` - Log files
- `application/cache/` - Cache files

## CI/CD Pipeline (GitLab)

The `.gitlab-ci.yml` file handles deployment:
- Detects changed files using `git diff`
- Uploads only changed files to FTP server (`ftp.paenergy.ph`)
- Uploads to `public_html/erp/` directory
- Production server already has database configured via `database.php`
- No Docker needed in production - server runs PHP directly

## Development Setup

For local development, developers need to:

1. **Clone the repository**
   ```bash
   git clone <repo-url>
   cd erp
   ```

2. **Add database SQL files** (not in git):
   - Place `pae_erp.sql` in `db/main/`
   - Place `pae_erp_audit_backup.sql` in `db/audit/`

3. **Start development environment**:
   ```bash
   docker-compose up -d
   ```

## Production Deployment

Production deployment is handled by GitLab CI/CD:
- Files are uploaded via FTP to the web server
- The web server runs PHP directly (no Docker)
- Database connection is configured via `database.php` on the server
- The `database.php` file should already exist on the production server

## Environment Variables

For production, consider using environment variables in `database.php`:

```php
$db_config['host_server'] = getenv('DB_HOST') ?: 'your-production-db-host';
$db_config['host_user'] = getenv('DB_USER') ?: 'your-db-user';
// etc.
```

## CI/CD Pipeline (GitLab)

The `.gitlab-ci.yml` file automatically:
1. Detects changes in the `/application` directory
2. Uploads only changed files to FTP server
3. Production server runs PHP directly (no Docker)

See `.gitlab-ci.yml` for the exact configuration.

## Important Notes

1. **Database Migrations**: SQL files are NOT in git. Production database:
   - Already set up and running on the server
   - Managed separately (backups, migrations via other tools)
   - Connected via `database.php` configuration on server

2. **Configuration Files**: 
   - `application/config/database.php` should already exist on the FTP server
   - CI/CD does NOT overwrite this file (it's excluded from git)
   - Server maintains its own database configuration

3. **File Permissions**:
   - Ensure `uploads/`, `logs/`, and `cache/` directories are writable on FTP server
   - These directories are excluded from git and CI/CD uploads

4. **Docker is Dev-Only**:
   - Docker setup (`docker-compose.yml`) is ONLY for local development
   - Production server runs PHP directly (no Docker containers)
   - CI/CD uploads PHP files to FTP, server executes them natively

## Troubleshooting

### Database Connection Issues
- Verify `database.php` is correctly configured on production
- Check network connectivity between containers and database
- Ensure database credentials are correct

### Missing SQL Files in Dev
- Developers must manually add SQL files to `db/main/` and `db/audit/`
- These files are excluded from git due to size
- Consider using a separate artifact storage for these files

