<?php
/*
@Author: William Lindner

Config will initialize a set of global variables translated from the superglobals for our internal
uses. It is advisable only to use the global variables and not expand to further mem space.

Additional global variables are initialized and setup for the same use as above.
 */
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
define('FILE', $_SERVER['PHP_SELF']);
define('FILE_PATH', $_SERVER['SCRIPT_FILENAME']);
define('METHOD', $_SERVER['REQUEST_METHOD']);
define('AGENT', $_SERVER['HTTP_USER_AGENT']);
define('LANG', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
define('WHERE_AM_I', $_SERVER['SERVER_NAME']);
define('HTTPS_PATH', 'https://' . WHERE_AM_I);

define('ASSET_PATH', ROOT_PATH . '/../assets');
define('CONFIG_PATH', ASSET_PATH . '/config');
$split_path = array_values(array_diff(explode("/", FILE_PATH), explode("/", ROOT_PATH)))[0];
$split_path = strpos($split_path, '.php') ? 'home' : $split_path;
define('HUB', $split_path);
unset($split_path);
define('DISPLAY_ERRORS', 1);

// load the class framework for the site
spl_autoload_register(function ($class) {
 include ASSET_PATH . '/classes/' . $class . '.php';
});

/* Force configuration of timezones to CT */
ini_set('date.timezone', 'America/Chicago');
date_default_timezone_set('America/Chicago');

/*
Additional globals will be setup in Server.php:
ENVIRONMENT
COOKIE_NAME

A global error and exception handler is established.
 */

Server::setup();
$session = Session::start();

if (isset($_GET['logout'])) {
 if ($_GET['logout']) {
  if ($session->destroy()) {
   include "$ac_path/templates/logout.tpl";
   exit;
  } else {
   exit("There was an error processing your request...");
  }

 }
}

if (ENVIRONMENT !== 'local') {
 $empty = 0;
} else {
 Session::localSetup();
}

Authentication::verify();