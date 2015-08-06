<?php

namespace likefifa\controllers;

use CException;
use CHttpException;
use CJSON;
use LfMaster;
use LfPrice;
use LfSalon;
use LfService;
use LfSpecialization;
use LfWork;
use likefifa\components\helpers\SphinxHelper;
use su;
use UndergroundLine;
use Yii;
use FrontendController;
use UndergroundStation;

/**
 * Class AjaxController
 *
 * @package likefifa\controllers
 */
class AjaxController extends FrontendController
{
	/**
	 * Фильтры
	 * Добавляет кэширование списков садджество
	 *
	 * @return array
	 */
	public function filters()
	{
		return array(
			array(
				'COutputCache + metroSuggest, specSuggest',
				'duration'    => Yii::app()->params["cacheTime"],
				'varyByParam' => array($_SERVER["REQUEST_URI"]),
			),
		);
	}

	/**
	 * Возвращает список садджестов для станций метро (поиск на главной)
	 *
	 * @param null $term
	 * @param null $id
	 */
	public function actionMetroSuggest($term = null, $id = null)
	{
		$data = Yii::app()->db->createCommand()
			->select('t.id, t.name, l.color')
			->from(UndergroundStation::model()->tableName() . ' as t')
			->join(UndergroundLine::model()->tableName() . ' as l', 'l.id = t.underground_line_id');

		if ($term != null) {
			$data->where(['like', 't.name', '%' . $term . '%']);
		}

		if ($id != null) {
			$data->where(['in', 't.id', explode(',', $id)]);
		}

		$data = $data->order('t.name ASC')
			->queryAll();
		foreach ($data as &$d) {
			$d['text'] = '<span style="color: #' . $d['color'] . '">' . $d['name'] . '</span>';
		}
		$this->sendJson($data);
	}

	/**
	 * Возвращает список садджестов по специализациям
	 *
	 * @param string $search
	 */
	public function actionSpecSuggest($search)
	{
		$search = SphinxHelper::clearQuery(trim($search));
		$items = [];
		if (empty($search)) {
			$this->sendJson($items);
		}

		$func = function ($search) {
			return Yii::app()->search->select('*')->from('specsAndServices')->where('*' . $search . '*')
				->rankingMode(SPH_MATCH_EXTENDED2)
				->orderby('@weight desc')
				->limit(0, 10)
				->searchRaw();
		};

		$results = $func($search);
		if ($results['total_found'] == 0) {
			$search = SphinxHelper::suggest($search, $results['words'], 'specSuggest');
			$results = $func($search);
		}
		foreach ($results['matches'] as $match) {
			$data = $match['attrs'];
			$model = null;
			if ($data['type'] == 'spec') {
				$model = LfSpecialization::model()->findByPk($data['entity_id']);
			} elseif ($data['type'] == 'service') {
				$model = LfService::model()->findByPk($data['entity_id']);
			}
			if ($model != null) {
				$items[su::lcfirst($model->name)] = [
					'id'   => [
						$model instanceof LfSpecialization ? $model->id : $model->specialization_id,
						$model instanceof LfService ? $model->id : null,
					],
					'text' => su::lcfirst($model->name),
					'url'  => $model->getSearchUrl(),
				];
			}
		}

		$this->sendJson(array_values($items));
	}

	public function actionWorkCounter()
	{
		$id = Yii::app()->request->getPost('id');
		$model = LfWork::model()->findByPk($id);
		if ($model == null) {
			throw new CHttpException(404);
		}
		$model->saveCounters(['click_count' => 1]);
	}

	/**
	 * Возвращает все работы, отфильтрованные по специализации и услуге
	 *
	 * @param integer $master_id
	 * @param integer $salon_id
	 * @param integer $spec_id
	 * @param integer $service_id
	 * @param integer $view_count
	 *
	 * @throws CHttpException
	 * @throws CException
	 */
	public function actionFilteredWorks(
		$master_id = null,
		$salon_id = null,
		$spec_id = null,
		$service_id = null,
		$view_count = 3
	)
	{
		$specialization = LfSpecialization::model()->findByPk($spec_id);
		$service = LfService::model()->findByPk($service_id);

		$model = $view = null;
		if ($master_id) {
			$model = LfMaster::model()->findByPk($master_id);
			$view = '//masters/partials/_view_works';
		} elseif ($salon_id) {
			$model = LfSalon::model()->findByPk($salon_id);
			$view = '//salons/_view_works';
		}

		if (!$model) {
			throw new CHttpException(404);
		}

		$works = $model->getFilteredWorks($specialization, $service, true);

		$this->renderPartial(
			$view,
			[
				'data'           => $model,
				'works'          => $works,
				'specialization' => $specialization,
				'service'        => $service,
				'all'            => true,
				'count'          => $view_count,
			]
		);
	}

	/**
	 * Подгружает цены на услуги для списка мастеров и салонов
	 *
	 * @param integer $id
	 * @param integer $type
	 * @param integer $service_id
	 * @param integer $spec_id
	 *
	 * @throws CHttpException
	 * @throws CException
	 */
	public function actionGetPricesList($id, $type, $service_id = null, $spec_id = null)
	{
		$specialization = LfSpecialization::model()->findByPk($spec_id);
		$service = LfService::model()->findByPk($service_id);

		$model = $view = null;
		if ($type == 'master') {
			$model = LfMaster::model()->findByPk($id);
		} elseif ($type == 'salon') {
			$model = LfSalon::model()->findByPk($id);
		}

		if (!$model) {
			throw new CHttpException(404);
		}

		$prices =
			LfPrice::model()->getPrices(
				$type == 'master' ? $model : null,
				$type == 'salon' ? $model : null,
				$specialization,
				$service,
				true
			);
		$this->renderPartial(
			'//partials/_prices',
			[
				'data'           => $model,
				'prices'         => $prices,
				'all'            => true,
				'specialization' => $specialization,
				'service'        => $specialization
			]
		);
	}

	/**
	 * Выводит array => json данные
	 *
	 * @param $data
	 */
	private function sendJson($data)
	{
		echo CJSON::encode($data);
		Yii::app()->end();
	}
} 