<?php
/**
 * MariaDB Initialization Script
 * Connects to the running MariaDB (via socket) and sets up system tables + app databases.
 */
$sock = '/home/runner/mysql-run/mysql.sock';
$share = '/nix/store/a4jsa8kjdn3wlccj2wkvhxqza38rpxzf-mariadb-server-10.11.13/share/mysql';

// Suppress deprecated errors for older SQL syntax
set_error_handler(function() { return true; });

$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
if (!mysqli_real_connect($conn, 'localhost', 'root', '', '', 3306, $sock)) {
    echo "Connect failed: " . mysqli_connect_error() . "\n";
    exit(1);
}
echo "Connected to MariaDB via socket\n";

// Create mysql system database
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS mysql");
mysqli_select_db($conn, "mysql");
echo "Selected mysql database\n";

function runSqlFile($conn, $filepath) {
    $content = file_get_contents($filepath);
    if ($content === false) {
        echo "ERROR: Cannot read $filepath\n";
        return;
    }

    // Parse SQL statements (handle multi-statement blocks carefully)
    $delimiter = ';';
    $statement = '';
    $errors = 0;
    $count = 0;

    foreach (explode("\n", $content) as $line) {
        $trimmed = trim($line);
        // Skip empty lines and comments
        if ($trimmed === '' || strpos($trimmed, '--') === 0 || strpos($trimmed, '#') === 0) {
            continue;
        }
        $statement .= $line . "\n";

        // Check if statement ends
        if (substr(rtrim($trimmed), -1) === $delimiter) {
            $stmt = trim($statement);
            if (!empty($stmt)) {
                // Execute via multi_query to handle compound statements
                if (!mysqli_query($conn, $stmt)) {
                    $errno = mysqli_errno($conn);
                    // Ignore "already exists" type errors
                    if (!in_array($errno, [1050, 1060, 1061, 1062, 1065])) {
                        $errors++;
                        if ($errors <= 5) {
                            echo "  SQL Error $errno: " . mysqli_error($conn) . "\n";
                            echo "  Statement: " . substr(trim($stmt), 0, 100) . "\n";
                        }
                    }
                } else {
                    $count++;
                }
                // Drain any remaining result sets
                while (mysqli_more_results($conn)) {
                    mysqli_next_result($conn);
                }
            }
            $statement = '';
        }
    }
    echo "  Executed " . basename($filepath) . ": $count OK, $errors errors\n";
}

echo "Loading system tables...\n";
runSqlFile($conn, "$share/mysql_system_tables.sql");
runSqlFile($conn, "$share/mysql_system_tables_data.sql");

// Flush privileges to activate the loaded tables
mysqli_query($conn, "FLUSH PRIVILEGES");
echo "Privileges flushed\n";

// Create application databases
$dbs = ['uub4rmw23inpzxn9_erp', 'uub4rmw23inpzxn9_erp_audit'];
foreach ($dbs as $db) {
    $result = mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "Database `$db`: " . ($result ? "created/exists" : mysqli_error($conn)) . "\n";
}

// Create app user with access from all common hosts
$appUser = 'uub4rmw23inpzxn9_pae_root';
$appPass = '959@M+U1GOat';
foreach (['%', 'localhost', '127.0.0.1'] as $host) {
    foreach ($dbs as $db) {
        $sql = "GRANT ALL PRIVILEGES ON `$db`.* TO '$appUser'@'$host' IDENTIFIED BY '$appPass'";
        if (!mysqli_query($conn, $sql)) {
            echo "Grant error for $host: " . mysqli_error($conn) . "\n";
        }
    }
}
mysqli_query($conn, "FLUSH PRIVILEGES");
echo "User '$appUser' granted on all hosts\n";

// Verify
echo "\n--- Verification ---\n";
$r = mysqli_query($conn, "SHOW DATABASES");
while ($row = mysqli_fetch_row($r)) {
    echo "  DB: " . $row[0] . "\n";
}
$r2 = mysqli_query($conn, "SELECT User, Host FROM mysql.user WHERE User != ''");
if ($r2) {
    while ($row = mysqli_fetch_row($r2)) {
        echo "  User: " . $row[0] . "@" . $row[1] . "\n";
    }
}

mysqli_close($conn);
echo "\nInitialization complete!\n";
