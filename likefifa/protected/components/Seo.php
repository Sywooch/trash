<?php

namespace likefifa\components;
use FrontendController;
use likefifa\models\CityModel;
use likefifa\models\RegionModel;
use Yii;
use likefifa\components\helpers\ListHelper;

/**
 * Seo class file.
 *
 * Класс для работы СЕО
 * СЕО для мастеров и салонов
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see     https://docdoc.megaplan.ru/task/1002975/card/
 * @package likefifa\components\common
 */
class Seo extends FrontendController
{

	/**
	 * Модель города или региона для СЕО
	 *
	 * @var CityModel|RegionModel
	 */
	public static $location = null;

	/**
	 * Конструктор
	 *
	 * @param CityModel $city
	 *
	 * @return Seo
	 */
	public function __construct($city = null)
	{
		if ($city) {
			self::$location = $city;
		} else {
			self::$location = Yii::app()->activeRegion->getModel();
		}
	}

	/**
	 * Задает СЕО для мастеров
	 *
	 * @param string   $action     действие
	 * @param string[] $params     параметры
	 * @param string   $pageString страница с номером
	 *
	 * @return void
	 */
	public function setForMaster($action, $params, $pageString)
	{
		switch ($action) {
			case 'custom':
				if ($params['speciality']) {
					switch ($params["speciality"]) {

						case "visagiste":
							if ($params["stations"]) {
								$stationsConcatenated = ListHelper::buildNameList($params["stations"]);
								if (count($params['stations']) > 1) {
									$this->pageTitle = "Визажисты {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Визажисты";
									$this->pageSubheader = "На станциях метро: {$stationsConcatenated}";
								} else {
									$this->pageTitle = "Визажисты на метро {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Лучшие визажисты на метро {$stationsConcatenated}";
								}
								$this->metaKeywords = "Визажисты {$stationsConcatenated}";
								$this->metaDescription = "На нашем сайте представлены лучшие визажисты, которые
									работают на станции метро {$stationsConcatenated}.";
							} else {
								$this->pageTitle = "Лучшие визажисты " .
									self::$location->name_genitive .
									" - фото работ, рейтинг, отзывы - LikeFifa.ru";
								$this->metaKeywords = "визажист, визажисты " . self::$location->name_genitive . ",
									визажисты услуги, нужен визажист";
								$this->metaDescription = "Если вам нужен визажист, на сайте LikeFifa.ru вы сможете
									найти лучших мастеров, прочитать отзывы, узнать стоимость их услуг и записаться.";
								$this->pageHeader = "Лучшие визажисты " .
									self::$location->name_genitive;
								if (!$params["specialization"]) {
									$this->pageSubheader = "
										<p><strong>Визажист</strong> – специалист, который занимается нанесением
											макияжа, созданием образа с помощью декоративной косметики.</p>
										<p><strong>Для чего нужен визажист?</strong> Профессиональный визажист работает
											на мероприятиях, модных показах, свадьбах, фотосъемках и т.д. В услуги
											визажиста могут входить как макияж лица, так и всего тела (боди-арт).</p>
										<p><strong>Сколько стоят услуги визажиста?</strong>  Стоимость услуги визажиста
											зависит от типа мероприятия, профессионализма мастера и сложности
											выполнения макияжа. Например, цены на макияж для фотосессии начинаются
											от 1000 руб.</p>
										<p><strong>На портале LikeFifa</strong> Вы найдете анкеты самых востребованных
											визажистов " .
										self::$location->name_genitive .
										".</p>
									";
								}
							}
							break;

						case "hairdresser":
							if ($params["stations"]) {
								$stationsConcatenated = ListHelper::buildNameList($params["stations"]);
								if (count($params['stations']) > 1) {
									$this->pageTitle = "Парикмахеры {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Парикмахеры";
									$this->pageSubheader = "На станциях метро: {$stationsConcatenated}";
								} else {
									$this->pageTitle = "Парикмахеры на метро {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Лучшие парикмахеры на метро {$stationsConcatenated}";
								}
								$this->metaKeywords = "Парикмахеры {$stationsConcatenated}";
								$this->metaDescription = "На нашем сайте представлены лучшие парикмахеры, которые
									работают на станции метро {$stationsConcatenated}.";
							} else {
								$this->pageTitle = "Лучшие парикмахеры " .
									self::$location->name_genitive .
									" - фото работ, рейтинг, отзывы - LikeFifa.ru";
								$this->metaKeywords =
									"парикмахер, парикмахеры " . self::$location->name_genitive . ",
									парикмахеры услуги, нужен парикмахер";
								$this->metaDescription = "Если вам нужен парикмахер, на сайте LikeFifa.ru вы сможете
									найти лучших мастеров, прочитать отзывы, узнать стоимость их услуг и записаться.";
								$this->pageHeader = "Лучшие парикмахеры " .
									self::$location->name_genitive;
								if (!$params["specialization"]) {
									$this->pageSubheader = "
										<p><strong>Парикмахер</strong> – специалист, который занимается созданием
											образа и стиля с помощью волос. Профессиональный парикмахер выполняет не
											только стрижку и окрашивание волос, но и укладку,  сложные вечерние
											прически, уход за волосами и т.д.</p>
										<p><strong>Для чего нужен парикмахер?</strong> Парикмахеры занимаются созданием
											прически в соответствии с образом клиентки. Частные парикмахеры также
											занимаются созданием образов на модных показах, свадьбах, работают со
											звездами перед важными мероприятиями, участвуют в фотосъемках.
											Мастер-парикмахер может принимать в салоне или выезжать на дом.</p>
										<p><strong>В " .
										self::$location->name_prepositional .
										" парикмахера поможет найти портал LikeFifa.</strong> На сайте Вы
											найдете анкеты специалистов, через которые сможете записаться к парикмахеру
											в один клик.</p>
									";
								}
							}
							break;

						case "nail-service":
							if ($params["stations"]) {
								$stationsConcatenated = ListHelper::buildNameList($params["stations"]);
								if (count($params['stations']) > 1) {
									$this->pageTitle =
										"Мастера ногтевого сервиса {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Мастера ногтевого сервиса";
									$this->pageSubheader = "На станциях метро: {$stationsConcatenated}";
								} else {
									$this->pageTitle =
										"Мастера ногтевого сервиса на метро {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader =
										"Лучшие мастера ногтевого сервиса на метро {$stationsConcatenated}";
								}
								$this->metaKeywords = "Мастера ногтевого сервиса {$stationsConcatenated}";
								$this->metaDescription = "На нашем сайте представлены лучшие мастера ногтевого сервиса,
									которые работают на станции метро {$stationsConcatenated}.";
							} else {
								$this->pageTitle =
									"Лучшие мастера ногтевого сервиса " .
									self::$location->name_genitive .
									" - фото работ, рейтинг, отзывы - LikeFifa.ru";
								$this->metaKeywords = "мастер ногтевого сервиса, мастера ногтевого сервиса
									" . self::$location->name_genitive . ",
									мастера ногтевого сервиса услуги, нужен мастер ногтевого сервиса";
								$this->metaDescription = "Если вам нужен мастер ногтевого сервиса, на сайте LikeFifa.ru
									вы сможете найти лучших мастеров, прочитать отзывы, узнать стоимость их услуг и
									записаться. ";
								$this->pageHeader = "Лучшие мастера ногтевого сервиса " .
									self::$location->name_genitive;
								if (!$params["specialization"]) {
									$this->pageSubheader = "
										<p><strong>Мастер ногтевого сервиса</strong> – специалист, который занимается
											косметической и декоративной обработкой ногтей. Внутри этой специальности
											выделяют мастеров по маникюру и мастеров по педикюру.</p>
										<p><strong>Чем занимается мастер ногтевого сервиса?</strong> Мастер по ногтям
											занимается обработкой рук, ног и ногтевых пластин. В его услуги входит
											маникюр, педикюр, покрытие лаком, наращивание и  дизайн ногтей.</p>
										<p><strong>На портале LikeFifa</strong> собраны лучшие мастера ногтевого
											сервиса" . self::$location->name_genitive . ".</p>
									";
								}
							}
							break;

						case "masseur":
							if ($params["stations"]) {
								$stationsConcatenated = ListHelper::buildNameList($params["stations"]);
								if (count($params['stations']) > 1) {
									$this->pageTitle = "Массажисты {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Массажисты";
									$this->pageSubheader = "На станциях метро: {$stationsConcatenated}";
								} else {
									$this->pageTitle = "Массажисты на метро {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Лучшие массажисты на метро {$stationsConcatenated}";
								}
								$this->metaKeywords = "Массажисты {$stationsConcatenated}";
								$this->metaDescription = "На нашем сайте представлены массажисты,
									которые работают на станции метро {$stationsConcatenated}.";
							} else {
								$this->pageTitle = "Лучшие массажисты " .
									self::$location->name_genitive .
									" - фото работ, рейтинг, отзывы - LikeFifa.ru";
								$this->metaKeywords =
									"массажист, массажисты " . self::$location->name_genitive . ",
									массажисты услуги, нужен массажист";
								$this->metaDescription = "Если вам нужен массажист, на сайте LikeFifa.ru вы сможете
									найти лучших мастеров, прочитать отзывы, узнать стоимость их услуг и записаться.";
								$this->pageHeader = "Лучшие массажисты " .
									self::$location->name_genitive;
								if (!$params["specialization"]) {
									$this->pageSubheader = "
										<p><strong>Массажист</strong> – специалист, который использует приемы
											механического и рефлекторного воздействия на ткани с помощью специальной
											аппаратуры или руками. В услуги массажиста входит оздоровительный и
											лечебный массажи, массажи для релаксации (спа-массаж, массаж камнями),
											антицеллюлитный массаж и т.д.</p>
										<p><strong>Стоимость услуг.</strong> На услуги массажистов цена зависит от типа
											массажа, продолжительности сеанса и месте приема. Частные массажисты
											принимают в медицинских центрах, частных кабинетах и салонах красоты.
											Средняя цена лечебно-оздоровительного массажа – 1000 руб.</p>
										<p><strong>На портале LikeFifa</strong> разместили свои анкеты массажисты " .
										self::$location->name_genitive .
										". Здесь Вы найдете подробный перечень услуг, их стоимость. О
											массажистах отзывы оставляют только клиенты, записавшиеся через портал
											LikeFifa.</p>
									";
								}
							}
							break;

						case "cosmetologist":
							if ($params["stations"]) {
								$stationsConcatenated = ListHelper::buildNameList($params["stations"]);
								if (count($params['stations']) > 1) {
									$this->pageTitle = "Косметологи {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Косметологи";
									$this->pageSubheader = "На станциях метро: {$stationsConcatenated}";
								} else {
									$this->pageTitle = "Косметологи на метро {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Лучшие косметологи на метро {$stationsConcatenated}";
								}
								$this->metaKeywords = "Косметологи {$stationsConcatenated}";
								$this->metaDescription = "На нашем сайте представлены косметологи,
									которые работают на станции метро {$stationsConcatenated}.";
							} else {
								$this->pageTitle = "Лучшие косметологи " .
									self::$location->name_genitive .
									" - фото работ, рейтинг, отзывы - LikeFifa.ru";
								$this->metaKeywords =
									"косметолог, косметологи " . self::$location->name_genitive . ",
									косметологи услуги , нужен косметолог";
								$this->metaDescription = "Если вам нужен косметолог, на сайте LikeFifa.ru вы сможете
									найти лучших мастеров, прочитать отзывы, узнать стоимость их услуг и записаться. ";
								$this->pageHeader = "Лучшие косметологи " .
									self::$location->name_genitive;
								if (!$params["specialization"]) {
									$this->pageSubheader = "
										<p><strong>Косметолог</strong> – специалист в индустрии красоты, который
											занимается уходом за лицом и телом. Частные косметологи принимают в салонах,
											медицинских центрах и частных кабинетах. В услуги косметолога входит
											коррекция бровей, проведение инъекций, наращивание ресниц, обертывания
											и т.д.</p>
										<p><strong>Для чего нужен косметолог?</strong> Хороший косметолог поможет
											подобрать правильных уход за кожей лица и тела, провести процедуры по
											устранению недостатков, дать рекомендации по домашнему уходу и многое
											другое.</p>
										<p><strong>Сколько стоят услуги  косметолога?</strong> Стоимость приема
											косметолога зависит от проводимой процедуры. В среднем стоимость коррекции
											бровей составляет 500 руб., косметологии лица – 1500 руб., косметологии
											тела – 3000 руб.</p>
										<p>Найти косметолога поможет портал LikeFifa.ru, где собраны анкеты лучших
											косметологов " .
										self::$location->name_genitive .
										".</p>
									";
								}
							}
							break;

						case "piercing":
							if ($params["stations"]) {
								$stationsConcatenated = ListHelper::buildNameList($params["stations"]);
								if (count($params['stations']) > 1) {
									$this->pageTitle =
										"Мастера по пирсингу {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Мастера по пирсингу";
									$this->pageSubheader = "На станциях метро: {$stationsConcatenated}";
								} else {
									$this->pageTitle =
										"Мастера по пирсингу на метро {$stationsConcatenated} —
										LikeFifa.ru";
									$this->pageHeader =
										"Лучшие мастера по пирсингу на метро {$stationsConcatenated}";
								}
								$this->metaKeywords = "Мастера по пирсингу {$stationsConcatenated}";
								$this->metaDescription = "На нашем сайте представлены мастера по пирсингу,
									которые работают на станции метро {$stationsConcatenated}.";
							} else {
								$this->pageTitle =
									"Лучшие мастера по пирсингу " .
									self::$location->name_genitive .
									" - фото работ, рейтинг, отзывы - LikeFifa.ru";
								$this->metaKeywords = "мастер по пирсингу, мастера по пирсингу
									" . self::$location->name_genitive . ", мастера по
									пирсингу услуги, нужен мастер по пирсингу";
								$this->metaDescription = "Если вам нужен мастер по пирсингу, на сайте
									LikeFifa.ru вы сможете найти лучших мастеров, прочитать отзывы, узнать стоимость их
									услуг и записаться. ";
								$this->pageHeader = "Лучшие мастера по пирсингу " .
									self::$location->name_genitive;
								if (!$params["specialization"]) {
									$this->pageSubheader = "
										<p><strong>Мастера по пирсингу</strong> или пирсеры, занимаются украшением тела
											с помощью декоративных сережек и других украшений, прокалывая кожу.
											Частный пирсинг-мастерделает пирсинг ушей, пупка, языка, бровей, носа,
											губы, пирсинг «монро», интимный пирсинг и другие виды.</p>
										<p><strong>Если Вы решили сделать пирсинг</strong> и Вам нужен пирсинг-мастер,
											Вы можете найти специалиста на нашем сайте. Пирсеры
											" . self::$location->name_genitive . " принимают в
											салонах, частных кабинетах, а также выезжают на дом.</p>
										<p><strong>На портале LikeFifa</strong> собраны анкеты лучших мастеров пирсинга
										в " . self::$location->name_prepositional . ".</p>
									";
								}
							}
							break;

						case "tattoos":
							if ($params["stations"]) {
								$stationsConcatenated = ListHelper::buildNameList($params["stations"]);
								if (count($params['stations']) > 1) {
									$this->pageTitle =
										"Мастера по татуировкам {$stationsConcatenated} — LikeFifa.ru";
									$this->pageHeader = "Мастера по татуировкам";
									$this->pageSubheader = "На станциях метро: {$stationsConcatenated}";
								} else {
									$this->pageTitle =
										"Мастера по татуировкам на метро {$stationsConcatenated} —
										LikeFifa.ru";
									$this->pageHeader =
										"Лучшие мастера по татуировкам на метро {$stationsConcatenated}";
								}
								$this->metaKeywords = "Мастера по татуировкам {$stationsConcatenated}";
								$this->metaDescription = "На нашем сайте представлены мастера по татуировкам,
									которые работают на станции метро {$stationsConcatenated}.";
							} else {
								$this->pageTitle =
									"Лучшие мастера по татуировкам " .
									self::$location->name_genitive .
									" - фото работ, рейтинг, отзывы - LikeFifa.ru";
								$this->metaKeywords = "мастер по татуировкам, мастера по татуировкам
									" . self::$location->name_genitive . ", мастера по
									татуировкам услуги, нужен мастер по татуировкам";
								$this->metaDescription = "Если вам нужен мастер по татуировкам, на сайте
									LikeFifa.ru вы сможете найти лучших мастеров, прочитать отзывы, узнать стоимость их
									услуг и записаться. ";
								$this->pageHeader = "Лучшие мастера по татуировкам " .
									self::$location->name_genitive;
								if (!$params["specialization"]) {
									$this->pageSubheader = "
										<p><strong>Мастера татуировок</strong> занимаются нанесением рисунков на кожу
											тела с помощью подкожной иглы. Существует несколько направлений в
											мастерстве исполнения тату: от простой каллиграфии (иероглифы, надписи)
											до масштабных изображений, включая абстрактные и геометрические рисунки.
										</p>
										<p><strong>Частные мастера татуировки</strong> делают черно-белые и цветные
											рисунки, шрамирование, временные татуировки и татуировки хной на любом
											участке коже. Художник татуировщик поможет Вам продумать эскиз Вашей
											татуировки и поможет выбрать стилистику рисунка.</p>
										<p><strong>Если Вы решили сделать татуировку</strong> и Вам нужен
											мастер-татуировщик, Вы можете найти специалиста на нашем сайте.
											Татуировщики " . self::$location->name_genitive . "
											принимают в салонах, частных кабинетах, а также
											выезжают на дом.</p>
										<p><strong>На портале LikeFifa</strong> собраны анкеты лучших мастеров
											пирсинга и мастеров татуировок в " .
											self::$location->name_prepositional . ".</p>
									";
								}
							}
							break;
					}
				} else {
					if ($params['specialization'] || $params['service']) {
						$serviceName = $this->getServiceName($params);
						if ($params['city'] && !$params['city']->isMoscow()) {
							$this->pageTitle = $serviceName . ' в г' . $params['city']->name . $pageString;
							$this->pageHeader = $serviceName . ' в городе ' . $params['city']->name;
							$this->metaKeywords = $serviceName . ' ' . $params['city']->name;
							$this->metaDescription =
								'На нашем сайте представлены лучшие мастера по услуге “' .
								$serviceName .
								'”, которые работают в городе ' .
								$params['city']->name;
						} elseif ($params['stations']) {
							$c = count($params['stations']);
							$stationsConcatenated = ListHelper::buildNameList($params['stations']);

							$this->pageTitle = $serviceName . ' (' . $stationsConcatenated . ')' . $pageString;
							if ($c == 1) {
								$this->pageHeader = $serviceName . ' на м.' . $stationsConcatenated;
							} elseif ($c > 1) {
								$this->pageHeader = $serviceName;
							}
							$this->metaKeywords = $serviceName . ' ' . $stationsConcatenated;
							$this->metaDescription =
								'На нашем сайте представлены лучшие мастера по услуге “' .
								$serviceName .
								'”, которые работают на станции метро ' .
								$stationsConcatenated .
								'.';
						} elseif ($params['districts']) {
							$c = count($params['districts']);
							$districtsConcatenated = ListHelper::buildNameList($params['districts']);
							$this->pageTitle =
								'Сделать ' . $serviceName . ' в районе ' . $districtsConcatenated . $pageString;
							$this->metaKeywords = $serviceName . ' - район ' . $districtsConcatenated;
							$this->metaDescription =
								'На нашем сайте представлены лучшие мастера по услуге “' .
								$serviceName .
								'”, которые работают в районе ' .
								$districtsConcatenated .
								'.';
							if ($c == 1) {
								$this->pageHeader = $serviceName . ' в районе ' . $districtsConcatenated;
							} else {
								if ($c > 1) {
									if (Yii::app()->request->getQuery("districts")) {
										$this->pageSubheader = "В районах {$districtsConcatenated}";
										$this->pageHeader = $serviceName;
									} else {
										$area = $params["area"];
										$this->pageHeader =
											"Лучшие мастера делающие {$serviceName} в районе {$area->name}";
										$this->pageTitle =
											"Сделать {$serviceName} в округе {$area->name} — LikeFifa.ru";
										$this->metaKeywords =
											"На сайте LikeFifa.ru Вы сможете выбрать мастера делающего {$serviceName}
											в округе {$area->name} по рейтигу, отзывам, фотографиям работ.
											Онлайн-запись в салоны красоты";
										$this->metaDescription = "{$serviceName} {$area->name},
									сделать {$serviceName} в округе {$area->name}";
									}
								}
							}
						} else {
							$this->pageTitle = $serviceName . $pageString;
							$this->pageHeader = $serviceName;
							$this->metaKeywords = $serviceName;
							$this->metaDescription =
								'На нашем сайте представлены лучшие мастера по услуге “' . $serviceName . '”.';
						}
					} elseif ($params['stations'] || $params['districts']) {
						if ($params['stations']) {
							$c = count($params['stations']);
							$stationsConcatenated = ListHelper::buildNameList($params['stations']);

							$this->pageTitle = 'Мастера (' . $stationsConcatenated . ')' . $pageString;
							if ($c == 1) {
								$this->pageHeader = 'Мастера на м.' . $stationsConcatenated;
							} elseif ($c > 1) {
								$this->pageHeader = 'Поиск мастеров';
							}
							$this->metaKeywords = 'Мастера ' . $stationsConcatenated;
							$this->metaDescription =
								'На нашем сайте представлены лучшие мастера, которые работают на станции метро ' .
								$stationsConcatenated .
								'.';
						} elseif ($params['districts']) {
							$c = count($params['districts']);
							$districtsConcatenated = ListHelper::buildNameList($params['districts']);

							$this->pageTitle = 'Мастера в районе ' . $districtsConcatenated . $pageString;
							if ($c == 1) {
								$this->pageHeader = 'Мастера в районе ' . $districtsConcatenated;
							} else {
								if ($c > 1) {
									$this->pageHeader = 'Поиск мастеров';
									if (Yii::app()->request->getQuery("districts")) {
										$this->pageSubheader = "В районах {$districtsConcatenated}";
									}
								}
							}
							$this->metaKeywords = 'Мастера - район ' . $districtsConcatenated;
							$this->metaDescription =
								'На нашем сайте представлены лучшие мастера, которые работают в районе ' .
								$districtsConcatenated .
								'.';
						}
					} else {
						$this->pageTitle = 'Поиск мастеров' . $pageString;
						$this->pageHeader = 'Поиск мастеров';
					}
				}
				break;

			case 'gallery':
				if ($params['specialization'] || $params['service']) {
					$serviceName = $this->getServiceName($params);

					$this->pageTitle = $serviceName . ' в картинках. Фото лучших работ мастеров';
					$this->pageHeader = $serviceName . ' в картинках. Фото лучших работ мастеров';
					$this->metaKeywords = $serviceName . ' в картинках, ' . $serviceName . ' фото';
					$this->metaDescription = $serviceName . ' в картинках. Фото лучших работ мастеров.';
				} else {
					$this->pageTitle = 'Фото работ';
					$this->pageHeader = 'Фото работ';
				}
				break;
		}
	}

