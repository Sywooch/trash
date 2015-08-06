<?php
class LinkedItemsWidget extends CWidget {
	
	public $title 			= 'Связанные объекты';
	public $items 			= array();
	public $noLinkedItems 	= 'Нет связанных объектов';
	
	public function run() {
		$this->render('linkeditems', array(
			'title' 		=> $this->title,
			'items' 		=> $this->items,
			'noLinkedItems'	=> $this->noLinkedItems,
		));
	}

}