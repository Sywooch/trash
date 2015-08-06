<?php

namespace dfs\docdoc\api\feed;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\DoctorOpinionModel;
use CActiveDataProvider;
use CDataProviderIterator;
use Yii;

/**
 * Class YmlFeed
 *
 * @package dfs\docdoc\api\feed
 *
 *
 */
class YmlFeed {

	const CATEGORY_CLINIC = "clinic";
	const CATEGORY_DOCTOR = "doctor";
	const CATEGORY_DIAGNOSTIC = "diagnostic";

	/**
	 * документ
	 * @var \DOMDocument
	 */
	private $_doc = null;
	/**
	 * Идентификатор города
	 * @var integer
	 */
	private $_city_id = null;

	/**
	 * Город
	 * @var CityModel
	 */
	private $_city = null;

	/**
	 * @param integer $city_id
	 * @throws \CException
	 */
	public function __construct($city_id)
	{
		$this->_city_id = $city_id;
		$this->_city = CityModel::model()->findByPk($this->_city_id);

		if ($this->_city === null) {
			throw new \CException("Некорректный город");
		}

		$this->_doc = new \DOMDocument('1.0', 'utf-8');
	}

	/**
	 * Получение фида в виде XML
	 * @return string
	 */
	public function getFeed()
	{
		$this->createFeed();
		return $this->_doc->saveXML();
	}

	/**
	 * получение документа
	 *
	 * @return \DOMDocument|null
	 */
	public function getDocument()
	{
		return $this->_doc;
	}

	/**
	 * Сборка документа
	 */
	public function createFeed()
	{
		$yml_catalog = $this->_doc->createElement('yml_catalog');
		$yml_catalog->setAttribute('date', date('Y-m-d H:i'));

		$yml_catalog->appendChild($this->shop());

		$this->_doc->appendChild($yml_catalog);
	}

	/**
	 *	<shop>
	 * 		<name>DocDoc</name>
	 *		<company>DocDoc</company>
	 *		<url>http://docdoc.ru/</url>
	 *		<currencies>
	 *			......
	 *		</currencies>
	 *
	 *		<categories>
	 *		.......
	 *		</categories>
	 *
	 *		<offers>
	 * 		......
	 * 		</offers>
	 *  </shop>
	 *
	 * @return \DOMElement
	 */
	public function shop()
	{
		$shop = $this->_doc->createElement('shop');

		$name = $this->_doc->createElement('name');
		$name->appendChild($this->_doc->createTextNode('DocDoc'));

		$company = $this->_doc->createElement('company');
		$company->appendChild($this->_doc->createTextNode('DocDoc'));

		$url = $this->_doc->createElement('url');
		$url->appendChild($this->_doc->createTextNode("http://{$this->_city->prefix}docdoc.ru/"));

		$shop->appendChild($name);
		$shop->appendChild($company);
		$shop->appendChild($url);
		$shop->appendChild($this->categories());
		$shop->appendChild($this->currencies());
		$shop->appendChild($this->offers());

		return $shop;
	}


	/**
	 *	<categories>
	 *		<!--
	 *		Три основных услуги:
	 *
	 *		1. запись в клинику
	 *		-->
	 *		<category id="clinic">Запись в клинику</category>
	 *
	 *		<!--
	 *		2. запись на прием к врачу
	 *		-->
	 *		<category id="doctor">Запись на прием к врачу</category>
	 *		<category parentId="doctor" id="doctor_2">Врач акушер-гинеколог</category>
	 *		<category  parentId="doctor" id="doctor_3">Врач-дерматолог</category>
	 *
	 *		<!--
	 *		2. запись на диагностику
	 *		-->
	 *		<category id="diagnostic">Запись на диагностику</category>
	 *		<category parentId="diagnostic" id="diagnostic_7">Узи</category>
	 *		<category  parentId="diagnostic_7" id="diagnostic_8">Узи-печени</category>
	 *		</categories>
	 *
	 * @return \DOMElement
	 */
	public function categories()
	{
		$categories = $this->_doc->createElement('categories');

		$categories->appendChild($this->category('Запись в клинику', $this->getCategoryClinic()));
		$categories->appendChild($this->category('Запись на прием к врачу', $this->getCategoryDoctor()));

		// провайдер данных и итератор
		$dataProvider = new CActiveDataProvider(
			'dfs\docdoc\models\SectorModel',
			array(
				'criteria' => array(
					'scopes' => ['active', 'inCity' => $this->_city_id],
					'together' => true,
					'group' => 't.id'
				),
			)
		);
		$sectorIterator = new CDataProviderIterator($dataProvider, 30);

		foreach ($sectorIterator as $sector) {
			$categories->appendChild($this->category($sector->name, $this->getSectorId($sector->id), $this->getCategoryClinic()));
		}

		/*
		 * запись на диагностику пока что не отдаем
		$categories->appendChild($this->category('Запись на диагностику', $this->getCategoryDiagnoctic()));
		$diagnostics = DiagnosticaModel::model()
			->onlyParents()
			->with('childs')
			->findAll(['order' => 't.sort, childs.sort']);

		foreach ($diagnostics as $d) {
			$categories->appendChild($this->category($d->name, $d->id, $this->getCategoryDiagnoctic()));

			foreach ($d->childs as $c) {
				$categories->appendChild($this->category($c->name, "diagnostic_" . $c->id, $d->id));
			}

		}*/

		return $categories;
	}

