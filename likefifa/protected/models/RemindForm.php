<?php

class RemindForm extends CFormModel
{
	public $email = null;
	
	protected $master = null;
	protected $salon = null;

	public function rules() {
		return array(
			array('email', 'required'),
			array('email', 'checkUser'),
		);
	}

	public function attributeLabels(){
		return array();
	}

	public function checkUser($attribute,$params)
	{
		if ($this->hasErrors()) return;

		$this->master = LfMaster::model()->findByEmail($this->email);
		$this->salon = LfSalon::model()->findByEmail($this->email);
		if (!$this->master && !$this->salon) {
			$this->addError($attribute, '<br>Такой e-mail не зарегистрирован в нашей базе. Проверьте правильность адреса.');
		}
	}
	
	public function createRemind() {
		if (!$this->master && !$this->salon) return false;
		
		if($this->master)
		LfRemind::model()->
			createForUser($this->master, 'master')->
			notify();
		if($this->salon)
			LfRemind::model()->
			createForUser($this->salon, 'salon')->
			notify();
		
		return true;
	}
	
}
