<?php
class LfHtml extends CHtml
{

	public static function radioButtonList($name, $select, $data, $htmlOptions = array())
	{
		$separator = isset($htmlOptions['separator']) ? $htmlOptions['separator'] : "\n";
		unset($htmlOptions['template'], $htmlOptions['separator'], $htmlOptions['container']);
		unset($htmlOptions['labelOptions']);

		$items = array();
		$baseID = self::getIdByName($name);
		$id = 0;
		foreach ($data as $value => $label) {
			$checked = !strcmp($value, $select);
			$htmlOptions['value'] = $value;
			$htmlOptions['id'] = $baseID . '_' . $id++;

			$items[] =
				'<span class="form-inp_check form-inp_radio" data-check-id="' . $htmlOptions['id'] . '">'
				. '<i id="i-check_' . $htmlOptions['id'] . '" class="png' . ($checked ? ' checked' : '') . '"></i>'
				. '<input type="radio" autocomplete="off" id="inp-check_' . $htmlOptions['id'] . '" value="' .
				$htmlOptions['value'] .
				'" name="' .
				$name .
				'" ' .
				($checked ? 'checked="checked"' : '') .
				' />'
				.
				$label .
				'</span>';
		}

		return '<div class="form-group_radio">' . implode($separator, $items) . '</div>';
	}

	public static function activeRadioButtonList($model, $attribute, $data, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		$selection = self::resolveValue($model, $attribute);
		if (is_object($selection)) {
			$selection = $selection->id;
		}
		if ($model->hasErrors($attribute)) {
			self::addErrorCss($htmlOptions);
		}
		$name = $htmlOptions['name'];
		unset($htmlOptions['name']);

		if (array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		} else {
			$uncheck = '';
		}

		$hiddenOptions =
			isset($htmlOptions['id']) ? array('id' => self::ID_PREFIX . $htmlOptions['id']) : array('id' => false);
		$hidden = $uncheck !== null ? self::hiddenField($name, $uncheck, $hiddenOptions) : '';

		return $hidden . self::radioButtonList($name, $selection, $data, $htmlOptions);
	}

	public static function checkBox($name, $checked = false, $htmlOptions = array())
	{
		return
			'<span class="form-inp_check" data-check-id="' . $htmlOptions['id'] . '">'
			. '<i id="i-check_' . $htmlOptions['id'] . '" class="png"></i>'
			. '<input type="checkbox" autocomplete="off" id="inp-check_' .
			$htmlOptions['id'] .
			'" name="' .
			$name .
			'" value="' .
			$htmlOptions['value'] .
			'" ' .
			($checked ? 'checked="checked"' : '') .
			' />' .
			(empty($htmlOptions['label']) ? '' : $htmlOptions['label']) .
			'</span>';
	}

	public static function activeCheckBox($model, $attribute, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		if (!isset($htmlOptions['value'])) {
			$htmlOptions['value'] = 1;
		}
		if (!isset($htmlOptions['checked']) && self::resolveValue($model, $attribute) == $htmlOptions['value']) {
			$htmlOptions['checked'] = 'checked';
		}

		return self::checkBox($htmlOptions['name'], !empty($htmlOptions['checked']), $htmlOptions);
	}

	public static function checkBoxList($name, $select, $data, $htmlOptions = array())
	{
		$template = isset($htmlOptions['template']) ? $htmlOptions['template'] : '{input} {label}';
		$separator = isset($htmlOptions['separator']) ? $htmlOptions['separator'] : "\n";
		$container = isset($htmlOptions['container']) ? $htmlOptions['container'] : 'span';
		unset($htmlOptions['template'], $htmlOptions['separator'], $htmlOptions['container']);

		if (substr($name, -2) !== '[]') {
			$name .= '[]';
		}

		if (isset($htmlOptions['checkAll'])) {
			$checkAllLabel = $htmlOptions['checkAll'];
			$checkAllLast = isset($htmlOptions['checkAllLast']) && $htmlOptions['checkAllLast'];
		}
		unset($htmlOptions['checkAll'], $htmlOptions['checkAllLast']);

		$labelOptions = isset($htmlOptions['labelOptions']) ? $htmlOptions['labelOptions'] : array();
		unset($htmlOptions['labelOptions']);

		$items = array();
		$baseID = self::getIdByName($name);
		$id = 0;
		$checkAll = true;

		foreach ($data as $value => $label) {
			$checked = !is_array($select) && !strcmp($value, $select) || is_array($select) && in_array($value, $select);
			$checkAll = $checkAll && $checked;
			$htmlOptions['label'] = $label;
			$htmlOptions['value'] = $value;
			$htmlOptions['id'] = $baseID . '_' . $id++;
			//$option=self::checkBox($name,$checked,$htmlOptions);
			//$label=self::label($label,$htmlOptions['id'],$labelOptions);

			$items[] =
				'<div class="prof-check-work">'
				. self::checkBox($name, $checked, $htmlOptions)
				. '</div>';/*'<div class="prof-check-work">'
				.'<span class="form-inp_check" data-check-id="'.$htmlOptions['id'].'">'
				.'<i id="i-check_'.$htmlOptions['id'].'" class="png"></i>'
				.'<input type="checkbox" autocomplete="off" id="inp-check_'.$htmlOptions['id'].'" name="'.$name.'" value="'.$value.'" '.($checked ? 'checked="checked"' : '').' />'.$label.'</span>'
				.'</div>';
			
				/*'<span class="form-inp_check form-inp_radio" data-check-id="'.$htmlOptions['id'].'">'
				.'<i id="i-check_'.$htmlOptions['id'].'" class="png'.($checked ? 'act' : '').'"></i>'
				.'<input type="radio" autocomplete="off" id="inp-check_'.$htmlOptions['id'].'" value="'.$htmlOptions['value'].'" name="'.$name.'" '.($checked ? 'checked="checked"' : '').' />'
				.$label.'</span>'*/;
		}

		return implode($separator, $items);
	}

