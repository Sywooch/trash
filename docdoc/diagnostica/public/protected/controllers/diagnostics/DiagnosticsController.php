<?php

use dfs\docdoc\models\DiagnosticClinicModel;


class DiagnosticsController extends FrontendController
{
	public function actionIndex() {
		$this->render('index', array('diagnostics'=>$this->diagnostics));
	}

	/**
	 * Получение диагностик для клиники
	 *
	 * @param $clinicId
	 */
	public function actionListForClinic($clinicId) {
		$diagnostics = [];
		$parentIds = [];
		$allParents = [];

		/**
		 * @var DiagnosticClinicModel[] $items
		 */
		$items = [];

		$specialities = [];
		$st = Yii::app()->request->getQuery('specialities');
		if ($st) {
			foreach (explode(',', $st) as $id) {
				$specialities[$id] = true;
			}
		}

		$data = DiagnosticClinicModel::model()
			->cache(3600)
			->byClinic($clinicId)
			->findAll();

		foreach ($data as $item) {
			$items[$item->diagnostica_id] = $item;
		}

		foreach ($this->diagnostics as $d) {
			if (!$d->parent_id) {
				$allParents[$d->id] = $d;
			}

			if ($specialities && !isset($specialities[$d->id]) && !isset($specialities[$d->parent_id])) {
				continue;
			}

			if (isset($items[$d->id])) {
				$item = $items[$d->id];

				$diagnostics[$d->id] = [
					'id'                => intval($d->id),
					'name'              => $d->name,
					'reduction_name'    => $d->reduction_name,
					'parent_id'         => intval($d->parent_id),
					'price'             => intval($item->price),
					'special_price'     => intval($item->special_price),
					"price_for_online"  => intval($item->price_for_online),
					"discount"          => $item->getDiscountForOnline(),
				];

				if ($d->parent_id) {
					$parentIds[$d->parent_id][$d->id] = true;
				}
				elseif (!isset($parentIds[$d->id])) {
					$parentIds[$d->id] = [];
				}
			}
		}

		foreach ($parentIds as $id => $childIds) {
			if (!isset($diagnostics[$id]) && isset($allParents[$id])) {
				$d = $allParents[$id];
				$diagnostics[$id] = [
					'id'             => intval($d->id),
					'name'           => $d->name,
					'reduction_name' => $d->reduction_name,
					'parent_id'      => 0,
				];
			}
			$diagnostics[$id]['childIds'] = array_keys($childIds);
		}

		$this->renderJson([
			'diagnostics' => $diagnostics,
			'parentIds' => array_keys($parentIds),
		]);
	}
}
