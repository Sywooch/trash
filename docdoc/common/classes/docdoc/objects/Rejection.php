<?php
namespace dfs\docdoc\objects;

/**
 * Class Rejection
 */
class Rejection
{
	/**
	 * Причины отказов
	 */
	const REASON_OPERATOR_NOT_ANSWER = 1;
	const REASON_ONLY_THIS_DOCTOR = 2;
	const REASON_DUBLICATE = 4;
	const REASON_PARTNERSHIP = 5;
	const REASON_CLIENT_NOT_ANSWER = 6;
	const REASON_TEST = 7;
	const REASON_DISCONNECT = 8;
	const REASON_NO_OPTIONS = 9;
	const REASON_LOCATION = 10;
	const REASON_NO_SUCH_SERVICE = 11;
	const REASON_OTHER = 13;
	const REASON_NIGHT = 14;
	const REASON_REFINE = 15;
	const REASON_DIAGNOSTIC = 16;
	const REASON_PRICE = 17;
	const REASON_TIME = 18;
	const REASON_HAVE_CONTACTS = 20;
	const REASON_NOT_NEED = 24;
	const REASON_NOT_COME = 28;
	const REASON_NO_CLINIC_DIAGNOSTIC = 29;
	const REASON_PRICE_CALL_HOME = 30;
	const REASON_NO_CLINIC_OR_DOCTOR = 31;
	const REASON_ALREADY_RECORDED = 32;
	const REASON_REFINE_ADDRESS = 33;
	const REASON_REFINE_PRICE = 34;
	const REASON_REFINE_DOCTOR_INFO = 35;
	const REASON_REFINE_WORK_TIME = 36;
	const REASON_NEED_CONSULTATION = 37;
	const REASON_DISAGREE_CHANGE_CLINIC = 38;
	const REASON_FROD = 39;
	const REASON_SPAM = 40;
	const REASON_REPEAT_REQUEST = 42;
	const REASON_ANALYSIS = 41;

	/**
	 * Названия причин отказов
	 *
	 * @var array
	 */
	static private $_reasons = array(
		self::REASON_OPERATOR_NOT_ANSWER    => 'Оператор не ответил',
		self::REASON_ONLY_THIS_DOCTOR       => 'Хотели именно к этому врачу',
		self::REASON_DUBLICATE              => 'Дубль',
		self::REASON_PARTNERSHIP            => 'Сотрудничество',
		self::REASON_CLIENT_NOT_ANSWER      => 'Клиент не отвечает/неверный номер',
		self::REASON_TEST                   => 'Тест',
		self::REASON_DISCONNECT             => 'Обрыв связи',
		self::REASON_NO_OPTIONS             => 'Не подошел ни один вариант из предложенного',
		self::REASON_LOCATION               => 'Нет вариантов по местоположению',
		self::REASON_NO_SUCH_SERVICE        => 'Нет такого специалиста/нет услуги',
		self::REASON_OTHER                  => 'Другое',
		self::REASON_NIGHT                  => 'Ночная',
		self::REASON_REFINE                 => 'Уточнение данных',
		self::REASON_DIAGNOSTIC             => 'Диагностика',
		self::REASON_PRICE                  => 'Не устроила цена',
		self::REASON_TIME                   => 'Не устроило время/дата',
		self::REASON_HAVE_CONTACTS          => 'Взяли контакты клиники/пациента',
		self::REASON_NOT_NEED               => 'В услуге больше не нуждается',
		self::REASON_NOT_COME               => 'Не был на приеме',
		self::REASON_NO_CLINIC_DIAGNOSTIC   => 'Клиника не платит за эту диагностику',
		self::REASON_PRICE_CALL_HOME        => 'Не устроила стоимость вызова на дом',
		self::REASON_NO_CLINIC_OR_DOCTOR    => 'Нет клиники/врача в БО',
		self::REASON_ALREADY_RECORDED       => 'Клиент уже записан',
		self::REASON_REFINE_ADDRESS         => 'Уточнение адреса клиники/как добраться',
		self::REASON_REFINE_PRICE           => 'Уточнение стоимости приема',
		self::REASON_REFINE_DOCTOR_INFO     => 'Уточнение информации о враче',
		self::REASON_REFINE_WORK_TIME       => 'Уточнение графика работы',
		self::REASON_NEED_CONSULTATION      => 'Нужна онлайн консультация',
		self::REASON_DISAGREE_CHANGE_CLINIC => 'Отказ от перевода в клинику',
		self::REASON_FROD                   => 'ФРОД',
		self::REASON_SPAM                   => 'СПАМ',
		self::REASON_ANALYSIS               => 'Анализы',
		self::REASON_REPEAT_REQUEST         => 'Повторная запись',
	);

	/**
	 * Получение причин отказов
	 *
	 * @param string $fieldId
	 * @param string $filedName
	 *
	 * @return array
	 */
	static public function getReasons($fieldId = 'Id', $filedName = 'Name')
	{
		$data = array();
		asort(self::$_reasons);
		foreach (self::$_reasons as $key => $item) {
			$data[] = [
				$fieldId => $key,
				$filedName => $item,
			];
		}
		return $data;
	}

	/**
	 * Текстовое представление отказа по ключу
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public static function getReason($id)
	{
		$reason = 'Причина не известна';

		if (array_key_exists($id, static::$_reasons)) {
			$reason = static::$_reasons[$id];
		}

		return $reason;
	}

	/**
	 * Причины отказа, при которых заявка считается отклоненной
	 *
	 * @return string[]
	 */
	static public function getReasonsForDecline()
	{
		$data = array(
			self::REASON_DUBLICATE,
			self::REASON_TEST,
			self::REASON_PARTNERSHIP,
			self::REASON_REFINE,
			self::REASON_DIAGNOSTIC,
			self::REASON_CLIENT_NOT_ANSWER,
			self::REASON_OPERATOR_NOT_ANSWER,
			self::REASON_DISCONNECT,
			self::REASON_OTHER,
			self::REASON_NO_CLINIC_DIAGNOSTIC,
		);
		return $data;
	}

	/**
	 * Получить все отказы
	 *
	 * @return int[]
	 */
	public static function getAllReasons()
	{
		return array_keys(self::$_reasons);
	}
} 