	public static function activeCheckBoxList($model, $attribute, $data, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		$selection = self::resolveValue($model, $attribute);
		if ($model->hasErrors($attribute)) {
			self::addErrorCss($htmlOptions);
		}
		$name = $htmlOptions['name'];
		unset($htmlOptions['name']);

		if (array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		} else {
			$uncheck = '';
		}

		$hiddenOptions =
			isset($htmlOptions['id']) ? array('id' => self::ID_PREFIX . $htmlOptions['id']) : array('id' => false);
		$hidden = $uncheck !== null ? self::hiddenField($name, $uncheck, $hiddenOptions) : '';

		return $hidden . self::checkBoxList($name, $selection, $data, $htmlOptions);
	}

	/**
	 * Получает HTML-код выпадающего списка
	 *
	 * @param string $name        название поля, например specialization_id
	 * @param int    $select      активный элемент из выпадающего списка
	 * @param array  $data        элементы списка
	 * @param array  $htmlOptions атрибуты html
	 *
	 * @return string
	 */
	public static function dropDownList($name, $select, $data, $htmlOptions = array())
	{
		$id = self::getIdByName($name);
		if (isset($htmlOptions["addition"])) {
			$id .= $htmlOptions["addition"];
		}
		$inner =
			'<input type="hidden" id="inp-select-popup-' . $id . '" value="' . ($select) . '" name="' .
			$htmlOptions['name'] .
			'" />'
			.
			'<div class="form-select-over" data-select-popup-id="select-popup-' .
			$id .
			'"></div>'
			.
			'<div class="form-select" id="cur-select-popup-' .
			$id .
			'">' .
			(isset($data[$select]) ? $data[$select] : '') .
			'</div><div class="form-select-arr png"></div>'
			.
			'<div class="form-select-popup" id="select-popup-' .
			$id .
			'">'
			.
			'<div class="form-select-popup-long">';

		foreach ($data as $value => $label) {
			$inner .=
				'<span class="item ' .
				($value == $select ? 'act' : '') .
				'" data-value="' .
				CHtml::encode($value) .
				'">' .
				$label .
				'</span>';
		}

		$inner .=
			'</div></div>';

		unset($htmlOptions['name'], $htmlOptions['id']);
		$htmlOptions['class'] = (!empty($htmlOptions['class']) ? $htmlOptions['class'] : '') . ' form-inp';

		return self::tag('div', $htmlOptions, $inner);
	}

	/**
	 * Получает HTML-код активного выпадающего списка
	 *
	 * @param object $model          модель
	 * @param string $attribute      атрибут модели
	 * @param array  $data           элементы списка
	 * @param array  $htmlOptions    атрибуты html
	 *
	 * @return string
	 */
	public static function activeDropDownList($model, $attribute, $data, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		$selection = self::resolveValue($model, $attribute);
		$options = "\n" . self::listOptions($selection, $data, $htmlOptions);
		self::clientChange('change', $htmlOptions);
		if ($model->hasErrors($attribute)) {
			self::addErrorCss($htmlOptions);
		}
		if (isset($htmlOptions['multiple'])) {
			if (substr($htmlOptions['name'], -2) !== '[]') {
				$htmlOptions['name'] .= '[]';
			}
		}
		return self::dropDownList($attribute, $selection, $data, $htmlOptions);
	}

	public static function activeTextField($model, $attribute, $htmlOptions = array())
	{
		if ($model->hasErrors($attribute)) {
			self::addErrorCss($htmlOptions);
		}
		return
			'<div class="form-inp ' .
			(!empty($htmlOptions['class']) ? $htmlOptions['class'] : '') .
			'">' .
			parent::activeTextField($model, $attribute, $htmlOptions) .
			'</div>';
	}

	public static function activeTextArea($model, $attribute, $htmlOptions = array())
	{
		if ($model->hasErrors($attribute)) {
			self::addErrorCss($htmlOptions);
		}
		return
			'<div class="form-inp ' .
			(!empty($htmlOptions['class']) ? $htmlOptions['class'] : '') .
			'">' .
			parent::activeTextArea($model, $attribute, $htmlOptions) .
			'</div>';
	}

	public static function activePasswordField($model, $attribute, $htmlOptions = array())
	{
		if ($model->hasErrors($attribute)) {
			self::addErrorCss($htmlOptions);
		}
		return
			'<div class="form-inp ' .
			(!empty($htmlOptions['class']) ? $htmlOptions['class'] : '') .
			'">' .
			parent::activePasswordField($model, $attribute, $htmlOptions) .
			'</div>';
	}

	/**
	 * Получает html-код лоадера
	 *
	 * @param string $class название класса-вспомогателя
	 *
	 * @return string
	 */
	public static function loader($class = null)
	{
		if ($class) {
			$class = " loader-{$class}";
		}
		return "
			<div class=\"loader{$class}\">
				<div class=\"f_circleG frotateG_01\"></div>
				<div class=\"f_circleG frotateG_02\"></div>
				<div class=\"f_circleG frotateG_03\"></div>
				<div class=\"f_circleG frotateG_04\"></div>
				<div class=\"f_circleG frotateG_05\"></div>
				<div class=\"f_circleG frotateG_06\"></div>
				<div class=\"f_circleG frotateG_07\"></div>
				<div class=\"f_circleG frotateG_08\"></div>
			</div>
		";
	}
}