	/**
	 * Генерация тега <category>....</category>
	 *
	 * @param string $text
	 * @param string $id
	 * @param null|string $parentId
	 *
	 * @return \DOMElement
	 */
	public function category($text, $id, $parentId = null)
	{
		$category = $this->_doc->createElement('category');
		$category->setAttribute('id', $id);
		if ($parentId !== null) {
			$category->setAttribute('parentId', $parentId);
		}
		$category->appendChild($this->_doc->createTextNode($text));

		return $category;
	}

	/**
	 * Генерация тега с курсами валют
	 *
	 * <currencies>
	 *		<currency id="RUR" rate="1" plus="0"/>
	 *	</currencies>
	 *
	 * @return \DOMElement
	 */
	public function currencies()
	{
		$currencies = $this->_doc->createElement('currencies');

		$currency = $this->_doc->createElement('currency');
		$currency->setAttribute('id', 'RUR');
		$currency->setAttribute('rate', '1');
		$currency->setAttribute('plus', '0');

		$currencies->appendChild($currency);

		return $currencies;
	}

	/**
	 * Генерация offers
	 *
	 * <offers>
	 * 		<offer>
	 * 			......
	 * 		</offer>
	 * 	</offers>
	 *
	 * @return \DOMElement
	 */
	public function offers()
	{
		$offers = $this->_doc->createElement('offers');

		foreach ($this->clinicOffers() as $offer) {
			$offers->appendChild($offer);
		}

		foreach ($this->doctorOffers() as $offer) {
			$offers->appendChild($offer);
		}

		//на этом этапе делаем без диагностики
		/*
		foreach ($this->diagnosticOffers() as $offer) {
			$offers->appendChild($offer);
		}
		*/


		return $offers;
	}


	/**
	 * Генератор offers для записи в клинику
	 * @return \Generator
	 */
	public function clinicOffers()
	{
		$dataProvider = new CActiveDataProvider(
			'dfs\docdoc\models\ClinicModel',
			array(
				'criteria' => array(
					'scopes' => ['active', 'onlyClinic', 'inCity' => $this->_city_id],
					'together' => true,
					'group' => 't.id',
				),
			)
		);
		$clinicIterator = new CDataProviderIterator($dataProvider, 30);

		foreach ($clinicIterator as $clinic) {
			yield $this->clinicOffer($clinic);
		}
	}

