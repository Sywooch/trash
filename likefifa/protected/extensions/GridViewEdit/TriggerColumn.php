<?php
class TriggerColumn extends CDataColumn {
	
	protected static $scriptPublished = false;
	
	public $url = null;
	public $param = null;
	
	public function init() {
		parent::init();
		
		if (!self::$scriptPublished) {
			Yii::app()->clientScript->
				registerScriptFile(
					CHtml::asset(dirname(__FILE__).'/js/GridViewEdit.js'), 
					CClientScript::POS_END
				);
				
			self::$scriptPublished = true;
		}
	}
	
	protected function renderFilterCellContent()
	{
		echo CHtml::activeDropDownList(
			$this->grid->filter, 
			$this->name, 
			array('нет', 'да'), 
			array('id'=>false,'prompt'=>'')
		);
	}

	protected function renderDataCellContent($row,$data)
	{
		echo CHtml::checkBox(
			'', 
			$data->{$this->name} ? true : false, 
			array(
				'data-grid-id'	=> $this->grid->id,
				'data-param' 	=> $this->param ?: $this->name, 
				'data-url' 		=> $this->evaluateExpression($this->url, compact('row', 'data')), 
				'class' 		=> 'trigger-column',
			)
		);
	}

}