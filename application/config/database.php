<?php
/**
 * Database configuration file for CodeIgniter
 * This file contains the database connection settings
 * for different environments such as development, production, and testing.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

$active_group               = 'pae';
$active_group_audit         = 'audit';
$connect                    = 'dev';

$db_config = array();

if( $connect == 'dev' ) {
    // LOCALHOST
    $db_config['host_server']            = 'localhost';
    $db_config['host_user']              = 'dev_erp';
    $db_config['host_db']                = 'pae_erp';
    $db_config['host_pass']              = 'G3HhN[.Ez6G2YRZA';

    $db_config['audit_server']           = 'localhost';
    $db_config['audit_user']             = 'dev_erp';
    $db_config['audit_db']               = 'pae_erp_audit';
    $db_config['audit_pass']             = 'G3HhN[.Ez6G2YRZA';

} else if( $connect == 'online' ) {
    // Turbify
    $db_config['host_server']            = 'localhost';
    $db_config['host_user']              = 'uub4rmw23inpzxn9_pae_root';
    $db_config['host_db']                = 'uub4rmw23inpzxn9_erp';
    $db_config['host_pass']              = '959@M+U1GOat';

    $db_config['audit_server']           = 'localhost';
    $db_config['audit_user']             = 'uub4rmw23inpzxn9_pae_root';
    $db_config['audit_db']               = 'uub4rmw23inpzxn9_erp_audit';
    $db_config['audit_pass']             = '959@M+U1GOat';

} else {
    // PAE SERVER
    $db_config['host_server']            = '172.20.224.5';
    $db_config['host_user']              = 'lucky';
    $db_config['host_db']                = 'pae';
    $db_config['host_pass']              = 'F4D3R0N88';

    $db_config['audit_server']           = '172.20.224.5';
    $db_config['audit_user']             = 'lucky';
    $db_config['audit_db']               = 'pae_audit';
    $db_config['audit_pass']             = 'F4D3R0N88';
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
));

// ###################################################
// ############### PECO MAIN #########################
// ###################################################
$db['peco'] = array_merge($default_db_config, array(
    'hostname' => '44.195.210.193',
    'username' => 'rwlisxy9bxboh4qh_pecoerp',
    'password' => 'P3c0!2022web##',
    'database' => 'rwlisxy9bxboh4qh_peco_erp',
));

// ###################################################
// ############### TVI MAIN ##########################
// ###################################################
$db['tvi'] = array_merge($default_db_config, array(
    'hostname' => 'localhost',
    'username' => 'uub4rmw23inpzxn9_pae_root',
    'password' => '959@M+U1GOat',
    'database' => 'uub4rmw23inpzxn9_tvi_erp',
));