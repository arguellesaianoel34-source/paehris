<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

$active_group               = 'pae';
$active_group_audit         = 'audit';

$mysql_socket = '/home/runner/mysql-run/mysql.sock';
ini_set('mysqli.default_socket', $mysql_socket);

$query_builder = TRUE;

$db[$active_group]['sysmode']  = $active_group;
$db[$active_group]['sysaudit'] = $active_group_audit;

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
// ############### ERP MAIN ##########################
// ###################################################
$db['pae'] = array_merge($default_db_config, array(
    'hostname' => '127.0.0.1',
    'username' => 'uub4rmw23inpzxn9_pae_root',
    'password' => '959@M+U1GOat',
    'database' => 'uub4rmw23inpzxn9_erp',
    'port'     => '3306',
));

// ###################################################
// ############### ERP AUDIT #########################
// ###################################################
$db['audit'] = array_merge($default_db_config, array(
    'hostname' => '127.0.0.1',
    'username' => 'uub4rmw23inpzxn9_pae_root',
    'password' => '959@M+U1GOat',
    'database' => 'uub4rmw23inpzxn9_erp_audit',
    'port'     => '3306',
));

// ###################################################
// ############### PECO MAIN #########################
// ###################################################
$db['peco'] = array_merge($default_db_config, array(
    'hostname' => '172.174.114.142',
    'username' => 'rwlisxy9bxboh4qh_pecoerp',
    'password' => 'P3c0!2022web##',
    'database' => 'rwlisxy9bxboh4qh_peco_erp',
));

// ###################################################
// ############### TVI MAIN ##########################
// ###################################################
$db['tvi'] = array_merge($default_db_config, array(
    'hostname' => 'localhost',
    'database' => 'uub4rmw23inpzxn9_tvi_erp',
    'username' => 'uub4rmw23inpzxn9_api',
    'password' => 'P@3API2025',
));
