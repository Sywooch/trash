<?php

namespace dfs\docdoc\components\seo\diagnostica\searchcontroller;

use dfs\docdoc\components\seo\AbstractSeo,
	dfs\docdoc\diagnostica\models\Diagnostica,
	dfs\docdoc\models\StationModel,
	dfs\docdoc\models\DistrictModel,
	dfs\docdoc\models\RegCityModel,
	dfs\docdoc\models\AreaModel;

/**
 * Сео для страниц поиска диагностических центров
 *
 * Class IndexSeo
 * @package dfs\docdoc\components\seo\diagnostica\searchcontroller
 */
class DefaultSeo extends AbstractSeo
{

	/**
	 * Генерация СЕО в зависимости от action
	 */
	public function routeByAction()
	{
		switch ($this->getAction()) {
			case 'viewAction' :
				$this->seoView();
				break;
			case 'defaultAction' :
				$this->seoIndex();
		}
	}

	/**
	 *
	 */
	public function seoInfo()
	{
		$this->routeByAction();
	}

	/**
	 * Сео для полной анкеты клиники
	 */
	public function seoView()
	{
		$name = $this->params["clinicName"];
		$title = "Запись в {$name}: адрес, телефон, график работы, рейтинг - DocDoc.ru";
		$description =
				"{$name}: запись на диагностические процедуры. Адрес, телефон, график работы и рейтинг - DocDoc.ru.";
		$keywords = "{$name}, {$name} адрес, {$name} график работы, {$name} рейтинг, {$name} диагностика";
		$this->setTitle($title);
		$this->setMetaKeywords($keywords);
		$this->setMetaDescription($description);
	}