	/**
	 * Генератор offer для записи в клинику
	 *
	 * <!-- Запись в клинику -->
	 *	<offer id="clinic_12345" available="true">
	 *		<url>https://docdoc.ru/clinic/on_clinic_tsvetnoj</url>
	 *		<!--
	 *		выводить сюда минимальную стоимость любой услуги в клинике
	 *		-->
	 *		<price>1200</price>
	 *
	 *		<currencyId>RUR</currencyId>
	 *		<categoryId>1</categoryId>
	 *		<picture>https://docdoc.ru/img/clinic/13.gif</picture>
	 *		<name>Многопрофильный медицинский центр «Он Клиник» на Цветном бульваре</name>
	 *		<description>
	 *		Многопрофильный медицинский центр. Обследование взрослых. Расположен в 5 мин. ходьбы от м. Цветной Бульвар. Прием происходит по предварительной записи. Позвоните по многоканальному телефону +7 (495) 223-00-56 и выберите удобное для Вас время.
	 *		</description>
	 *		<param name="Тип заявки">Запись в клинику</param>
	 *		<param name="ID типа заявки">clinic</param>
	 *	</offer>
	 *
	 *
	 * @param ClinicModel $c
	 *
	 * @return \DOMElement
	 */
	public function clinicOffer(ClinicModel $c)
	{
		$offer = $this->_doc->createElement('offer');

		$domain = "http://" . $this->_city->prefix . Yii::app()->params['hosts']['front'];
		$nodes = [
			'url' => ['text' => "{$domain}/clinic/{$c->rewrite_name}", 'attr' => []],
			'price' => ['text' => $c->getMinPrice(), 'attr' => []],
			'currencyId' => ['text' => "RUR", 'attr' => []],
			'categoryId' => ['text' => $this->getCategoryClinic(), 'attr' => []],
			'picture' => ['text' => $c->getLogo(), 'attr' => []],
			'name' => ['text' => $c->name, 'attr' => []],
			'description' => ['text' => $c->shortDescription, 'attr' => []],
		];

		foreach ($nodes as $k => $v) {
			$offer->appendChild($this->node($k, $v['text'], $v['attr']));
		}

		$offer->appendChild($this->node('param', "Запись в клинику", ['name' => 'Тип заявки']));
		$offer->appendChild($this->node('param', $this->getCategoryClinic(), ['name' => 'ID типа заявки']));

		$offer->setAttribute('id', $this->getClinicId($c->id));
		$offer->setAttribute('available', "true");

		return $offer;
	}


	/**
	 * Генератор offers для записи к врачу
	 * @return \Generator
	 */
	public function doctorOffers()
	{
		$criteria=new \CDbCriteria;
		$criteria->scopes = ['active', 'inCity' => $this->_city_id];
		$criteria->together = true;
		$criteria->group = 't.id';

		$dataProvider = new CActiveDataProvider(
			'dfs\docdoc\models\DoctorModel',
			[
				'criteria' => $criteria,
			]
		);

		$doctorIterator = new CDataProviderIterator($dataProvider, 30);
		foreach ($doctorIterator as $doctor) {
			//оффер = врач одной специальности в одной клинике
			//у нас на 1 врача может быть столько предложений, сколько у него специальностей и в скольки клиниках он работает
			foreach ($this->doctorOffer($doctor) as $offer) {
				yield $offer;
			}
		}
	}

