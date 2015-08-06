<?php
namespace dfs\docdoc\models;

use dfs\docdoc\objects\Phone;

/**
 * модель для таблицы Client
 *
 * @property integer $clientId
 * @property string $name
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $email
 * @property string $phone
 * @property string $registered_in_mixpanel
 *
 *
 * @method ClientModel findByPk
 * @method ClientModel find
 * @method ClientModel[] findAll
 *
 * @property RequestModel[] $requests
 *
 */
class ClientModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClientModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'client';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'phone',
				'filter',
				'filter' => array('dfs\docdoc\objects\Phone', 'strToNumber')
			),
			array(
				'name, first_name, last_name, middle_name',
				'filter',
				'filter' => 'strip_tags',
			),
			array(
				'email',
				'email',
				'allowEmpty' => true,
			),
			array(
				'phone',
				'dfs\docdoc\validators\PhoneValidator',
			),
			array(
				'phone',
				'required'
			),
			array(
				'email',
				'email',
				'allowEmpty' => true
			),
		);
	}

	/**
	 * @return string первичный ключ
	 */
	public function primaryKey()
	{
		return 'clientId';
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'requests' => array(
				self::HAS_MANY, 'dfs\docdoc\models\RequestModel', 'clientId'
			),
		);

	}

	/**
	 * Фильтр по номру телефона
	 *
	 * @param string $phone
	 *
	 * @return $this
	 */
	public function byPhone($phone)
	{
		//убираем лишнее форматирование телефона
		$phone = new Phone($phone);

		$this->getDbCriteria()->mergeWith(
			[
				'condition' => "phone =:phone",
				'params'    => [
					":phone" => $phone->getNumber(),
				]
			]
		);

		return $this;
	}

	/**
	 * Сохранеине клиента из заявки по следующей схеме
	 *
	 *         нашел клинета
	 *        нет/       \да
	 *        новый?     update
	 *     нет/  \да
	 *   update  insert on duplicate key update
	 *
	 * @param RequestModel $request
	 *
	 * @return ClientModel|null
	 */
	public function saveFromRequest(RequestModel $request)
	{
		//ищем, есть существует ли клиент с таким номером телефона
		$client = ClientModel::model()
			->byPhone($request->client_phone)
			->find();

		if($client){
			$client->name = $request->client_name;
			$client->phone = $request->client_phone;
			$client->save();

		} else {
			$client = $this;
			$client->name = $request->client_name;
			$client->phone = $request->client_phone;

			if($client->getIsNewRecord()){

				if($client->validate()){
					$sql = "INSERT INTO client (name, phone) values (:name, :phone)
						ON DUPLICATE KEY UPDATE name = VALUES(name), phone=VALUES(phone);";

					$command = $this->getDbConnection()->createCommand($sql);
					$command->execute([':name' => $request->client_name, ':phone' => $request->client_phone]);

					$client = ClientModel::model()
						->byPhone($request->client_phone)
						->find();

					if (!is_null($client)) {
						//магия чтобы отработал afterSave
						//есть мизерная вероятность если это будет update
						$client->setIsNewRecord(true);
						$client->afterSave();
						$client->setIsNewRecord(false);
					}
				}

			} else {
				$client->save();
			}
		}

		return $client;
	}

	/**
	 * Сохранение флага, что клиент зарегистрирован в микспанели
	 *
	 * @return bool
	 */
	public function saveRegisteredInMixPanel()
	{
		$this->registered_in_mixpanel = 1;
		return $this->save(true, ['registered_in_mixpanel']);
	}

	/**
	 * выполнить после сохранения
	 */
	public function afterSave()
	{
		parent::afterSave();

		if ($this->getIsNewRecord()) {
			/** @var \dfs\docdoc\components\EventDispatcher $eventDispatcher */
			$eventDispatcher = \Yii::app()->eventDispatcher;
			// Вызываем  событие, что создан клиент
			$eventDispatcher->raiseEvent('onClientCreated', new \CEvent($this));
		}
	}

}
