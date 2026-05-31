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

// ── Module Navigation ─────────────────────────────────────────────────────────
$sqls[] = "CREATE TABLE IF NOT EXISTS `prime_module_main` (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
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

$conn->close();
echo "\nSchema setup complete!\n";