	/**
	 * Генератор offer для записи к врачу
	 *
	 * <!--
	 *		запись к врачу
	 *
	 *		 Врачи могут быть нескольких специальностей
	 *
	 *		запись к тому же врачу, но другой специальности
	 *		id = DOCTOR_ВРАЧ_СПЕЦИАЛЬНОСТЬ_КЛИНИКА
	 *		-->
	 *		<offer id="doctor_12345_78_1" available="true">
	 *			<url>http://docdoc.ru/doctor/Kornak_Boris</url>
	 *			<price>1200</price>
	 *			<currencyId>RUR</currencyId>
	 *			<typePrefix>Хирург</typePrefix>
	 *			<categoryId>78</categoryId>
	 *			<picture>https://docdoc.ru/img/doctors/1x1/761.jpg</picture>
	 *			<name>Корняк Борис Степанович</name>
	 *			<description>Проктолог . Доктор медицинских наук. Профессор. Стаж 30 лет. Член Ассоциации
	 *			хирургов-гепатологов России.
	 *			Член International Association of Surgeons and Gastroenterologists.
	 *			Участник 6-го Всемирного Конгресса эндоскопических хирургов (Рим) (1998 г.)
	 *			</description>
	 *			<vendor>«Он Клиник» на Арбате</vendor>
	 *			<param name="Отзывы">http://docdoc.ru/doctor/Kornak_Boris#reviews</param>
	 *			<param name="Рейтинг">9.5</param>
	 *			<!-- массив отзывов -->
	 *			<param name="Отзыв_4286">Большое спасибо, врач был очень внимателен, полностью описал проблему, назначил лечение.</param>
	 *			<param name="Отзыв_4287">Большое спасибо, врач был очень внимателен, полностью описал проблему, назначил лечение.</param>
	 *			<param name="Отзыв_4288">Большое спасибо, врач был очень внимателен, полностью описал проблему, назначил лечение.</param>
	 *
	 *			<param name="Тип заявки">Запись на прием к врачу</param>
	 *			<param name="ID типа заявки">10</param>
	 *		</offer>
	 *
	 * @param DoctorModel $d
	 *
	 * @return \DOMElement
	 */
	public function doctorOffer(DoctorModel $d)
	{
		$domain = "http://" . $this->_city->prefix . Yii::app()->params['hosts']['front'];
		$base_nodes = [
			'url' => ['text' => "{$domain}/doctor/{$d->rewrite_name}", 'attr' => []],
			'price' => ['text' => $d->price, 'attr' => []],
			'currencyId' => ['text' => "RUR", 'attr' => []],
			'picture' => ['text' => $d->getImg(), 'attr' => []],
			'name' => ['text' => $d->name, 'attr' => []],
			'description' => ['text' => $d->text, 'attr' => []],
		];

		foreach ($d->doctorClinics as $docInClinic)
		{
			foreach ($d->sectors as $sector) {
				$offer = $this->_doc->createElement('offer');
				$offer->setAttribute('id', $this->getDoctorId([
					'doctor' => $d->id,
					'sector' => $sector->id,
					'clinic' => $docInClinic->clinic_id
				]));
				$offer->setAttribute('available', "true");

				$nodes = $base_nodes;
				$nodes['categoryId'] = ['text' => $this->getSectorId($sector->id), 'attr' => []];
				$nodes['typePrefix'] = ['text' => $sector->name, 'attr' => []];

				$nodes['vendor'] = ($docInClinic->clinic->isPrivatDoctor === 'yes') ?
					['text' => 'Частный врач', 'attr' => []] :
					['text' => $docInClinic->clinic->name, 'attr' => []];


				foreach ($nodes as $k => $v) {
					$offer->appendChild($this->node($k, $v['text'], $v['attr']));
				}
				$offer->appendChild($this->node('param', $d->getDoctorRating(), ["name" => "Рейтинг"]));
				$offer->appendChild($this->node('param', "{$domain}/doctor/{$d->rewrite_name}#reviews", ["name" => "Отзывы"]));

				$reviews = DoctorOpinionModel::model()
					->allowed()
					->byDoctor($d->id)
					->findAll();

				foreach ($reviews as $review) {
					$offer->appendChild($this->node('param', $review->text, ["name" => "Отзыв_{$review->id}"]));
				}

				$offer->appendChild($this->node('param', "Запись на прием к врачу", ['name' => 'Тип заявки']));
				$offer->appendChild($this->node('param', $this->getCategoryDoctor(), ['name' => 'ID типа заявки']));

				yield $offer;
			}
		}
	}

	/**
	 * создание Dom елемента с текстом и аттрибутами
	 *
	 * @param string $nodeName
	 * @param null|string  $text
	 * @param array $attr
	 *
	 * @return \DOMElement
	 */
	private function node($nodeName, $text = null, $attr = [])
	{
		$node = $this->_doc->createElement($nodeName);
		if ($text !== null) {
			$node->appendChild($this->_doc->createTextNode($text));
		}

		if (count($attr)) {
			foreach ($attr as $k => $v) {
				$node->setAttribute($k, $v);
			}
		}

		return $node;
	}

	/**
	 * получение id для специальности
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function getSectorId($id)
	{
		return "sector_{$id}";
	}

	/**
	 * получение id для специальности
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function getClinicId($id)
	{
		return self::CATEGORY_CLINIC . '_' . $id;
	}

	/**
	 * получение id для специальности
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function getDoctorId(array $params)
	{
		return self::CATEGORY_DOCTOR . "_{$params['doctor']}_{$params['sector']}_{$params['clinic']}";
	}

	public function getCategoryClinic()
	{
		return self::CATEGORY_CLINIC;
	}

	public function getCategoryDoctor()
	{
		return self::CATEGORY_DOCTOR;
	}

	public function getCategoryDiagnostic()
	{
		return self::CATEGORY_DIAGNOSTIC;
	}
}
