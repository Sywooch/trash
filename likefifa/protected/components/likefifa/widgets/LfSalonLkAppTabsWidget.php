<?php
class LfSalonLkAppTabsWidget extends CWidget {
	
	public $tabs			= array(
		'new' 	=> array('title' => 'Новые', 'url' => '/salonlk/appointment/new'),
		'apply'		=> array('title' => 'Принятые', 'url' => '/salonlk/appointment/apply'),
		'cancel' 	=> array('title' => 'Отклоненные', 'url' => '/salonlk/appointment/cancel'),
		'completed'		=> array('title' => 'Завершенные', 'url' => '/salonlk/appointment/completed'),
	);
	public $currentTab		= null;
	public $itemsCount 		= array();
	
	public function run() {
		$this->render('LfSalonLkAppTabsWidget', array(
			'tabs'			=> $this->tabs,
			'currentTab'	=> $this->currentTab,
			'itemsCount' 	=> $this->itemsCount,
		));
	}

}