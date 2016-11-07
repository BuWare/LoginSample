<?php

namespace MyApp\common;

/**
 * SystemErrorException.php
 *
 * @since 2015/07/24
 */
class SystemErrorException extends \Exception
{

	/**
	 * コンストラクタ
	 * @param type $code
	 */
	public function __construct($code, array $args = [])
	{
		$message = ExceptionCode::getMessage($code);
		self::writeLog(vsprintf($message, $args));
		self::sendMail(vsprintf($message, $args));
		parent::__construct('システムエラーが発生しました。', $code);
	}

	/**
	 * 管理者へメール
	 * @param type $message
	 */
	static private function sendMail($message)
	{

	}

	/**
	 * ログを書く
	 * @param type $message
	 */
	static private function writeLog($message)
	{
		Log::write($message, \LoggerLevel::ERROR);
	}

}
