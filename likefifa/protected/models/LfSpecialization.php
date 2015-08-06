<?php
use likefifa\components\system\ActiveRecord;
use likefifa\models\CityModel;

/**
 * This is the model class for table "lf_specialization".
 *
 * The followings are the available columns in table 'lf_specialization':
 *
 * @property integer     $id
 * @property string      $name
 * @property string      $profession
 * @property string      $rewrite_name
 *
 * The followings are the available model relations:
 * @property LfService[] $services модели услуг
 * @property LfGroup     $groupOne специализация
 *
 * @method LfSpecialization   findByPk
 * @method LfSpecialization   ordered
 * @method LfSpecialization[] findAll
 * @method LfSpecialization   with
 * @method LfSpecialization   findByRewrite
 * @method string             getRewriteName()
 * @method LfGroup            groupOne()
 * @method int[]              getRelationIds()
 */
class LfSpecialization extends ActiveRecord
{
	protected $SearchEntity = 'masters';

	/**
	 * Специализации с запретом размещения и отображения фото работ
	 *
	 * @var array
	 */
	protected $excludedByPhoto = [18];

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfSpecialization the static model class
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
		return 'lf_specialization';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, profession, sector_id, group_id', 'required'),
			array('name, profession, rewrite_name, group_id, dative_name, genitive_name', 'length', 'max' => 256),
			array('weight, sort, binded_service_id', 'numerical', 'integerOnly' => true),
			array('id, name, profession, rewrite_name', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'sector'                 => array(self::BELONGS_TO, 'Sector', 'sector_id'),
			'group'                  => array(
				self::MANY_MANY,
				'LfGroup',
				'lf_group_specialization(specialization_id, group_id)'
			),
			'groupOne'               => array(self::BELONGS_TO, 'LfGroup', 'group_id'),
			'services'               => array(self::HAS_MANY, 'LfService', 'specialization_id'),
			'seoText'                => array(self::HAS_ONE, 'LfSeoText', 'specialization_id'),
			'articles'               => array(self::HAS_MANY, 'Article', 'article_section_id'),
			'bindedService'          => array(self::BELONGS_TO, 'LfService', 'binded_service_id'),
			'lfSalonSpecializations' => array(self::HAS_MANY, 'LfSalonSpecialization', 'specialization_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'           => 'ID',
			'name'         => 'Название',
			'profession'   => 'Profession',
			'rewrite_name' => 'Rewrite Name',
		);
	}

	/**
	 * @return array the scope definition. The array keys are scope names; the array
	 * values are the corresponding scope definitions. Each scope definition is represented
	 * as an array whose keys must be properties of {@link CDbCriteria}.
	 */
	public function scopes()
	{
		return array(
			'ordered' => array(
				'order' => 't.weight, t.name ASC',
			),
		);
	}

	/**
	 * Поведения
	 *
	 * @return array the behavior configurations (behavior name=>behavior configuration)
	 */
	public function behaviors()
	{
		return array(
			'CArRewriteBehavior'  => array(
				'class' => 'application.extensions.CArRewriteBehavior',
			),
			'CAdvancedArBehavior' => array(
				'class' => 'application.extensions.CAdvancedArBehavior',
			),
		);
	}

	public function getListItems($withEmpty = false)
	{
		$items = array();

		if ($withEmpty) {
			$items[''] = 'специализация не выбрана';
		}

		$specializations = $this->ordered()->findAll();
		foreach ($specializations as $specialization) {
			$items[$specialization->id] = su::ucfirst($specialization->name);
		}

		return $items;
	}

	/**
	 * Получает дерево для прайс-листа
	 *
	 * @param LfMaster $master модель мастера
	 *
	 * @return string[]
	 */
	public function getTree(LfMaster $master)
	{
		$tree = array();

		if ($master->groups) {
			foreach ($master->groups as $group) {
				$tree[(int)$group->id]["genitive_one"] = $group->genitive_one;
				if ($group->specializations) {
					foreach ($group->specializations as $spec) {
						$services = array();
						foreach ($spec->services('services:ordered') as $service) {
							$services[(int)$service->id] = $service->getAttributes();
						}
						$tree[(int)$group->id]["spec"][(int)$spec->id] =
							array('name' => trim($spec->name), 'services' => $services);
					}
				}
			}
		}

		return $tree;
	}

