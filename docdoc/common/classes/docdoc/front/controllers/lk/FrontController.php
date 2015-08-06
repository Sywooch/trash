<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\components\AppController;
use dfs\docdoc\front\components\LkUserIdentity;
use dfs\docdoc\models\AuthTokenModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ClinicAdminModel;
use dfs\docdoc\models\ContractModel;
use dfs\docdoc\models\DoctorModel;

/**
 * Class FrontController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class FrontController extends AppController
{
	/**
	 * Клиника под которой залогинились
	 *
	 * @var ClinicModel | null
	 */
	protected $_clinic = null;

	/**
	 * Пользователь под которым залогинились
	 *
	 * @var ClinicAdminModel | null
	 */
	protected $_admin = null;

	/**
	 * Меню
	 *
	 * @var array
	 */
	protected $_menu = [
		'patients' => [
			'Title'      => 'Ваши пациенты',
			'URL'        => '/lk/patients',
			'Rules'      => [ 'clinic', 'privatDoctor' ],
		],
		'reports' => [
			'Title'      => 'Отчеты',
			'URL'        => '/lk/reports',
			'Hidden'     => true,
		],
		'doctors' => [
			'Title'      => 'Врачи',
			'URL'        => '/lk/doctors',
			'Rules'      => [ 'clinic', 'privatDoctor' ],
		],
		'reviews' => [
			'Title'      => 'Отзывы',
			'URL'        => '/lk/reviews',
			'Rules'      => [ 'clinic', 'privatDoctor' ],
		],
		'drequest' => [
			'Title'      => 'Заявки на диагностику',
			'URL'        => '/lk/drequest',
			'Rules'      => [ 'diagnostic' ],
		],
		'diagnostics' => [
			'Title'      => 'Диагностики',
			'URL'        => '/lk/diagnostics',
			'Rules'      => [ 'diagnostic' ],
		],
		'contracts' => [
			'Title'      => 'Тарифы',
			'URL'        => '/lk/contracts',
		],
		'info' => [
			'Title'      => 'О клинике',
			'URL'        => '/lk/info',
		],
		'settings' => [
			'Title'      => 'Настройки',
			'URL'        => '/lk/settings',
		],
		'logout' => [
			'Title'      => 'Выйти из личного кабинета',
			'URL'        => '/lk/service/logout',
			'Class'      => 'nav_list__link-quit',
		],
	];

	/**
	 * @var array
	 */
	protected $_values = [];

	/**
	 * Фильтры
	 *
	 * @return array
	 */
	public function filters()
	{
		return [ 'accessControl' ];
	}

	/**
	 * Возвращает правила доступа для контроллера
	 *
	 * @return array
	 */
	public function accessRules()
	{
		return [
			[ 'allow', 'users' => [ '@' ] ],
			[ 'deny' ],
		];
	}

	/**
	 * The filter method for 'accessControl' filter.
	 *
	 * @param \CFilterChain $filterChain
	 */
	public function filterAccessControl($filterChain)
	{
		if (\Yii::app()->user->isGuest) {
			$token = \Yii::app()->request->getQuery('authToken');

			$authToken = $token ? AuthTokenModel::model()->active()->findByToken($token) : null;

			if ($authToken) {
				$identity = new LkUserIdentity('', '');

				if ($identity->authenticateByToken($authToken)) {
					\Yii::app()->user->login($identity);
				}
			}
		}

		parent::filterAccessControl($filterChain);
	}

	/**
	 * Выполняем перед любым действием
	 *
	 * @param \CAction $action
	 *
	 * @return bool
	 */
	public function beforeAction($action)
	{
		$user = \Yii::app()->user;

		$adminId = $user->getState('id');
		$clinicId = $user->getState('clinicId');

		if ($adminId) {
			$this->_clinic = ClinicModel::model()->findByPk($clinicId);
			$this->_admin = ClinicAdminModel::model()->findByPk($adminId);
		}
		elseif (!$user->isGuest) {
			$user->logout();
		}

		return parent::beforeAction($action);
	}

	/**
	 * Ссылка на главную страницу
	 *
	 * @return string
	 */
	protected function getMainPageUrl()
	{
		$url = '/lk/settings';

		$pages = [ 'patients', 'drequest' ];
		$menu = $this->getMenu();

		foreach ($pages as $page) {
			if (isset($menu[$page]) && empty($menu[$page]['Hidden'])) {
				$url = $menu[$page]['URL'];
				break;
			}
		}

		return $url;
	}

	/**
	 * Список страниц в меню
	 *
	 * @return array
	 */
	protected function getMenu()
	{
		if (!isset($this->_values['menu'])) {
			$menu = $this->_menu;

			$menu['patients']['Hidden'] = true;
			$menu['drequest']['Hidden'] = true;

			foreach ($this->_clinic->getClinicContracts() as $tariff) {
				switch ($tariff->contract_id) {
					case ContractModel::TYPE_DOCTOR_RECORD:
					case ContractModel::TYPE_DOCTOR_VISIT:
					case ContractModel::TYPE_DOCTOR_CALL:
						$menu['patients']['Hidden'] = false;
						break;
					case ContractModel::TYPE_DIAGNOSTIC_CALL:
					case ContractModel::TYPE_DIAGNOSTIC_RECORD:
					case ContractModel::TYPE_DIAGNOSTIC_VISIT:
					case ContractModel::TYPE_DIAGNOSTIC_ONLINE:
						$menu['drequest']['Hidden'] = false;
						break;
				}
			}

			$clinic = $this->_clinic;
			foreach ($menu as &$item) {
				if (!empty($item['Rules']) && empty($item['Hidden'])) {
					$visible = false;
					foreach ($item['Rules'] as $rule) {
						switch ($rule) {
							case 'clinic':
								$visible = $clinic->isClinic();
								break;
							case 'diagnostic':
								$visible = $clinic->isDiagnostic();
								break;
							case 'privatDoctor':
								$visible = $clinic->isPrivatDoctor == 'yes';
								break;
						}
						if ($visible) {
							break;
						}
					}
					$item['Hidden'] = !$visible;
				}
			}

			$this->_values['menu'] = $menu;
		}

		return $this->_values['menu'];
	}

	/**
	 * Список филиалов клиники
	 *
	 * @var ClinicModel[]
	 */
	public function getAdminClinicList()
	{
		if (!isset($this->_values['adminClinicList'])) {
			$this->_values['adminClinicList'] = ClinicModel::model()
				->active()
				->onlyClinic()
				->searchByAdminId($this->_admin->clinic_admin_id)
				->findAll();
		}

		return $this->_values['adminClinicList'];
	}

	/**
	 * Количество докторов в клинике
	 *
	 * @var int
	 */
	public function getDoctorsCount()
	{
		if (!isset($this->_values['doctorsCount'])) {
			$this->_values['doctorsCount'] = DoctorModel::model()
				->inClinics([ $this->_clinic->id ])
				->inStatuses([ DoctorModel::STATUS_ACTIVE, DoctorModel::STATUS_BLOCKED, DoctorModel::STATUS_MODERATED ])
				->count();
		}

		return $this->_values['doctorsCount'];
	}

	/**
	 * Генерация json-ответа, для старых js-скриптов
	 *
	 * @param bool $status
	 * @param string $error
	 * @param string | bool $url
	 */
	protected function renderJsonAnswer($status, $error = null, $url = false)
	{
		$result = [];

		if ($status) {
			$result['status'] = 'success';
			$result['url'] = $url;
		} else {
			$result['status'] = 'error';
			$result['error'] = $error;
		}

		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

		foreach (\Yii::app()->log->routes as $route) {
			if($route instanceof \CWebLogRoute) {
				$route->enabled = false;
			}
		}

		\Yii::app()->end();
	}

	/**
	 * Идентификатор основной клиники
	 *
	 * @return int
	 */
	protected function getMainClinicId()
	{
		return $this->_clinic ? ($this->_clinic->parent_clinic_id ?: $this->_clinic->id) : null;
	}

	/**
	 * Возвращает массив идентификаторов либо выбранного филиала, либо всех филиалов клиники
	 *
	 * @param int $clinicId
	 *
	 * @return array
	 */
	protected function getClinicBranchIds($clinicId = 0)
	{
		if ($clinicId) {
			return [$this->_clinic->hasBranch($clinicId) ? $clinicId : 0];
		}

		$ids = [$this->_clinic->id];

		foreach ($this->_clinic->branches as $branch) {
			$ids[] = $branch->id;
		}

		return $ids;
	}

	/**
	 * Сделать label-value массив из записей
	 *
	 * @param array $records
	 * @param string $fieldName
	 * @param string $fieldValue
	 *
	 * @return array
	 */
	protected function recordsAsLabelValues($records, $fieldName, $fieldValue)
	{
		$values = [];

		foreach ($records as $item) {
			$values[] = [
				'label' => $item->{$fieldName},
				'value' => $item->{$fieldValue},
			];
		}

		return $values;
	}
}
