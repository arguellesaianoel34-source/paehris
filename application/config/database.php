<?php
// filepath: p:\PAE\erp\application\config\database.php
if (!defined('BASEPATH')) exit('No direct script access allowed');

$active_group               = 'pae';
$active_group_audit         = 'audit';

// Replit local environment: use local MariaDB via socket
$mysql_socket = '/home/runner/mysql-run/mysql.sock';
$is_replit = file_exists($mysql_socket) || getenv('REPLIT_DEV_DOMAIN') !== false;

// Check if running in Docker container
$is_docker = getenv('DOCKER_CONTAINER') === 'true' || gethostname() === 'pae_erp_web' || isset($_SERVER['DOCKER_CONTAINER']);

$db_config = array();
if ($is_replit && !$is_docker) {
    // Local Replit environment: use local MariaDB via TCP on 127.0.0.1
    // (using 127.0.0.1 forces TCP; 'localhost' uses socket which may differ)
    ini_set('mysqli.default_socket', $mysql_socket);
    $db_config['host_server']        = '127.0.0.1';
    $db_config['host_user']          = 'uub4rmw23inpzxn9_pae_root';
    $db_config['host_db']            = 'uub4rmw23inpzxn9_erp';
    $db_config['host_pass']          = '959@M+U1GOat';

    $db_config['audit_server']       = '127.0.0.1';
    $db_config['audit_user']         = 'uub4rmw23inpzxn9_pae_root';
    $db_config['audit_db']           = 'uub4rmw23inpzxn9_erp_audit';
    $db_config['audit_pass']         = '959@M+U1GOat';
} else {
    // Docker or remote
    $db_config['host_server']        = $is_docker ? 'mysql' : 'localhost';
    $db_config['host_user']          = 'uub4rmw23inpzxn9_pae_root';
    $db_config['host_db']            = 'uub4rmw23inpzxn9_erp';
    $db_config['host_pass']          = '959@M+U1GOat';

    $db_config['audit_server']       = $is_docker ? 'mysql_audit' : 'localhost';
    $db_config['audit_user']         = 'uub4rmw23inpzxn9_pae_root';
    $db_config['audit_db']           = 'uub4rmw23inpzxn9_erp_audit';
    $db_config['audit_pass']         = '959@M+U1GOat';
}

$query_builder = TRUE;

$db[$active_group]['sysmode'] = $active_group;
$db[$active_group]['sysaudit'] = $active_group_audit;

// Default settings for all databases
$default_db_config = array(
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => TRUE,
    'db_debug' => TRUE,
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'autoinit' => TRUE,
    'stricton' => FALSE,
);

// ###################################################
// ############### ERP LOCAL #########################
// ###################################################
$db['pae'] = array_merge($default_db_config, array(
    'hostname' => $db_config['host_server'],
    'username' => $db_config['host_user'],
    'password' => $db_config['host_pass'],
    'database' => $db_config['host_db'],
    'port'     => '3306',
));

// ###################################################
// ############### ERP AUDIT LOCAL ###################
// ###################################################
$db['audit'] = array_merge($default_db_config, array(
    'hostname' => $db_config['audit_server'],
    'username' => $db_config['audit_user'],
    'password' => $db_config['audit_pass'],
    'database' => $db_config['audit_db'],
    'port'     => '3306',
));

// ###################################################
// ############### PECO MAIN #########################
// ###################################################
$db['peco'] = array_merge($default_db_config, array(
    'hostname' => '172.174.114.142',
    //'hostname' => '44.195.210.193',
    'username' => 'rwlisxy9bxboh4qh_pecoerp',
    'password' => 'P3c0!2022web##',
    'database' => 'rwlisxy9bxboh4qh_peco_erp',
));

// ###################################################
// ############### TVI MAIN ##########################
// ###################################################
$db['tvi'] = array_merge($default_db_config, array(
    'hostname' => $is_docker ? 'mysql' : 'localhost',
    'database' => 'uub4rmw23inpzxn9_tvi_erp',
    'username' => 'uub4rmw23inpzxn9_api',
    'password' => 'P@3API2025',
));
