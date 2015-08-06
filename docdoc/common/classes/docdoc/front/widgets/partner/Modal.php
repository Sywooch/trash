<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 02.12.14
 * Time: 16:16
 */

namespace dfs\docdoc\front\widgets\partner;

/**
 * Class Modal
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 */
class Modal extends PartnerWidget
{
	/**
	 * имя виджета
	 * @var string
	 */
	public $name = 'Modal';

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
	 * Врачи определенной специальности
	 *
	 * @var null
	 */
	public $specialityId = null;


	/**
	 * действия при загрузке виджета
	 */
	public function loadWidget()
	{

	}

	/**
	 * Текстовое поле с именем клинета
	 *
	 * @return string
	 */
	public function getNameTextField()
	{
		return \CHtml::textField('dd-name', null, array("class" => "dd-sign-up-popup-form-input"));
	}

	/**
	 * Текстовое поле с именем телефона
	 *
	 * @return string
	 */
	public function getPhoneTextField()
	{
		return \CHtml::textField('dd-phone', null, array("class" => "dd-sign-up-popup-form-input"));
	}

	/**
	 * Текущий хост виджета
	 *
	 * @return string
	 */
	public function getHost()
	{
		return $this->type == self::TYPE_DOCTOR ? \Yii::app()->params['hosts']['front'] : \Yii::app()->params['hosts']['diagnostica'];
	}

	/**
	 * Урл для фрейма
	 *
	 * @return string
	 */
	public function getFrameUrl()
	{
		if ($this->type == self::TYPE_DOCTOR) {
			$link = '/request/form';
			$params = [
				'clinic' => $this->clinicId,
				'doctor' => $this->doctorId,
				'speciality' => $this->specialityId,
				'pid' => $this->partner->id,
			];
		} else {
			$link = '/request/requestForm/';
			$params = [
				'clinicId' => $this->clinicId,
				'pid' => $this->partner->id,
				'diagnosticId' => $this->diagnosticId,
				'specialities' => is_array($this->specialities) ? implode(',', $this->specialities) : '',
			];
		}

		return 'http://' . $this->getHost() . $link . '?' . http_build_query($params);
	}

	/**
	 * Получает ширину контейнера
	 *
	 * @return int
	 */
	public function getWidth()
	{
		return $this->type == "Doctor" ? 745 : 732;
	}

	/**
	 * Получает высоту контейнера
	 *
	 * @return int
	 */
	public function getHeight()
	{
		$height = 475;

		if ($this->type == "Doctor") {
			$height += 135;
		}

		return $height;
	}
}