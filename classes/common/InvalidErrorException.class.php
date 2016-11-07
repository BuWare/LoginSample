<?php

namespace MyApp\common;

/**
 * InvalidErrorException.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2015/07/24
 */
class InvalidErrorException extends \Exception
{

	/**
	 * コンストラクタ
	 * @param string $code
	 * @param \Exception $previous
	 */
	public function __construct($code, \Exception $previous = null)
	{
		$message = ExceptionCode::getMessage($code);
		parent::__construct($message, $code, $previous);
	}

}
