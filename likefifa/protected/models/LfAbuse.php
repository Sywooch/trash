<?php
use likefifa\components\extensions\LfMandrill;

/**
 * This is the model class for table "lf_abuse".
 *
 * The followings are the available columns in table 'lf_abuse':
 * @property integer $id
 * @property integer $master_id
 * @property string $comment
 *
 * The followings are the available model relations:
 * @property LfMaster $master
 * @property LfSalon $salon
 */
class LfAbuse extends CActiveRecord
{
	const TYPE_OTHER 		= 1;
	const TYPE_NO_CONTACT	= 2;
	const TYPE_WRONG_PRICE 	= 3;
	const TYPE_WRONG_ADDRESS= 4;
	
	protected $typeNames = array(
		'master' => array(
			self::TYPE_NO_CONTACT	=> 'Мастер не связался со мной',
			self::TYPE_WRONG_PRICE	=> 'Неверная цена',
			self::TYPE_WRONG_ADDRESS=> 'Неверный адрес',
			self::TYPE_OTHER 		=> 'Другое',		
		),
		'salon' => array(
			self::TYPE_NO_CONTACT	=> 'Салон не связался со мной',
			self::TYPE_WRONG_PRICE	=> 'Неверная цена',
			self::TYPE_WRONG_ADDRESS=> 'Неверный адрес',
			self::TYPE_OTHER 		=> 'Другое',		
		),
	);
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LfAbuse the static model class
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
		return 'lf_abuse';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('master_id, salon_id, type', 'numerical', 'integerOnly'=>true),
			array('type', 'numerical', 'integerOnly'=>true, 'min' => 1, 'max' => 4),
			array('comment', 'length', 'max'=>512),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, master_id', 'safe', 'on'=>'search'),
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
	
	public function behaviors()
	{
		return array(
				'CArModTimeBehavior' => array(
						'class' => 'application.extensions.CArModTimeBehavior',
				)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
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
		$criteria->compare('master_id',$this->master_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getTypeListItems() {
		return $this->typeNames[$this->salon_id ? 'salon' : 'master'];
	}
	public function getTypeName() {
		$types = $this->getTypeListItems();
		return isset($types[$this->type]) ? $types[$this->type] : null;
	}

	public function notify()
	{
		/** @var LfMandrill $mailer */
		$mailer = Yii::app()->mailer;
		$templateName = 'Жалоба';
		$templateContent = [];
		$target = null;
		if ($this->salon != null) {
			$target = 'salon';
			$templateContent = $mailer->prepareTemplateContent(
				[
					'userUrl'     => $this->salon->getAbsoluteModelUrl(),
					'userName'    => $this->salon->name,
					'typeName'    => $this->getTypeName(),
					'commentText' => $this->comment,
				]
			);
		}
		if ($this->master != null) {
			$target = 'master';
			$templateContent = $mailer->prepareTemplateContent(
				[
					'userUrl'     => $this->master->getAbsoluteProfileUrl(),
					'userName'    => $this->master->getFullName(),
					'typeName'    => $this->getTypeName(),
					'commentText' => $this->comment,
				]
			);
		}

		$message = [
			'to'                => [
				['email' => Yii::app()->params['adminEmail']],
			],
			'global_merge_vars' => $mailer->prepareTemplateContent(
					[
						'target'  => $target,
						'comment' => empty($this->comment) ? 0 : 1
					]
				)
		];
		$mailer->sendTemplate($templateName, $templateContent, $message);
	}
	
}