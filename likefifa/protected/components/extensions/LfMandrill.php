<?php

namespace likefifa\components\extensions;

use CApplicationComponent;
use CException;
use Mandrill;
use Yii;

/**
 * Компонент для работы с mandrill @see http://mandrillapp.com
 *
 * @property \Mandrill_Messages $messages
 *
 * Class LfMandrill
 *
 * @package likefifa\components
 */
class LfMandrill extends CApplicationComponent
{
	/**
	 * @var string
	 */
	public $apikey;

	private $_md;

	public function init()
	{
		$this->_md = new Mandrill($this->apikey);
		parent::init();
	}

	/**
	 * Конвертирует данные шаблона в нужный формат
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function prepareTemplateContent(array $data)
	{
		$content = [];
		foreach ($data as $key => $value) {
			$content[] = [
				'name' => $key,
				'content' => $value,
			];
		}
		return $content;
	}

	/**
	 * @see \Mandrill_Messages::sendTemplate
	 *
	 * @param      $template_name
	 * @param      $template_content
	 * @param      $message
	 * @param bool $async
	 * @param null $ip_pool
	 * @param null $send_at
	 *
	 * @return array
	 */
	public function sendTemplate(
		$template_name,
		$template_content,
		$message,
		$async = false,
		$ip_pool = null,
		$send_at = null
	)
	{
		if ($this->canSend($message['to'])) {
			return $this->messages->sendTemplate($template_name, $template_content, $message, $async, $ip_pool, $send_at);
		}
		return false;
	}

	/**
	 * Проверяет, можно ли отправлять почту данному получателю
	 *
	 * @param string $email адрес получателя
	 *
	 * @return bool
	 */
	private function canSend($email)
	{
		if (YII_DEBUG) {
			if (empty(Yii::app()->params['devEmails'])) {
				return true;
			}

			if (count($email) == 1 && !in_array($email[0]['email'], Yii::app()->params['devEmails'])) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Call a MailChimp functions
	 *
	 * @param string $method
	 * @param array  $params
	 *
	 * @return mixed
	 * @throws CException
	 */
	public function __call($method, $params)
	{
		if (is_object($this->_md) && get_class($this->_md) === 'Mandrill') {
			return call_user_func_array(array($this->_md, $method), $params);
		} else {
			throw new CException(Yii::t('MailChimp', 'Can not call a method of a non existent object'));
		}
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return mixed|void
	 * @throws CException
	 */
	public function __set($name, $value)
	{
		if (is_object($this->_md) && get_class($this->_md) === 'Mandrill') {
			$this->_md->$name = $value;
		} else {
			throw new CException(Yii::t('EMailer', 'Can not set a property of a non existent object'));
		}
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws CException
	 */
	public function __get($name)
	{
		if (is_object($this->_md) && get_class($this->_md) === 'Mandrill') {
			return $this->_md->$name;
		} else {
			throw new CException(Yii::t('EMailer', 'Can not access a property of a non existent object'));
		}
	}
} 