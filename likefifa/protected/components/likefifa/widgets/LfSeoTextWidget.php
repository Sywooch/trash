<?php
class LfSeoTextWidget extends CWidget {
	
	public $seoText			= null;
	
	public function run() {
		$this->render('LfSeoTextWidget', array(
			'seoText' => $this->seoText,
		));
	}

}