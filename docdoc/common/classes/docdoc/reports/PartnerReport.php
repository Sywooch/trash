<?php

namespace dfs\docdoc\reports;

use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\objects\google\requests\Partners;
use CActiveDataProvider;
use CDataProviderIterator;

/**
 * Class PartnerReport
 * @package dfs\docdoc\reports
 */
class PartnerReport extends BigQueryReport
{
	/**
	 * Получение модели
	 *
	 * @return \dfs\docdoc\objects\google\BigQuery|Partners
	 */
	public function getBqModel()
	{
		return new Partners();
	}

	/**
	 * Генерация отчета
	 *
	 * @return array
	 */
	public function generate()
	{
		$dataProvider = new CActiveDataProvider(PartnerModel::class);
		$partnersIterator = new CDataProviderIterator($dataProvider, 1000);

		foreach ($partnersIterator as $p) {
			$row = [
				'id'    => $p->id,
				'name'  => $p->name,
			];

			$this->addData($row);
		}
	}

}