<?php

class LfSalonLkTabsWidget extends CWidget
{

	protected $tabs = array();
	public $currentTab = null;

	public function run()
	{
		$this->tabs = array(
			'profile'      => array('title' => 'Страница салона', 'url' => '/salonlk/index'),
			'masters'      => array('title' => 'Мастера', 'url' => '/salonlk/masters'),
			'appointment' => array('title' => 'Заказы', 'url' => '/salonlk/appointment'),
			'rules'        => array('title' => 'Правила сотрудничества', 'url' => '/salonlk/rules'),
		);

		$this->render(
			'LfSalonLkTabsWidget',
			array(
				'tabs'       => $this->tabs,
				'currentTab' => $this->currentTab,
			)
		);
	}

}