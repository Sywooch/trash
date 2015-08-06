<?php

abstract class BackendController extends CController
{
	public $menu=array();
	public $breadcrumbs=array();
	public $layout='//layouts/admin';
	
	
	public function beforeAction($action) {
		Yii::app()->session->open();
		return true;
	}

	public function filters()
	{
		return array(
			'accessControl',
		);
	}
	
	public function accessRules()
	{
		return array(
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions' => array('admin', 'index', 'view', 'create', 'update', 'delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
}