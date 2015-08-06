<?php
namespace dfs\docdoc\models;

/**
 * This is the model class for table "MailQuery".
 *
 * The followings are the available columns in table 'MailQuery':
 *
 * @property integer $idMail
 * @property string $emailTo
 * @property string $subj
 * @property string $message
 * @property string $resendCount
 * @property string $status
 * @property string $crDate
 * @property string $sendDate
 * @property string $priority
 * @property string $reply
 *
 * @method MailQueryModel find
 * @method MailQueryModel[] findAll
 * @method MailQueryModel findByPk
 * @method MailQueryModel findByAttributes
 */
class MailQueryModel extends \CActiveRecord
{

	// Приоритет отправки сообщения по умолчанию
	const PRIORITY_DEFAULT = 5;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return MailQueryModel the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mailQuery';
	}

	/**
	 * первичный ключ
	 *
	 * @return string the associated database table name
	 */
	public function primaryKey()
	{
		return 'idMail';
	}

	/**
	 * Отправка письма при изменении заявки
	 *
	 * @param \CEvent $event
	 */
	public function onChangeRequestMail(\CEvent $event)
	{
		/**
		 * @var RequestModel $request
		 */
		$request = $event->sender;

		if (!$request->clinic) {
			return;
		}

		if ($request->getScenario() === RequestModel::SCENARIO_LK_CLINIC) {
			$messages = [];

			if ($request->isChanged('billing_status')) {
				switch ($request->billing_status) {
					case RequestModel::BILLING_STATUS_YES:
						$this->subj = 'Клиника ' . $request->clinic->short_name . ' подтвердила заявку #' . $request->req_id;
						$messages[] = $this->subj;
						break;

					case RequestModel::BILLING_STATUS_REFUSED:
						$this->subj = 'Клиника ' . $request->clinic->short_name . ' отклонила заявку #' . $request->req_id;
						$messages[] = $this->subj;
						break;
				}
			}

			if ($request->isChanged('date_admission')) {
				$messages[] = "Клиника изменила дату приема на "  . date("d.m.Y H:i", $request->date_admission) . ".";
			}

			if (count($messages)) {
				$url = "https://" . \Yii::app()->params['hosts']['back'] . "/request/request.htm?id={$request->req_id}";
				$this->message = implode("<br>", $messages) . "<br><a href=\"{$url}\">{$url}</a>";
				if (!$this->subj) {
					$this->subj = 'Клиника ' . $request->clinic->short_name . ' изменила заявку #' . $request->req_id;
				}
				$this->emailTo = \Yii::app()->params['email']['account'];
				$this->save();
			}
		}
		elseif ($request->req_type == RequestModel::TYPE_ONLINE_RECORD && $request->isNew()) {
			$emails = $request->clinic->getNotifyEmails();
			$emails[] = \Yii::app()->params['email']['support'];

			$authToken = null;
			$clinicAdmin = $request->clinic->getAdmin();

			if ($clinicAdmin) {
				$authToken = new AuthTokenModel();
				$authToken->generateToken(AuthTokenModel::TYPE_LK, $clinicAdmin->clinic_admin_id);
				$authToken->save();
			}

			$this->createMail('clinic_new_online_request', $emails, [
				'request' => $request,
				'authToken' => $authToken,
			]);
		}
	}

	/**
	 * Перед сохранением
	 * @return bool
	 */
	public function beforeSave()
	{
		if (empty($this->crDate)) {
			$this->crDate = date('Y-m-d H:i:s');
		}

		if (empty($this->priority)) {
			$this->priority = self::PRIORITY_DEFAULT;
		}

		return true;
	}

	/**
	 * Письмо о изменении пароля
	 *
	 * @param PartnerModel $partner
	 * @param string $password
	 *
	 * @return bool
	 */
	public function sendMailPartnerChangePassword(PartnerModel $partner, $password)
	{
		$message = '<div>Добрый день!</div>';
		$message .= '<div>Вы поменяли пароль для входа в личный кабинет. Новый пароль для Вашего аккаунта: ';
		$message .= "<strong>$password</strong> ";
		$message .= '<div>Пожалуйста, сохраняйте, этот пароль, так как DocDoc.Ru не хранит пароли в открытом виде</div></div>';
		$message .= '<div><em>Служба автоматических уведомлений<em></div>';

		$mail = new self();
		$mail->message = $message;
		$mail->subj = '[docdoc.ru] Изменение пароля в партнерском кабинете';
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = $partner->contact_email;

		return $mail->save();
	}

	/**
	 * Письмо о востановлении пароля
	 *
	 * @param string $email
	 * @param string $password
	 *
	 * @return bool
	 */
	public function sendMailPartnerRecovery($email, $password)
	{
		$message = "Ваш новый временный пароль: <b>$password</b><br>";
		$message .= "Изменить пароль Вы сможете в личном кабинете в разделе 'Настройки'";

		$mail = new self();
		$mail->message = $message;
		$mail->subj = '[docdoc.ru] Восстановление пароля';
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = $email;

		return $mail->save();
	}

	/**
	 * Письмо с вопросом от партнера
	 *
	 * @param PartnerModel $partner
	 * @param string $questionText
	 *
	 * @return bool
	 */
	public function sendMailPartnerQuestion(PartnerModel $partner, $questionText)
	{
		$message = "<p>{$partner->contact_name}<br>";
		$message .= "Администратор: {$partner->contact_email}<br>";
		$message .= "ID партнера: {$partner->id}, " .
			'IP: ' . $_SERVER['REMOTE_ADDR'] . '/' . (isset($_SERVER['X-Real-IP']) ? $_SERVER['X-Real-IP'] : '') .
			'</p>';
		$message .= "<p>Текст сообщения: " . $questionText . "</p>";

		$mail = new self();
		$mail->message = $message;
		$mail->subj = "[docdoc.ru] Вопрос от партнера {$partner->contact_name} (#{$partner->id}, {$partner->name})";
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = \Yii::app()->params['email']['partner'];
		$mail->reply = $partner->contact_email;

		return $mail->save();
	}


	/**
	 * Письмо о изменении пароля клиники
	 *
	 * @param ClinicAdminModel $admin
	 * @param string $password
	 *
	 * @return bool
	 */
	public function sendMailClinicChangePassword(ClinicAdminModel $admin, $password)
	{
		$message = '<div>Добрый день!</div>';
		$message .= '<div>Вы поменяли пароль для входа в личный кабинет. Новый пароль для Вашего аккаунта: ';
		$message .= "<strong>$password</strong>";
		$message .= '</div>';
		$message .= '<div><em>Служба автоматических уведомлений<em></div>';

		$mail = new self();
		$mail->message = $message;
		$mail->subj = '[docdoc.ru] Изменение пароля';
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = $admin->email;

		return $mail->save();
	}

	/**
	 * Письмо о востановлении пароля
	 *
	 * @param string $email
	 * @param string $password
	 *
	 * @return bool
	 */
	public function sendMailClinicRecovery($email, $password)
	{
		$message = "Ваш новый временный пароль: <b>$password</b><br>";
		$message .= "Изменить пароль Вы сможете в личном кабинете в разделе 'Настройки'";

		$mail = new self();
		$mail->message = $message;
		$mail->subj = '[docdoc.ru] Восстановление пароля';
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = $email;

		return $mail->save();
	}

	/**
	 * Письмо с вопросом от клиники
	 *
	 * @param ClinicModel      $clinic
	 * @param ClinicAdminModel $admin
	 * @param string           $questionText
	 *
	 * @return bool
	 */
	public function sendMailClinicQuestion(ClinicModel $clinic, ClinicAdminModel $admin, $questionText)
	{
		$link = "https://" . \Yii::app()->params['hosts']['back'] . "/clinic/index.htm?id={$clinic->id}";

		$message = "<p>{$clinic->name}<br>";
		$message .=	"Администратор: {$admin->getFullName()} ({$admin->email})<br>";
		$message .= "ID клиники: {$clinic->id}, IP: " . $_SERVER['REMOTE_ADDR'] . "</p>";
		$message .= "<p>Текст сообщения: $questionText</p>";
		$message .= "<p><a href='$link'>Посмотреть анкету клиники</a></p>";

		$mail = new self();
		$mail->message = $message;
		$mail->subj = "[docdoc.ru] Вопрос от клиники {$clinic->name} (#{$clinic->id})";
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = \Yii::app()->params['email']['account'];

		return $mail->save();
	}

	/**
	 * Отправить письмо об внесенных клиникой изменениях
	 *
	 * @param ClinicModel $clinic
	 * @param DoctorModel $doctor
	 *
	 * @return bool
	 */
	public function sendEmailChangeDoctor(ClinicModel $clinic, DoctorModel $doctor)
	{
		$link = 'https://' . \Yii::app()->params['hosts']['back'] . '/doctor/index.htm?id=' . $doctor->id;

		$message = "<div>Клиника ({$clinic->name}) изменила параметры врача <a href=\"$link\">{$doctor->name}</a></div>";
		$message .= '<div><em>Служба автоматических уведомлений<em></div>';

		$mail = new self();
		$mail->message = $message;
		$mail->subj = "[docdoc.ru] Клиника изменила параметры врача ({$doctor->name})";
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = \Yii::app()->params['email']['content'];

		return $mail->save();
	}

	/**
	 * Отправить письмо об внесенных клиникой изменениях
	 *
	 * @param ClinicModel $clinic
	 * @param DiagnosticClinicModel $dc
	 *
	 * @return bool
	 */
	public function sendEmailChangeClinicDiagnostic(ClinicModel $clinic, DiagnosticClinicModel $dc)
	{
		$link = 'https://' . \Yii::app()->params['hosts']['back'] . '/clinic/index.htm?id=' . $dc->clinic_id;

		$diagnosticName = $dc->diagnostic->getFullName();

		$message = "<div>Клиника ({$clinic->name}) изменила диагностику <a href=\"$link\">{$diagnosticName}</a></div>";
		$message .= '<div><em>Служба автоматических уведомлений<em></div>';

		$mail = new self();
		$mail->message = $message;
		$mail->subj = "[docdoc.ru] Клиника изменила параметры диагностики ({$diagnosticName})";
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = \Yii::app()->params['email']['content'];

		return $mail->save();
	}

	/**
	 * Уведомление о том, что достигнут лимит записей по контракту клиники
	 *
	 * @param ClinicRequestLimitModel $requestLimit
	 *
	 * @return bool
	 */
	public function sendMailLimitRequests($requestLimit)
	{
		$clinic = $requestLimit->clinicContract->clinic;

		$message = "<p>{$clinic->name}<br>";
		$message .= "ID клиники: {$clinic->id}<br>";
		$message .= "Тариф: {$requestLimit->clinicContract->contract->title}</p>";
		$message .= "Текущий лимит: {$requestLimit->limit}</p>";

		$mail = new self();
		$mail->message = $message;
		$mail->subj = "[docdoc.ru] Достигнут лимит по записям в клинику {$clinic->name} (#{$clinic->id})";
		$mail->priority = self::PRIORITY_DEFAULT;
		$mail->emailTo = \Yii::app()->params['email']['account'];

		return $mail->save();
	}


	/**
	 * Постановка письма в очередь
	 *
	 * @param string $to куда слать
	 * @param string $subject тема
	 * @param string $message тело
	 *
	 * @return boolean
	 */
	public function addMessage($to, $subject, $message)
	{
		$mail = new self();
		$mail->emailTo = $to;
		$mail->subj = $subject;
		$mail->message = $message;

		return $mail->save();
	}

	/**
	 * Создание письма по шаблону
	 *
	 * @param string $template
	 * @param string | array $to
	 * @param array $params
	 *
	 * @return bool
	 */
	public function createMail($template, $to, $params = null)
	{
		$mail = new self();

		$mail->priority = self::PRIORITY_DEFAULT;

		$filename = ROOT_PATH . '/common/views/emails/' . $template . '.php';
		if (!file_exists($filename)) {
			return false;
		}

		if ($params) {
			extract($params, EXTR_SKIP);
		}

		ob_start();
		require($filename);
		$mail->message = ob_get_clean();

		$result = false;

		if (is_array($to)) {
			foreach ($to as $emailTo) {
				$mailCopy = clone $mail;
				$mailCopy->emailTo = $emailTo;
				$result = $mailCopy->save() || $result;
			}
		} else {
			$mail->emailTo = $to;
			$result = $mail->save();
		}

		return $result;
	}

	/**
	 * Отправить письмо адресату
	 *
	 * @return int
	 */
	public function sendMail()
	{
		$mailer = \Yii::app()->mailer;

		$message = $mailer->createMessage($this->subj, $this->message, 'text/html', 'utf-8');

		$message->setFrom(\Yii::app()->params['email']['from'], 'Docdoc.ru');
		$message->setTo($this->emailTo);

		if ($this->reply) {
			$message->setReplyTo($this->reply);
		}

		return $mailer->send($message);
	}
}
