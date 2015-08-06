<?php
abstract class StatusEntity extends CActiveRecord {

	const STATUS_REGISTRATION 	= 1;
	const STATUS_NEW 			= 2;
	const STATUS_ACTIVE 		= 3;
	const STATUS_BLOCKED 		= 4;
	const STATUS_ARCHIVE 		= 5;
	
	public function getStatusListItems() {
		return $this->statusNames;
	}
	
	public function getStatusName() {
		return $this->statusNames[$this->status];
	}
	
	public function scopes() {
		return array(
			'onlyRegistration' => array(
				'condition' => 'status = '.self::STATUS_REGISTRATION,
			),
			
			'onlyNew' => array(
				'condition' => 'status = '.self::STATUS_NEW,
			),
			
			'onlyActive' => array(
				'condition' => 'status = '.self::STATUS_ACTIVE,
			),
			
			'yetInactive' => array(
				'condition' => '(status = '.self::STATUS_REGISTRATION.' OR status = '.self::STATUS_NEW.')',
			),
		);
	}

}