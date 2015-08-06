<?php
namespace dfs\docdoc\components\seo;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\StreetModel;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\IllnessModel;
use RussianTextUtils, Yii, Doctor;




/**
 * Description of SEO
 * Временный костыль, в котором содержится вся логика по работе с SEO для docdoc.
 * Постепенно должен быть разбит по классам в seo/docdoc
 *
 * @author Danis
 */
class SEO extends AbstractSeo
{

	public $url = '';
	public $page = '';
	public $title = '';
	public $metaKeywords = '';
	public $metaDescription = '';
	public $head = '';
	public $text = array();
	public $city;

	/**
	 * Конструктор
	 * @param $url
	 * @param $page
	 */
	public function __construct($url, $page)
	{
		$this->url = parse_url($url, PHP_URL_PATH);
		$this->page = $page;
		$this->setCity(Yii::app()->city->getCity());
	}

	protected function pageDoctorView()
	{
		$data = explode('/', $this->url);
		$data = explode('?', $data[2]);
		$alias = $data[0];
		$doctor = new Doctor($alias);
		$doctorName = $doctor->data['Name'];
		$doctorSpecs = array();
		if (isset($doctor->data['SpecList']) && count($doctor->data['SpecList']) > 0)
			foreach ($doctor->data['SpecList'] as $spec)
				$doctorSpecs[] = $spec['Name'];
		$doctorSpecs = strtolower(implode(', ', $doctorSpecs));

		$this->setTitle("Врач-$doctorSpecs $doctorName. Запись на прием - DocDoc.ru");
		$this->setMetaKeywords("врач $doctorName");
		$this->setMetaDescription("Врач-$doctorSpecs $doctorName. На нашем сайте Вы сможете ознакомиться с информацией о враче и записаться к нему на прием!");
	}

