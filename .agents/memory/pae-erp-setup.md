---
name: PAE ERP setup decisions
description: Key quirks, decisions, and constraints for the PA Energy Portal (CodeIgniter 3 + PHP 8.2 + MariaDB 10.11) running in Replit on port 5000.
---

## Startup
- `start.sh` inits MariaDB (normal mode), waits for ready, runs `db_init.php`, `db_schema.php`, then starts PHP dev server on 0.0.0.0:5000
- `db_schema.php` is idempotent — safe to re-run; runs `CREATE TABLE IF NOT EXISTS` + `INSERT IGNORE` + `INSERT … ON DUPLICATE KEY UPDATE`
- DB creds encoded in `db_schema.php` and `application/config/database.php`

## Auth
- Login is AJAX-only: POST to `/auth/check_database` with header `X-Requested-With: XMLHttpRequest`
- Password uses bcrypt (`password_hash`/`password_verify`). Admin: admin / admin123
- `super_admin()` returns true if `user_id() == 1` OR role 1 is in `prime_system_users_roles_matrix` for the user

## Sidebar / Navigation (fixed)
- `prime_module_navigations_main` must be seeded — table is empty on fresh DB
- Schema was missing a `desc` column (queried by leftnav.php and nav_children helper) — added via `ALTER TABLE … ADD COLUMN IF NOT EXISTS` in db_schema.php
- `check_nav_parent()` in `peco_helper.php` had no super_admin bypass — it always returned 0 when `prime_system_users_roles_matrix_access` was empty, hiding all dynamic nav items even for admin
- **Fix**: added super_admin fast-path at top of `check_nav_parent()` that counts children directly from the nav table
- Nav structure: 18 level-1 module groups (parent=0) + 69 level-2 sub-items seeded via db_schema.php INSERT IGNORE
- Module groups: CAD, Billing, Collections/AR, MRD, CWDO, Inspection, Installation, HRIS, Payroll, Inventory, EPRS, Assets, Reports, JO, CRM, Legal, BOS, ITD

## Tables added beyond original schema
- `prime_system_users_info_main` (userid, firstname, middlename, lastname, status)
- `prime_system_users_info_img`
- `prime_chart_of_accounts`
- `prime_system_users_preferences`
- `system_logs`
- `person` (for admin personid FK)
- Added columns to existing tables: `prime_system_users` (firstname, lastname, allowexternal, idletime, nickname), `prime_module_navigations_main` (desc), `prime_module_navigations_main` original schema (code, name, hashcode, etc.), `prime_system_users_roles_matrix` (type, status), `prime_system_roles_dashboards` (navids)
- `prime_module_navigations_public` (navid, status) — JOIN target in model_admin get_user_dashboard_access
- `prime_module_navigations_departments` (navid, deptid, status) — used in tellering module
- `prime_system_users_module_shortcut` (moduleid, userid, status) — user shortcuts panel
- `system_quick_launch_main` + `system_quick_launch_role_matrix` — top-bar quick launch menu
- `system_users_conversation_messages` — in-app messenger
- `prime_system_tags`, `prime_system_tags_module`, `prime_system_tagging` — module tagging system
- `system_monitoring_version_details` — settings/version monitoring
- `address_barangay`, `address_province`, `address_region`, `address_landmark` — address lookup dropdowns
- All above are now in db_schema.php (CREATE TABLE IF NOT EXISTS) and will be created on fresh runs

## Sidebar nav (leftnav.php) fix applied
- Query now filters `nm.levels = 1` (only top-level groups) and adds `ORDER BY nm.sorting ASC`
- Without levels=1, all 80+ nav items were fetched and processed, items appeared in random order
- Administration (sorting=19) and Finance (sorting=21) now appear consistently after groups 1–18

## Firebase note
Replacing MariaDB with Firebase Realtime Database is not practical for this app — CI3 uses Active Record/SQL throughout hundreds of controllers with no Firebase PHP driver.

## Deployment
- Autoscale target; run command: `["bash", "/home/runner/workspace/start.sh"]`
- APP_URL set in `application/config/constants.php` using `REPLIT_DEV_DOMAIN` env var
