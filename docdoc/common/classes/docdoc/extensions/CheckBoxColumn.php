<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 30.10.14
 * Time: 19:59
 */

namespace dfs\docdoc\extensions;

use CHtml;

class CheckBoxColumn extends \CCheckBoxColumn
{

	/**
	 * @var mixed the HTML code representing a filter input (eg a text field, a dropdown list)
	 * that is used for this data column. This property is effective only when
	 * {@link CGridView::filter} is set.
	 * If this property is not set, a text field will be generated as the filter input;
	 * If this property is an array, a dropdown list will be generated that uses this property value as
	 * the list options.
	 * If you don't want a filter for this data column, set this value to false.
	 * @since 1.1.1
	 */
	public $filter;

	/**
	 * Renders the filter cell content.
	 * This method will render the {@link filter} as is if it is a string.
	 * If {@link filter} is an array, it is assumed to be a list of options, and a dropdown selector will be rendered.
	 * Otherwise if {@link filter} is not false, a text field is rendered.
	 * @since 1.1.1
	 */
	protected function renderFilterCellContent()
	{
		if (is_string($this->filter))
			echo $this->filter;
		elseif ($this->filter !== false && $this->grid->filter !== null && $this->name !== null && strpos($this->name, '.') === false) {
			if (is_array($this->filter))
				echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, ['id' => false, 'prompt' => '']);
			elseif ($this->filter === null)
				echo CHtml::activeTextField($this->grid->filter, $this->name, ['id' => false]);
		} else
			parent::renderFilterCellContent();
	}

	/**
	 * Renders the header cell content.
	 * This method will render a link that can trigger the sorting if the column is sortable.
	 */
	protected function renderHeaderCellContent()
	{
		if ($this->name !== null && $this->header === null) {
			if ($this->grid->dataProvider instanceof \CActiveDataProvider)
				echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
			else
				echo CHtml::encode($this->name);
		} else {
			parent::renderHeaderCellContent();
		}
	}

} 
