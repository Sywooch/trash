<?php
namespace dfs\docdoc\back\widgets;

use dfs\docdoc\models\DiagnosticaModel;

/**
 * Class DiagnosticsFormWidget
 */
class DiagnosticsFormWidget extends \CWidget
{

	/**
	 * @var array;
	 */
	public $selectedItems;

	/**
	 * Запуск виджета формы выбора диагностики
	 *
	 * @throws \CException
	 */
	public function run()
	{
		$diagnostics = DiagnosticaModel::model()
			->onlyParents()
			->findAll();

		$this->render('diagnosticsForm', array(
			'diagnostics' => $diagnostics,
		));
	}

}