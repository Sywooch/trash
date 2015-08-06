<?php
namespace dfs\docdoc\back\widgets;


/**
 * Class DiagnosticsFormWidget
 */
class ClinicTariffWidget extends \CWidget
{

	public $dateFrom = "";
	public $dateTo = "";
	public $clinic = null;

	/**
	 * Запуск виджета формы выбора диагностики
	 *
	 * @throws \CException
	 */
	public function run()
	{
		$this->render('clinicTariffs', array(
				'clinic'  => $this->clinic,
				'dateFrom' => $this->dateFrom,
				'dateTo'   => $this->dateTo,
		));
	}

}