	protected function pageDoctorList()
	{

		$params = array();

		$addPattern = '(/na-dom)?(/deti)?(/order/(experience|price|rating)/direction/(asc|desc))?(/page/([0-9]+))?';
		$addAlias = array('departure', 'kidsReception', 'trash', 'orderType', 'orderDir', 'trash', 'page');

		$map = array(
			'~^/doctor/([a-zA-Z_]+)/city/([a-zA-Z_-]+)' . $addPattern . '$~',
			'~^/doctor/([a-zA-Z_]+)/district/([a-zA-Z_]+)' . $addPattern . '$~',
			'~^/doctor/([a-zA-Z_]+)/area/([a-zA-Z_]+)(/(?!deti)([a-zA-Z_]+))?' . $addPattern . '$~',
			'~^/doctor/([a-zA-Z_]+)/stations/([0-9,]+)' . $addPattern . '$~',
			'~^/doctor/([a-zA-Z_]+)/((?!na-dom|deti)[0-9a-zA-Z_-]+)' . $addPattern . '$~',
			'~^/doctor/([a-zA-Z_]+)/na-dom$~',
			'~^/doctor/([a-zA-Z_]+)/deti$~',
			'~^/doctor/([0-9a-zA-Z_-]+)?' . $addPattern . '$~',
			'~^/(context|landing)/([a-zA-Z_]+)' . $addPattern . '$~',
			'~^/search/stations/([0-9,]+)(/near)?' . $addPattern . '$~',
			'~^/register' . $addPattern . '$~'
		);

		$aliases = array(
			array_merge(array('trash', 'speciality', 'regCity'), $addAlias),
			array_merge(array('trash', 'speciality', 'district'), $addAlias),
			array_merge(array('trash', 'speciality', 'area', 'trash', 'district'), $addAlias),
			array_merge(array('trash', 'speciality', 'stations'), $addAlias),
			array_merge(array('trash', 'speciality', 'stationAlias'), $addAlias),
			array_merge(array('trash', 'speciality', 'departure'), $addAlias),
			array_merge(array('trash', 'speciality', 'kidsReception'), $addAlias),
			array_merge(array('trash', 'speciality'), $addAlias),
			array_merge(array('trash', 'pageType', 'speciality'), $addAlias),
			array_merge(array('trash', 'stations', 'near'), $addAlias),
			array_merge(array('trash'), $addAlias)
		);

		foreach ($map as $key => $pattern) {
			if (preg_match($pattern, $this->url, $matches)) {
				foreach ($matches as $index => $value) {
					if ($aliases[$key][$index] != 'trash' && !empty($value)) {
						$params[$aliases[$key][$index]] = $value;
					}
				}
			}
		}

		// Номер страницы
		$page = isset($params['page']) ? (int)$params['page'] : 0;
		if ($page > 1)
			$pageText = ' - страница ' . $page;
		else
			$pageText = '';

		// По умолчанию
		$this->setTitle("Поиск врачей в " . $this->city['inPrepositional'] . " по всем специальностям");
		$this->setMetaKeywords("");
		$this->setMetaDescription("");

		$stationsNames = array();
		$district = '';
		$area = array();
		$regCity = '';

		if (isset($params['stations'])) {

			//params['station'] приходят в виде строки 1,2,3,4 Поэтому для проверки корректности параметров разбиваем ее в массив
			$stations = array_map(function($v) {return (int)$v;}, explode(',', $params['stations']));

			$sql = "SELECT DISTINCT name
					FROM underground_station
					WHERE id IN (" . implode(',', $stations) . ")";
			$result = query($sql);
			if (num_rows($result) > 0) {
				while ($row = fetch_object($result)) {
					$stationsNames[] = $row->name;
				}
			}
		} elseif (isset($params['stationAlias'])) {
			$sql = "SELECT DISTINCT name
                    FROM underground_station
                    WHERE rewrite_name='" . $params['stationAlias'] . "'";
			$result = query($sql);
			if (num_rows($result) > 0) {
				$row = fetch_object($result);
				$stationsNames[] = $row->name;
			}
		} elseif (isset($params['district'])) {
			$sql = "SELECT name
                    FROM district
                    WHERE rewrite_name='" . $params['district'] . "'";
			$result = query($sql);
			$row = fetch_object($result);
			$district = $row->name;
		} elseif (isset($params['area'])) {
			$sql = "SELECT id, name, full_name AS fullName, seo_text AS seoText
                    FROM area_moscow
                    WHERE rewrite_name='" . $params['area'] . "'";
			$result = query($sql);
			$area = fetch_array($result);
		} elseif (isset($params['regCity'])) {
			$sql = "SELECT name
                    FROM reg_city
                    WHERE rewrite_name='" . $params['regCity'] . "'";
			$result = query($sql);
			$row = fetch_object($result);
			$regCity = $row->name;
		}

		if (isset($params['speciality'])) {

			$spec = SectorModel::model()
				->byRewriteName($params['speciality'])
				->find();

			if (!is_null($spec)) {
				$specInLower = mb_strtolower($spec->name);
				$specInGenetive = RussianTextUtils::wordInGenitive($specInLower);
				$specInGenetivePlural = RussianTextUtils::wordInGenitive($specInLower, true);
				$specInDative = RussianTextUtils::wordInDative($specInLower);
				$specInNominative = RussianTextUtils::wordInNominative($specInLower, true);

				$this->setSeoTextsBySpeciality($spec, $params);

				if (isset($params['stations']) || isset($params['stationAlias'])) {

					if (count($stationsNames) == 1) {

						$num = 0;
						if (isset(Yii::app()->params->docFoundNum)) {
							$num = Yii::app()->params->docFoundNum;
						}

						$title_replace = array(
							'{founded}'=> RussianTextUtils::caseForNumber($num, array('Найден','Найдено','Найдено')),
							'{good}' => RussianTextUtils::caseForNumber($num, array('хороший','хороших','хороших')),
							'{doctor}' => RussianTextUtils::caseForNumber($num, array('врач','врача','врачей')),
							'{num}' => $num,
							'{station}' => $stationsNames[0],
						);

						// если это 1,21,31 но не 111,1111,11111 - именительный падеж, единственное число
						if (substr($num,-1) === '1' && substr($num,-2) !== '11') {
							$title_replace['{spec}'] = $specInLower;
						} else {
							//родительный падеж + зависимость от количества
							//1,21,31 - гастроэнтеролог сюда не попадут
							//2,3,4 - гастроэнтеролога, единственное число число
							//6,7,8 - гастроэнтерологово, множественное число
							$many = RussianTextUtils::caseForNumber($num, array(false,false,true));
							$title_replace['{spec}'] = RussianTextUtils::wordInGenitive($specInLower, $many);
						}

						$this->setTitle(strtr($spec->sector_seo_title, $title_replace));
						$this->setMetaDescription("На нашем сайте представлены лучшие врачи-$specInNominative, которые ведут прием в районе метро " . $stationsNames[0] . ". Рейтинги специалистов и отзывы пациентов на DocDoc.ru." . $pageText);
						$this->setMetaKeywords($specInNominative . " " . strtolower($stationsNames[0]));
						$this->clearSeoText();
					} elseif (count($stationsNames) > 1) {

						$this->setTitle(RussianTextUtils::wordInNominative($spec->name, true) . ' на станции метро: ' . implode(', ', $stationsNames) . ' - DocDoc.ru ' . $pageText);
						$this->setMetaDescription(RussianTextUtils::wordInNominative($spec->name, true) . ' на станции метро: ' . implode(', ', $stationsNames) . ' ' . $pageText);
						$this->setMetaKeywords($specInLower . " " . strtolower(implode(' ', $stationsNames)));

						$this->clearSeoText();
						$this->setSeoText(1, 'На станциях метро: ' . strtolower(implode(', ', $stationsNames)));
					}
				} else {

					if (empty($district) && !empty($area)) {

						$this->setTitle("Врачи-$specInNominative в " . RussianTextUtils::wordInPrepositional($area['fullName']) . " (" . $area['name'] . ") - DocDoc.ru" . $pageText);
						$this->setMetaDescription("На сайте DocDoc.ru Вы сможете найти врача-$specInGenetive в " . $area['name'] . ", узнать цену приема и записаться к нему на консультацию." . $pageText);
						$this->setMetaKeywords("врач " . $specInLower . " " . strtolower($area['name']));

						$this->clearSeoText();
						if ($area['id'] <> 1) {
							$this->setSeoText(SeoInterface::SEO_TEXT_TOP, "Если Вы проживаете в городе " . $area['seoText'] . " и Вам нужен врач-" . $specInLower . ", DocDoc.ru рекомендует Вам
						            хороших специалистов из тех клиник " . $this->city['inGenitive'] . ", которые расположены максимально близко к Вашему городу."
							);
						}
					} elseif (!empty($district)) {

						$this->setTitle("Врачи-$specInNominative в районе: " . $district . " - DocDoc.ru" . $pageText);
						$this->setMetaDescription("На нашем сайте представлены лучшие врачи-$specInNominative, которые ведут прием в районе: $district. Рейтинги специалистов и отзывы пациентов на DocDoc.ru" . $pageText);
						$this->setMetaKeywords($spec->name . " " . strtolower($district));

						$this->clearSeoText();
					} elseif (!empty($regCity)) {

						$this->setTitle(RussianTextUtils::wordInNominative($spec->name, true) . " в г. $regCity - DocDoc.ru" . $pageText);
						$this->setMetaDescription("У нас на сайте представлены врачи-$specInNominative из клиник " . $this->city['inGenitive'] . ", до которых удобнее всего добираться из г.$regCity." . $pageText);
						$this->setMetaKeywords($specInLower . " " . $regCity);

						$this->clearSeoText();
						if ($this->city['id'] == 1) {
							$cityText = 'московской ';
							$cityTextInPlural = 'московских ';
						} elseif ($this->city['id'] == 2) {
							$cityText = 'питерской ';
							$cityTextInPlural = 'питерских ';
						} else {
							$cityText = '';
							$cityTextInPlural = '';
						}
						$this->setSeoText(SeoInterface::SEO_TEXT_TOP, "<p><b>Нужен " . $specInLower . " в городе $regCity</b>? Лучше всего пройти консультацию врача из хорошей " . $cityText . " клиники.<br>DocDoc.ru  предлагает Вам специальную подборку лучших врачей из тех клиник " . $this->city['inGenitive'] . ", до которых удобнее всего добираться из города $regCity.</p>
						         <p><b>" . RussianTextUtils::wordInNominative($spec->name, true) . " на сайте DocDoc.ru.</b> На нашем сайте собраны высокопрофессиональные <b>$specInNominative</b> из лучших " . $cityTextInPlural . " клиник. Ознакомьтесь с информацией об образовании докторов, их стаже работы и обязательно прочтите отзывы, оставленные другими пациентами. Запишитесь на прием прямо сейчас!</p>"
						);
					} elseif (count($stationsNames) == 0) {

						$this->setTitle(RussianTextUtils::wordInNominative($spec->name, true) . " " . $this->city['inGenitive'] . ", запись на прием, рейтинги и отзывы на DocDoc.ru" . $pageText);
						$this->setMetaDescription("У нас на сайте представлены врачи - $specInNominative из центров и клиник "
							. $this->city['inGenitive'] . ". Вы сможете прочитать отзывы, узнать стоимость консультации и записаться на прием к врачу-$specInDative." . $pageText);
						$this->setMetaKeywords($spec->name . ", врачи $specInNominative " . $this->city['inGenitive'] . ", консультация $specInGenetive, прием $specInGenetive");

						if (isset($params['departure'])) {
							$this->setTitle("Вызов $specInGenetive на дом. Рейтинги врачей и отзывы пациентов на DocDoc.ru" . $pageText);

							$this->setMetaDescription("У нас на сайте представлены врачи-$specInNominative с выездом на дом."
								. " Вы сможете прочитать отзывы, узнать стоимость консультации и  вызвать на дом  врача-$specInGenetive" . $pageText);

							$this->setMetaKeywords($spec->name . " на дом, " . $spec->name . " выезд на дом, " . $spec->name . " вызов на дом, " . $spec->name . " вызов врача, " . $spec->name . " вызов");

						} elseif (isset($params['kidsReception'])) {
								$this->setTitle('Детские ' . $specInNominative . ' ' . $this->city['inGenitive'] . ': отзывы, запись на прием - DocDoc.ru' . $pageText);

								$this->setMetaDescription('У нас на сайте представлены детские врачи-' . $specInNominative . ' из центров и клиник Москвы. ' .
									'Вы сможете прочитать отзывы, узнать стоимость и записаться на прием к десткому ' . $specInDative . $pageText);

								$this->setMetaKeywords(
									'детский ' . $spec->name . ', ' .
									'детский ' . $spec->name . ' ' . $this->city['inGenitive'] . ', ' .
									'детский ' . $spec->name . ' отзывы, ' .
									'хорошие детские ' . $specInNominative
								);
						}
					}
				}
			}

			if (isset($params['pageType']) && $params['pageType'] == 'landing') {
				$this->clearSeoText();
			}
		} else {
			$addText = count($stationsNames) > 0 ? ' рядом с метро ' . implode(', ', $stationsNames) : '';
			$this->setTitle('Поиск врачей в ' . $this->city['inPrepositional'] . ' по всем специальностям' . $addText);
		}
	}

