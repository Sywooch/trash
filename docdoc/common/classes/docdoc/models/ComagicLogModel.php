<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 31.07.14
 * Time: 12:16
 */

namespace dfs\docdoc\models;

use dfs\docdoc\validators\UniqueAttributesValidator;

/**
 * Логи Comagic
 *
 * Class ComagicLogModel
 *
 * @property int    $id
 * @property string $numa            С какого номера звонили
 * @property string $numb            На какой номер звонили
 * @property int    $ac_id           Id рекламной кампании
 * @property string $call_date       Время звонка
 * @property int    $wait_time       Время ожидания
 * @property int    $duration        Продолжительность разговора
 * @property string $status          Статус звонка (принятый(normal)/пропущенный(lost))
 * @property string $utm_source      utm-метки
 * @property string $usdftm_medium
 * @property string $utm_term
 * @property string $utm_content
 * @property string $utm_campaign
 * @property string $os_service_name OpenStat метки
 * @property string $os_campaign_id
 * @property string $os_ad_id
 * @property string $os_source_id
 * @property string $session_start   Время начала сессии, связанной со звонком
 * @property string $visitor_id      Id посетителя
 * @property string $search_engine   Поисковая система
 * @property string $search_query    Поисковый запрос
 * @property string $file_link       Ссылка на запись разговора
 * @property string $ua_client_id    User ID Universal Analytics
 * @property string $page_url        Полный адрес посадочной страницы
 * @property string $referrer        Полный адрес страницы, с которой был сделан переход
 * @property string $ef_id           Значение метки ef_id
 * @property int    $request_id      Идентификатор заявки, найденной по этому логу
 * @property string $checked_time    Время проверки лога
 *
 * @method ComagicLogModel[] findAll
 * @method ComagicLogModel findByPk
 */
class ComagicLogModel extends \CActiveRecord
{
	/**
	 * Для проверки на факт изменения $this->request_id
	 *
	 * @var null
	 */
	private $_request_id = null;

	/**
	 * Максимальная разница в секундах между датой звонка и временем создания заявки
	 */
	const DELTA_TOLERANCE = 90;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ComagicLogModel the static model class
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
		return 'comagic_log';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Правила валидации
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			['numa', UniqueAttributesValidator::class, 'with' => 'call_date'],
			[
				'numa, numb, ac_id, call_date, wait_time, duration, status, utm_source, utm_medium, utm_term, utm_content, utm_campaign,
								 os_service_name, os_campaign_id, os_ad_id, os_source_id, session_start, visitor_id, search_engine, search_query, file_link, ua_client_id,
								 page_url, referrer, ef_id',
				'safe',
				'on' => 'log_collector',
			]
		];
	}

	/**
	 * Перед сохранением
	 *
	 * @return bool|void
	 */
	public function beforeValidate()
	{
		$parentBeforeValidate = parent::beforeValidate();

		//кладу в базу null чтобы не падала уникальность с пустой строкой
		if($this->numa === ''){
			$this->numa = null;
		}

		return $parentBeforeValidate;
	}

	/**
	 * Ищет не проверенные еще записи
	 *
	 * @return $this
	 */
	public function notChecked()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'checked_time is null',
				]
			);

		return $this;
	}

	public function withoutRequestId()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'request_id is null',
				]
			);

		return $this;
	}

	/**
	 * Ищет заявку и проставляет если найдет в request_id
	 *
	 * @return boolean
	 */
	public function saveRequest()
	{
		if (!$this->checked_time && !$this->request_id) {

			$request = null;

			//если не пусто
			if($this->numa !== null){
				$request = RequestModel::model()
					->byClientPhone($this->numa)
					->createdInInterval(strtotime($this->call_date), strtotime($this->call_date) + self::DELTA_TOLERANCE)
					->find();
			}

			$request instanceof RequestModel && $this->request_id = $request->req_id;
			$this->checked_time = date('Y-m-d H:i:s', time());
			$this->save();
		}

		return !is_null($this->request_id);
	}

	/**
	 * Поиск максимальной даты звонка
	 *
	 * @return string|null
	 */
	public function getMaxCallDate()
	{
		$cr = $this->getDbCriteria();
		$criteria = clone $cr;
		$criteria->select = 'max(call_date)';

		$command = $this->getCommandBuilder()
			->createFindCommand($this->getTableSchema(), $criteria, $this->getTableAlias());

		$startTime = $command->queryScalar();

		return $startTime;
	}

	/**
	 * Вызывается после создания экземпляра модели
	 *
	 * @return void
	 */
	protected function afterFind()
	{
		$this->_request_id = $this->request_id;
	}
} 
