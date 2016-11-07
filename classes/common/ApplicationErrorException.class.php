<?php

namespace MyApp\common;

/**
 * ApplicationErrorException.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2015/07/24
 */
class ApplicationErrorException extends \Exception
{

	/**
	 * コンストラクタ
	 * @param type $code
	 * @param \Exception $previous
	 */
	public function __construct($code, \Exception $previous = null)
	{
		$message = ExceptionCode::getMessage($code);
		self::writeLog($message);
		parent::__construct('アプリケーションエラーが発生しました。', $code, $previous);
	}

	/**
	 * ログを書く
	 * @param type $message
	 */
	static private function writeLog($message)
	{
		Log::write($message, \LoggerLevel::WARN);
	}

}
