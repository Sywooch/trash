<?php

use dfs\docdoc\models\RequestModel;


/**
 * Class user
 */
class user
{
	public $idUser;
	public $login;
	public $userFirstName;
	public $userLastName;
	public $email;
	public $phone;
	public $skype;
	public $status;
	public $operator_stream;
	public $userRights = [];
	public $userCodeRights = [];
	public $formName;

	/*	Определяем начальное состояние самого себя	*/
	function __construct()
	{
		$user = Yii::app()->session['user'];

		if ($user && !empty($user->idUser)) {
			$this->getUserById($user->idUser);
		}
	}

	/*	Устанавливает состояние  */
	function setUser()
	{
		Yii::app()->session['user'] = $this;
	}

	/**
	 * Авторизация пользователя. После авторизации установка данных в сессию
	 *
	 * @param string $login Логин
	 * @param string $pass  Пароль
	 */
	function logIn($login = '', $pass = '')
	{
		$login  = trim(strip_tags($login));
		$passwd = trim(strip_tags($pass));

		$session = Yii::app()->session;

		$session['login'] = $login;

		if (!empty($login) && !empty($passwd)) {
			try {
				$sql    = "
					SELECT
						user_id AS userId,
						status
					FROM `user`
					WHERE
						user_login=:login
						AND user_password=:passwd
				";
				$result = query($sql,[
					':login' =>  $login,
					':passwd'  => md5($passwd),
				]);
				if (!$result) {
					throw new Exception("Ошибка выполнения запроса");
				}

				if (num_rows($result) == 1) {
					$row = fetch_object($result);
					$this->getUserById($row->userId);
					$session['user'] = $this;
					Yii::app()->user->login(new CUserIdentity($login, $passwd));
					//Проверка statusa
					if ($row->status == 'disable') {
						$session['status'] = 'disable';
						throw new Exception("Ваш аккаунт заблокирован");
					}
				} else {
					throw new Exception("Ошибка авторизации. Проверьте логин и пароль");
				}
			} catch (Exception $e) {
				$errorMsg[]           = $e->getMessage();
				$session["errorMsg"] = $errorMsg;

				return;
			}
		} else {
			$errorMsg[]           = "Вы не заполнили поля формы";
			$session["errorMsg"] = $errorMsg;
		}
	}

	/*	Получение объекта по id пользователя	*/
	function getUserById($id)
	{
		$userId = intval($id);

		try {
			$sql = "SELECT
						user_id AS userId,
						user_login AS login,
						user_email AS email,
						status, 
						phone,
						skype,
						operator_stream,
						user_lname AS lastName,
						user_fname AS firstName
					FROM  `user`
					WHERE user_id=" . $userId;
			$result = query($sql);
			if (!$result) {
				throw new Exception("Пользователь не определился");
			}

			if (num_rows($result) == 1) {
				$row                  = fetch_object($result);
				$this->idUser         = $row->userId;
				$this->login          = $row->login;
				$this->userFirstName  = $row->firstName;
				$this->userLastName   = $row->lastName;
				$this->email          = $row->email;
				$this->phone          = $row->phone;
				$this->skype          = $row->skype;
				$this->status         = $row->status;
				$this->operator_stream = $row->operator_stream;
				$this->userRights     = [];
				$this->userCodeRights = [];

				$sql = "	SELECT
									t1.right_id AS rightId, t2.code
								FROM right_4_user t1
								LEFT JOIN  user_right_dict t2 ON (t1.right_id=t2.right_id)
								WHERE t1.user_id=" . $row->userId;
				//echo $sql."<br>";
				$resultAdd = query($sql);
				if (!$resultAdd) {
					throw new Exception("Ошибка определения прав пользователя");
				}

				if (num_rows($resultAdd) > 0) {
					while ($rowAdd = fetch_object($resultAdd)) {
						array_push($this->userRights, $rowAdd->rightId);
						array_push($this->userCodeRights, strtoupper($rowAdd->code));
					}
				}
			}
		} catch (Exception $e) {
			echo($e->getMessage() . "<br>");
		}
	}

	/*	Проверяет залогини пользователь или нет    */
	function checkLoginUser()
	{
		$out = false;

		(isset($this->idUser) && intval($this->idUser) > 0) ? ($out = true) : ($out = false);

		return $out;
	}

	/*	Проверяет права пользователя  */
	function checkRight4user($right)
	{
		$out = false;
		(!empty($right) && in_array($right, $this->userRights)) ? ($out = true) : ($out = false);

		return $out;
	}

	/*	Проверяет права пользователя по code */

	function checkRight4page($rightCode = [], $mode = '')
	{
		global $url;

		if (empty($rightCode) || !$this->checkRight4userByCode($rightCode)) {
			$session = Yii::app()->session;
			$url  = $_SERVER['REQUEST_URI'];
			$session['url'] = $url;
			if (!empty($session['user'])) {
				header("Location: /noRights.htm?mode=" . $mode);
				exit;
			}
			$session['url'] = $url;
			header("Location: /index.htm");
			exit;
		}

		return true;
	}

	/*	Проверяет права пользователя по code и перенаправляет его, если прав нет  */

	function checkRight4userByCode(array $rightCode = [])
	{
		$out = false;
		(!empty($rightCode) && count(array_intersect($rightCode, $this->userCodeRights)) > 0) ? ($out = true) : ($out = false);

		return $out;
	}

	/*	возвращает XML данные пользователя из сессии	*/

	function getUserXML()
	{
		$xml = "";

		if (!empty($this->idUser)) {
			$xml .= "<UserData id='" . $this->idUser . "'>";
			$xml .= "<Login>" . $this->login . "</Login>";
			$xml .= "<FirstName>" . $this->userFirstName . "</FirstName>";
			$xml .= "<LastName>" . $this->userLastName . "</LastName>";
			$xml .= "<Email>" . $this->email . "</Email>";
			$xml .= "<Phone>" . $this->phone . "</Phone>";
			$xml .= "<Skype>" . $this->skype . "</Skype>";
			$xml .= "<Status>" . $this->status . "</Status>";
			$streamTitle = '';
			if ($this->operator_stream == RequestModel::OPERATOR_STREAM_NEW) {
				$streamTitle = 'Новые заявки';
			}
			elseif ($this->operator_stream == RequestModel::OPERATOR_STREAM_CALL_LATER) {
				$streamTitle = 'Заявки на перезвон';
			}
			$xml .= "<OperatorStream title='" . $streamTitle . "'>" . $this->operator_stream . "</OperatorStream>";
			if (!empty($this->userRights) && !empty($this->userCodeRights)) {
				$xml .= "<Rights>";
				$code = $this->userCodeRights;
				foreach ($this->userRights as $right => $data) {
					$xml .= "<Right id=\"" . $data . "\">" . strtoupper($code[$right]) . "</Right>";
				}
				$xml .= "</Rights>";
			}
			$xml .= "</UserData>";
		}

		return $xml;
	}
}
