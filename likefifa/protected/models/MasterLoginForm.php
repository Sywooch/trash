<?php
class MasterLoginForm extends CFormModel {
	
	public $email		= null;
	public $password	= null;
	
	protected $identity	= null;
	public $userType;
	
	public function rules() {
		return array(
			array('email, password', 'required'),
			array('password', 'authenticate')	
		);
	}
	
	public function attributeLabels(){
		return array();
	}
	
	public function authenticate($attribute, $params) {
		if (!$this->hasErrors()) {
			$this->identity = new MasterIdentity($this->email, $this->password);
			if (!$this->identity->authenticate()) {
				$this->addError($attribute, 'Wrong email or password');
			}
			else {
				$this->userType = $this->identity->userType;
			}
		}
		else {
			$this->password = null;
		}
	}
	
	public function login() {
		if (!$this->identity) return false;

		Yii::app()->user->login($this->identity, Yii::app()->session->timeout);
		Yii::app()->session->open();
		if($this->userType == 'master') Yii::app()->user->setState('masterLoggedInPublic', true);
		if($this->userType == 'salon') Yii::app()->user->setState('salonLoggedInPublic', true);
		return true;
	}
	

	
	
	
}