<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 02.12.14
 * Time: 16:16
 */

namespace dfs\docdoc\front\widgets\partner;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ContractModel;

/**
 * Class Modal
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 */
class Button extends PartnerWidget
{
	/**
	 * имя виджета
	 * @var string
	 */
	public $name = 'Button';

	/**
	 * @var int
	 */
	public $clinicId = null;

	/**
	 * @var int
	 */
	public $doctorId = null;

	/**
	 * Запись на диагностику
	 *
	 * @var null
	 */
	public $diagnosticId = null;

	/**
	 * ID специальности
	 *
	 * @var null
	 */
	public $specialityId = null;

	/**
	 * Наблон модального окна
	 *
	 * @var null
	 */
	public $modalTemplate = 'Modal';

	/**
	 * Действия при загрузке виджетов
	 *
	 */
	public function loadWidget()
	{
		if ($this->type == self::TYPE_DIAGNOSTIC && $this->allowOnline) {
			$clinic = ClinicModel::model()->findByPk($this->clinicId);
			if ($clinic && $clinic->getClinicContract(ContractModel::TYPE_DIAGNOSTIC_ONLINE)) {
				$this->modalTemplate = 'Online';
			} else {
				$this->modalTemplate = null;
			}
		}
		//если не нужно показывать модальное окно, то и кнопку не показываем
		if (is_null($this->modalTemplate)) {
			$this->template = null;
		}
	}

	/**
	 * Получение JSON с параметрами вызываемого виджета
	 *
	 * @return string
	 */
	public function getDataWidget()
	{
		$data = [
			'widget' => 'Modal',
			'template' => $this->modalTemplate,
			'id' => $this->modalTemplate . $this->type,
			'action' => 'LoadWidget',
			'clinicId' => $this->clinicId,
			'doctorId' => $this->doctorId,
			'diagnosticId' => $this->diagnosticId,
			'type' => $this->type,
			'specialities' => $this->specialities,
			'specialityId' => $this->specialityId,
		];

		return json_encode($data);
	}
}