	public function getSalonTree(LfSalon $salon)
	{
		$tree = array();

		$specs = $this->ordered()->with('services')->findAll();
		foreach ($specs as $spec) {
			$services = array();
			foreach ($spec->services('services:ordered') as $service) {
				$services[$service->id] = $service->getAttributes();
			}
			$tree[$spec->id] = array('name' => $spec->name, 'services' => $services);
		}

		return $tree;
	}

	public function getIdsTree()
	{
		$ids = array();
		foreach ($this->with('services')->findAll() as $spec) {
			$ids[$spec->id] = array();
			foreach ($spec->services as $service) {
				$ids[$spec->id][] = $service->id;
			}
		}
		return $ids;
	}

	public function getSectionUrl()
	{
		return '/articles/' . ($this->rewrite_name ? : $this->id) . '/';
	}

	public function setMastersSearch()
	{
		$this->SearchEntity = 'masters';
		return $this;
	}

	public function setSalonsSearch()
	{
		$this->SearchEntity = 'salons';
		return $this;
	}

	/**
	 * @param CityModel $city
	 *
	 * @return string
	 */
	public function getSearchUrl($city = null)
	{
		$params = ['specialization' => $this->getRewriteName()];
		if ($city) {
			$params['city'] = $city->rewrite_name;
		}
		return Yii::app()->createUrl($this->SearchEntity . '/custom', $params);
	}

	public function getServicesListItems($withEmpty = false)
	{
		$items = array();

		if ($withEmpty) {
			$items[''] = 'услуга не выбрана';
		}

		if (!$this->isNewRecord) {
			foreach ($this->services('services:ordered') as $service) {
				$items[$service->id] = $service->name;
			}
		}

		return $items;
	}

	/**
	 * @return LfService
	 */
	public function getFirstService()
	{
		$services = $this->services('services:ordered');
		return $services ? $services[0] : null;
	}

	public function getGroupsConcatenated()
	{
		$groups = array();
		foreach ($this->group as $group) {
			$groups[] = trim($group->name);
		}
		return implode(', ', $groups);
	}

	public function asArray()
	{
		$result = array(
			'id'       => (int)$this->id,
			'name'     => su::ucfirst($this->name),
			'services' => array(),
		);

		foreach ($this->services as $service) {
			$result['services'][] = $service->asArray();
		}

		return $result;
	}

	public function getFullTree(LfMaster $master = null)
	{
		$tree = array();

		if ($master) {
			$groupIds = array();
			foreach ($master->groups as $group) {
				$groupIds[] = $group->id;
			}
		}

		$specs =
			$master
				? $this->ordered()->with('services')->findAll(
				$groupIds ? 't.group_id IN (' . implode(',', $groupIds) . ')' : ''
			)
				: $this->ordered()->with('services')->findAll();

		foreach ($specs as $spec) {
			$tree[] = $spec->asArray();
		}

		return $tree;
	}

	/**
	 * Получает список моделей специальностей
	 *
	 * @param string $speciality абривиатура специализации
	 *
	 * @return LfSpecialization[]
	 */
	public function getRelevantModels($speciality = null)
	{
		if (!$speciality) {
			return $this->ordered()->findAll();
		} else {
			$list = array();

			$model = LfGroup::model()->getModelByRewriteName($speciality);
			if ($model) {
				if ($model->specializations) {
					foreach ($model->specializations as $spec) {
						$list[] = $spec;
					}
				}
			}

			return $list;
		}
	}

	/**
	 * Можно ли загружать и показывать фотографии для данной специализации
	 *
	 * @return bool
	 */
	public function isAllowPhoto()
	{
		return array_search($this->id, $this->excludedByPhoto) === false;
	}
}