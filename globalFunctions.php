<?php

/**
 * globalFunctions.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/10/31
 */

/**
 * エラーハンドラ関数
 * @param string $code
 * @param string $message
 * @param string $file
 * @param int $line
 * @return void
 * @throws \ErrorException
 */
function error_handler($code, $message, $file, $line)
{
	if (0 == error_reporting()) {
		return;
	}
	throw new \ErrorException($message, 0, $code, $file, $line);
}

/**
 * 例外ハンドラ関数
 * @param \exception $ex
 * @return void
 */
function exception_handler($ex)
{
	$message = sprintf('%s %s in %s %s'
		, $ex->getCode()
		, $ex->getMessage()
		, $ex->getFile()
		, $ex->getLine()
	);
	echo $message;
	$strMessage = sprintf("Exception: [%d]%s", $ex->getCode(), $message);
	\MyApp\common\Log::write($strMessage, \LoggerLevel::FATAL);
	return;
}

/**
 * シャットダウン時に実行する関数
 */
function shutdownfunction()
{
	$strMessage = sprintf('**** END OF %s ****'
		, filter_input(INPUT_SERVER, 'SCRIPT_NAME')
	);
	\MyApp\common\Log::write($strMessage);
}
