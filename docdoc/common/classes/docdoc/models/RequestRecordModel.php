<?php

namespace dfs\docdoc\models;

use dfs\docdoc\objects\call\Provider;

/**
 * This is the model class for table "request_record".
 *
 * The followings are the available columns in table 'request_record':
 *
 * @property integer      $record_id
 * @property integer      $request_id
 * @property string       $record
 * @property string       $crDate
 * @property integer      $duration
 * @property string       $comments
 * @property string       $isOpinion
 * @property string       $isAppointment
 * @property string       $isVisit
 * @property integer      $source
 * @property integer      $clinic_id
 * @property integer      $year
 * @property integer      $month
 * @property integer      $type
 * @property string       $replaced_phone
 * @property string       $external_call_id
 *
 * @property array        $attributes
 *
 * The followings are the available model relations
 *
 * @property RequestModel $request
 *
 * @method RequestRecordModel find
 * @method RequestRecordModel findByPk
 * @method RequestRecordModel[] findAll
 *
 */
class RequestRecordModel extends \CActiveRecord
{
	/**
	 * Типы аудиозаписей
	 */
	const TYPE_UNDEFINED = 0;
	const TYPE_IN = 1;
	const TYPE_OUT = 2;
	const TYPE_TRANSFER = 3;

	/**
	 * Флаг загруженной в ручную записи
	 */
	const SOURCE_MANUAL_UPLOADED = -1;
	/**
	 * Источник не определен, либо астериск
	 */
	const SOURCE_DEFAULT = 0;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return RequestRecordModel the static model class
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
		return 'request_record';
	}

	/**
	 * Первичный ключ
	 *
	 * @return string первичный ключ
	 */
	public function primaryKey()
	{
		return 'record_id';
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'request' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\RequestModel',
				'request_id'
			),
		);

	}

	/**
	 * Правила валидации
	 *
	 * @return array
	 */
	public function rules()
	{
		return array(
			array(
				'isAppointment',
				'in',
				'range'      => array('yes', 'no'),
				'allowEmpty' => true,
			),
			array(
				'isAppointment',
				'safe',
				'on' => 'saveAppointment'
			),
			[
				'request_id, record, crDate, duration, comments, isOpinion, isAppointment, isVisit, source, clinic_id, year, month, type, replaced_phone',
				'safe',
				'on' => 'copy'
			]
		);

	}

	/**
	 * после валидации
	 */
	function afterValidate()
	{
		if (empty($this->isAppointment)) {
			$this->isAppointment = 'no';
		}
	}

	/**
	 * Пациент был/не был записан на прием в клинику по этой записи
	 *
	 * @param string $appointment
	 *
	 * @return bool
	 */
	public function saveAppointment($appointment)
	{
		$this->setScenario("saveAppointment");
		$this->isAppointment = $appointment;
		return $this->save();
	}

	/**
	 * Проверка флага записи
	 *
	 * @return bool
	 */
	public function wasAppointment()
	{
		return ($this->isAppointment === 'yes');
	}

	/**
	 * Проверка флага записи
	 *
	 * @return bool
	 */
	public function wasVisit()
	{
		return ($this->isVisit === 'yes');
	}

	/**
	 * @param array $sources
	 *
	 * @return $this
	 */
	public function inSources(array $sources)
	{
		$criteria = new \CDbCriteria();
		$criteria->addInCondition('source', $sources);

		$this->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * @return null|string телефон, с которого звонили
	 */
	public function getCallerPhone()
	{
		$callerPhone = null;

		if($this->getSourceProvider()){
			$callerPhone = $this->getSourceProvider()->getCallerPhone($this->record);
		}

		return $callerPhone;
	}

	/**
	 * @return null|string телефон, на который звонили
	 */
	public function getDestinationPhone()
	{
		$dph = null;

		if($this->getSourceProvider()){
			$dph = $this->getSourceProvider()->getDestinationPhone($this->record);
		}

		return $dph;
	}

	/**
	 * @return null|string
	 */
	public function getPartnerId()
	{
		if($this->getSourceProvider()){
			return $this->getSourceProvider()->getPartnerId($this->record);
		}

		return null;
	}

	/**
	 * @return \dfs\docdoc\objects\call\ProviderInterface|null
	 */
	public function getSourceProvider()
	{
		return Provider::findById($this->source);
	}

	/**
	 * @param string $startDate
	 * @param string $endDate
	 *
	 * @return $this
	 */
	public function between($startDate, $endDate)
	{
		$cr = (new \CDbCriteria())
			->addBetweenCondition('crDate', $startDate, $endDate);

		$this->getDbCriteria()
			->mergeWith($cr);

		return $this;
	}

	/**
	 * @param string $string
	 *
	 * @return $this
	 */
	public function recordNotLike($string)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'record not like :string',
					'params'    => [
						':string' => '%' . $string . '%',
					]
				]
			);

		return $this;
	}

	/**
	 * Парсит имя файла и берет дату
	 *
	 * @param string $filename
	 *
	 * @return bool|int
	 */
	public static function extractTimeFromFilename($filename)
	{
		$filenameTimeExtractorPatterns = array(
			array(
				'pattern'             => '/^(\d+)[\._][_\d\(\)]+\.(mp3|wav)$/i',
				'time_extract_method' => 'unixtime_as_is',
				'match_num'           => 1
			),
			array(
				'pattern'             => '/^\d+_7\d+_\d{1,2}\.\d{1,2}\-(\d{2})(\d{2})(\d{4})\.(mp3|wav)$/i',
				'time_extract_method' => 'strtotime',
				'matches_template'    => "{#3}-{#2}-{#1}",
			),
			array(
				'pattern'             => '/^\_?7\d+_\d{1,2}\.\d{1,2}\-(\d{2})(\d{2})(\d{4})\.(mp3|wav)$/i',
				'time_extract_method' => 'strtotime',
				'matches_template'    => "{#3}-{#2}-{#1}",
			),
			array(
				'pattern'             => '/^record_(\d{1,2})([a-z]{3})(20\d{2})_(\d{1,2})h(\d{1,2})m(\d{1,2})s+\.(mp3|wav)$/i',
				'time_extract_method' => 'strtotime',
				'matches_template'    => "{#1} {#2} {#3} {#4}:{#5}:{#6}"
			),
			array(
				'pattern'             => '/^(p_)?(\d{1,4})-(\d{1,2})-(\d{1,2})_(\d{1,2}).(\d{1,2}).(\d{1,2})(.*)$/i',
				'time_extract_method' => 'strtotime',
				'matches_template'    => "{#4}-{#3}-{#2} {#5}:{#6}:{#7}"
			),
			array(
				'pattern'             => '/^record +(\d{1,2})([a-z]{3})(20\d{2})_(\d{1,2})h(\d{1,2})m(\d{1,2})s+\.(mp3|wav)$/i',
				'time_extract_method' => 'strtotime',
				'matches_template'    => "{#1} {#2} {#3} {#4}:{#5}:{#6}"
			),
			array(
				'pattern'             => '/^record_\d+_(\d{10})\.(mp3|wav)$/i',
				'time_extract_method' => 'unixtime_as_is',
				'match_num'           => 1
			),
			array(
				'pattern'             => '/^record_+(\d{10})_\d*\.(mp3|wav)$/i',
				'time_extract_method' => 'unixtime_as_is',
				'match_num'           => 1
			),
			array(
				'pattern'             => '/^record_+(\d+)\.(mp3|wav)$/i',
				'time_extract_method' => 'unixtime_as_is',
				'match_num'           => 1
			),
			array(
				'pattern'             => '/^record_\d+_(\d+)_\d*\.(mp3|wav)$/i',
				'time_extract_method' => 'unixtime_as_is',
				'match_num'           => 1
			)
		);

		$matches = array();

		foreach ($filenameTimeExtractorPatterns as $extractConf) {

			if (preg_match($extractConf['pattern'], $filename, $matches)) {

				if ($extractConf['time_extract_method'] == 'unixtime_as_is') {
					return intval($matches[$extractConf['match_num']]);
				} elseif ($extractConf['time_extract_method'] == 'strtotime') {
					$strtotime = $extractConf['matches_template'];

					foreach ($matches as $num => $replace) {
						$strtotime = str_replace("{#" . $num . "}", $replace, $strtotime);
					}

					return strtotime($strtotime);
				} else {
					return false;
				}
			}
		}

		return false;
	}

	/**
	 * УРЛ для скачивания записи
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return 'https://' . \Yii::app()->params['hosts']['back'] . '/2.0/record/download/' . $this->record_id;
	}

	/**
	 * Выборка записей, у которых длительность = 0
	 *
	 * @return $this
	 */
	public function hasEmptyDuration()
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'duration = 0',
				]
			);

		return $this;
	}

	/**
	 * @return bool|null|string
	 */
	public function getCreatedDate()
	{
		$date = null;

		if($this->crDate){
			$date = $this->crDate;
		} elseif($this->getSourceProvider()) {
			$date = $this->getSourceProvider()->getCreatedTime($this->record);
		}

		if(is_null($date)){
			$date = date('c', RequestRecordModel::extractTimeFromFilename($this->record));
		}

		return $date;

	}
}
