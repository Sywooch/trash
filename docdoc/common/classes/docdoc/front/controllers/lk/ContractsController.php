<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\models\ClinicContractCostModel;


/**
 * Class ContractsController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class ContractsController extends FrontController
{
	protected $_fields = [
		'contract' => [
			'label' => 'Тариф',
			'width' => 250,
		],
		'group' => [
			'label' => 'Специальность',
			'width' => 250,
		],
		'from_num' => [
			'label' => 'Кол-во пациентов',
			'width' => 45,
		],
		'cost' => [
			'label' => 'Стоимость',
			'width' => 45,
		],
	];

	protected $_columns = ['contract',  'group', 'from_num', 'cost'];

	/**
	 * Страница тарифов
	 */
	public function actionIndex()
	{
		$vars = [
			'tableConfig' => [
				'url' => '/lk/contracts/list',
				'dtDom'   => 'lfrtip',
				'fields' => $this->_fields,
				'columns' => $this->_columns,
			],
		];
		$this->render('index', $vars);
	}

	/**
	 * Получение списка тарифных ставок
	 */
	public function actionList()
	{
		$contracts = $this->_clinic->tariffs;

		$data = [];
		foreach ($contracts as $contract) {
			foreach ($contract->costRules as $item) {
				$data[] = $this->mappingContractCostList($item);
			}
		}

		$this->renderJSON([
			'data' => $data,
		]);
	}

	/**
	 * Тарифные ставки отправляемые в datatables
	 *
	 * @param ClinicContractCostModel $contractCost
	 *
	 * @return array
	 */
	protected function mappingContractCostList($contractCost)
	{
		$tariff = $contractCost->tariff;
		return [
			'contract' => $tariff->contract->title,
			'group' => $contractCost->contractGroup->name,
			'from_num'   => $contractCost->from_num,
			'cost' => $contractCost->cost,
		];
	}

}