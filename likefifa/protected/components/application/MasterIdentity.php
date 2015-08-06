<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class MasterIdentity extends CUserIdentity
{
	private $_id;
	public $userType = null;
    
	public function authenticate()
    {
        $username = strtolower($this->username);
        $number = preg_replace('/\D+/', '', $username);
        if (su::startsWith($number, '8')) {
        	$number = '7'.su::substr($number, 1);
        }
        
        $user = 
        	$number
        		? LfMaster::model()->find('email=? OR phone_numeric=?', array($username, $number))
        		: LfMaster::model()->find('email=?', array($username));
        
        $this->userType = 'master';
        if ($user === null) {
        	$user = LfSalon::model()->find('email=?', array($username));
        	$this->userType = 'salon';
        }
        if ($user === null){
        	$this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        else if (!$user->validatePassword($this->password))
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        else {
            $this->_id = $user->id;
            $this->username = $user->email;
            $this->errorCode = self::ERROR_NONE;
        }

        Yii::log('Logging '.$this->userType.' with '.$this->username.' : '.$this->password.' from '.$_SERVER['REMOTE_ADDR'].' completed with status '.$this->errorCode);

        return $this->errorCode == self::ERROR_NONE;
    }
    
    public function getId()
    {
        return $this->_id;
    }
}