	/**
	 * Задает СЕО для салонов
	 *
	 * @param string   $action     действие
	 * @param string[] $params     параметры
	 * @param string   $pageString страница с номером
	 *
	 * @return void
	 */
	public function setForSalons($action, $params, $pageString)
	{
		switch ($action) {
			case 'custom':
				if ($params['specialization'] || $params['service']) {
					$serviceName = $this->getServiceName($params);
					if ($params['city'] && !$params['city']->isMoscow()) {
						$this->pageTitle = $serviceName . ' в г.' . $params['city']->name . $pageString;
						$this->pageHeader = $serviceName . ' в г.' . $params['city']->name;
						$this->metaKeywords = $serviceName . ' ' . $params['city']->name;
						$this->metaDescription =
							'На likefifa.ru Вы найдете большое число салонов красоты по услуге “' .
							$serviceName .
							'”, которые работают в городе ' .
							$params['city']->name;
					} elseif ($params['stations']) {
						$c = count(ListHelper::buildPropList('name', $params['stations']));

						$stationsConcatenated = ListHelper::buildNameList($params['stations']);
						$this->pageTitle =
							$serviceName .
							' на м.' .
							$stationsConcatenated .
							" - Салоны красоты " . self::$location->name_genitive . " – LikeFifa" .
							$pageString;
						if ($c == 1) {
							$this->pageHeader = $serviceName . ' в салоне на м.' . $stationsConcatenated;
						}
						elseif ($c > 1) {
							$this->pageHeader = $serviceName;
						}
						$this->metaKeywords =
							'салон красоты ' .
							$serviceName .
							' ' .
							$stationsConcatenated .
							', студия красоты ' .
							$serviceName .
							' ' .
							$stationsConcatenated;
						$this->metaDescription =
							'На likefifa.ru Вы найдете большое число салонов красоты по услуге “' .
							$serviceName .
							'” около метро ' .
							$stationsConcatenated .
							'. Вы сможете узнать адреса салонов, прочитать отзывы посетителей, узнать цены и
								записаться к мастерам красоты! ';
					} elseif ($params['districts']) {
						$c = count($params['districts']);
						$districtsConcatenated = ListHelper::buildNameList($params['districts']);

						$this->pageTitle =
							$serviceName .
							' в районе ' .
							$districtsConcatenated .
							" - Салоны красоты " . self::$location->name_genitive . " – LikeFifa" .
							$pageString;
						if ($c == 1) {
							$this->pageHeader = $serviceName . ' в районе ' . $districtsConcatenated;
						} else if ($c > 1) {
							$this->pageHeader = $serviceName;
							if (Yii::app()->request->getQuery("districts")) {
								$this->pageSubheader = "В районах {$districtsConcatenated}";
							}
						}
						$this->metaKeywords =
							'салон красоты ' .
							$serviceName .
							' ' .
							$districtsConcatenated .
							', студия красоты ' .
							$serviceName .
							' ' .
							$districtsConcatenated;
						$this->metaDescription =
							'На likefifa.ru Вы найдете большое число салонов красоты по услуге “' .
							$serviceName .
							'” в районе ' .
							$districtsConcatenated .
							'. Вы сможете узнать адреса салонов, прочитать отзывы посетителей, узнать цены и
							записаться к мастерам красоты! ';
					} else {
						$this->pageTitle =
							"{$serviceName} - Салоны красоты " . self::$location->name_genitive . ": адреса, цены,
							отзывы – LikeFifa {$pageString}";
						$this->pageHeader = $serviceName . ' в салонах красоты';
						$this->metaKeywords = 'салон красоты ' . $serviceName . ', студия красоты ' . $serviceName;
						$this->metaDescription =
							'На likefifa.ru Вы найдете огромное число салонов красоты по услуге “' .
							$serviceName .
							'”. Вы сможете узнать адреса салонов, прочитать отзывы посетителей, узнать цены и
							записаться к мастерам красоты!';
					}
				} else if ($params['stations'] || $params['districts']) {
					if ($params['stations']) {
						$c = count($params['stations']);
						$stationsConcatenated = ListHelper::buildNameList($params['stations']);
						$this->pageTitle =
							'Лучшие салоны красоты на метро ' .
							$stationsConcatenated .
							' - LikeFifa.ru' .
							$pageString;
						if ($c == 1) {
							$this->pageHeader =
								'Салоны красоты на м.' .
								$stationsConcatenated;
							$this->metaDescription =
								'На сайте LikeFifa.ru Вы сможете выбрать салон красоты в районе метро ' .
								$stationsConcatenated .
								' по рейтигу, отзывам, фотографиям работ. Онлайн-запись в салоны красоты.';
						} elseif ($c > 1) {
							$this->pageHeader =
								'Салоны и студии красоты на станциях метро ' .
								$stationsConcatenated;
							$this->metaDescription =
								'На сайте LikeFifa.ru Вы сможете выбрать салон красоты в районе метро ' .
								$stationsConcatenated;
						}
						$this->metaKeywords =
							'салоны красоты ' .
							$stationsConcatenated .
							', студии красоты ' .
							$stationsConcatenated;

					} else if ($params['districts']) {
						$c = count($params['districts']);
						$districtsConcatenated = ListHelper::buildNameList($params['districts']);
						$this->pageTitle =
							'Лучшие салоны красоты в районе ' .
							$districtsConcatenated .
							' - LikeFifa.ru' .
							$pageString;
						if ($c == 1) {
							$this->pageHeader =
								'Салоны красоты в районе ' .
								$districtsConcatenated;
							$this->metaKeywords =
								'салоны красоты ' .
								$districtsConcatenated .
								', студии красоты ' .
								$districtsConcatenated;
							$this->metaDescription =
								'На сайте LikeFifa.ru Вы сможете выбрать салон красоты в районе ' .
								$districtsConcatenated .
								' по рейтигу, отзывам, фотографиям работ. Онлайн-запись в салоны красоты.';
						} else if ($c > 1) {
							$areaMoscow = $params['area'];
							$this->pageHeader = "Салоны и студии красоты в округе {$areaMoscow->name}";
							if (Yii::app()->request->getQuery("districts")) {
								$this->pageSubheader = "В районах {$districtsConcatenated}";
							}
							$this->pageTitle = "Лучшие салоны красоты в округе {$areaMoscow->name} — LikeFifa.ru";
							$this->metaDescription =
								"салоны красоты {$areaMoscow->name}, студии красоты {$areaMoscow->name}";
							$this->metaKeywords =
								"На сайте LikeFifa.ru Вы сможете выбрать салон красоты в округе {$areaMoscow->name}
								по рейтигу, отзывам, фотографиям работ. Онлайн-запись в салоны красоты";
						}
					}
				} else {
					$this->pageTitle =
						"Салоны красоты " . self::$location->name_genitive . ": адреса, цены на услуги, отзывы, запись
						в салоны – LikeFifa {$pageString}";
					$this->pageHeader = 'Поиск салонов';
					$this->metaKeywords =
						'салон красоты, салоны красоты в {self::$location->name_prepositional},
						салон красоты цены,  услуги салона красоты ,
						московские салоны красоты, прайс салона красоты, где салон красоты, адреса салонов красоты,
						студия красоты, студия красоты отзывы, студия красоты москва';
					$this->metaDescription =
						'На likefifa.ru Вы найдете огромное число салонов красоты в
						{self::$location->name_prepositional}. Вы сможете узнать адреса
						салонов, прочитать отзывы посетителей, узнать цены и записаться к мастерам красоты! ';
				}
				break;

			case 'gallery':
				if ($params['specialization'] || $params['service']) {
					$serviceName = $this->getServiceName($params);

					$this->pageTitle = $serviceName . ' в картинках. Фото лучших работ мастеров';
					$this->pageHeader = $serviceName . ' в картинках. Фото лучших работ мастеров';
					$this->metaKeywords = $serviceName . ' в картинках, ' . $serviceName . ' фото';
					$this->metaDescription = $serviceName . ' в картинках. Фото лучших работ мастеров.';
				} else {
					$this->pageTitle = 'Фото работ';
					$this->pageHeader = 'Фото работ';
				}
				break;
		}
	}

	/**
	 * Проверяет мертвые услуги и получает ссылку на редирект
	 *
	 * @param string $rewriteName  абривиатура url
	 * @param string $controllerId идентификатор контрaоллера
	 *
	 * @return string
	 */
	public static function checkAndRedirectEmptyService($rewriteName, $controllerId = "masters")
	{
		if ($rewriteName) {
			switch ($rewriteName) {
				case "fenshuy-manikur":
					return Yii::app()->createUrl("{$controllerId}/manikur");
				case "dizain-nogtey-babochki":
					return Yii::app()->createUrl("{$controllerId}/dizain-nogtey");
				case "mramorniy-manikur":
					return Yii::app()->createUrl("{$controllerId}/dizain-nogtey");
				case "kitauskaya-rospis":
					return Yii::app()->createUrl("{$controllerId}/dizain-nogtey");
			}
		}
	}
}