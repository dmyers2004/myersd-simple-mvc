<?php 

$config['sess_driver'] = 'mysqli'; /* "driver" to use */
$config['sess_cookie_name'] = 'spl_session'; /* session cookie name */
$config['sess_cookie_lifetime'] = 60*60*24*365; /* the cookie will stay in the users browser this many seconds */
$config['sess_chance_to_update'] = 10; /* percentage chance to regenerate the session id */
$config['sess_table_name'] = 'heat_sessions'; /* database table name */
$config['sess_gc_probability'] = 20; /* if gc divisor is 100 this is a "percent" (of 100) chance of happening */
$config['sess_gc_divisor'] = 100; /* leave this at 100 (100%) */
$config['sess_gc_maxlifetime'] = 24*60*60; /* the session will be closed unless action is taken in this many seconds */

$config['sess_hostname'] = 'localhost';
$config['sess_port'] = 27017;
$config['sess_auth'] = FALSE;
$config['sess_username'] = '';
$config['sess_password'] = '';
$config['sess_database'] = 'heat';
$config['sess_replicaset'] = FALSE;
$config['sess_autoindex'] = TRUE;

/* record lock settings */
$config['sess_lock_timeout'] = 5000;
$config['sess_lock_retryinterval'] = 100;

$config['cookie_prefix']	= 'APP_';
$config['cookie_domain']	= $_SERVER['SERVER_NAME'];
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;
