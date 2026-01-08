# CodeIgniter 3 ERP System - Project Analysis

## Executive Summary

This is a **CodeIgniter 3** Enterprise Resource Planning (ERP) system named **"PA Energy Portal"** (PAE). The project appears to be a comprehensive business management system for an energy/utility company, with modules covering billing, HRIS (Human Resources Information System), CRM (Customer Relationship Management), inventory, purchasing, and more.

**Project Name:** PA Energy Portal (PAE ERP)  
**Framework:** CodeIgniter 3.x  
**Language:** PHP  
**Database:** MySQL (mysqli driver)  
**Timezone:** Asia/Manila (Philippines)

---

## 1. Project Structure

### 1.1 Standard CodeIgniter 3 Structure
```
erp/
├── application/          # Application code
│   ├── config/          # Configuration files
│   ├── controllers/    # 50+ controllers
│   ├── models/         # 44 models
│   ├── views/          # 1300+ view files
│   ├── helpers/        # Custom helper functions
│   ├── libraries/      # Custom libraries
│   └── third_party/    # Third-party libraries
├── system/             # CodeIgniter core (framework files)
├── assets/             # Frontend assets (CSS, JS, images)
├── db/                 # Database backup files
└── index.php           # Front controller
```

### 1.2 Key Directories

**Controllers (50+):**
- `admin.php`, `auth.php`, `billing.php`, `hris.php`, `crm.php`
- `inventory.php`, `purchasing.php`, `payroll.php`, `tellering.php`
- `peco.php` (default controller), `customer.php`, `reports.php`
- And many more specialized modules

**Models (44):**
- Follows naming convention: `model_[module].php`
- Examples: `model_auth.php`, `model_billing.php`, `model_hris.php`

**Views:**
- Organized by module and purpose
- `admin/` - Admin panel views
- `frontend/` - Public-facing views
- `mobile/` - Mobile-responsive views
- `custom/` - Custom templates

---

## 2. Technology Stack

### 2.1 Backend
- **PHP:** Version 7.x (based on PHP files in `/php` directory)
- **Framework:** CodeIgniter 3.x
- **Database:** MySQL (mysqli driver)
- **Session Management:** Database-backed sessions

### 2.2 Frontend Libraries & Plugins
The project uses extensive frontend libraries (found in `assets/global/plugins/`):

**UI Frameworks:**
- Bootstrap (multiple versions)
- jQuery (3.7.1 via npm)
- DataTables (for data grids)
- Select2 (dropdown enhancement)

**Specialized Plugins:**
- **Charts:** AmCharts, Morris.js
- **Date/Time:** Bootstrap DatePicker, DateTimePicker, Daterangepicker
- **File Upload:** jQuery File Upload, Dropzone, Plupload
- **Rich Text:** CKEditor, Bootstrap Summernote, Markdown
- **PDF Generation:** DomPDF, TCPDF, FPDF
- **Barcode/QR:** PHP QR Code, Zend Barcode
- **Printing:** EscPos (for receipt printers)
- **Excel:** PHPExcel
- **Maps:** Google Maps API
- **Notifications:** Bootstrap Growl, jQuery Notific8
- **And 50+ more plugins**

### 2.3 Third-Party PHP Libraries
- **DomPDF:** HTML to PDF conversion
- **PHPExcel:** Excel file generation/reading
- **TCPDF:** PDF generation
- **Zend Framework:** Barcode generation, validation
- **Mike42 EscPos:** Thermal printer support

---

## 3. Database Configuration

### 3.1 Database Connections
The system supports multiple database connections:

**Primary Database (`pae`):**
- Main ERP database
- Configurable via `$connect` variable in `database.php`
- Supports: `dev`, `online`, or PAE server

**Audit Database (`audit`):**
- Separate audit/logging database
- Tracks system changes and user activities

**External Databases:**
- **PECO:** `172.174.114.142` - Separate PECO ERP system
- **TVI:** Localhost - TVI ERP system

### 3.2 Database Tables (Naming Convention)
Tables use `prime_` prefix:
- `prime_system_users_sessions` - Session storage
- `prime_system_users_roles_matrix` - User role assignments
- `prime_module_main` - Module definitions
- `prime_module_navigations_main` - Navigation structure
- And many more business-specific tables

### 3.3 Security Concerns
⚠️ **CRITICAL:** Database credentials are hardcoded in `application/config/database.php`:
- Production passwords visible in source code
- Should be moved to environment variables or `.env` file
- Consider using encrypted configuration files

---

## 4. Key Features & Modules

### 4.1 Core Modules Identified

1. **Authentication & Authorization (`auth.php`)**
   - User login/logout
   - Session management
   - User locking mechanism
   - Role-based access control

2. **Billing (`billing.php`)**
   - Customer billing management
   - Payment processing
   - Receipt generation

3. **HRIS - Human Resources (`hris.php`)**
   - Employee management
   - Payroll processing
   - Attendance tracking

4. **CRM - Customer Relationship Management (`crm.php`)**
   - Customer profiles
   - Interaction tracking
   - Service requests