	/**
	 * Установка сео-текстов при выбранной специальности
	 *
	 * @param SectorModel $spec
	 * @param array       $params
	 */
	private function setSeoTextsBySpeciality(SectorModel $spec, $params)
	{
		$page = isset($params['page']) ? (int)$params['page'] : 1;
		$specInLower = mb_strtolower($spec->name);
		$specInGenitive = RussianTextUtils::wordInGenitive($specInLower);
		$specInGenitivePlural = RussianTextUtils::wordInGenitive($specInLower, true);
		$specInNominative = RussianTextUtils::wordInNominative($specInLower, true);

		if ($page == 1) {
			if (isset($params['departure'])) {
				$cityInDative = Yii::app()->city->isMoscow()
					? 'Москве и Московской области'
					: Yii::app()->city->getTitle('dative');
				if (isset($params['kidsReception'])) {
					// сео-тексты при поиске детских врачей, которые выезжают на дом
					$this->setSeoText(SeoInterface::SEO_TEXT_TOP, "<p>Ниже представлены анкеты детских {$specInGenitivePlural}, которые выезжают на дом.
						В анкете врача Вы найдете всю подробную информацию об образовании врача, его специализации, а так же отзывы других пациентов.
						Чтобы вызвать на дом детского врача, Вам необходимо оставить заявку на сайте или позвонить по телефону в контактный центр.</p>
						<p>Детские врачи-{$specInNominative} осуществляют выезд на дом по {$cityInDative}.</p>
						<p>Цена в анкете детского врача-{$specInGenitive} указана за прием в клинике.
						Стоимость выезда специалиста для ребенка будет отличаться от цены, указанной в анкете.
						Окончательную стоимость вызова детского врача на дом уточняйте в контактном центре,
						так как цена может зависеть от степени удаленности медицинского центра.</p>
						");
				} else {
					$addText = Yii::app()->city->isMoscow() ? ' Стоимость выезда на дом может меняться в зависимости от степени удаленности от МКАДа конечного пункта назначения.' : '';
					$this->setSeoText(SeoInterface::SEO_TEXT_TOP, "<p>Ниже представлены анкеты врачей-{$specInGenitivePlural}, осуществляющих выезд на дом. В анкете врача Вы найдете подробную информацию о его профессиональной деятельности, а также отзывы, оставленные пациентами. Если Вам необходимо вызвать {$specInGenitive} на дом, Вы можете заполнить электронную заявку или позвонить в наш контактный центр, и консультант подберет специалиста с учетом Ваших требований.</p>
						<p>Врачи-$specInNominative осуществляют выезд на дом по {$cityInDative}.</p>
						<p>Обратите внимание, что в анкетах врачей указана стоимость приема врача в клинике. Стоимость выезда на дом будет отличаться от указанной стоимости приема. Для того чтобы узнать, сколько стоит выезд на дом конкретного врача, позвоните в наш контактный центр, и консультант сообщит Вам всю необходимую информацию.$addText</p>
					");
				}
			} elseif (isset($params['kidsReception'])) {
				// сео-тексты при поиске детских врачей
				$this->setSeoText(SeoInterface::SEO_TEXT_TOP, "<p>Если Вашему ребенку нужна консультация детского {$specInGenitive},
						обратитесь за помощью на наш портал. На DocDoc.ru Вы можете найти врача для своего ребенка в любом районе города и записаться к нему на прием.
						Ниже представлены детские врачи-{$specInNominative} {$this->city['inGenitive']} с указанием цены и места приема.</p>
						<p><strong>На нашем портале</strong> вы можете выбрать детского врача из лучших клиник {$this->city['inGenitive']} и записаться к нему на прием.
						Найти частного детского {$specInGenitive} вам помогут анкеты врачей с информацией об их опыте работы, образовании, а также отзывы пациентов.</p>
					");
				$this->setSeoText(SeoInterface::SEO_TEXT_BOTTOM, "<p>Часто задаваемые вопросы:</p>
						<p><strong>Где найти хорошего детского {$specInGenitive}?</strong></p>
						<p>Хорошего детского {$specInGenitive} вы можете найти на DocDoc.ru.
						Здесь вы сможете выбрать врача для ребенка исходя из важных для вас критериев: район проживания,
						стоимость услуг, опыт работы врача.</p>
						<p><strong>Посоветуйте хорошего {$specInGenitive} для ребенка.</strong></p>
						<p>Для выбора хорошего {$specInGenitive} посмотрите отзывы и рекомендации пациентов о специалистах нашего портала,
						также стоит обратить внимание на образование и опыт работы врача, указанные в анкете.</p>
					");
			} else {
				foreach ($spec->seoTexts as $seoText) {
					$text = str_replace('Москвы', Yii::app()->city->getTitle('genitive'), $seoText->text);
					$this->setSeoText($seoText->position, $text);
				}
			}
		} elseif($page == 2 && !isset($params['kidsReception'])) {
			if ($spec->rewrite_name == 'stomatolog') {

				$this->setSeoText(SeoInterface::SEO_TEXT_TOP, "<p>На портале DocDoc представлены ведущие врачи " . Yii::app()->city->getTitle('genitive') . "</p>
					<p>В данном разделе Вы можете выбрать из списка стоматологов хорошего зубного врача, который ведет прием на удобной для Вас станции метро.</p>
					<p>Если Вам нужен стоматолог с большим стажем работы или определенной стоимостью приема, Вы можете отсортировать врачей по важному для Вас критерию.</p>"
				);
				$this->setSeoText(SeoInterface::SEO_TEXT_BOTTOM, "<p>Часто задаваемые вопросы:</p><p><strong>Посоветуйте, пожалуйста, хорошего стоматолога?</strong></p>
					<p>Все врачи на нашем портале являются высококвалифицированными стоматологами, чтобы подобрать подходящего вам врача обратите внимание в анкете на рейтинг и опыт работы врача.</p>
					<p><strong>Ищу стоматолога, порекомендуйте кого-нибудь?</strong></p>
					<p>Если вам нужны рекомендации по врачам, советуем вам ознакомиться с отзывами, опубликованными в анкетах стоматологов. Это поможет вам подобрать хорошего врача.</p>"
				);
			} elseif ($spec->rewrite_name == 'ginekolog') {

				$this->setSeoText(SeoInterface::SEO_TEXT_TOP, "<p>В данном разделе вы можете выбрать платного гинеколога и записаться на консультацию к нему онлайн.</p>
							<p>Если Вам  нужен гинеколог с большим стажем работы или определенной стоимостью приема, Вы можете отсортировать врачей по важному для Вас критерию. Кроме того вы можете подобрать гинеколога, посещение которого будет проходить на удобной для вас станции метро.</p>"
				);
				$this->setSeoText(SeoInterface::SEO_TEXT_BOTTOM, "<p>Часто задаваемые вопросы:</p><p><strong>Подскажите, пожалуйста,  гинеколога с большим опытом работы?</strong></p>
					<p>Все врачи на нашем портале являются высококвалифицированными гинекологами, чтобы подобрать подходящего вам врача обратите внимание в анкете на рейтинг и опыт работы врача.</p>
					<p><strong>Подскажите, как часто нужно бывать на осмотре гинеколога?</strong></p>
					<p>Осмотр гинеколога нужно проходить ежегодно. Так же необходимо обратиться к гинекологу при возникновении, каких  либо жалоб или симптомов.</p>"
				);
			}
		}
	}

	protected function pageRegister()
	{
		$this->setTitle('Регистрация врачей и клиник');
		$this->setMetaKeywords("регистрация врачей и клиник");
		$this->setMetaDescription("DocDoc.ru – регистрация врачей и клиник");
	}

	protected function pageIllness()
	{
		$this->setTitle('Справочник заболеваний');
		$this->setMetaKeywords("болезни, справочник заболеваний, справочник болезней");
		$this->setMetaDescription("Самый полный справочник заболеваний");
	}

	protected function pageHelp()
	{
		$this->setTitle('DocDoc.ru - у нас легко найти врача');
	}

	protected function pageIllnessText()
	{
		$pattern = '~^/illness/([a-zA-Z_-]+/)?(?P<alias>([0-9a-zA-Z_-]+))?(.+)?$~';
		preg_match($pattern, $this->url, $matches);

		if (!empty($matches['alias'])) {
			$illness = IllnessModel::model()->byRewriteName($matches['alias'])->find();

			if (!empty($illness->title))
				$this->setTitle($illness->title);
			else if (!empty($illness->name))
				$this->setTitle($illness->name . ": симптомы и лечение. Ведущие специалисты по лечению  - DocDoc.ru");
			else {
				$this->setTitle('Справочник заболеваний');
			}

			$this->setMetaKeywords($illness->meta_keywords);
			if (!empty($illness->meta_desc))
				$this->setMetaDescription($illness->meta_desc);
			else {
				$cityName = Yii::app()->city->getCity()->title;

				$this->setMetaDescription("У нас на сайте представлены ведущие специалисты по лечению " . $illness->full_name . " из центров и клиник " . RussianTextUtils::wordGenitive($cityName) . ". Вы сможете прочитать отзывы, узнать стоимость консультации и записаться на прием.");
			}
		}
	}

	protected function pageLibrary()
	{
		$this->setTitle('Справочник пациента. Медицинские статьи - DocDoc');
		$this->setMetaKeywords("");
		$this->setMetaDescription("");

		$pattern = '~^/library(/([a-zA-Z_-]+))?(/([a-zA-Z_-]+))?$~';
		$aliases = array('trash', 'trash', 'section', 'trash', 'article');

		if (preg_match($pattern, $this->url, $matches)) {

			foreach ($matches as $index => $value) {
				if ($aliases[$index] != 'trash' && !empty($value)) {
					$params[$aliases[$index]] = $value;
				}
			}
			//var_dump($params);die;
		}

		if (isset($params['section'])) {
			$section = getSection($params['section']);
			if (isset($section['Name']))
				$this->setTitle($section['Name']);
		}

		if (isset($params['article'])) {
			$article = getArticleByAlias($params['article']);
			if (count($article) > 0) {
				$this->setTitle($article['Title']);
				$this->setMetaKeywords($article['MetaKeywords']);
				$this->setMetaDescription($article['MetaDescription']);
			}
		}
	}

	protected function pageIndex()
	{
		$this->setTitle('DocDoc - поиск врачей в ' . $this->city['inPrepositional']);
		$this->setMetaKeywords("врач, врачи " . $this->city['inGenitive'] . ", найти врача, поиск врачей");
		$this->setMetaDescription("DocDoc.ru – сервис по поиску врачей");
	}

	protected function page404()
	{
		$this->setTitle('DocDoc - поиск врачей в ' . $this->city['inPrepositional']);
		$this->setMetaKeywords("");
		$this->setMetaDescription("");
	}

	protected function pageClinicList()
	{

		$params = array();

		$map = array(
			'~^/clinic(/spec)?(/page/([0-9]+))?$~',
			'~^/clinic/spec/([a-zA-Z_]+)(/page/([0-9]+))?$~',
			'~^/clinic/spec/([a-zA-Z_]+)/city/([a-zA-Z_-]+)(/page/([0-9]+))?$~',
			'~^/clinic/spec/([a-zA-Z_]+)/district/([a-zA-Z_-]+)(/page/([0-9]+))?$~',
			'~^/clinic/spec/([a-zA-Z_]+)/area/([a-zA-Z_-]+)(/([a-zA-Z_-]+))?(/page/([0-9]+))?$~',
			'~^/clinic/(spec/([a-zA-Z_]+))?/stations/([0-9,]+)(/page/([0-9]+))?$~',
			'~^/clinic/spec/([a-zA-Z_]+)/([0-9a-zA-Z_-]+)(/page/([0-9]+))?$~',
			'~^/clinic/station/([0-9a-zA-Z_-]+)(/page/([0-9]+))?$~',
			'~^/clinic/street/([0-9a-zA-Z_-]+)(/page/([0-9]+))?$~',
			'~^/clinic/district/([a-zA-Z_-]+)(/page/([0-9]+))?$~',
			'~^/clinic/area/([a-zA-Z_-]+)(/([a-zA-Z_-]+))?(/page/([0-9]+))?$~',
		);

		$aliases = array(
			array('trash', 'trash', 'trash', 'page'),
			array('trash', 'specialization', 'trash', 'page'),
			array('trash', 'specialization', 'regCity', 'trash', 'page'),
			array('trash', 'specialization', 'district', 'trash', 'page'),
			array('trash', 'specialization', 'area', 'trash', 'district', 'trash', 'page'),
			array('trash', 'trash', 'specialization', 'stations', 'near', 'trash', 'page'),
			array('trash', 'specialization', 'stationAlias', 'trash', 'page'),
			array('trash', 'stationAlias', 'trash', 'page'),
			array('trash', 'streetAlias', 'trash', 'page'),
			array('trash', 'district', 'trash', 'page'),
			array('trash', 'area', 'trash', 'district', 'trash', 'page'),
		);

		foreach ($map as $key => $pattern) {
			if (preg_match($pattern, $this->url, $matches)) {
				foreach ($matches as $index => $value) {
					if ($aliases[$key][$index] != 'trash' && !empty($value)) {
						$params[$aliases[$key][$index]] = $value;
					}
				}
			}
		}

		// Номер страницы
		$page = isset($params['page']) ? (int)$params['page'] : 0;
		if ($page > 1) {
			$pageText = ' - страница ' . $page;
		} else {
			$pageText = '';
		}

		// По умолчанию
		$this->setTitle("Медицинские центры и клиники " . $this->city['inGenitive'] . " - DocDoc" . $pageText);
		$this->setMetaKeywords(
			"клиники, медицинские центры, медицинские центры " .
			$this->city['inGenitive'] .
			", клиники " .
			$this->city['inGenitive'] .
			", медцентры"
		);
		$this->setMetaDescription(
			"Все медицинские центры и клиники " .
			$this->city['inGenitive'] .
			" на DocDoc.ru. Отзывы пациентов, рейтинги клиники. Удобный поиск по метро/районам."
		);
		$this->setHead("Медицинские центры и клиники " . $this->city['inGenitive']);

		$stationsNames = array();
		$district = '';
		$area = array();
		$regCity = '';

		if (isset($params['stations'])) {
			$sql = "SELECT DISTINCT name
                    FROM underground_station
                    WHERE id IN (" . $params['stations'] . ")";
			$result = query($sql);
			if (num_rows($result) > 0) {
				while ($row = fetch_object($result)) {
					$stationsNames[] = $row->name;
				}
			}
		} elseif (isset($params['stationAlias'])) {
			$sql = "SELECT DISTINCT name
                    FROM underground_station
                    WHERE rewrite_name='" . $params['stationAlias'] . "'";
			$result = query($sql);
			if (num_rows($result) > 0) {
				$row = fetch_object($result);
				$stationsNames[] = $row->name;
			}
		} elseif (isset($params['district'])) {
			$sql = "SELECT name
                    FROM district
                    WHERE rewrite_name='" . $params['district'] . "'";
			$result = query($sql);
			$row = fetch_object($result);
			$district = $row->name;
		} elseif (isset($params['area'])) {
			$sql = "SELECT id, name, full_name AS fullName, seo_text AS seoText
                    FROM area_moscow
                    WHERE rewrite_name='" . $params['area'] . "'";
			$result = query($sql);
			$area = fetch_array($result);
		} elseif (isset($params['regCity'])) {
			$sql = "SELECT name
                    FROM reg_city
                    WHERE rewrite_name='" . $params['regCity'] . "'";
			$result = query($sql);
			$row = fetch_object($result);
			$regCity = $row->name;
		}

		if (isset($params['specialization'])) {

			$sql = "SELECT
						id, clinic_seo_title AS name, LOWER(spec_name) AS specName, sector_seo_title as seoTitle
					FROM sector
					WHERE rewrite_spec_name='" . $params['specialization'] . "'";

			$result = query($sql);
			if (num_rows($result) == 1) {

				$spec = fetch_object($result);

				$sql = "SELECT t1.text AS Text, t1.position AS Position
						FROM sector_seo_text t1
						INNER JOIN sector_seo_text_sector t2 ON t2.sector_seo_text_id=t1.id
						WHERE t2.sector_id='" . $spec->id . "'
						    AND t1.page_type = 2
						    AND t1.disabled = 0";
				$result = query($sql);
				while ($row = fetch_object($result)) {
					$text = str_replace('Москвы', $this->city['inGenitive'], $row->Text);
					$this->setSeoText($row->Position, $text);
				}

				if ($page > 1) {
					$this->clearSeoText();
				}

				$this->setMetaDescription(
					"Запись в " .
					mb_strtolower($spec->name) .
					". Все медицинские центры " .
					$this->city['inGenitive'] .
					" на одном сайте! Рейтинги, отзывы посетителей." .
					$pageText
				);
				$this->setMetaKeywords(
					"клиника " .
					RussianTextUtils::wordInGenitive($spec->specName) .
					", частные клиники " .
					RussianTextUtils::wordInGenitive($spec->specName) .
					", " .
					$spec->specName .
					" клиники " .
					$this->city['inGenitive'] .
					", " .
					"платная клиника " .
					$spec->specName .
					", медицинский центр " .
					RussianTextUtils::wordInGenitive($spec->specName) .
					", центр " .
					RussianTextUtils::wordInGenitive($spec->specName)
				);
				$this->setHead($spec->name);
				if (isset($params['stations']) || isset($params['stationAlias'])) {

					if (count($stationsNames) == 1) {
						$this->setTitle($spec->name . " на м. " . $stationsNames[0] . " - DocDoc.ru" . $pageText);
						$this->setMetaKeywords($spec->name . " " . $stationsNames[0]);
						$this->setMetaDescription($spec->name . " на метро " . $stationsNames[0] . " - DocDoc.ru");
						$this->setHead($spec->name . " на м. " . $stationsNames[0]);
						$this->clearSeoText();
					} elseif (count($stationsNames) > 1) {
						$this->setTitle(
							$spec->name .
							' на станциях метро: ' .
							implode(', ', $stationsNames) .
							' - DocDoc.ru ' .
							$pageText
						);
						$this->clearSeoText();
					}

				} else {

					if (empty($district) && !empty($area)) {
						$this->setTitle($spec->name . " в округе " . $area['name'] . " - DocDoc.ru" . $pageText);
						$this->setMetaKeywords($spec->name . " округ " . $area['name']);
						$this->setMetaDescription($spec->name . " в округе " . $area['name'] . " - DocDoc.ru");
						$this->setHead($spec->name . " в округе " . $area['name']);
						$this->clearSeoText();
					} elseif (!empty($district)) {
						$this->setTitle($spec->name . " в районе " . $district . " - DocDoc.ru" . $pageText);
						$this->setMetaKeywords($spec->name . " район " . $district);
						$this->setMetaDescription($spec->name . " в районе " . $district . " - DocDoc.ru");
						$this->setHead($spec->name . " в районе: " . $district);
						$this->clearSeoText();
					} elseif (!empty($regCity)) {
						$this->setTitle($spec->name . " в г. $regCity - DocDoc.ru" . $pageText);
						$this->setMetaKeywords($spec->name . " " . $regCity);
						$this->setMetaDescription($spec->name . " в г. " . $regCity . " - DocDoc.ru");
						$this->setHead($spec->name . " в г. " . $regCity);
						$this->clearSeoText();
					} elseif (count($stationsNames) == 0) {
						$this->setTitle(
							$spec->name .
							" в " .
							$this->city['inPrepositional'] .
							" - запись, рейтинги, отзывы - DocDoc.ru" .
							$pageText
						);
					}

				}

			}

		} elseif (isset($params['streetAlias'])) {
			$street = StreetModel::model()
				->searchByAlias($params['streetAlias'])
				->inCity($this->city['id'])
				->find();

			$streetTitle = $street->getFullTitle();

			$this->setTitle('Клиники по адресу: "' . $streetTitle . '" - DocDoc.ru' . $pageText);
			$this->setMetaKeywords("клиника " . $streetTitle . ", медицинский центр " . $streetTitle . ", медцентр " . $streetTitle);
			$this->setMetaDescription('На нашем портале Вы сможете найти медицинские центры по адресу: "' . $streetTitle . '". Рейтинги, отзывы, запись на прием — DocDoc.ru');
			$this->setHead('Медицинские центры по адресу: "' . $streetTitle . '"');
		} else {
			if (count($stationsNames) == 1) {
				$this->setTitle("Клиники и медицинские центры на м. {$stationsNames[0]} - DocDoc.ru");
				$this->setMetaKeywords("клиника {$stationsNames[0]}, медцентр {$stationsNames[0]}, медицинский центр {$stationsNames[0]}");
				$this->setMetaDescription("На нашем сайте представлены клиники на м. {$stationsNames[0]}. У нас вы найдете: отзывы пациентов, информацию о врачах клиники, контактный телефон и адрес. Запись по телефону или online!");
				$this->setHead('Медицинские центры и клиники на м. ' . $stationsNames[0]);
				$this->setSeoText(1, "<p>На нашем портале вы можете найти лучшие клиники и медицинские центры на метро {$stationsNames[0]}. В анкетах клиник обратите внимание на цены и отзывы посетителей.</p>");
			} elseif (count($stationsNames) > 1) {
				$this->setTitle("Медицинские центры и клиники рядом с метро " . implode(', ', $stationsNames) . " - запись, рейтинги, отзывы - DocDoc.ru {$pageText}");
			} elseif (!empty($district)) {
				$this->setTitle('Клиники в районе "' . $district . '" - DocDoc.ru');
				$this->setMetaKeywords("клиника {$district}, медицинский центр {$district}, медцентр {$district}");
				$this->setMetaDescription("На DocDoc.ru Вы найдете клиники и медицинские центры в районе {$district}. Врачи, отзывы пациентов, контактные данные.");
				$this->setHead('Клиники и медицинские центры в районе: "' . $district . '"');
				$this->setSeoText(1, '<p>На нашем портале вы можете найти лучшие клиники и медицинские центры в районе "' . $district . '". В анкетах клиник обратите внимание на цены и отзывы посетителей.</p>');
			} elseif (!empty($area)) {
				$this->setTitle("Клиники в округе {$area['name']} - DocDoc.ru");
				$this->setMetaKeywords("клиника {$area['name']}, медицинский центр {$area['name']}, медцентр {$area['name']}");
				$this->setMetaDescription("На DocDoc.ru Вы найдете клиники и медицинские центры в округе {$area['name']}. Врачи, отзывы пациентов, контактные данные.");
				$this->setHead("Клиники и медицинские центры в {$area['name']}");
				$this->setSeoText(1, "<p>На нашем портале вы можете найти лучшие клиники и медицинские центры в округе {$area['name']}. В анкетах клиник обратите внимание на цены и отзывы посетителей.</p>");
			}
		}

		if (isMobileBrowser()) {
			$this->clearSeoText();
		}
	}

	/**
	 * Генерация тайтлов и мета-тегов для страницы клиники
	 */
	protected function pageClinic()
	{

		$pattern = '~^/clinic/(?P<alias>([0-9a-zA-Z_-]+))?(.+)?$~';
		preg_match($pattern, $this->url, $matches);

		if (!empty($matches['alias'])) {
			$clinic = ClinicModel::model()->searchByAlias($matches['alias'])->find();

			if (!is_null($clinic)) {
				$this->setTitle("{$clinic->name} - врачи, отзывы, телефон, адрес - DocDoc.ru");
				$this->setMetaKeywords("{$clinic->name}, {$clinic->name} врачи, {$clinic->name} отзывы, {$clinic->name} телефон, {$clinic->name} адрес, {$clinic->name} как добраться");
				$this->setMetaDescription("{$clinic->name}: врачи клиники, отзывы пациентов, контактный телефон и адрес. Запись онлайн на DocDoc.ru.");
			}
		}

	}

	/**
	 * Устанока используемого города
	 * @param CityModel $city
	 */
	public function setCity(CityModel $city)
	{
		$this->city = [
			'id'                => $city->id_city,
			'alias'             => $city->rewrite_name,
			'name'              => $city->title,
			'inPrepositional'   => $city->title_prepositional,
			'inGenitive'        => $city->title_genitive,
			'inDative'          => $city->title_dative,
		];
	}

	public function setTitle($text)
	{
		$this->title = $text;
	}

	public function setMetaKeywords($text)
	{
		$this->metaKeywords = $text;
	}

	public function setMetaDescription($text)
	{
		$this->metaDescription = $text;
	}

	/**
	 * Установка заголовка для страницы
	 * @param string $text
	 */
	public function setHead($text)
	{
		$this->head = $text;
	}

	public function setSeoText($pos, $text)
	{
		$data = array(
			'Position' => $pos,
			'Text' => $text,
		);
		array_push($this->text, $data);
	}

	public function clearSeoText()
	{
		$this->text = array();
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function getMetaKeywords()
	{
		return $this->metaKeywords;
	}

	public function getMetaDescription()
	{
		return $this->metaDescription;
	}

	/**
	 * Получение заголовка для страницы
	 * @return string
	 */
	public function getHead()
	{
		return $this->head;
	}

	/**
	 * Выбор метода
	 *
	 * @return boolean
	 */
	public function seoInfo()
	{
		$method = 'page' . $this->page;

		if (method_exists($this, $method))
			call_user_func(array('self', $method));

		return true;
	}

}
