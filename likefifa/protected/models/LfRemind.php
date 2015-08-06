<?php
use likefifa\components\extensions\LfMandrill;

/**
 * This is the model class for table "lf_remind".
 *
 * The followings are the available columns in table 'lf_remind':
 * @property integer $id
 * @property string $hash
 * @property integer $created
 * @property integer $master_id
 *
 * The followings are the available model relations:
 * @property LfMaster $master
 */
class LfRemind extends CActiveRecord
{
	public $user;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LfRemind the static model class
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
		return 'lf_remind';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hash, created', 'required'),
			array('created, master_id, salon_id', 'numerical', 'integerOnly'=>true),
			array('hash', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, hash, created, master_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'master' => array(self::BELONGS_TO, 'LfMaster', 'master_id'),
			'salon' => array(self::BELONGS_TO, 'LfSalon', 'salon_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'hash' => 'Hash',
			'created' => 'Created',
			'master_id' => 'Master',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('hash',$this->hash,true);
		$criteria->compare('created',$this->created);
		$criteria->compare('master_id',$this->master_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function findByHash($hash) {
		return $this->find('hash = :hash', compact('hash'));
	}
	
	public function createForUser($user, $userType) {
		if($userType == 'master')
		$this->deleteAll('master_id = :master', array('master' => $user->id));
		if($userType == 'salon')
		$this->deleteAll('salon_id = :salon', array('salon' => $user->id));
		
		$model = new LfRemind;
		
		$model->hash 		= md5(base64_encode(microtime(true).rand(0, 10000).rand(0, 10000).rand(0, 10000).rand(0, 10000)));
		$model->created 	= time();
		if($userType == 'master') {
		$model->master_id 	= $user->id;
		}
		if($userType == 'salon') {
		$model->salon_id 	= $user->id;
		}
		$model->save();
		
		
		return $model;
	}
	
	public function getHashUrl() {
		return Yii::app()->createAbsoluteUrl('remind/hash', array('hash' => $this->hash));
	}
	
	public function notify() {
		$url = $this->getHashUrl();
		if($this->master_id) $this->user = $this->master;
		if($this->salon_id) $this->user = $this->salon;

		/** @var LfMandrill $mailer */
		$mailer = Yii::app()->mailer;
		$templateName = 'Запрос на новый пароль';
		$templateContent = $mailer->prepareTemplateContent(
			[
				'userName'  => $this->user->getFullName(),
				'remindUrl' => $url,
			]
		);

		$message = [
			'to'                => [
				['email' => $this->user->email],
			],
			'global_merge_vars' => $mailer->prepareTemplateContent(
					[
						'remindUrl' => $url,
					]
				),
		];
		$mailer->sendTemplate($templateName, $templateContent, $message);

		return $this;
	}
	
	public function apply() {
		if($this->master_id) $this->user = $this->master;
		if($this->salon_id) $this->user = $this->salon;
		$password = $this->user->generatePassword();
		
		$this->user->setPassword($password);
		$this->user->save();

		/** @var LfMandrill $mailer */
		$mailer = Yii::app()->mailer;
		$templateName = 'Успешное восстановление пароля';
		$templateContent = $mailer->prepareTemplateContent(
			[
				'userName'  => $this->user->getFullName(),
				'userPassword' => $password,
			]
		);

		$message = [
			'to'                => [
				['email' => $this->user->email],
			],
		];
		$mailer->sendTemplate($templateName, $templateContent, $message);

		$this->delete();
	}
}