5. **Inventory (`inventory.php`)**
   - Stock management
   - Item tracking
   - Warehouse operations

6. **Purchasing (`purchasing.php`)**
   - Purchase orders
   - Vendor management
   - Procurement workflow

7. **Tellering (`tellering.php`)**
   - Cashier operations
   - Transaction processing
   - Queue management

8. **Reports (`reports.php`)**
   - Business intelligence
   - Data analytics
   - Export capabilities

9. **Legal (`legal.php`)**
   - Document management
   - Compliance tracking

10. **Installation (`installation.php`)**
    - Service installation tracking
    - Field operations

11. **Inspection (`inspection.php`)**
    - Quality control
    - Inspection workflows

12. **Smart Meter (`smartmeter.php`)**
    - Meter reading management
    - Automated data collection

13. **CAD - Computer-Aided Design (`cad.php`)**
    - Technical drawings
    - Design documentation

14. **MRD - Material Requirements Document (`mrd.php`)**
    - Material planning
    - Resource allocation

15. **And many more specialized modules...**

### 4.2 System Features

- **Multi-database support** (main + audit)
- **Role-based access control** (RBAC)
- **Session management** with database storage
- **Audit logging** system
- **PDF generation** (multiple libraries)
- **Excel import/export**
- **Barcode/QR code generation**
- **Receipt printing** (thermal printers)
- **Mobile-responsive** views
- **AJAX-heavy** interface
- **File upload** capabilities
- **Notification system**
- **Calendar integration**
- **Search functionality**
- **Query builder** interface

---

## 5. Configuration Analysis

### 5.1 Application Configuration (`config.php`)

**Key Settings:**
- **Base URL:** Auto-detected via `APP_URL` constant
- **Index Page:** `index.php` (not using URL rewriting)
- **Encryption Key:** `P4E2021` (⚠️ Should be stronger and environment-specific)
- **Session:**
  - Cookie name: `PAE`
  - Database storage: Enabled
  - Table: `prime_system_users_sessions`
  - Expiration: 0 (no expiration)
- **CSRF Protection:** Disabled (⚠️ Security risk)
- **XSS Filtering:** Disabled globally (⚠️ Security risk)
- **Query Strings:** Enabled (`?c=controller&m=method`)

### 5.2 Autoload Configuration

**Libraries Auto-loaded:**
- `database` - Database connection
- `datatables` - Custom DataTables library
- `session` - Session management
- `table` - HTML table generation
- `simplexml` - XML processing
- `user_agent` - Browser detection

**Helpers Auto-loaded:**
- `url`, `directory`, `sys`, `peco`, `hris`, `operations`, `payroll`, `frontend`, `cookie`

**Models Auto-loaded:**
- `model_auth` - Authentication model

### 5.3 Routes Configuration

**Default Controller:** `peco` (login/landing page)

**Custom Routes:**
- `/customer/(:any)` → `customer/index/$1`
- `/profile/(:any)` → `profile/index/$1`
- `/ajax/(:any)` → `ajax/index/$1`
- `/person/(:any)` → `person/index/$1`
- `/setup/*` - Setup utilities

**404 Handler:** `pages/error404`

---

## 6. Security Analysis

### 6.1 ⚠️ Security Concerns

1. **CSRF Protection Disabled**
   - `$config['csrf_protection'] = FALSE;`
   - **Risk:** Vulnerable to Cross-Site Request Forgery attacks
   - **Recommendation:** Enable CSRF protection

2. **XSS Filtering Disabled**
   - `$config['global_xss_filtering'] = FALSE;`
   - **Risk:** Vulnerable to XSS attacks
   - **Recommendation:** Enable or implement manual filtering

3. **Weak Encryption Key**
   - Key: `P4E2021` (too short and predictable)
   - **Risk:** Weak encryption for sessions/cookies
   - **Recommendation:** Generate strong random key (32+ characters)

4. **Database Credentials in Source**
   - Passwords hardcoded in `database.php`
   - **Risk:** Credential exposure if code is leaked
   - **Recommendation:** Use environment variables

5. **Session Security**
   - No IP matching: `sess_match_ip = FALSE`
   - No user agent matching: `sess_match_useragent = FALSE`
   - **Risk:** Session hijacking
   - **Recommendation:** Enable both for production

6. **Error Reporting**
   - Currently set to `development` mode
   - **Risk:** May expose sensitive information
   - **Recommendation:** Set to `production` in live environment

7. **Google API Key Exposed**
   - `SYSTEM_GOOGLE_API` in constants.php
   - **Risk:** API key abuse if exposed
   - **Recommendation:** Move to environment config

### 6.2 ✅ Security Features Present

- Database-backed sessions
- Password hashing (using `hashvalidate()` function)
- Role-based access control
- User locking mechanism
- Session expiration management
- Input validation (form_validation library)

---

## 7. Code Quality Observations

### 7.1 Strengths
- Well-organized MVC structure
- Consistent naming conventions
- Modular design (separate controllers/models per feature)
- Extensive use of helpers for reusable functions
- Database abstraction (using CodeIgniter Query Builder)

