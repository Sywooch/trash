<?php

use dfs\docdoc\components\seo\SeoFactory,
	dfs\docdoc\components\seo\SEO,
	dfs\docdoc\components\seo\SeoException;



/**
 * Class page
 *
 * Содержит методы для генерации всех страниц сайта
 *
 * @author Danis
 */
class Page
{

	public $url = '';
	public $map = array();
	public $method = null;
	public $params = array();

	/**
	 * Конструктор
	 *
	 * устанавливает базовый урл страницы и сохраняет в объекте маппинг урлов
	 *
	 * @param string $url url страницы
	 * @param array $map массив $routesMap с правилами роутинга запросов
	 */
	public function __construct($url, $map)
	{
		$url_parts = parse_url($url);

		if (empty($url_parts['path'])) {
			$url_parts['path'] = "/";
		}

		$this->url = $url_parts['path'];
		$this->url = urldecode($this->url);
		$this->map = $map;

		$this->method = $this->getPageMethod();
	}

	public function getPageMethod()
	{
		foreach ($this->map as $route) {
			if (preg_match($route['pattern'], $this->url, $matches)) {
				$this->method = 'page' . $route['method'];
				foreach ($matches as $index => $value) {
					if ($route['aliases'][$index] != 'trash' && !empty($value))
						$this->params[$route['aliases'][$index]] = $value;
				}
				break;
			}
		}

		if (!empty($this->method)) {
			if (method_exists($this, $this->method)) {
				return $this->method;
			}
		}

		return null;
	}

	public function generate()
	{
		if (!empty($this->method)) {
			if (method_exists($this, $this->method))
				call_user_func(array('self', $this->method));
		} else {
			$this->pageError();
		}
	}

	public function pageError($message = 'Неправильный запрос')
	{
		$params['message'] = $message;
		$this->loadPage('404', $params);
		die;
	}

	/**
	 * Редирект на данный URL
	 *
	 * @param $url
	 */
	public function redirect($url)
	{
		header("Location: {$url}", true, 301);
		exit();
	}

	protected function pageIndex()
	{
		$this->loadPage('index');
	}

	protected function pageDoctor()
	{
		$params = $this->params;

		if (!isset($params['alias']) && isset($params['speciality']))
			$params['alias'] = $params['speciality'];

		if (isset($params['alias'])) {

			$sql = "SELECT
                        id AS Id, rewrite_name AS Alias, name AS Name
                    FROM sector
                    WHERE 
                        rewrite_name='" . $params['alias'] . "'";
			$result = query($sql);

			if (num_rows($result) == 0)
				$this->loadPage('doctorView', $params);
			else {
				$params['speciality'] = $params['alias'];
				if (isset($params['area']) || isset($params['stationAlias']) || isset($params['district']))
					$params['context'] = true;
				$this->loadPage('doctorList', $params);
			}
		} else
			$this->loadPage('doctorList', $params);
	}

	protected function pageContext()
	{

		$params = $this->params;
		$params['context'] = true;

		$this->loadPage('doctorList', $params);
	}

	/**
	 * Загрузка лэндинг-страницы
	 */
	protected function pageLanding()
	{

		$params = $this->params;
		$params['landing'] = true;

		$this->loadPage('doctorList', $params);
	}

	protected function pageContextSearch()
	{

		$params = $this->params;

		$this->loadPage('doctorList', $params);
	}

	protected function pageIllness()
	{
		$params = $this->params;

		if (isset($params['illness'])) {
			$this->loadPage('illnessText', $params, true);
		} else
			$this->loadPage('illness', $params, true);
	}

	protected function pageLibrary()
	{
		$params = $this->params;

		$articleExceptions = array(
			'disbakterioz-kishechnika',
			'mrt',
			'priznaki-disbakterioza-kishtchnika',
			'pitanie-pri-disbakterioze',
			'kak-delaut-rentgen',
			'kogda-delat-3d-uzi',
			'podgotovka-k-kolonoskopii',
			'podgotovka-k-uzi-malogo-taza',
			'uzi-novorojdennomu',
			'chto-lechit-urolog',
			'chto-lechit-vrach-terapevt',
			'boyazn-stomatologa'
		);

		if (isset($params['section'])) {


			if (in_array($params['section'], $articleExceptions)) {

				$params['article'] = $params['section'];
				$article = $params['section'];
				unset($params['section']);
				$this->loadPage('libraryArticle', $params);
			} elseif (isset($params['article'])) {
				$this->loadPage('libraryArticle', $params);
			} else {
				$this->loadPage('librarySection', $params);
			}
		} else {

			$this->loadPage('library');
		}
	}

	protected function pageSitemap()
	{
		$params = $this->params;

		if (isset($params['speciality'])) {
			$params['entity'] = 'doctor';
		} elseif (isset($params['specialization'])) {
			$params['speciality'] = $params['specialization'];
			$params['entity'] = 'clinic';
		}

		$this->loadPage('sitemap', $params);
	}

	protected function pageAbout()
	{
		$params = $this->params;

		$this->loadPage('docdocAbout', $params);
	}

	protected function pageContact()
	{
		$params = $this->params;

		$this->loadPage('docdocContact', $params);
	}

	protected function pageNews()
	{
		$params = $this->params;

		$this->loadPage('docdocNews', $params);
	}

	protected function pageSMI()
	{
		$params = $this->params;

		$this->loadPage('docdocSMI', $params);
	}

	/**
	 * Страница с офертой
	 */
	protected function pageOffer()
	{
		$params = $this->params;

		$this->loadPage('docdocOffer', $params);
	}

	protected function pageClinic()
	{
		$params = $this->params;

		if (isset($params['alias'])) {
			$this->loadPage('clinic', $params, true);
		} else {
			$this->loadPage('clinicList', $params, true);
		}
	}

	protected function pagePopupSpeciality()
	{
		$params = $this->params;

		$this->loadPage('popupSpeciality', $params);
	}

	protected function pagePopupMap()
	{
		$params = $this->params;

		$this->loadPage('popupMap', $params);
	}

	protected function pagePopupOffer()
	{
		$params = $this->params;

		$this->loadPage('popupOffer', $params);
	}

	protected function pageOpinionMore()
	{
		$params = $this->params;

		$this->loadPage('opinionMore', $params);
	}

	protected function pageRegister()
	{
		$params = $this->params;

		$this->loadPage('register', $params);
	}

	protected function pageRequest()
	{
		$params = $this->params;

		$this->loadPage('requestScreen', $params);
	}

	protected function pageRequestForm()
	{
		$params = $this->params;

		$this->loadPage('requestForm', $params);
	}

	protected function pageHelp()
	{
		$this->loadPage('pageHelp');
	}

	protected function loadPage($template = '', $params = array(), $useController = false)
	{
		\Yii::app()->newRelic->nameTransaction("router/$template");

		$this->initSeo($template, $params);

		if ($useController) {
			Yii::app()->run();
		} else {
			\Yii::app()->params['skipNewRelicTransactionName'] = true;
			extract($params);
			require $template . '.php';
		}
	}

	/**
	 * создаем Yii::app()->seo
	 *
	 * @param string $controller
	 * @param array $params
	 */
	public function initSeo($controller = '', $params = [])
	{
		$city = Yii::app()->city;

		try {
			$seo = SeoFactory::getSeo($controller, '', $this->url, $city->id_city);

			$seo->addParam('city', $city->getCity()->getAttributes());
			$seo->addParam('page', $params);
		}
		catch (SeoException $e) {
			$seo = new SEO($this->url, $controller);
		}

		Yii::app()->setComponent('seo', $seo);
	}
}
