<?php
class LfLkAppTabsWidget extends CWidget {
	
	public $tabs			= array(
		'new' 	=> array('title' => 'Новые', 'url' => '/lk/appointment/new'),
		'apply'		=> array('title' => 'Принятые', 'url' => '/lk/appointment/apply'),
		'cancel' 	=> array('title' => 'Отклоненные', 'url' => '/lk/appointment/cancel'),
		'completed'		=> array('title' => 'Завершенные', 'url' => '/lk/appointment/completed'),
	);
	public $currentTab		= null;
	public $itemsCount 		= array();
	
	public function run() {
		$this->render('LfLkAppTabsWidget', array(
			'tabs'			=> $this->tabs,
			'currentTab'	=> $this->currentTab,
			'itemsCount' 	=> $this->itemsCount,	
		));
	}

}