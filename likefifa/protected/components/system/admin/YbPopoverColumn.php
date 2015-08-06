<?php


namespace likefifa\components\system\admin;

use CHtml;
use CJavaScriptExpression;
use TbDataColumn;

/**
 * Рисует колонку грида с подсказкой
 *
 * Class YbPopoverColumn
 *
 * @package likefifa\components\system\admin
 */
class YbPopoverColumn extends TbDataColumn
{
	/**
	 * @var array $pickerOptions the javascript options for the picker bootstrap plugin. The picker bootstrap plugin
	 * extends from the tooltip plugin.
	 *
	 * Note that picker has also a 'width' just in case we display AJAX'ed content.
	 *
	 * @see http://getbootstrap.com/javascript/#popovers
	 */
	public $options = array();

	/**
	 * Renders a data cell content, wrapping the value with the link that will activate the picker
	 *
	 * @param int   $row
	 * @param mixed $data
	 */
	public function renderDataCellContent($row, $data)
	{

		$value = '';
		if ($this->value !== null) {
			$value = $this->evaluateExpression($this->value, array('data' => $data, 'row' => $row));
		} else {
			if ($this->name !== null) {
				$value = CHtml::value($data, $this->name);
			}
		}

		$htmlOptions = array('data-toggle' => 'popover');
		foreach ($this->options as $key => $val) {
			if ($this->isAValidOption($key)) {
				if ((!$val instanceof CJavaScriptExpression) && strpos($val, 'js:') === 0) {
					$val = new CJavaScriptExpression($val);
				} elseif ($key == 'content') {
					$val = $this->evaluateExpression($val, array('data' => $data, 'row' => $row));
				}

				$htmlOptions['data-' . $key] = $val;
			}
		}

		echo CHtml::link($value, "#", $htmlOptions);
	}

	protected function isAValidOption($option)
	{
		return in_array(
			$option,
			array(
				'animation',
				'html',
				'placement',
				'selector',
				'trigger',
				'title',
				'content',
				'delay',
				'container'
			)
		);
	}
} 