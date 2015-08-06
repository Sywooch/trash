<?php
class LfLkTabsWidget extends CWidget {

	public $tabs			= array(
		'profile'     => array('title' => 'Анкета', 'url' => '/lk/index'),
		'rules'       => array('title' => 'Правила сотрудничества', 'url' => '/lk/rules'),
		'appointment' => array('title' => 'Заказы', 'url' => '/lk/appointment'),
		'payments' => array('title' => 'Платежи', 'url' => '/lk/payments'),
	);
	public $currentTab		= null;

	public function run() {
		$this->render('LfLkTabsWidget', array(
			'tabs'			=> $this->tabs,
			'currentTab'	=> $this->currentTab,
		));
	}

}