---
name: PAE ERP Setup
description: Key decisions and quirks for running the PA Energy Portal (CodeIgniter 3 + PHP 8.2 + MariaDB) on Replit.
---

# PAE ERP Replit Setup

## Architecture
- MariaDB 10.11 via Nix, data at `/home/runner/mysql-data`, socket `/home/runner/mysql-run/mysql.sock`, port 3306 localhost
- PHP 8.2 built-in server on 0.0.0.0:5000 via `router.php`
- Startup: `start.sh` → MariaDB init (first run only via `db_init.php`) → schema (`db_schema.php`) → PHP server
- Deployment target: autoscale, run `["bash", "/home/runner/workspace/start.sh"]`

## Critical Quirks

**Password hashing**: App uses PHP `password_hash()`/`password_verify()` (bcrypt). Default admin: `admin`/`admin123`.

**Login is AJAX-only**: Auth controller redirects non-AJAX requests back to home. Login posts to `/auth/check_database` with `X-Requested-With: XMLHttpRequest`.

**Schema not in repo**: No SQL dump was committed. `db_schema.php` was hand-built by reverse-engineering the application. It creates ~42 tables/statements with all required columns.

**Missing columns discovered at runtime**: The original db_schema.php was incomplete. Many columns the app queries directly on `prime_system_users` (firstname, lastname, allowexternal, idletime, nickname) weren't in the schema. Similarly `prime_module_navigations_main` needed: code, name, hashcode, namehash, htmlclass, htmlid, url, pagefile, levels, type, sorting, withpay, icon, moduleid.

**`prime_system_users_info_main` required**: App JOINs this table in `init_userpage_data()` for every page header. Must have a row for admin user (userid=1) or dashboard won't load.

**`desc` is reserved**: MySQL/MariaDB treats `desc` as reserved word — use backtick escaping `` `desc` `` in ALTER/CREATE statements.

**PHP 8.2 compat fixes applied**:
- `index.php`: E_DEPRECATED suppressed
- `system/core/Input.php`: filter_var flag empty string → 0

**APP_URL**: `application/config/constants.php` uses `HTTP_HOST` + `HTTP_X_FORWARDED_PROTO` for Replit proxy compatibility.

## Default Credentials
- Admin username: `admin`, password: `admin123`
- DB user: `uub4rmw23inpzxn9_pae_root`, DB: `uub4rmw23inpzxn9_erp`

**Why:** No SQL dump in repo; schema had to be reconstructed. Store these decisions so future schema changes are consistent with discovered column requirements.
