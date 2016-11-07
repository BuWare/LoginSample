<?php

namespace MyApp\controller;

use \MyApp\model\UserModel;
use \MyApp\common\Db;
use \MyApp\common\InvalidErrorException;
use \MyApp\common\ExceptionCode;
use \MyApp\common\Log;
use \MyApp\common\Mail;
use \MyApp\common\Csrf;

/**
 * LoginController
 */
class LoginController
{

	/**
	 * ログイン成功時の遷移先
	 */
	const TARGET_PAGE = '/dashboard.php';

	/**
	 * セッションに保存する名前
	 */
	const LOGINUSER = 'loginUserModel';

	/**
	 * ログインエラー回数
	 */
	private static $loginFailureCount = 0;

	/**
	 * メールアドレスとパスワードでログインする
	 * @return void
	 */
	static public function login()
	{
		// POSTされていないときは、処理を中断する
		if (!filter_input_array(INPUT_POST)) {
			return;
		}

		// CSRFチェック
		Csrf::check();

		//フォームからの値を受け取る
		$email = filter_input(INPUT_POST, 'email');
		$password = filter_input(INPUT_POST, 'password');

		// いずれかが空文字の場合、例外
		if ('' == $email || '' == $password) {
			throw new InvalidErrorException(ExceptionCode::ARGUMENT_REQUIRED);
		}

		//トランザクションを開始する
		Db::transaction();

		//	email から ユーザーモデル を取得する
		$objUserModel = new UserModel();
		$objUserModel->getModelByEmail($email);

		//	ロックされたアカウントかどうかをチェックする
		if ($objUserModel->isAccountLock()) {

			// コミット（ロールバックでも構わない。トランザクションを残さないため。）
			Db::commit();

			//	ロックされている
			throw new InvalidErrorException(ExceptionCode::INVALID_LOCK);
		}

		//パスワードチェック
		if (!$objUserModel->checkPassword($password)) {

			// ログイン失敗記録
			$objUserModel->loginFailureIncrement();

			// コミット（失敗回数を書き込むので）
			Db::commit();

			// アカウントロック通知(Mail)
			self::noticeAccountLockForMail($objUserModel);

			// ログインに失敗しました。
			throw new InvalidErrorException(ExceptionCode::INVALID_LOGIN_FAIL);
		}

		//	ログイン失敗をリセット
		$objUserModel->loginFailureReset();

		setcookie('login_failure_count', 0, time() + 60 * 30, '/');

		//コミット
		Db::commit();

		//セッション固定攻撃対策
		session_regenerate_id(true);

		//セッションに保存
		$_SESSION[self::LOGINUSER] = $objUserModel;

		//ページ遷移
		header(sprintf("location: %s", self::TARGET_PAGE));
	}

	/**
	 * アカウントロック・メール通知
	 * @param UserModel $objUserModel
	 * @return void
	 */
	private static function noticeAccountLockForMail(UserModel $objUserModel)
	{
		// 規定回数以内のとき、なにもしない。
		if (UserModel::LOCK_COUNT > $objUserModel->getLoginFailureCount()) {
			return;
		}

		// メール通知
		$strRecipient = $objUserModel->getEmail();
		$strSubject = 'アカウントをロックしました。';
		$strBody = 'とりあえず空';
		Mail::send($strRecipient, $strSubject, $strBody);

		throw new InvalidErrorException(ExceptionCode::INVALID_LOCK);
	}

	/**
	 * ログインしているかどうかチェックする
	 * @return void
	 */
	static public function checkLogin()
	{
		$objUserModel = (isset($_SESSION[self::LOGINUSER])) ?
			$_SESSION[self::LOGINUSER] :
			null;

		// ログイン成功
		if (is_object($objUserModel) && 0 < $objUserModel->getUserId()) {
			$strMessage = 'ログイン成功';
			Log::write($strMessage);
			return;
		}

		// ログイン失敗
		header('Location: /');
	}

	/**
	 * パスワードを変更する
	 * @param UserModel $objUserModel
	 * @return boolean
	 */
	static public function changePassword(UserModel $objUserModel)
	{
		// POSTされていないときは、処理を中断する
		if (!filter_input_array(INPUT_POST)) {
			return;
		}

		// CSRFチェック
		Csrf::check();

		$oldPassword = filter_input(INPUT_POST, 'old_password');
		$newPassword = filter_input(INPUT_POST, 'new_password');
		$cnfPassword = filter_input(INPUT_POST, 'cnf_password');

		// 新しいパスワードと確認用パスワードの一致確認
		if ($newPassword !== $cnfPassword) {
			return;
		}

		// 古いパスワードがあっているか
		if (!$objUserModel->checkPassword($oldPassword)) {
			return;
		}

		// トランザクション・スタート
		Db::transaction();

		// 新しいパスワードに変更する
		$hashPassword = password_hash($newPassword, PASSWORD_DEFAULT);
		$objUserModel->setPassword($hashPassword)
			->save();

		// コミット
		Db::commit();

		return true;
	}

	/**
	 * トークンからユーザーモデルを取得し、ロック中かどうかを判定する
	 * @return boolean
	 */
	public static function isAccountLock()
	{
		$token = filter_input(INPUT_GET, 'token');
		$objUserModel = new UserModel();
		$objUserModel->getModelByToken($token);
		return $objUserModel->isAccountLock();
	}

	/**
	 * ロックを解除する
	 * @return boolean
	 */
	public static function unlock()
	{
		if (null == filter_input_array(INPUT_POST)) {
			return;
		}

		// CSRFチェック
		Csrf::check();

		$token = filter_input(INPUT_GET, 'token');

		Db::transaction();
		$objUserModel = new UserModel();
		$objUserModel->getModelByToken($token);
		$objUserModel->setLoginFailureCount(0)
			->setLoginFailureDatetime(NULL)
			->setToken('')
			->save();
		DB::commit();

		return true;
	}

	/**
	 * ログイン中のユーザーモデルを取得する
	 * @return UserModel
	 */
	static public function getLoginUser()
	{
		return $_SESSION[self::LOGINUSER];
	}

	/**
	 * ログアウトする
	 * @return void
	 */
	static public function logout()
	{
		$_SESSION = [];
		session_destroy();
		header('Location: /');
	}

}
