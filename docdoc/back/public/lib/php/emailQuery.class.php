<?php
/**
 * emailQuery
 *
 * Выполняет отправку почты, создаёт очередь
 *
 * @version 1.0
 */
class emailQuery
{
	public $id = 0;
	public $crDate;
	public $emailTo;
	public $subj;
	public $message;
	public $status;
	protected $statusArray = ["new", 'sended', 'error', 'deleted', 'canceled'];

	protected function init()
	{
		$this->id            = 0;
		$this->crDate        = "";
		$this->emailTo       = "";
		$this->phoneToDigits = "";
		$this->priority      = 99;
		$this->subj          = "";
		$this->message       = "";
		$this->status        = "new";
	}

	public function __construct($id = 0)
	{
		$id = intval($id);

		if ($id > 0) {
			$this->getMessage($id);
		} else {
			$this->init();
		}
	}

	public function getMessage($id)
	{
		$id = intval($id);

		if ($id <= 0) {
			return false;
		}

		$sql = "
			SELECT
				idMail AS id,
				DATE_FORMAT( crDate,'%d.%m.%Y %H:%i') AS CrDate,
				emailTo,
				message,
				subj,
				status
			FROM `mailQuery`
			WHERE idMail = " . $id;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row           = fetch_object($result);
			$this->id      = $row->id;
			$this->crDate  = $row->CrDate;
			$this->emailTo = $row->emailTo;
			$this->message = $row->message;
			$this->subj    = $row->subj;
			$this->status  = $row->status;
		}
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($newStatus)
	{

		$newStatus = (isset($newStatus)) ? checkField($newStatus, "e", "", false, $this->statusArray) : $this->status;
		if (empty($newStatus)) {
			return false;
		}

		$sql          = "UPDATE mailQuery SET status = '" . $newStatus . "' WHERE idMail = " . $this->id;
		query($sql);
		$this->status = $newStatus;

		return true;
	}

	static function deleteMessage($id)
	{
		$id = intval($id);

		if ($id <= 0) {
			return false;
		}

		$sql    = "DELETE FROM mailQuery WHERE idMail = " . $id;
		query($sql);

		return true;
	}

	static function statusList()
	{
		return SELF::$statusArray;
	}

	/**
	 * Отправляет поступившие письма в очередь
	 *
	 * @param array $params список параметров тела письма
	 *
	 * @return bool|int возвращает результат вставки в базу
	 */
	static function addMessage($params = [])
	{
		$sqlAdd = "";

		$message = $params["message"];
		if (empty($message)) {
			return false;
		}

		$subj = $params["subj"];
		if (empty($subj)) {
			return false;
		}

		$emailTo = $params["emailTo"];
		if (!checkEmail($emailTo)) {
			return false;
		}

		$status = (isset($params["status"])) ? $params["status"] : 'new';
		if (!empty($status)) {
			$sqlAdd .= " status = '" . $status . "', ";
		} else {
			$sqlAdd .= " status = 'new', ";
		}

		$sql = "
			INSERT INTO mailQuery SET
				emailTo = :emailTo,
				subj    = :subj,
				message = :message,
				{$sqlAdd}
				crDate  = NOW()
		";
		query($sql, [':emailTo' => $emailTo, ':subj' => $subj, ':message' => $message,]);
		$id = legacy_insert_id();

		if (intval($id) > 0) {
			return $id;
		}

		return false;
	}

	/**
	 * принимает константу -спиок почтовых ящиков, преобразует в массив и в цикле отправляет письма в очередь
	 *
	 * @param string $emails список ящиков через запятую
	 * @param array  $params массив параметров тела письма
	 */
	static function sendEMails($emails, $params)
	{
		$emails = explode(",", $emails);
		foreach ($emails as $email) {
			$params['emailTo'] = trim($email);
			self::addMessage($params);
		}
	}

	public function addCount($id)
	{
		$id = intval($id);

		if ($id > 0) {
			$sql    = "UPDATE mailQuery SET resendCount = resendCount+1 WHERE idMail = " . intval($id);
			query($sql);
		}
	}

	public function setStatusById($id, $newStatus)
	{
		$id = (isset($id)) ? checkField($id, "i", 0) : 0;
		if (empty($newStatus)) {
			return false;
		}

		if ($id > 0) {
			$sql    = "UPDATE mailQuery SET status = '" . $newStatus . "' WHERE idMail = " . $id;
			query($sql);

			return true;
		}
	}
}
