<?php

namespace MyApp\model;

use MyApp\dao\UserDao;

/**
 * UserModel
 */
final class UserModel extends UserModelBase
{

	/**
	 * アカウントをロックするログイン失敗回数
	 */
	const LOCK_COUNT = 3;

	/**
	 * アカウントをロックする時間（分）
	 */
	const LOCK_MINUTE = 15;

	/**
	 * メールアドレスからユーザーを検索する
	 * @param string $strEmail
	 * @return \MyApp\model\UserModel
	 */
	public function getModelByEmail($strEmail)
	{
		$dao = UserDao::getDaoFromEmail($strEmail);
		return (isset($dao[0])) ? $this->setProperty(reset($dao)) : null;
	}

	/**
	 * トークンからユーザーを検索する
	 * @param string $token
	 * @return \MyApp\model\UserModel
	 */
	public function getModelByToken($token)
	{
		$dao = UserDao::getDaoFromToken($token);
		return (isset($dao[0])) ? $this->setProperty(reset($dao)) : null;
	}

	/**
	 * パスワードが一致しているかどうかを判定する
	 * @param type $password
	 * @return bool
	 */
	public function checkPassword($password)
	{
		$hash = $this->getPassword();
		return password_verify($password, $hash);
	}

	/**
	 * ログイン失敗をリセットする
	 * １以上のときに０にする
	 * @return bool
	 */
	public function loginFailureReset()
	{
		$count = $this->getLoginFailureCount();
		if (0 < $count) {
			$this->setLoginFailureCount(0)
				->setLoginFailureDatetime(null);
			return $this->save();
		}
		//変更の必要がない
		return true;
	}

	/**
	 * ログイン失敗をインクリメントする
	 * 指定回数（self::LOCK_COUNT）に満たないときのみ＋１
	 * @return bool
	 */
	public function loginFailureIncrement()
	{
		$count = $this->getLoginFailureCount();
		if (self::LOCK_COUNT > $count) {
			$now = (new \DateTime())->format('Y-m-d H:i:s');
			$this->setLoginFailureCount(1 + $count)
				->setLoginFailureDatetime($now);

			if (self::LOCK_COUNT === 1 + $count) {
				$token = sha1(uniqid());
				$this->setToken($token);
			}

			return $this->save();
		}

		//ログイン失敗が設定以上のとき
		return true;
	}

	/**
	 * アカウントがロックされているかどうかを判定する
	 * @return bool ロックされていたら true
	 */
	public function isAccountLock()
	{
		$count = $this->getLoginFailureCount();
		$datetime = $this->getLoginFailureDatetime();

		$lastFailureDatetime = new \DateTime($datetime);
		$interval = new \DateInterval(
			sprintf('PT%dM', self::LOCK_MINUTE)
		);
		$lastFailureDatetime->add($interval);

		//設定時間以内で、かつ設定回数以上の失敗を記録しているとき
		if ($lastFailureDatetime > new \DateTime() && self::LOCK_COUNT <= $count) {
			return true;
		}
		return false;
	}

}
