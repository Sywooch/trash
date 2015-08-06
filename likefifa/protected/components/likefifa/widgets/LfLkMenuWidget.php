<?php
class LfLkMenuWidget extends CWidget {
	
	public $actions			= array();
	public $currentAction	= null;
	public $model			= null;
	
	public function run() {
		$this->render('LfLkMenuWidget', array(
			'actions'		=> $this->actions,
			'currentAction'	=> $this->currentAction,
			'model'			=> $this->model,
		));
	}

}