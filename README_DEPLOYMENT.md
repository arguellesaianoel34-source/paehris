# Deployment Guide

## Quick Answer: Will This Work for CI/CD?

**Yes!** The setup is designed to work for both CI/CD and local development:

### ✅ For CI/CD (Production):
- **No SQL files needed** - Production database already exists on server
- **Changed files only** - GitLab CI/CD detects changes and uploads to FTP
- **FTP upload** - Files uploaded to `ftp.paenergy.ph/public_html/erp/`
- **No Docker** - Production server runs PHP directly
- **Database config** - `database.php` already configured on server (not in git, won't be overwritten)

### ✅ For Local Development:
- **SQL files needed** - Developers add them locally (not in git)
- **Uses `docker-compose.yml`** - Includes MySQL containers with SQL imports
- **Full setup** - Everything runs locally in Docker

## File Organization

```
erp/
├── docker-compose.yml          # Dev: Includes MySQL + SQL imports
├── Dockerfile                  # Builds PHP/Apache image (dev only)
├── .gitlab-ci.yml              # CI/CD: Uploads files to FTP
├── .gitignore                  # Excludes SQL files, database.php
├── db/
│   ├── main/
│   │   ├── .gitkeep           # ✅ In git
│   │   └── pae_erp.sql        # ❌ NOT in git (107MB)
│   └── audit/
│       ├── .gitkeep           # ✅ In git
│       └── pae_erp_audit_backup.sql  # ❌ NOT in git
└── application/config/
    └── database.php            # ❌ NOT in git (has credentials)
```

## CI/CD Pipeline Flow (GitLab)

1. **Git Commit** → Only PHP code, configs (no SQL files, no database.php)
2. **GitLab CI/CD** → Detects changed files using `git diff`
3. **FTP Upload** → Uploads changed files to `ftp.paenergy.ph/public_html/erp/`
4. **Production** → Server runs PHP directly (no Docker)
5. **Database** → Connects to existing production database via `database.php` on server (not overwritten)

## Local Development Flow

1. **Git Clone** → Gets all PHP code
2. **Add SQL Files** → Developer manually adds `db/main/pae_erp.sql` and `db/audit/pae_erp_audit_backup.sql`
3. **Start Dev** → `docker-compose up -d` (includes MySQL + imports)

## Key Points

- ✅ SQL files (107MB) are excluded from git via `.gitignore`
- ✅ `database.php` is excluded (contains credentials, stays on server)
- ✅ Production: GitLab CI/CD uploads PHP files to FTP, server runs PHP directly
- ✅ Dev: Uses Docker with local MySQL containers and SQL imports
- ✅ Docker is **dev-only** - production doesn't use Docker
- ✅ CI/CD uploads only changed files (detected via `git diff`) to FTP
- ✅ `database.php` is excluded from git, so it won't be overwritten on server

## Verification

To verify your setup works:

```bash
# Check what's in git (should NOT include SQL files)
git ls-files | grep -E "\.sql$|database\.php"

# Should return nothing (or only .gitkeep files)
```

The setup is **CI/CD ready** and **dev-friendly**! 🚀

