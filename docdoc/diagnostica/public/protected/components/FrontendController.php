<?php
use dfs\docdoc\models\DiagnosticaModel;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\components\seo\SeoFactory;
use dfs\docdoc\components\seo\SeoException;
use dfs\docdoc\diagnostica\models\Diagnostica;

/**
 * Class FrontendController
 */
class FrontendController extends Controller
{

	/**
	 * @var Diagnostica[]
	 */
	public $diagnostics = array();
	public $diagnostic = null;
	public $parentDiagnostic = null;
	public $stations = array();
	public $area = null;
	public $district = null;
	public $parentDiagnostics = null;
	public $oneDiagnostic = false;
	public $diagnosticCenterCount = null;
	public $metaKeywords = null;
	public $metaDescription = null;
	public $profilePage = null;
	public $countTotalClinic = null;
	public $countVisitedWeek = null;
	public $diagnosticTree = null;
	public $head = null;

	/**
	 * Страница лендинга
	 *
	 * @var bool
	 */
	public $isLandingPage = false;

	/**
	 * @var int
	 */
	public $clinicId = null;

	/**
	 * @var bool
	 */
	public $isMobile = false;

	/**
	 * @var CityModel[]
	 */
	public $cities = array();

	/**
	 * @param CAction $action
	 * @return bool
	 */
	public function beforeAction($action)
	{
		$this->layout = '//layouts/diagnostics';

		$this->diagnostics = Diagnostica::model()
			->ordered()
			->cache(3600)
			->findAll();

		$this->cities = CityModel::model()
			->active()
			->hasDiagnostic()
			->cache(3600)
			->findAll();

		$file = 'include/xml/counter.xml';
		if (file_exists($file)) {
			$xml = simplexml_load_file($file);
			$this->countTotalClinic = sprintf("%03d", $xml->Center->Count);
			$this->countVisitedWeek = sprintf("%04d", $xml->Employer->Count);
		}

		$this->applyRewriteRules();

		//инициализируем компонент с seo-описанием страниц
		try {
			Yii::app()->setComponent(
				'seo',
				SeoFactory::getSeo(
					get_class($this),
					$action->id,
					Yii::app()->request->url,
					Yii::app()->city->getCityId()
				)
			);

			Yii::app()->seo->addParam('city', Yii::app()->city->getCity());

		} catch (SeoException $e) {

		}

		$this->isMobile = Yii::app()->mobileDetect->isAdaptedMobile();

		return true;
	}

	/**
	 *	Выполняем перед рендерингом вьюхи
	 * 1. Определяем SEO информацию
	 *
	 * @param string $view
	 * @return bool
	 */
	protected function beforeRender($view)
	{
		//1. Определяем SEO информацию
		//если для данного контроллера и action есть класс, возвращающий seo-информацию
		if (Yii::app()->getComponent('seo') !== null) {
			//генерируем SEO информацию
			Yii::app()->seo->seoInfo();

			$this->setTitle(Yii::app()->seo->getTitle());
			$this->setMetaKeywords(Yii::app()->seo->getMetaKeywords());
			$this->setMetaDescription(Yii::app()->seo->getMetaDescription());
			$this->setHead(Yii::app()->seo->getHead());
		}

		return true;
	}

	public function getCountFormatted($counter = 'countTotalClinic')
	{
		$count = $this->$counter;
		$result = '';
		for ($i = 0; $i < strlen($count); $i++) {
			$result .= '<span>' . $count[$i] . '</span>';
		}

		return $result;
	}

	public function getCountVisitedWeek()
	{
		$numArr = array();
		if ($this->$countVisitedWeek < 1000) {
			$numArr[0] = 0;
			if ($this->countTotalClinic < 10)
				$numArr[1] = 0;
			else
				$numArr[1] = $this->countTotalClinic[1];
		} else {
			$numArr[0] = $this->countTotalClinic[0];
			$numArr[1] = $this->countTotalClinic[1];
		}
		$numArr[2] = $this->countTotalClinic[2];

		return "<span>" . $numArr[0] . "</span><span>" . $numArr[1] . "</span><span>" . $numArr[2] . "</span>";
	}

	protected function validateAjaxFields($keys)
	{
		$errors = array();

		foreach ($keys as $key) {
			if (!isset($_POST[$key]) || !mb_strlen($_POST[$key]))
				$errors[] = $key;
		}

		if ($errors) {
			$errors = array_combine($errors, $errors);

			echo json_encode(compact('errors'));
			Yii::app()->end();
		}
	}

	protected function sendAjaxMessage($message, $url = null)
	{
		echo json_encode(compact('message', 'url'));
		Yii::app()->end();
	}

	protected function setTitle($title = null)
	{
		$this->pageTitle = $title;
		return $this;
	}

	protected function setMetaKeywords($keywords)
	{
		$this->metaKeywords = $keywords;
		return $this;
	}

	protected function setMetaDescription($description)
	{
		$this->metaDescription = $description;
		return $this;
	}

	/**
	 * Установка заголовка для страницы
	 *
	 * @param $head
	 *
	 * @return $this
	 */
	protected function setHead($head)
	{
		$this->head = $head;
		return $this;
	}

	/**
	 * Применение редиректов
	 */
	private function applyRewriteRules()
	{
		/*
			Редирект со страниц вида:
				/поддиагностика         => /диагностика/поддиагностика/
				/kliniki/поддиагностика => /диагностика/поддиагностика/
		*/
		$pattern = '~^(/kliniki)?/(?P<diagnostic>([0-9a-zA-Z_-]+))(?P<other>(.+)?)$~';
		preg_match($pattern, Yii::app()->request->url, $matches);
		if (isset($matches['diagnostic'])) {
			$diagnostic = DiagnosticaModel::model()->searchByAlias($matches['diagnostic'])->with('parent')->find();
			if (!is_null($diagnostic) && !is_null($diagnostic->parent)) {
				$other = isset($matches['other']) ? $matches['other'] : '';
				$this->redirect("/{$diagnostic->parent->getRewriteName()}/{$diagnostic->getRewriteName()}{$other}", true, 301);
			}
		}
	}
}
