<?php

/**
 * common
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2015/07/23
 */
ini_set('include_path'
	, ini_get('include_path')
);

if (function_exists('opcache_reset')) {
	opcache_reset();
}

require_once 'config.php';
define('MODE', DEVELOPPING);

ini_set('display_errors', (MODE === DEVELOPPING));
error_reporting(E_ALL);

require_once BASE_DIR . '/autoload.php';
require_once BASE_DIR . '/vendor/autoload.php';
spl_autoload_register('autoloader');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

require_once 'globalFunctions.php';
set_error_handler("\\error_handler");
set_exception_handler('\\exception_handler');
register_shutdown_function('\\shutdownfunction');
