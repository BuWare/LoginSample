<?php

/**
 * autoload
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2015/07/23
 */
function autoloader($name)
{
	$arrToken = explode('\\', $name);
	$arrToken[0] = '/classes';
	$filename = BASE_DIR . implode("/", $arrToken) . '.class.php';

	if (file_exists($filename)) {
		require_once($filename);
	}
}
