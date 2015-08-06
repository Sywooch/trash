<?php
/**
 * ButtonColumn class file.
 * Extends {@link CButtonColumn}
 *
 * Allows additional evaluation of ID in options.
 *
 * @version $Id$
 *
 */
class LfButtonColumn extends CButtonColumn
{
	/**
	 * @var boolean whether the ID in the button options should be evaluated.
	 */
	public $optionsData = false;

	/**
	 * Renders the button cell content.
	 * This method renders the view, update and delete buttons in the data cell.
	 * Overrides the method 'renderDataCellContent()' of the class CButtonColumn
	 * @param integer $row the row number (zero-based)
	 * @param mixed $data the data associated with the row
	 */
	public function renderDataCellContent($row, $data)
	{
		$tr=array();
		ob_start();
		foreach($this->buttons as $id=>$button)
		{
			if($this->optionsData and isset($button['options']['data-id']))
			{
				$button['options']['data-id'] = $this->evaluateExpression($button['options']['data-id'], array('row'=>$row,'data'=>$data));
			}

			if ($this->optionsData and isset($button['options']['data-service_price'])) {
				$button['options']['data-service_price'] =
					$this->evaluateExpression(
						$button['options']['data-service_price'],
						array('row' => $row, 'data' => $data)
					);
			}

			$this->renderButton($id,$button,$row,$data);
			$tr['{'.$id.'}']=ob_get_contents();
			ob_clean();
		}
		ob_end_clean();
		echo strtr($this->template,$tr);
	}
	
	public function renderDataCell($row)
	{
		$data=$this->grid->dataProvider->data[$row];
		$options = array();
		if($this->optionsData) {
			foreach($this->htmlOptions as $key=>$value) {
				$options[$key] = $this->evaluateExpression($value,array('row'=>$row,'data'=>$data));
			}
		}
		else $options=$this->htmlOptions;
		if($this->cssClassExpression!==null)
		{
			$class=$this->evaluateExpression($this->cssClassExpression,array('row'=>$row,'data'=>$data));
			if(isset($options['class']))
				$options['class'].=' '.$class;
			else
				$options['class']=$class;
		}
		echo CHtml::openTag('td',$options);
		$this->renderDataCellContent($row,$data);
		echo '</td>';
	}
}