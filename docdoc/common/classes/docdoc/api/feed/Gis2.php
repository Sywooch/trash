<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 01.10.14
 * Time: 16:05
 */

namespace dfs\docdoc\api\feed;

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\objects\Phone;

/**
 * Класс сборки xml для 2Gis
 *
 * Class Gis2
 * @package dfs\docdoc\objects
 */
class Gis2
{
	/**
	 * Документ
	 *
	 * @var \DOMDocument
	 */
	protected $_doc = null;

	/**
	 * Конструктор
	 */
	public function __construct()
	{
		$this->_doc = new \DOMDocument('1.0', 'utf-8');
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
	 *
	 * @param array $config
	 *
	 * @return bool
	 */
	public function generateXml(array $config)
	{
		$clinics = ClinicModel::model()
			->excludePrivateDoctor()
			->active()
			->isSignedContract()
			->findAll();

		$offers = $this->_doc->createElement('offers');

		foreach ($clinics as $clinic) {
			$offers->appendChild($this->generateClinicXml($clinic, $config));
		}

		$this->_doc->appendChild($offers);

		return $this->_doc->saveXML();
	}

	/**
	 * xml для отдельной клиники
	 *
	 * @param ClinicModel $clinic
	 * @param array $config
	 *
	 * @return \DOMElement
	 */
	public function generateClinicXml(ClinicModel $clinic, array $config)
	{
		$root = $this->_doc->createElement('offer');
		$elements = [];

		//Уникальный идентификатор филиала в системе провайдера. Латинские буквы и цифры
		$elements[] = $this->_doc->createElement('id', $clinic->id);
		//Вознаграждение от провайдера в рублях (roubles) или процентах (percent) для аукциона предложений

		if(isset($config['reward'][$clinic->clinicCity->rewrite_name])){
			$reward = $config['reward'][$clinic->clinicCity->rewrite_name];
		} else {
			$reward = $config['reward']['other'];
		}

		$element = $this->_doc->createElement('reward', $reward);
		$element->setAttribute('metric', 'roubles');
		$elements[] = $element;
		//URL виджета для записи онлайн
		$elements[] = $this->_doc->createElement('order_url', "http://docdoc.ru/appointment?clinicId={$clinic->id}");
		//Двухсимвольный код страны (ISO 3166). Опционально
		$elements[] = $this->_doc->createElement('country_code', 'ru');
		$elements[] = $this->_doc->createElement('city', $clinic->city);
		$elements[] = $this->generateSupplierXml($clinic);

		foreach ($elements as $e) {
			$root->appendChild($e);
		}

		return $root;
	}

	/**
	 * <supplier>
	 *        <name>Планета Суши</name> <!-- Название фирмы -->
	 *        <address>Новосибирск, Николаевский проспект 27</address> <!-- Адрес -->
	 *        <coordinates> <!-- Координаты WGS 84. Опционально. Указав их, увеличивается шанс правильно найти фирму в базе 2ГИС -->
	 *            <lon>82.927818</lon>
	 *            <lat>55.03923</lat>
	 *        </coordinates>
	 *        <url>http://sushi.ru</url> <!-- Веб-сайт. Опционально -->
	 *        <email>mail@sushi.ru</email> <!-- Email. Опционально -->
	 *        <phones> <!-- Номера телефонов. Опционально -->
	 *            <phone>+7 (383) 244-19-92</phone>
	 *            <phone>+7 (383) 244-19-93</phone>
	 *        </phones>
	 * </supplier>
	 *
	 * @param ClinicModel $clinic
	 * @return \DOMElement
	 */
	public function generateSupplierXml(ClinicModel $clinic)
	{
		$root = $this->_doc->createElement('supplier');
		$elements = [];

		//Название фирмы
		$elements[] = $this->_doc->createElement('name', $clinic->name);

		//Адрес
		$elements[] = $this->_doc->createElement('address', $clinic->getAddress());

		//Координаты WGS 84. Опционально. Указав их, увеличивается шанс правильно найти фирму в базе 2ГИС
		$elements[] = $this->generateCoordinatesXml($clinic);

		//Веб-сайт. Опционально
		$elements[] = $this->_doc->createElement('url', $clinic->url);

		//Email. Опционально
		$elements[] = $this->_doc->createElement('email', $clinic->email);

		//supplier
		$phones = $this->generatePhonesXml($clinic);
		if ($phones !== null) {
			$elements[] = $phones;
		}

		foreach ($elements as $e) {
			$root->appendChild($e);
		}

		return $root;
	}

	/**
	 * Координаты
	 *
	 * @param ClinicModel $clinic
	 * @return \DOMElement
	 */
	public function generateCoordinatesXml(ClinicModel $clinic)
	{
		$root = $this->_doc->createElement('coordinates');
		$elements = [];

		$elements[] = $this->_doc->createElement('lat', $clinic->latitude);
		$elements[] = $this->_doc->createElement('lon', $clinic->longitude);

		foreach($elements as $e){
			$root->appendChild($e);
		}

		return $root;
	}

	/**
	 * Телефоны
	 *
	 * @param ClinicModel $clinic
	 * @return \DOMElement
	 */
	public function generatePhonesXml(ClinicModel $clinic)
	{
		if(empty($clinic->phone)){
			return null;
		}

		$root = $this->_doc->createElement('phones');
		$formattedPhone = (new Phone($clinic->phone))->prettyFormat('+7 ');
		$element = $this->_doc->createElement('phone', $formattedPhone);
		$root->appendChild($element);
		return $root;
	}
} 
