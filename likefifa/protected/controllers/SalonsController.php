<?php
use likefifa\components\helpers\ListHelper;

class SalonsController extends SearchController {

	public $searchEntity = 'salons';
	
	public function forDefault() {
		return $this->forSalons();
	}
	
	public function getModelClass() {
		return 'LfSalon';
	}
	
	protected function getAdditionalCriteria($action, $params) {
		$criteria = array();
	
		switch ($action) {
			case 'gallery':
				$criteria['condition'] = 'master.salon_id IS NOT NULL';
				break;
		}
	
		return $criteria;
	}
	
	protected function getAdditionalOrCriteria($action, $params) {
		return array();
	}

	/**
	 * Отображает страницу салона
	 *
	 * @param LfSalon $model модель салона
	 *
	 * @return void
	 */
	public function actionIndex($model) {
		if (!$model) {
			throw new CHttpException(404, 'Salon not found');
		}

		$searchUrl =
		!empty(Yii::app()->session['searchUrl'])
		&& (
				empty($_SERVER['HTTP_REFERER'])
				|| (
						strpos($_SERVER['HTTP_REFERER'], '/salons/') !== false
						|| strpos($_SERVER['HTTP_REFERER'], '/salon/') !== false
				)
		)
		? Yii::app()->session['searchUrl'].'#salon'.$model->id
		: null;
	
		$opinion = new LfOpinion;
		$opinionSent = Yii::app()->user->hasFlash('opinionSent') ? true : false;

		$this->pageTitle = $model->getFullName();
		$this->render('index', compact('model', 'opinion', 'opinionSent', 'searchUrl'));
	}

	protected function getModelPlurals() {
		return array(
			'салон',
			'салона',
			'салонов',		
		);
	}

	/**
	 * Получает количество записей и выводит на экран
	 *
	 * @return void
	 */
	public function actionCount() {
		$params = $this->getSearchParams();
		$params['hasDeparture'] = false;
		$this->getCount($params);
	}

	/**
	 * Получает объект с сео-текстом страницы
	 *
	 * @TODO вынести HTML в соответствующие вьюхи
	 *
	 * @param string[] $params
	 *
	 * @return LfSeoText
	 */
	protected function getSalonsSeoText($params)
	{
		$seoText = new LfSeoText;
		if ($params['specialization'] || $params['service']) {
			$serviceName = $this->getServiceName($params);
			if (count($params['stations']) == 1) {
				$stationsConcatenated = ListHelper::buildNameList($params['stations']);
				$seoText->text =
					'<p><strong>' .
					$serviceName .
					' возле метро ' .
					$stationsConcatenated .
					'.</strong> Если Вам нужна услуга "' .
					$serviceName .
					'", найдите себе салон красоты на нашем сайте.</p><p>На сайте LikeFifa.ru собраны лучшие студии
						красоты города Москвы, расположенные возле метро ' .
					$stationsConcatenated .
					' В анкете каждого салона указаны цены на услуги, график работы и адрес. Выбирая салон красоты,
						обратите внимание на его рейтинг и отзывы посетителей.</p>';
			} else if (count($params['districts']) == 1) {
				$districtsConcatenated = ListHelper::buildNameList($params['districts']);
				$seoText->text =
					'<p><strong>' .
					$serviceName .
					' в районе ' .
					$districtsConcatenated .
					'.</strong> Если Вам нужна услуга "' .
					$serviceName .
					'", найдите себе салон красоты на нашем сайте.</p><p>На сайте LikeFifa.ru собраны лучшие студии
						красоты города Москвы, расположенные в районе ' .
					$districtsConcatenated .
					' В анкете каждого салона указаны цены на услуги, график работы и адрес. Выбирая салон красоты,
						обратите внимание на его рейтинг и отзывы посетителей.</p>';
			} else {
				$seoText->text =
					'<p><strong>' .
					$serviceName .
					' в салоне красоты.</strong> Если Вам нужна услуга "' .
					$serviceName .
					'", найдите себе салон красоты на нашем сайте.</p><p>На сайте LikeFifa.ru собраны лучшие студии
						красоты города Москвы. В анкете каждого салона указаны цены на услуги, график работы и адрес.
						Выбирая салон красоты, обратите внимание на его рейтинг и отзывы посетителей.</p>';
			}
		} else if ($params['stations'] || $params['districts']) {
			if (count($params['stations']) == 1) {
				$seoText->text =
					'На сайте LikeFifa.ru Вы легко сможете подобрать себе лучший салон красоты в районе метро ' .
					ListHelper::buildNameList($params['stations']) .
					'. В анкете салона Вы найдете подробную информацию об услугах и их стоимости, графике работы,
						а также квалификации специалистов. Определиться с выбором Вам помогут отзывы клиентов и
						профессиональный рейтинг.';
			} else if (count($params['districts']) == 1) {
				$seoText->text =
					'На сайте LikeFifa.ru Вы легко сможете подобрать себе лучший салон красоты в районе ' .
					ListHelper::buildNameList($params['districts']) .
					'. В анкете салона Вы найдете подробную информацию об услугах и их стоимости, графике работы,
						а также квалификации специалистов. Определиться с выбором Вам помогут отзывы клиентов и
						профессиональный рейтинг.';
			}
		}

		return $seoText;
	}
}