	/**
	 * Сео для страницы поиска
	 */
	public function seoIndex()
	{
		$city = $this->params['city'];
		$diagnostic = isset($this->params['diagnostic']) ? Diagnostica::model()->findByPk($this->params['diagnostic']) : null;
		$stations = isset($this->params['stations']) ? StationModel::model()->findAllByPk($this->params['stations']) : null;
		$area = isset($this->params['area']) ? AreaModel::model()->findByPk($this->params['area']) : null;
		$district = isset($this->params['district']) ? DistrictModel::model()->findByPk($this->params['district']) : null;
		$regCity = isset($this->params['regCity']) ? RegCityModel::model()->findByPk($this->params['regCity']) : null;
		$order = isset($this->params['order']) ? $this->params['order'] : null;
		$direction = isset($this->params['direction']) ? $this->params['direction'] : null;
		$page = isset($this->params['page']) ? $this->params['page'] : null;

// Без параметров поиска
		$this->setTitle("DocDoc.ru: Медицинские диагностические центры и клиники {$city['title_genitive']}");
		$this->setMetaKeywords(
			"клиника диагностики, клиника современной диагностики, диагностические клиники, диагностические центры, " .
			"диагностические центры " . mb_strtolower($city['title_genitive']) . ", диагностические медицинские центры"
		);
		$this->setMetaDescription(
			"На портале {$city['prefix']}diagnostica.docdoc.ru представлены медицинские диагностические центры {$city['title_genitive']}"
		);

// Если выбрана диагностика
		if (!is_null($diagnostic)) {
			$diagnosticFullName = '';

			if ($diagnostic->rewrite_name == "/komputernaya-tomografiya/") {
				$diagnostic->name = "компьютерная томография";
				$diagnostic->reduction_name = "";
			}
			$specDiagnostics = array(132, 138);
			if (!in_array($diagnostic->parent_id, $specDiagnostics)) {
				if ($diagnostic->parent_id != 0) {
					$parentDiag = Diagnostica::model()->findByPk($diagnostic->parent_id);
					$diagnosticFullName =
						$parentDiag->fullName() . ' ' . $diagnostic->name;
					if (!empty($parentDiag->reduction_name) || $parentDiag->reduction_name != '') {
						if ($parentDiag->rewrite_name == "/komputernaya-tomografiya/") {
							$diagnosticName = 'компьютерную томографию ' . $diagnostic->name;
						} else {
							$diagnosticName =
								$parentDiag->reduction_name . ' ' . $diagnostic->name;
						}
						$diagnosticName1 =
							$parentDiag->reduction_name . ' ' . $diagnostic->name;
						$diagnosticFullFullName =
							$parentDiag->nameInDative($parentDiag->fullName()) .
							' (' .
							$parentDiag->reduction_name .
							') ' .
							$diagnostic->name;
					} else {
						if ($diagnostic->rewrite_name == '/efgdc/') {
							$diagnostic->name = 'Эзофагогастродуоденоскопии';
						}
						$diagnosticName =
							$parentDiag->nameInDative($parentDiag->name) .
							' ' .
							$diagnostic->name;
						$diagnosticName1 = $parentDiag->name . ' ' . $diagnostic->name;
						$diagnosticFullFullName =
							$parentDiag->nameInDative($parentDiag->fullName()) .
							' ' .
							$diagnostic->name;
					}
				} else {
					$diagnosticFullName = $diagnostic->fullName();
					if (!empty($diagnostic->reduction_name) || $diagnostic->reduction_name != '') {
						$diagnosticName = $diagnostic->reduction_name;
						$diagnosticName1 = $diagnostic->reduction_name;
						$diagnosticFullFullName =
							$diagnostic->nameInDative($diagnostic->fullName()) .
							' (' .
							$diagnostic->reduction_name .
							')';
					} else {
						$diagnosticName = $diagnostic->nameInDative($diagnostic->name);
						$diagnosticName1 = $diagnostic->name;
						$diagnosticFullFullName = $diagnostic->nameInDative($diagnostic->fullName());
					}
				}

			} else {
				$parentDiag = Diagnostica::model()->findByPk($diagnostic->parent_id);
				if (!empty($parentDiag->reduction_name) || $parentDiag->reduction_name != '') {
					$diagnosticName =
						$parentDiag->reduction_name . ' ' . $diagnostic->name;
					$diagnosticName1 =
						$parentDiag->reduction_name . ' ' . $diagnostic->name;
					$diagnosticFullFullName =
						$parentDiag->nameInDative($parentDiag->fullName()) .
						' (' .
						$parentDiag->reduction_name .
						') ' .
						$diagnostic->name;
				} else {
					$diagnosticName = $parentDiag->nameInDative($diagnostic->name);
					$diagnosticName1 = $diagnostic->name;
					$diagnosticFullFullName = $parentDiag->nameInDative($diagnostic->name);
				}

			}

			if (!is_null($district) || !is_null($area)) {
				if (!is_null($district)) {
					$this->setTitle("Сделать {$diagnosticName} в районе: {$district->name} - DocDoc.ru");
					$this->setMetaDescription(
						'Здесь Вы сможете найти клинику, которая делает ' .
						$diagnosticFullFullName .
						' в вашем районе. Сделать ' .
						$diagnosticName .
						' в районе: ' .
						$district->name .
						' с DocDoc.ru - теперь просто!'
					);
					$this->setMetaKeywords($diagnosticName1 . ' ' . mb_strtolower($district->name));
					$this->setSeoText(
						0,
						"Предлагаем вам список диагностических центров проводящих {$diagnosticName} в районе {$district->name}. Здесь вы можете ознакомиться с подробной информацией о диагностических центрах: цены на исследования, график работы, телефон для записи, адрес, схема проезда."
					);
				} else {
					$this->setTitle("Сделать {$diagnosticName} в {$area->inPrepositional('full_name')} ({$area->name}) - DocDoc.ru");
					$this->setMetaDescription(
						'Здесь Вы сможете найти клинику, которая делает ' .
						$diagnosticFullFullName .
						' в вашем районе. Сделать ' .
						$diagnosticName .
						' в ' .
						$area->name .
						' с DocDoc.ru - теперь просто!'
					);
					$this->setMetaKeywords($diagnosticName1 . ' ' . mb_strtolower($area->name));
					$this->setSeoText(
						0,
						"Предлагаем вам список диагностических центров проводящих {$diagnosticName} в {$area->name}. Здесь вы можете ознакомиться с подробной информацией о диагностических центрах: цены на исследования, график работы, телефон для записи, адрес, схема проезда."
					);
				}
			} elseif (!empty($stations)) {
				$st = "";
				if (count($stations) === 1) {
					$st = $stations[0]->name;
					$headStation = "на м. {$stations[0]->name}";
					$metaTitle = "Сделать {$diagnosticName} {$headStation} - DocDoc.ru";
				} else {
					$stationNames = array();
					foreach ($stations as $station) {
						$stationNames[] = $station->name;
					}
					$st = implode(', ', $stationNames);
					$headStation = "";
					$metaTitle = mb_strtoupper(mb_substr($diagnosticName, 0, 1)) . mb_substr($diagnosticName, 1)
						. " на станциях метро {$st} - DocDoc.ru";
					$this->setSeoText(0, "Станции метро: {$st}");
				}

				$metaKeyWords = ($diagnostic) ? $diagnosticFullName : $diagnosticName1;
				$this->setSeoText(
					0,
					"Предлагаем вам список диагностических центров проводящих {$diagnosticName} в районе метро {$st}. Здесь вы можете ознакомиться с подробной информацией о диагностических центрах: цены на исследования, график работы, телефон для записи, адрес, схема проезда."
				);

				$this->setTitle($metaTitle);
				$this->setMetaDescription(
					'Здесь Вы сможете найти клинику, которая делает ' .
					$diagnosticFullFullName .
					' в вашем районе. Сделать ' .
					$diagnosticName .
					' ' .
					$headStation .
					' с DocDoc.ru - теперь просто!'
				);
				$this->setMetaKeywords($metaKeyWords . ' ' . mb_strtolower($stations[0]->name));
			} else {
				$metaKeyWords = ($diagnostic && !empty($diagnosticFullName)) ? "{$diagnosticFullName}, " : "";

				$this->setTitle("Сделать {$diagnosticName} в {$city['title_prepositional']} - DocDoc.ru");
				$this->setMetaDescription(
					'Здесь Вы сможете найти клинику, которая делает ' .
					$diagnosticFullFullName .
					' в вашем районе. Сделать ' .
					$diagnosticName .
					" в {$city['title_prepositional']} с DocDoc.ru - теперь просто!"
				);
				$this->setMetaKeywords($metaKeyWords . $diagnosticName1 . ", сделать {$diagnosticName} в {$city['title_prepositional']}");
				$seoText = str_replace('{cityInPrepositional}', $city['title_prepositional'], $diagnostic->meta_description);
				$this->setSeoText(0, $seoText);

				if ($order == 'price' && $direction == 'asc') {
					$this->setTitle("Сделать {$diagnosticName} в {$city['title_prepositional']} дешево - DocDoc.ru");
					$this->setMetaDescription(
						"Сделать {$diagnosticName} в {$city['title_prepositional']} недорого. " .
						"Рейтинг центров, отзывы пациентов, адреса, цены - DocDoc.ru"
					);
					$this->setMetaKeywords("{$diagnosticName} дешево, {$diagnosticName} недорого, {$diagnosticName} со скидкой");
					$this->setSeoText(1,
						"<p><b>Хотите сделать {$diagnosticName} недорого?</b></p>" .
						"У нас на сайте представлены лучшие медицинские центры в которых можно сделать {$diagnosticName} по самым низким ценам." .
						"<p><b>Ищите скидки на {$diagnosticName}?</b></p>" .
						"На нашем сайте вы можете записаться на {$diagnosticName} со скидкой - цены подсвечены красным цветом."
					);
				}
			}

			$headStr = '';
			if ($diagnostic->parent_id > 0 && !empty($parentDiag)) {
				if ($diagnostic->rewrite_name === '/efgdc/') {
					$headStr = 'Эзофагогастродуоденоскопия';
				} else if ($parentDiag->rewrite_name === "/func-diagnostika/") {
					$headStr = $diagnostic->name;
				} else if ($parentDiag->rewrite_name === "/endoskopicheskie-issledovaniya/") {
					$headStr = $diagnostic->name;
				} else {
					$headStr = $parentDiag->getParentName() . ' ' . $diagnostic->name;
				}
				$parentAlias = $parentDiag->rewrite_name;
			} else {
				$headStr .= ($diagnostic->rewrite_name == '/komputernaya-tomografiya/')
					? 'КТ (компьютерная томография)'
					: $diagnostic->getParentName();
				$parentAlias = $diagnostic->rewrite_name;
			}

			if (empty($district) && empty($area) && empty($stations) && empty($regCity)) {
				$exceptDiagnostics = array('/func-diagnostika/', '/endoskopicheskie-issledovaniya/');
				if (!in_array($parentAlias, $exceptDiagnostics)) {
					$this->setSeoText(1,
						"<p><b>Где пройти исследование «{$headStr}»?</b><br>" .
						"На сайте DocDoc.ru собраны лучшие клиники {$city['title_genitive']}, в которых проводят диагностическое " .
						"исследование «{$headStr}». Подберите себе центр для прохождения этой процедуры " .
						"прямо у нас на сайте по важным для Вас критериям: расположение центра, цена, график работы.</p>" .
						"<p><b>Столько стоит " . $headStr . "?</b><br>Цена исследования " .
						"«{$headStr}» зависит от многих факторов, в первую очередь, от качества оборудования и от расположения " .
						"диагностического центра. На DocDoc.ru Вы найдете лучшие центры {$city['title_genitive']}, которые сможете " .
						"отсортировать по стоимости процедуры. В анкете каждого центра имеется информация о его " .
						"графике работы, также указан номер телефона, по которому Вы можете сразу же записаться на " .
						"исследование.</p>"
					);
				}
			}

			if (!empty($district)) {
				$headStr .= ". Район: {$district->name}";
			} elseif (!empty($area)) {
				$headStr .= " ({$area->name})";
			} elseif (!empty($headStation)) {
				$headStr .= " {$headStation}";
			}
			$this->setHead($headStr);

		} else {
			// Поиск по гео-параметрам
			if (!empty($stations) && count($stations) == 1) {
				$this->setTitle("Диагностические центры на м. {$stations[0]->name} - DocDoc.ru");
				$this->setMetaKeywords(
					"диагностические центры {$stations[0]->name}, центр диагностики {$stations[0]->name}, " .
					"медицинские диагностические центры {$stations[0]->name}"
				);
				$this->setMetaDescription(
					"На нашем сайте представлены диагностические центры, которые расположены рядом с метро {$stations[0]->name}. " .
					"Удобный поиск по типу диагностики, цене."
				);
				$this->setHead("Диагностические центры на м. {$stations[0]->name}");
			} elseif (!is_null($district)) {
				$this->setTitle("Диагностические центры в районе {$district->name} - DocDoc.ru");
				$this->setMetaKeywords(
					"диагностические центры {$district->name}, центр диагностики {$district->name}, " .
					"медицинские диагностические центры {$district->name}"
				);
				$this->setMetaDescription(
					"На нашем сайте представлены диагностические центры, которые расположены в районе {$district->name}. " .
					"Удобный поиск по типу диагностики, цене."
				);
				$this->setHead("Диагностические центры в районе {$district->name}");
			} elseif (!is_null($regCity)) {
				$this->setTitle("Диагностические центры в городе {$regCity->name} - DocDoc.ru");
				$this->setMetaKeywords(
					"диагностические центры {$regCity->name}, центр диагностики {$regCity->name}, " .
					"медицинские диагностические центры {$regCity->name}"
				);
				$this->setMetaDescription(
					"На нашем сайте представлены диагностические центры, до которых удобнее всего добираться из города {$regCity->name}. " .
					"Удобный поиск по типу диагностики, цене."
				);
				$this->setHead("Диагностические центры в городе {$regCity->name}");
			}
		}

// Если страница не первая
		if ($page > 1) {
			$this->setTitle($this->getTitle() . " - страница {$page}");
			$this->resetSeoText();
		}
	}

}