<?php

namespace controllers;

use components\Controller;

/**
 * Class SiteController
 *
 * @package controllers
 */
class SiteController extends Controller
{

	/**
	 * View dir
	 *
	 * @var string
	 */
	protected $viewsDir = "site";

	/**
	 * Displays sites main page
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$this->render("index");
	}
}