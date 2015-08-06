<?php

/**
 * Class Uletter
 *
 * Класс расширяющий возможность отправки писем.
 * Добавлен фильтр на тестовые e-mail адреса
 *
 * @author Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @date   17.12.2013
 *
 * @see    https://docdoc.megaplan.ru/task/1002498/card/
 */
class Uletter extends letter
{

	public static function create() {
		return new self;
	}

	/**
	 * Переопредела функция отправления письма
	 * Введен фильтр на e-mail адреса
	 *
	 * @return self|bool
	 */
	public function send()
	{
		if ($this->canSend($this->to[0])) {
			return parent::send();
		}

		return false;
	}

	/**
	 * Проверяем можно ли отправлять почту или нет
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
			if (!in_array($email, Yii::app()->params['devEmails'])) {
				return false;
			}
		}
		return true;
	}
}