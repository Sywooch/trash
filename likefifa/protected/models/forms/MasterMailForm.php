<?php


namespace likefifa\models\forms;

use CFormModel;
use LfMaster;
use likefifa\components\LfMandrill;
use Yii;

/**
 * Модель для отправки письма мастеру из БО
 *
 * Class MasterMailForm
 *
 * @package likefifa\models\forms
 */
class MasterMailForm extends CFormModel
{
	/**
	 * @var LfMaster
	 */
	public $master;

	/**
	 * @var string
	 */
	public $text;

	/**
	 * @var bool
	 */
	public $success = false;

	/**
	 * @var string
	 */
	public $subject;

	public function rules()
	{
		return [
			['text, subject', 'required'],
			['master', 'safe']
		];
	}

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'subject' => 'Тема сообщения',
			'text'    => 'Текст сообщения',
		];
	}

	public function send() {
		/** @var LfMandrill $mailer */
		$mailer = Yii::app()->mailer;
		$message = [
			'to' => [
				['email' => $this->master->email],
			],
			'subject' => $this->subject,
			'text' => $this->text,
			'from_email' => 'fifa@likefifa.ru',
			'from_name' => 'Likefifa',
		];
		return $mailer->messages->send($message);
	}
}