### 7.2 Areas for Improvement
- **Mixed coding styles** (some inconsistent formatting)
- **Large helper files** (sys_helper.php has 1800+ lines)
- **Direct database queries** in some places (should use models)
- **Hardcoded values** (should use constants/config)
- **Limited error handling** in some controllers
- **No visible unit tests** or test structure
- **Commented-out code** present in routes.php

---

## 8. Dependencies

### 8.1 PHP Dependencies
- CodeIgniter 3.x (core framework)
- PHP 7.x (based on included PHP binaries)
- MySQLi extension
- Various PHP libraries (see Third-Party section)

### 8.2 JavaScript Dependencies (via npm)
```json
{
  "jquery": "^3.7.1",
  "jsdom": "^25.0.1"
}
```

### 8.3 Frontend Dependencies (Manual)
- Bootstrap (multiple versions)
- jQuery plugins (50+)
- DataTables
- AmCharts
- CKEditor
- And many more (see assets/global/plugins/)

---

## 9. Development Environment

### 9.1 Environment Detection
- **Development Mode:** `SYSTEM_DEV_MODE = TRUE`
- **Online Mode:** `SYSTEM_ONLINE = FALSE`
- **Environment:** Set to `development` in `index.php`

### 9.2 Database Connection Modes
Three connection modes available:
1. **`dev`** - Localhost development
2. **`online`** - Turbify hosting
3. **Default** - PAE Server (172.20.224.5)

### 9.3 Build Tools
- **Git:** Version control (git-sync scripts present)
- **Bitbucket Pipelines:** CI/CD configuration file present
- **NetBeans:** Project files present (nbproject/)

---

## 10. Notable Files & Utilities

### 10.1 Setup Utilities
- `application/controllers/setup.php` - System setup
- `application/views/system/setup/` - Setup interfaces
- Database setup routes

### 10.2 Utility Scripts
- `git-sync.cmd` / `git-sync.ps1` / `git-sync.sh` - Git synchronization
- `run.ps1` / `run.sh` - Application runner scripts
- `barcode.php` - Standalone barcode generator

### 10.3 Database Backups
- Multiple `.nb3` backup files in `/db` directory
- SQL schema files for various modules
- Migration/update scripts

---

## 11. Architecture Patterns

### 11.1 MVC Pattern
- **Models:** Data access layer
- **Views:** Presentation layer (PHP templates)
- **Controllers:** Business logic coordination

### 11.2 Helper Functions
Extensive use of helper functions for:
- User authentication (`user_id()`, `check_access()`)
- Role management (`get_users_roles_matrix_id_arr()`)
- System utilities (`random_str()`, etc.)
- Module-specific helpers (HRIS, PECO, operations, payroll)

### 11.3 Library Extensions
- Custom DataTables library
- Custom PDF libraries
- Custom XML processing

---

## 12. Recommendations

### 12.1 Immediate Security Fixes
1. ✅ Enable CSRF protection
2. ✅ Enable XSS filtering
3. ✅ Generate strong encryption key
4. ✅ Move credentials to environment variables
5. ✅ Enable session IP/user agent matching
6. ✅ Set environment to `production` for live

### 12.2 Code Improvements
1. Refactor large helper files
2. Implement consistent error handling
3. Add input validation everywhere
4. Remove commented-out code
5. Add PHPDoc comments
6. Implement logging strategy

### 12.3 Modernization
1. Consider migration to CodeIgniter 4 (or modern framework)
2. Implement Composer for dependency management
3. Add unit/integration tests
4. Implement API versioning
5. Add API documentation
6. Consider frontend framework (React/Vue) for complex UIs

### 12.4 Infrastructure
1. Implement proper CI/CD pipeline
2. Add automated backups
3. Implement monitoring/logging (e.g., Sentry)
4. Add database migration system
5. Implement caching strategy (Redis/Memcached)

---

## 13. Project Statistics

- **Controllers:** 50+
- **Models:** 44
- **Views:** 1300+ files
- **Helpers:** 12 custom helpers
- **Libraries:** Multiple custom libraries
- **Third-party Libraries:** 10+ major libraries
- **Frontend Plugins:** 50+ jQuery/Bootstrap plugins
- **Database Connections:** 4 (main, audit, PECO, TVI)
- **Lines of Code:** Estimated 100,000+ lines

---

## 14. Conclusion

This is a **large, feature-rich ERP system** built on CodeIgniter 3. It demonstrates:
- ✅ Comprehensive business functionality
- ✅ Modular architecture
- ✅ Extensive third-party integration
- ⚠️ Security vulnerabilities that need addressing
- ⚠️ Legacy framework (CodeIgniter 3 is outdated)
- ⚠️ Code quality improvements needed

The system appears to be **actively maintained** and **production-ready** but would benefit from security hardening and modernization efforts.

---

**Analysis Date:** 2024  
**Framework Version:** CodeIgniter 3.x  
**PHP Version:** 7.x  
**Project Status:** Production/Active

