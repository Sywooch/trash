<?php

use likefifa\components\helpers\ListHelper;

class MasterSearchController extends BackendController
{
	/**
	 * Поиск мастеров операторами
	 */
	public function actionIndex()
	{
		$params = $this->getSearchParams();
		$dataProvider = $this->createDataProvider($params, 30);

		$this->render('index', compact('dataProvider',  'params'));
	}

	/**
	 * Создает CActiveDataProvider
	 *
	 * @param string[] $params
	 * @param int      $pageSize
	 *
	 * @return CActiveDataProvider
	 */
	protected function createDataProvider(array $params, $pageSize)
	{
		$criteria = $this->getCriteria($params);

		$dataProvider = new CActiveDataProvider('LfMaster', array(
			'criteria'   => $criteria,
			'pagination' => array(
				'pageSize' => $pageSize,
				'pageVar'  => 'page',
			),
		));
		// Поиск ближайших станций метро
		if (intval($dataProvider->getTotalItemCount()) < 10) {
			$params['stations'] = CMap::mergeArray(
				$params['stations'],
				UndergroundStation::model()
					->near(ListHelper::buildPropList('id', $params['stations']), 3)
					->findAll()
			);

			$criteria = $this->getCriteria($params);
			$dataProvider = new CActiveDataProvider('LfMaster', array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => $pageSize,
					'pageVar'  => 'page',
				),
			));
		}

		return $dataProvider;
	}

	/**
	 * Возвращает критерию
	 *
	 * @param array $params
	 *
	 * @return CDbCriteria
	 */
	protected function getCriteria($params)
	{
		$criteria = array(
			'condition' => [],
			'params'    => [],
			'with'      => ['services'],
			'order'     => 't.rating_composite DESC',
			'group'     => 't.id'
		);

		$groupId = LfGroup::model()->getIdByRewriteName($params["speciality"]);
		if (
			$params["speciality"]
			&& $groupId
		) {
			$criteria["with"][] = "group";
			$criteria["condition"][] = "group.id = :groupId";
			$criteria["params"]["groupId"] = $groupId;
		}

		$criteria['condition'][] = "is_blocked != 1";

		if ($params['stations']) {
			$criteria['condition'][] =
				't.underground_station_id IN (' . ListHelper::buildIdList($params['stations']) . ')';
		}

		if ($params['service']) {
			$criteria['condition'][] = 'filledPrices.service_id = ' . $params['service']->id;
			$criteria['with'][] = 'services';
			$criteria['with'][] = 'filledPrices';
		} else {
			if ($params['specialization']) {
				$criteria['condition'][] = 'services.specialization_id = ' . $params['specialization']->id;
				$criteria['with'][] = 'services';
			}
		}

		// Фильтрация по прайсу
		if ($params['price'] && $params['service']) {
			$prices = explode('-', $params['price']);
			$priceCondition = '';
			if (trim($prices[0])) {
				$priceCondition = ':aliase.price >= :price_from';
				$criteria['params'][':price_from'] = trim($prices[0]);
			}
			if (count($prices) > 1 && trim($prices[1])) {
				$priceCondition .= ($priceCondition ? ' AND ' : '') . ':aliase.price <= :price_to';
				$criteria['params'][':price_to'] = trim($prices[1]);
			}
			$criteria['params'][':service_id'] = $params['service']->id;

			if (!empty($priceCondition)) {
				$joins = [];
				$joins[] = 'LEFT JOIN lf_price mp ON mp.master_id = t.id AND mp.service_id = :service_id';
				$joins[] = 'LEFT JOIN lf_salons salon ON salon.id = t.salon_id';
				$joins[] = 'LEFT JOIN lf_price sp ON sp.salon_id = salon.id AND mp.service_id = :service_id';

				$criteria['join'] = implode(' ', $joins);

				$criteria['order'] = 'IF(salon.id IS NOT NULL, sp.price, mp.price) asc, t.rating_composite DESC';

				$criteria['condition'][] = 'IF(salon.id IS NOT NULL,
				' . str_replace(':aliase', 'sp', $priceCondition) . ',
				' . str_replace(':aliase', 'mp', $priceCondition) . '
				)';
			}
		}

		$criteria['condition'][] = 't.is_published = 1';

		$criteria['condition'] = $criteria['condition'] ? implode(' AND ', $criteria['condition']) : '';

		$criteria = new CDbCriteria($criteria);

		return $criteria;
	}

	/**
	 * Парсим параметры поиска.
	 * Метод универсален для всех типов поиска, что следует учитывать
	 * при формировании URL.
	 *
	 * @return array
	 */
	protected function getSearchParams()
	{
		$specialization = null;
		if (!empty($_GET['specialization'])) {
			$specialization = LfSpecialization::model()->with('services')->findByRewrite($_GET['specialization']);
		}

		if (!$specialization) {
			unset($_GET['specialization']);
		} else {
			unset($_GET['speciality']);
		}

		$speciality = Yii::app()->request->getQuery("speciality");

		$service = null;
		if ($specialization && !empty($_GET['service'])) {
			$service = LfService::model()->findBySpecAndRewrite($specialization, $_GET['service']);
		}
		if (!$service) {
			unset($_GET['service']);
		}

		$stations = null;
		if (!empty($_GET['stations'])) {
			$stations = UndergroundStation::model()->findAllByRewrite(explode(',', rawurldecode($_GET['stations'])));
		}
		if (!$stations) {
			unset($_GET['stations']);
		}

		$price = Yii::app()->request->getQuery('price-filter');

		return compact(
			'specialization',
			'service',
			'stations',
			'speciality',
			'price'
		);
	}

	/**
	 * Возвращает ссылку для связывания анкеты с мастером
	 *
	 * @param LfMaster $model
	 *
	 * @return string
	 */
	public function getAppointmentLink($model)
	{
		$appointmentId = Yii::app()->request->getQuery("appointment_id");
		if (empty($appointmentId)) {
			return null;
		}

		return CHtml::link(
			'<i class="fa fa-link"></i> Связать',
			["/admin/appointment/linkByMaster", 'appointment_id' => $appointmentId, 'master_id' => $model->id],
			['class' => 'btn btn-primary btn-xs']
		);
	}
} 