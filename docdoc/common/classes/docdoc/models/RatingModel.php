<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.09.14
 * Time: 13:16
 */

namespace dfs\docdoc\models;

use dfs\docdoc\extensions\Logger;

/**
 * Class Rating
 * @package dfs\docdoc\models
 *
 * @property int id
 * @property int object_id
 * @property int object_type
 * @property int strategy_id
 * @property float rating_value
 *
 * relations
 * @property RatingStrategyModel strategy
 *
 * @method RatingModel find
 * @method RatingModel findByPk
 * @method RatingModel[] findAll
 */
class RatingModel extends \CActiveRecord
{
	/**
	 * Тип доктор
	 */
	const TYPE_DOCTOR = 1;

	/**
	 * Тип клиника
	 */
	const TYPE_CLINIC = 2;

	/**
	 * @return string
	 */
	public function tableName()
	{
		return 'rating';
	}

	/**
	 * @param string $className
	 * @return static
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			['id, object_id, object_type, strategy_id', 'numerical', 'integerOnly' => true],
			['rating_value', 'numerical'],
			['strategy_id', 'exist', 'attributeName' => 'id', 'className' => RatingStrategyModel::class],
		];
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return [
			'strategy' => [self::BELONGS_TO, RatingStrategyModel::class, 'strategy_id'],
		];
	}

	/**
	 * Поиск по имени
	 *
	 * @param int $strategyId
	 * @return $this
	 */
	public function byStrategy($strategyId)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'strategy_id = :strategyId',
					'params' => [':strategyId' => $strategyId]
				]
			);

		return $this;
	}

	/**
	 * Поиск по объекту
	 *
	 * @param int $objectId
	 * @param int $objectType
	 * @return $this
	 */
	public function byObject($objectId, $objectType)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'object_id = :object_id and object_type = :object_type',
					'params' => [':object_id' => $objectId, ':object_type' => $objectType]
				]
			);

		return $this;
	}

	/**
	 * Парсинг данных из CSV
	 *
	 * @param string $type
	 * @param string|null $fileName
	 *
	 * @return array
	 * @throws \CException
	 */
	public function parseCSV($type, $fileName = null)
	{
		is_null($fileName) && $fileName = $this->getTempCsvFileName($type);

		$handle = fopen($fileName, 'r');

		if(!$handle){
			throw new \CException('Ошибка открытия файла ' . $fileName);
		}

		$k = 0;
		$title = fgetcsv($handle, 1000, ",");
		$data = [];

		while (($row = fgetcsv($handle, 1000, ",")) !== false) {
			foreach ($row as $key => $item) {
				$data[$k][$title[$key]] = $item;
			}

			$k++;
		}

		fclose($handle);

		if($this->validateCSVData($data, $type)){
			return $data;
		} else {
			return false;
		}
	}

	/**
	 * Валидация формата данных
	 *
	 * @param array $data
	 * @param int $type
	 * @throws \CException
	 * @return bool
	 */
	public function validateCSVData(array $data, $type)
	{
		$isValid = true;

		switch($type){
			case self::TYPE_DOCTOR:
				if (!isset($data[0]['ID']) || !isset($data[0]['KB'])) {
					$isValid = false;
				}
				break;
			case self::TYPE_CLINIC:
				if (!isset($data[0]['ID']) || !isset($data[0]['KK']) || !isset($data[0]['PK']) || !isset($data[0]['CO'])) {
					$isValid = false;
				}
				break;
			default:
				throw new \CException("Неизвестный тип файла");
		}

		return $isValid;
	}

	/**
	 * Гетер для имени файла с рейтингами
	 *
	 * @param int $type
	 * @return string
	 * @throws \CException
	 */
	public function getTempCsvFileName($type)
	{
		switch($type){
			case self::TYPE_DOCTOR:
				$fileName = 'doctor';
				break;
			case self::TYPE_CLINIC:
				$fileName = 'clinic';
				break;
			default:
				throw new \CException('Неизвестный тип');
		}
		return ROOT_PATH . '/back/runtime/ratings/' . $fileName . '_rating.csv';
	}

	/**
	 * Сохранени из файла параметров + пересчет рейтингов для врачей
	 *
	 * @param int $type
	 * @throws \CException
	 */
	public function updateRatingFromFile($type)
	{
		if(file_exists($this->getTempCsvFileName($type)) && is_file($this->getTempCsvFileName($type))){
			if($data = RatingModel::model()->parseCSV($type)){
				switch($type){
					case self::TYPE_DOCTOR:
						$this->updateDoctorRatingFromFile($data);
						break;
					case self::TYPE_CLINIC:
						$this->updateClinicRatingFromFile($data);
						break;
					default:
						throw new \CException('Неизвестный тип');
				}
			} else {
				throw new \CException('Ошибка формата');
			}

			unlink($this->getTempCsvFileName($type));
		}
	}

	/**
	 * Данные для врачей
	 *
	 * @param array $data
	 */
	public function updateDoctorRatingFromFile(array $data)
	{
		$logger = \Yii::getLogger();

		if ($data) {
			DoctorModel::model()->updateAll([ 'conversion' => null ]);
		}

		foreach ($data as $item) {

			if ($doctor = DoctorModel::model()->findByPk($item['ID'])) {
				$doctor->setScenario(DoctorModel::SCENARIO_SKIP_UPDATE_RATING);
				$doctor->conversion = $item['KB'] / 100;

				if(!$doctor->save(true, ['conversion'])){
					$logger->log(
						'Доктор # ' . $doctor->id . ' ошибка обновления: ' . var_export($doctor->getErrors(), true),
						Logger::LEVEL_ERROR
					);
				}
			} else {
				\Yii::getLogger()->log('Доктор с ИД '  . $item['ID'] . ' не найден');
			}
		}
	}

	/**
	 * Данные для клиник
	 *
	 * @param array $data
	 */
	public function updateClinicRatingFromFile(array $data)
	{
		$logger = \Yii::getLogger();

		foreach ($data as $item) {
			if ($clinic = ClinicModel::model()->findByPk($item['ID'])) {
				$clinic->setScenario(ClinicModel::SCENARIO_SKIP_UPDATE_RATING);
				$clinic->conversion = $item['KK'] / 100;
				$clinic->hand_factor = $item['PK'];
				$clinic->admission_cost = $item['CO'];

				if(!$clinic->save(true, ['conversion', 'hand_factor', 'admission_cost'])){
					$logger->log(
						'Клиника # ' . $clinic->id . ' ошибка обновления:' . var_export($clinic->getErrors(), true),
						Logger::LEVEL_ERROR)
					;
				}
			}
		}
	}
} 
