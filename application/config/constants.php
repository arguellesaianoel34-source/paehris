<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
const FILE_READ_MODE = 0644;
const FILE_WRITE_MODE = 0666;
const DIR_READ_MODE = 0755;
const DIR_WRITE_MODE = 0777;

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

const FOPEN_READ = 'rb';
const FOPEN_READ_WRITE = 'r+b';
const FOPEN_WRITE_CREATE_DESTRUCTIVE = 'wb'; // truncates existing file data, use with care
const FOPEN_READ_WRITE_CREATE_DESTRUCTIVE = 'w+b'; // truncates existing file data, use with care
const FOPEN_WRITE_CREATE = 'ab';
const FOPEN_READ_WRITE_CREATE = 'a+b';
const FOPEN_WRITE_CREATE_STRICT = 'xb';
const FOPEN_READ_WRITE_CREATE_STRICT = 'x+b';

// FOR SYSTEM INFO
const SYSTEM_NAME = 'PA Energy Portal';
const SYSTEM_MSG_QRY = 'Error PHP: Query is not set yet, please check backend PHP!';
const SYSTEM_MSG_DEFAULT = 'Error PHP: Query is not set yet, please check backend PHP!';

// DATABASE SETUP
const SYSTEM_ONLINE = false; // TRUE IF CONNECT DB TO LOCALHOST
const SYSTEM_DEV_MODE = true; // TRUE IF CONNECT DB TO LOCALHOST
const SYSTEM_DEV_PORT = false; // FALSE if default | add port if 3306 is not the mysql port
const SYSTEM_GOOGLE_API = 'AIzaSyDqC5lmJR1TtWTnySj2psx8-3JynOFUyYE';

const APP_URL =
    ($_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http') .
    '://' . $_SERVER['SERVER_NAME'] .
    (($_SERVER['SERVER_NAME'] === 'localhost' && $_SERVER['SERVER_PORT'] == 8080) ? ':8080' : '') .
    str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

/* End of file constants.php */
/* Location: ./application/config/constants.php */