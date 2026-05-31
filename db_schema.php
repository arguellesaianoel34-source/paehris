<?php
/**
 * Creates the minimum schema needed for the PA Energy Portal ERP to start.
 * Run once after MariaDB initialization (idempotent - safe to re-run).
 */
$conn = new mysqli('127.0.0.1', 'uub4rmw23inpzxn9_pae_root', '959@M+U1GOat', 'uub4rmw23inpzxn9_erp', 3306);
if ($conn->connect_error) { echo "FAILED: " . $conn->connect_error . "\n"; exit(1); }
echo "Connected to ERP database\n";

$sqls = [];

// ── Session table (CodeIgniter 3) ─────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── System Settings ───────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codes` varchar(50) NOT NULL,
  `descriptions` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codes` (`codes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "INSERT IGNORE INTO `system_settings` (`codes`,`descriptions`,`status`) VALUES ('DEV','Development Mode',0)";

// ── System Icons ──────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `system_icons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── System Logs ───────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `system_logs` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ipaddress` varchar(50) DEFAULT NULL,
  `datecreated` datetime DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Gender / Person ───────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_gender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "INSERT IGNORE INTO `prime_gender` (`name`) VALUES ('Male'),('Female'),('Other')";

$sqls[] = "CREATE TABLE IF NOT EXISTS `person_title_main` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `person` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `datecreated` datetime DEFAULT NOW(),
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `person_title` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `personid` int(11) DEFAULT NULL,
  `titleid` int(11) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `person_address_matrix` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `personid` int(11) DEFAULT NULL,
  `districtid` int(11) DEFAULT NULL,
  `cityid` int(11) DEFAULT NULL,
  `countryid` int(11) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `person_contact_matrix` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `personid` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `value` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `persons_marital_status_logs` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `personid` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `datecreated` datetime DEFAULT NOW(),
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Employee ──────────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_employee_main` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `personid` int(11) DEFAULT NULL,
  `empid` varchar(50) DEFAULT NULL,
  `employeeno` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── System Users ──────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `personid` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `type` tinyint(1) DEFAULT 1 COMMENT '1=admin,2=user',
  `landing` varchar(255) DEFAULT NULL,
  `allowexternal` tinyint(1) DEFAULT 0,
  `idletime` int(11) DEFAULT 30,
  `status` tinyint(1) DEFAULT 1,
  `avatar` varchar(255) DEFAULT NULL,
  `datecreated` datetime DEFAULT NOW(),
  PRIMARY KEY (`sysid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_info_main` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `datecreated` datetime DEFAULT NOW(),
  PRIMARY KEY (`sysid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_info_img` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `imgpath` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `datecreated` datetime DEFAULT NOW(),
  PRIMARY KEY (`sysid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_logs` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `sessionip` varchar(45) DEFAULT NULL,
  `sessiondevice` varchar(100) DEFAULT NULL,
  `sessiondevicename` varchar(100) DEFAULT NULL,
  `sessionagent` varchar(255) DEFAULT NULL,
  `sessionlogtype` tinyint(1) DEFAULT 1,
  `sessionlogresponse` tinyint(1) DEFAULT 0,
  `sessionsegment` varchar(255) DEFAULT NULL,
  `sessionmoduleid` int(11) DEFAULT NULL,
  `sessiondatetime` datetime DEFAULT NOW(),
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_preferences` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `menutype` tinyint(1) DEFAULT 1,
  `theme` varchar(50) DEFAULT 'default',
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_roles_main` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `descriptions` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "INSERT IGNORE INTO `prime_system_users_roles_main` (`sysid`,`code`,`descriptions`) VALUES (1,'ADMIN','Administrator'),(2,'STAFF','Staff')";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_roles_matrix` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `roleid` int(11) DEFAULT NULL,
  `type` tinyint(1) DEFAULT 1,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_roles_matrix_access` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` int(11) DEFAULT NULL,
  `navid` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_confirmation` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `personid` int(11) DEFAULT NULL,
  `codes` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `datecreated` datetime DEFAULT NOW(),
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_super` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_legacy_code` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Roles Dashboards ──────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_roles_dashboards` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` int(11) DEFAULT NULL,
  `navids` int(11) DEFAULT NULL,
  `pagefile` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Public Navigation Access ──────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_module_navigations_public` (
  `sysid`  int(11) NOT NULL AUTO_INCREMENT,
  `navid`  int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`),
  KEY `idx_navid` (`navid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// ── Navigation Departments ────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_module_navigations_departments` (
  `sysid`  int(11) NOT NULL AUTO_INCREMENT,
  `navid`  int(11) NOT NULL DEFAULT 0,
  `deptid` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`),
  KEY `idx_navid` (`navid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// ── User Module Shortcuts ─────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_users_module_shortcut` (
  `sysid`    int(11) NOT NULL AUTO_INCREMENT,
  `moduleid` int(11) NOT NULL DEFAULT 0,
  `userid`   int(11) NOT NULL DEFAULT 0,
  `status`   tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`),
  KEY `idx_userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// ── Quick Launch ──────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `system_quick_launch_main` (
  `sysid`   int(11) NOT NULL AUTO_INCREMENT,
  `sorting` int(11) NOT NULL DEFAULT 0,
  `types`   tinyint(1) NOT NULL DEFAULT 1,
  `toggle`  varchar(100) DEFAULT '',
  `target`  varchar(100) DEFAULT '',
  `href`    varchar(255) DEFAULT '',
  `titles`  varchar(255) DEFAULT '',
  `icons`   varchar(100) DEFAULT '',
  `texts`   varchar(255) DEFAULT '',
  `labels`  varchar(100) DEFAULT '',
  `status`  tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sqls[] = "CREATE TABLE IF NOT EXISTS `system_quick_launch_role_matrix` (
  `sysid`  int(11) NOT NULL AUTO_INCREMENT,
  `navid`  int(11) NOT NULL DEFAULT 0,
  `roleid` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`),
  KEY `idx_navid` (`navid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// ── Messaging ─────────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `system_users_conversation_messages` (
  `sysid`       int(11) NOT NULL AUTO_INCREMENT,
  `cid`         int(11) NOT NULL DEFAULT 0,
  `userid`      int(11) NOT NULL DEFAULT 0,
  `texts`       text DEFAULT NULL,
  `datecreated` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sysid`),
  KEY `idx_cid` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// ── Tagging System ────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_tags` (
  `sysid`    int(11) NOT NULL AUTO_INCREMENT,
  `moduleid` int(11) NOT NULL DEFAULT 0,
  `codes`    varchar(100) DEFAULT NULL,
  `descs`    varchar(255) DEFAULT NULL,
  `txtcolor` varchar(50) DEFAULT NULL,
  `bgcolor`  varchar(50) DEFAULT NULL,
  `icon`     varchar(100) DEFAULT NULL,
  `status`   tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_tags_module` (
  `sysid`    int(11) NOT NULL AUTO_INCREMENT,
  `moduleid` int(11) NOT NULL DEFAULT 0,
  `tagid`    int(11) NOT NULL DEFAULT 0,
  `status`   tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_system_tagging` (
  `sysid`    int(11) NOT NULL AUTO_INCREMENT,
  `tagid`    int(11) NOT NULL DEFAULT 0,
  `moduleid` int(11) NOT NULL DEFAULT 0,
  `acctid`   int(11) NOT NULL DEFAULT 0,
  `status`   tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// ── Version Monitoring ────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `system_monitoring_version_details` (
  `sysid`     int(11) NOT NULL AUTO_INCREMENT,
  `verid`     int(11) NOT NULL DEFAULT 0,
  `authid`    int(11) NOT NULL DEFAULT 0,
  `commits`   int(11) DEFAULT 0,
  `insertion` int(11) DEFAULT 0,
  `deletion`  int(11) DEFAULT 0,
  `changes`   decimal(10,2) DEFAULT 0.00,
  `status`    tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// ── Address Lookup ────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `address_barangay` (
  `sysid`      int(11) NOT NULL AUTO_INCREMENT,
  `name`       varchar(255) DEFAULT NULL,
  `districtid` int(11) DEFAULT 0,
  `status`     tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sqls[] = "CREATE TABLE IF NOT EXISTS `address_province` (
  `sysid`    int(11) NOT NULL AUTO_INCREMENT,
  `name`     varchar(255) DEFAULT NULL,
  `regionid` int(11) DEFAULT 0,
  `status`   tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sqls[] = "CREATE TABLE IF NOT EXISTS `address_region` (
  `sysid`  int(11) NOT NULL AUTO_INCREMENT,
  `name`   varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sqls[] = "CREATE TABLE IF NOT EXISTS `address_landmark` (
  `sysid`  int(11) NOT NULL AUTO_INCREMENT,
  `name`   varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// ── Module Navigation ─────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_module_main` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `hashcode` varchar(100) DEFAULT NULL,
  `parent` int(11) DEFAULT 0,
  `type` tinyint(1) DEFAULT 1,
  `levels` int(11) DEFAULT 1,
  `htmlclass` varchar(100) DEFAULT NULL,
  `htmlid` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `pagefile` varchar(255) DEFAULT NULL,
  `withpay` tinyint(1) DEFAULT 0,
  `sorting` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `sortorder` int(11) DEFAULT 0,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_module_navigations_main` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `moduleid` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `hash` varchar(100) DEFAULT NULL,
  `hashcode` varchar(100) DEFAULT NULL,
  `namehash` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `htmlclass` varchar(100) DEFAULT NULL,
  `htmlid` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `pagefile` varchar(255) DEFAULT NULL,
  `levels` int(11) DEFAULT 1,
  `type` tinyint(1) DEFAULT 1,
  `sorting` int(11) DEFAULT 0,
  `withpay` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `sortorder` int(11) DEFAULT 0,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_module_users_logs` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `navid` int(11) DEFAULT NULL,
  `datecreated` datetime DEFAULT NOW(),
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Parameters ────────────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_types_parameter` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Transaction Flow ──────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_transaction_flow_main` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_transaction_flow_main_stages` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `flowid` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `sortorder` int(11) DEFAULT 0,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_transaction_flow_main_stages_owners` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `stageid` int(11) DEFAULT NULL,
  `roleid` int(11) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_transaction_flow_main_stages_required` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `stageid` int(11) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_transaction_flow_main_stages_modules` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `stageid` int(11) DEFAULT NULL,
  `moduleid` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_transaction_flow_main_stages_navigations` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `stageid` int(11) DEFAULT NULL,
  `navid` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Chart of Accounts ─────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_chart_of_accounts` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `codes` varchar(50) DEFAULT NULL,
  `descs` varchar(255) DEFAULT NULL,
  `types` tinyint(1) DEFAULT NULL,
  `groups` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Address Lookups ───────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `address_country` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "INSERT IGNORE INTO `address_country` (`name`) VALUES ('Philippines')";

$sqls[] = "CREATE TABLE IF NOT EXISTS `address_city` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `countryid` int(11) DEFAULT 1,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sqls[] = "CREATE TABLE IF NOT EXISTS `address_districts` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `cityid` int(11) DEFAULT NULL,
  PRIMARY KEY (`sysid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// ── Run all statements ────────────────────────────────────────────────────────
$ok = 0; $fail = 0;
foreach ($sqls as $sql) {
    if (!$conn->query($sql)) {
        $errno = $conn->errno;
        if (!in_array($errno, [1050, 1060, 1061, 1062])) {
            echo "  ERROR $errno: " . $conn->error . "\n  SQL: " . substr($sql, 0, 100) . "\n";
            $fail++;
        } else {
            $ok++;
        }
    } else {
        $ok++;
    }
}

echo "Statements: $ok OK, $fail failed\n";

// ── Seed admin user ───────────────────────────────────────────────────────────
$conn->query("INSERT IGNORE INTO `person` (`sysid`,`firstname`,`middlename`,`lastname`) VALUES (1,'System','','Administrator')");

echo "\nSetting up admin user...\n";
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);

$conn->query("INSERT INTO `prime_system_users`
  (`sysid`,`personid`,`username`,`password`,`firstname`,`type`,`landing`,`status`)
  VALUES (1, 1, 'admin', '$admin_pass', 'System', 1, 'admin/dashboard', 1)
  ON DUPLICATE KEY UPDATE `password`='$admin_pass', `firstname`='System'");
echo "prime_system_users: " . ($conn->error ?: "OK") . "\n";

$conn->query("INSERT IGNORE INTO `prime_system_users_info_main`
  (`userid`,`firstname`,`middlename`,`lastname`,`status`)
  VALUES (1,'System','','Administrator',1)");
echo "prime_system_users_info_main: " . ($conn->error ?: "OK") . "\n";

$conn->query("INSERT IGNORE INTO `prime_system_users_roles_matrix`
  (`userid`,`roleid`,`type`,`status`) VALUES (1,1,1,1)");
echo "prime_system_users_roles_matrix: " . ($conn->error ?: "OK") . "\n";

// ── Add missing `desc` column to prime_module_main ─────────────────────────
$conn->query("ALTER TABLE `prime_module_main` ADD COLUMN IF NOT EXISTS `desc` varchar(255) DEFAULT NULL AFTER `name`");
echo "prime_module_main desc column: " . ($conn->error ?: "OK") . "\n";

// ── Add missing `desc` column to prime_module_navigations_main ─────────────
$conn->query("ALTER TABLE `prime_module_navigations_main` ADD COLUMN IF NOT EXISTS `desc` varchar(255) DEFAULT NULL AFTER `name`");
echo "prime_module_navigations_main desc column: " . ($conn->error ?: "OK") . "\n";

// ── Module Navigation Seed ─────────────────────────────────────────────────
// Level-1: top-level sidebar groups (parent=0, type=1, levels=1)
echo "\nSeeding navigation modules...\n";
$nav_groups = [
    [1,  'cad',          'Customer Accounts','Customer Account Division',   'fa-users',        'blue',     1 ],
    [2,  'billing',      'Billing',          'Billing Management',           'fa-file-text-o',  'green',    2 ],
    [3,  'ar',           'Collections / AR', 'Collections & Accounts Rec.',  'fa-credit-card',  'teal',     3 ],
    [4,  'mrd',          'Meter Reading',    'Meter Reading Division',       'fa-tachometer',   'red',      4 ],
    [5,  'cwdo',         'CWDO',             'Construction & Disconnect',    'fa-wrench',       'yellow',   5 ],
    [6,  'inspection',   'Inspection',       'Inspection Department',        'fa-search',       'blue-grey',6 ],
    [7,  'installation', 'Installation',     'Installation Department',      'fa-plug',         'purple',   7 ],
    [8,  'hris',         'HRIS',             'Human Resources',              'fa-id-badge',     'blue',     8 ],
    [9,  'payroll',      'Payroll',          'Payroll Management',           'fa-money',        'green',    9 ],
    [10, 'inventory',    'Inventory',        'Inventory Management',         'fa-archive',      'orange',   10],
    [11, 'eprs',         'Procurement',      'EPRS / Procurement',          'fa-shopping-cart','teal',     11],
    [12, 'assets',       'Assets',           'Asset Management',             'fa-building-o',   'blue-grey',12],
    [13, 'reports',      'Reports',          'System Reports',               'fa-bar-chart',    'red',      13],
    [14, 'jo',           'Job Orders',       'Job Order Management',         'fa-tasks',        'purple',   14],
    [15, 'crm',          'CRM',              'Customer Relations',           'fa-handshake-o',  'green',    15],
    [16, 'legal',        'Legal',            'Legal Department',             'fa-gavel',        'orange',   16],
    [17, 'bos',          'BOS',              'Back Office System',           'fa-university',   'teal',     17],
    [18, 'itd',          'ITD',              'IT Department',                'fa-laptop',       'blue',     18],
    [19, 'administration','Administration', 'Administration Menu',           'fa-cogs',         'blue',     19],
    [21, 'finance',      'Finance',          'Finance Menu',                 'fa-money',        'green',    21],
];
foreach ($nav_groups as $g) {
    [$sid, $code, $name, $desc, $icon, $hclass, $sort] = $g;
    $hashcode = sha1($code . '_grp');
    $conn->query("INSERT IGNORE INTO `prime_module_navigations_main`
        (`sysid`,`parent`,`code`,`name`,`desc`,`icon`,`htmlclass`,`hashcode`,`pagefile`,`levels`,`type`,`sorting`,`status`)
        VALUES ($sid, 0, '$code', '$name', '$desc', '$icon', '$hclass', '$hashcode', '', 1, 1, $sort, 1)");
}
echo "Level-1 groups: " . ($conn->error ?: "OK") . "\n";

// Level-2: sub-items (parent = group sysid, levels=2, type=1)
// [sysid, parent, code, name, desc, pagefile, sorting]
$nav_items = [
    // CAD (parent=1)
    [101, 1,  'cad-acct',   'Account Management',   'Manage customer accounts',      'cadmain',         1],
    [102, 1,  'cad-new',    'New Application',      'New service application',       'newaccount',      2],
    [103, 1,  'cad-arch',   'Archive',              'Account archives',              'cadarchive',      3],
    [104, 1,  'cad-info',   'Account Info',         'Customer information',          'acctinfo',        4],
    [105, 1,  'cad-appv',   'Application View',     'View submitted applications',   'appview',         5],
    // Billing (parent=2)
    [201, 2,  'bill-main',  'Billing',              'Billing main',                  'billingmain',     1],
    [202, 2,  'bill-add',   'Add Bill',             'Create billing entries',        'addbill',         2],
    [203, 2,  'bill-ct',    'Billing CT',           'CT billing entries',            'billingct',       3],
    [204, 2,  'bill-pae',   'PAE Billing',          'PAE billing module',            'paebilling',      4],
    // Collections / AR (parent=3)
    [301, 3,  'ar-rv',      'Revenue Voucher',      'Revenue voucher management',    'rvmenu',          1],
    [302, 3,  'ar-cash',    'Cash Menu',            'Cashier management',            'cashmenu',        2],
    [303, 3,  'ar-acctg',   'Accounting',           'Accounting entries',            'acctg',           3],
    // MRD (parent=4)
    [401, 4,  'mrd-menu',   'MRD Menu',             'Meter reading main',            'mrdmenu',         1],
    [402, 4,  'mrd-data',   'Meter Data',           'Meter reading data',            'mrddata',         2],
    [403, 4,  'mrd-enc',    'Encoding',             'Meter reading encoding',        'mrdenc',          3],
    [404, 4,  'mrd-bill',   'Add Bill',             'MRD billing',                   'mrdaddbill',      4],
    [405, 4,  'mrd-rep',    'Reports',              'MRD reports',                   'mrdreports',      5],
    [406, 4,  'mrd-lot',    'Lot Book',             'Lot book management',           'mrdlotbook',      6],
    [407, 4,  'mrd-smr',    'Smart Reading',        'Smart meter reading',           'smartreading',    7],
    // CWDO (parent=5)
    [501, 5,  'cwdo-main',  'CWDO',                 'Work / disconnect orders',      'cwdo',            1],
    [502, 5,  'cwdo-rep',   'Reports',              'CWDO reports',                  'cwdorep',         2],
    // Inspection (parent=6)
    [601, 6,  'insp-main',  'Inspection',           'Inspection records',            'inspection',      1],
    [602, 6,  'insp-leg',   'Legal Inspection',     'Legal inspection cases',        'legalinspection', 2],
    // Installation (parent=7)
    [701, 7,  'inst-main',  'Installation',         'Installation records',          'installation',    1],
    [702, 7,  'inst-comm',  'Commercial',           'Commercial new accounts',       'newaccountcomm',  2],
    [703, 7,  'inst-govt',  'Government',           'Government new accounts',       'newaccountgovt',  3],
    // HRIS (parent=8)
    [801, 8,  'hris-emp',   'Employees',            'Employee records',              'employee',        1],
    [802, 8,  'hris-att',   'Attendance',           'Attendance records',            'attendance',      2],
    [803, 8,  'hris-lv',    'Leave',                'Leave management',              'hrisleave',       3],
    [804, 8,  'hris-main',  'HR Main',              'HR administration',             'hrmain',          4],
    [805, 8,  'hris-rep',   'HR Reports',           'HR reports',                    'hrrep',           5],
    [806, 8,  'hris-loc',   'HR Locator',           'Employee locator',              'hrislocator',     6],
    // Payroll (parent=9)
    [901, 9,  'pay-main',   'Payroll',              'Payroll management',            'payroll',         1],
    [902, 9,  'pay-rf',     'Rank & File',          'Rank and file payroll',         'payrollranknfile',2],
    [903, 9,  'pay-t1',     'Tier 1',               'Tier 1 payroll',                'payrolltierd1',   3],
    [904, 9,  'pay-t2',     'Tier 2',               'Tier 2 payroll',                'payrolltierd2',   4],
    [905, 9,  'pay-mr',     'Meter Reader',         'Meter reader payroll',          'payrollmeterreader',5],
    [906, 9,  'pay-conf',   'Confidential',         'Confidential payroll',          'payrollconfi',    6],
    [907, 9,  'pay-rep',    'Reports',              'Payroll reports',               'payrollreports',  7],
    // Inventory (parent=10)
    [1001,10, 'inv-main',   'Inventory',            'Inventory management',          'inventory',       1],
    [1002,10, 'inv-trn',    'Transactions',         'Inventory transactions',        'invtrn',          2],
    [1003,10, 'inv-appr',   'Approval',             'Inventory approvals',           'invapprove',      3],
    [1004,10, 'inv-smr',    'Summary',              'Inventory summary',             'invsmr',          4],
    // EPRS (parent=11)
    [1101,11, 'eprs-list',  'PR List',              'Purchase request list',         'eprslist',        1],
    [1102,11, 'eprs-appr',  'Approval',             'PR approvals',                  'eprsapprove',     2],
    [1103,11, 'eprs-rfp',   'RFP',                  'Request for proposal',          'eprsrfp',         3],
    [1104,11, 'eprs-quote', 'Quotation',            'Supplier quotations',           'eprsquote',       4],
    [1105,11, 'eprs-po',    'Purchase Order',       'Purchase orders',               'eprspo',          5],
    [1106,11, 'eprs-hcs',   'HCS',                  'HCS management',                'eprshcs',         6],
    [1107,11, 'eprs-supp',  'Suppliers',            'Supplier management',           'suppliers',       7],
    // Assets (parent=12)
    [1201,12, 'ast-main',   'Assets',               'Asset records',                 'asset',           1],
    [1202,12, 'ast-entry',  'Asset Entry',          'Enter asset records',           'assetentry',      2],
    [1203,12, 'ast-rep',    'Reports',              'Asset reports',                 'assetreports',    3],
    // Reports (parent=13)
    [1301,13, 'rep-main',   'Reports',              'General reports',               'reports',         1],
    [1302,13, 'rep-apt',    'Aptitude',             'Aptitude reports',              'reportsapt',      2],
    [1303,13, 'rep-bdg',    'Budget',               'Budget reports',                'reportsbudget',   3],
    [1304,13, 'rep-col',    'Collection',           'Collection reports',            'reportscollection',4],
    [1305,13, 'rep-usr',    'User Reports',         'User activity reports',         'reportsuser',     5],
    [1306,13, 'rep-pay',    'Payroll Reports',      'Payroll summary reports',       'reppayroll',      6],
    // Job Orders (parent=14)
    [1401,14, 'jo-main',    'Job Orders',           'Job order list',                'jo',              1],
    [1402,14, 'jo-dash',    'Dashboard',            'Job order dashboard',           'jodash',          2],
    // CRM (parent=15)
    [1501,15, 'crm-menu',   'CRM Menu',             'CRM main menu',                 'crmmenu',         1],
    [1502,15, 'crm-ass',    'Assessment',           'Customer assessment',           'custassessment',  2],
    [1503,15, 'crm-trk',    'Tracker',              'Customer tracker',              'custtracker',     3],
    // Legal (parent=16)
    [1601,16, 'leg-menu',   'Legal Menu',           'Legal main menu',               'legalmenu',       1],
    [1602,16, 'leg-ver',    'Verification',         'Legal verification',            'legalver',        2],
    // BOS (parent=17)
    [1701,17, 'bos-main',   'BOS',                  'Back office system',            'bos',             1],
    [1702,17, 'bos-pceo',   'PCEO',                 'PCEO management',               'bospceo',         2],
    // ITD (parent=18)
    [1801,18, 'itd-main',   'ITD',                  'IT department main',            'itd',             1],
    // Administration (parent=19)
    [1901,19, 'adm-itd',    'ITD Menu',             'IT Department Menu',            'itd',             1],
    [1902,19, 'adm-hr',     'HR Info Sys',          'Human Resources Info System',   'hrmain',          2],
    [1903,19, 'adm-purch',  'Purchasing',           'Purchasing / Procurement',      'eprslist',        3],
    [1904,19, 'adm-inv',    'Inventory',            'Inventory Management',          'inventory',       4],
    [1905,19, 'adm-cal',    'Calendar',             'Calendar',                      'calendar',        5],
    [1906,19, 'adm-audit',  'Audit',                'Audit Management',              'audit',           6],
    // Finance (parent=21)
    [2101,21, 'fin-acctg',  'Accounting',           'Accounting Module',             'acctg',           1],
    [2102,21, 'fin-admin',  'Admin',                'Finance Admin',                 'financeadmin',    2],
];
foreach ($nav_items as $item) {
    [$sid, $parent, $code, $name, $desc, $pagefile, $sort] = $item;
    $hashcode = sha1($code . '_nav');
    $conn->query("INSERT IGNORE INTO `prime_module_navigations_main`
        (`sysid`,`parent`,`code`,`name`,`desc`,`icon`,`htmlclass`,`hashcode`,`url`,`pagefile`,`levels`,`type`,`sorting`,`status`)
        VALUES ($sid, $parent, '$code', '$name', '$desc', 'fa-circle-o', 'default', '$hashcode', '$pagefile', '$pagefile', 2, 1, $sort, 1)");
    if ($conn->error) { echo "Nav item $sid error: " . $conn->error . "\n"; }
}
echo "Level-2 nav items seeded\n";

$conn->close();
echo "\nSchema setup complete!\n";
