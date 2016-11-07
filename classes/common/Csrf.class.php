<?php

namespace MyApp\common;

/**
 * Csrf.class.php
 */
class Csrf
{

	/**
	 * トークン
	 * @var string
	 */
	private static $token = null;

	/**
	 * 初期化
	 */
	private static function init()
	{
		self::$token = sha1(uniqid());
	}

	/**
	 * CSRF用にトークンを生成する
	 * @return string
	 */
	public static function get()
	{
		if (is_null(self::$token)) {
			self::init();
		}
		$_SESSION['csrf_token'] = self::$token;
		return self::$token;
	}

	/**
	 * CSRFをチェックする
	 * @return boolean
	 * @throws ApplicationErrorException
	 */
	public static function check()
	{
		$csrf_token = (isset($_SESSION['csrf_token'])) ? $_SESSION['csrf_token'] : null;
		$_SESSION['csrf_token'] = null;

		if (filter_input(INPUT_POST, 'csrf_token') !== $csrf_token) {
			// 二重送信されたので処理を中断しました。
			throw new InvalidErrorException(ExceptionCode::INVALID_CSRF_ERR);
		}
		return true;
	}

}
