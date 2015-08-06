<?php


namespace likefifa\components\extensions\MailerHistory;

use CActiveRecordBehavior;
use CEvent;
use CMap;
use CModelEvent;
use LfMaster;
use LfSalon;
use Yii;

/**
 * Отправляет событийные письма мастерам
 *
 * @property LfMaster $owner
 *
 * @package likefifa\components\extensions
 */
class MasterStatusMailer extends CActiveRecordBehavior
{
	private $isPublishedOld = null;
	public $requireAttributes = [
		'specializations'      => 'Свою специализацию',
		'photo'                  => 'Фотографию',
		'experience'             => 'Ваш опыт работы',
		'underground_station_id' => 'Ближайшую к Вам станцию метро',
		'add_street'             => 'Ваш точный адрес приема',
		'prices'                 => 'Стоимость Ваших услуг',
		'educations'             => 'Ваше образование',
		'works'                  => 'Фотографии Ваших работ',
		'hrs_wd_from'            => 'Ваш график работы',
	];

	/**
	 * Событие публикации мастера. Отправляет или успешное письмо, или о недозаполненном профиле
	 *
	 * @param bool $checkEmpty проверка на незаполненные поля
	 *
	 * @return boolean
	 */
	public function sendPublishMail($checkEmpty = false)
	{
		// Если нужно проверить на незаполненные поля и проверки раньше не было
		$emptyAttributes = [];
		if ($checkEmpty == true && !$this->checkIsSend(MailerHistory::TYPE_EMPTY_FIELDS)) {
			$emptyAttributes = $this->getRequireErrors(['phone_cell' => 'Номер Вашего мобильного телефона']);
		}

		$type = MailerHistory::TYPE_PUBLISH;
		$mailer = Yii::app()->mailer;
		$templateName = 'Публикация мастера';
		$templateContent = $mailer->prepareTemplateContent(
			[
				'userName'    => $this->owner->getFullName(),
				'profileLink' => $this->owner->getProfileUrl(true),
			]
		);
		$message = [
			'to'                => [
				['email' => $this->owner->email],
			],
			'global_merge_vars' => $mailer->prepareTemplateContent(
				[
					'profileLink' => $this->owner->getProfileUrl(true),
				]
			)
		];

		// Если не заполнены все поля
		if (!empty($emptyAttributes)) {
			$type = MailerHistory::TYPE_EMPTY_FIELDS;
			$errorsHtml = [];
			foreach ($emptyAttributes as $text) {
				$errorsHtml[] = '<li>' . $text . '</li>';
			}

			$templateName = 'Публикация мастера, незаполненные поля';
			$templateContent = CMap::mergeArray(
				$templateContent,
				$mailer->prepareTemplateContent(
					[
						'errors' => implode('', $errorsHtml)
					]
				)
			);
		}

		// Если была проверка на пустые аттрибуты и их нет - пропускаем
		if ($checkEmpty && empty($emptyAttributes)) {
			return false;
		}
		$r = $mailer->sendTemplate($templateName, $templateContent, $message);
		if ($r && $r[0]['reject_reason'] == null) {
			$this->saveEvent($type);
			return true;
		}

		return false;
	}

	/**
	 * Отправляет письмо об отрицательном балансе
	 *
	 * @return bool
	 */
	public function sendNegativeBalance()
	{
		$mailer = Yii::app()->mailer;
		$templateName = 'Отрицательный баланс мастера';
		$templateContent = $mailer->prepareTemplateContent(
			[
				'userName'    => $this->owner->getFullName(),
				'profileLink' => $this->owner->getProfileUrl(true),
			]
		);
		$message = [
			'to'                => [
				['email' => $this->owner->email],
			],
			'global_merge_vars' => $mailer->prepareTemplateContent(
				[
					'profileLink' => $this->owner->getProfileUrl(true),
				]
			)
		];
		$r = $mailer->sendTemplate($templateName, $templateContent, $message);
		if ($r && $r[0]['reject_reason'] == null) {
			$this->saveEvent(MailerHistory::TYPE_NEGATIVE_BALANCE);
			return true;
		}

		return false;
	}

	/**
	 * Отправляет письмо о пустом профиле
	 *
	 * @return bool
	 */
	public function sendEmptyProfile()
	{
		$emptyAttributes = $this->getRequireErrors();
		if (count($emptyAttributes) < count($this->requireAttributes)) {
			return false;
		}

		$mailer = Yii::app()->mailer;
		$templateName = 'Не заполненная анкета';
		$templateContent = $mailer->prepareTemplateContent(
			[
				'userName'    => $this->owner->getFullName(),
			]
		);
		$message = [
			'to'                => [
				['email' => $this->owner->email],
			],
		];
		$r = $mailer->sendTemplate($templateName, $templateContent, $message);
		if ($r && $r[0]['reject_reason'] == null) {
			$this->saveEvent(MailerHistory::TYPE_EMPTY_PROFILE);
			return true;
		}

		return false;
	}

	/**
	 * @param CEvent $event
	 */
	public function afterFind($event)
	{
		$this->isPublishedOld = $this->owner->is_published;

		parent::afterFind($event);
	}

	/**
	 * @param CModelEvent $event
	 */
	public function afterSave($event)
	{
		// Письмо при публикации мастера
		if ($this->isPublishedOld != $this->owner->is_published &&
			$this->owner->is_published == 1 &&
			!$this->checkIsSend(MailerHistory::TYPE_PUBLISH)
		) {
			$this->sendPublishMail();
		}
		parent::afterSave($event);
	}

	/**
	 * Возвращает незаполненные аттрибуты
	 *
	 * @param array $additional
	 *
	 * @return array
	 */
	private function getRequireErrors($additional = null)
	{
		$errorsList = $this->requireAttributes;
		if ($additional != null) {
			$errorsList = CMap::mergeArray($errorsList, $additional);
		}

		$emptyAttributes = [];
		foreach ($errorsList as $attr => $e) {
			if ($this->owner->hasAttribute($attr) && empty($this->owner->$attr)) {
				$emptyAttributes[$attr] = $e;
			}

			if (array_key_exists($attr, $this->owner->relations()) && count($this->owner->$attr) == 0) {
				$emptyAttributes[$attr] = $e;
			}
		}

		return $emptyAttributes;
	}

	/**
	 * Проверяет, было ли нужное письмо отправлено ранее
	 *
	 * @param $type
	 *
	 * @return boolean
	 */
	private function checkIsSend($type)
	{
		return MailerHistory::model()->countByAttributes(
			[
				'master_id' => $this->owner instanceof LfMaster ? $this->owner->id : null,
				'salon_id'  => $this->owner instanceof LfSalon ? $this->owner->id : null,
				'type'      => $type,
			]
		) > 0;
	}

	/**
	 * Сохраняет событие отправки письма
	 *
	 * @param $type
	 */
	private function saveEvent($type)
	{
		$model = new MailerHistory;
		$model->master_id = $this->owner instanceof LfMaster ? $this->owner->id : null;
		$model->salon_id = $this->owner instanceof LfSalon ? $this->owner->id : null;
		$model->type = $type;
		$model->created = date('Y-m-d H:i:s');
		$model->save(false);
	}
}