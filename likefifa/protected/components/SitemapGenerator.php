<?php

use likefifa\models\CityModel;
use likefifa\models\RegionModel;

class SitemapGenerator {

	/**
	 * Максимальное количество ссылок в 1 файле
	 *
	 * @var int
	 */
	const PAGE_SIZE = 25000;

	/**
	 * Модель региона
	 *
	 * @var RegionModel
	 */
	public $regionModel = null;

	/**
	 * Получает массив моделей мастеров
	 *
	 * @return LfMaster[]
	 */
	protected function getMasters() {
		return LfMaster::model()->findAll($this->_getCriteria());
	}

	/**
	 * Получает массив моделей салонов
	 *
	 * @return LfSalon[]
	 */
	protected function getSalons() {
		return LfSalon::model()->findAll($this->_getCriteria());
	}

	/**
	 * Создает URL
	 *
	 * @param LfSpecialization     $specialization
	 * @param LfService            $service
	 * @param bool                 $hasDeparture
	 * @param UndergroundStation[] $stations
	 * @param AreaMoscow           $area
	 * @param DistrictMoscow[]     $districts
	 * @param CityModel            $city
	 *
	 * @return string
	 */
	protected function createSearchUrl(
		LfSpecialization $specialization = null,
		LfService $service = null,
		$hasDeparture = false,
		$stations = null,
		AreaMoscow $area = null,
		$districts = null,
		$city = null
	)
	{
		$params = array();

		if ($specialization) {
			$params['specialization'] = $specialization->getRewriteName();
		}
		if ($service) {
			$params['service'] = $service->getRewriteName();
		}
		if ($hasDeparture) {
			$params['hasDeparture'] = 1;
		}

		if ($stations) {
			$stationNames = array();
			foreach ($stations as $station) {
				$stationNames[] = $station->getRewriteName();
			}

			$params['stations'] = implode(',', $stationNames);
		}

		if ($area) $params['area'] = $area->getRewriteName();

		if ($districts) {
			$districtNames = array();
			foreach ($districts as $district) {
				$districtNames[] = $district->getRewriteName();
			}

			$params['districts'] = implode(',', $districtNames);
		}

		if ($city) {
			$params['city'] = $city->rewrite_name;
		}

		$url = Yii::app()->urlManager->createUrl("masters/custom", $params);
		return $this->_getAbsoluteSiteUrl() . substr($url, 1);
	}

	/**
	 * Получает URL станций метро
	 *
	 * @param LfSpecialization $specialization
	 * @param LfService        $service
	 *
	 * @return string[]
	 */
	private function _getStationUrls(LfSpecialization $specialization = null, LfService $service = null) {
		$urls = array();
		foreach (UndergroundStation::model()->findAll() as $station) {
			$urls[] = $this->createSearchUrl($specialization, $service, false, array($station));
		}

		return $urls;
	}

	/**
	 * Получает URL районов
	 *
	 * @param LfSpecialization $specialization
	 * @param LfService        $service
	 *
	 * @return string[]
	 */
	private function _getDistrictUrls(LfSpecialization $specialization = null, LfService $service = null) {
		$urls = array();
		foreach ($this->areas = AreaMoscow::model()->findAll() as $area) {
			foreach ($area->districts as $district) {
				$urls[] = $this->createSearchUrl($specialization, $service, false, array(), $area, array($district));
			}
		}

		return $urls;
	}

	/**
	 * Получает ссылки на услуги
	 *
	 * @param LfService $service
	 *
	 * @return string[]
	 */
	protected function getServiceUrls(LfService $service) {
		if ($this->regionModel->isMoscow()) {
			return
				array_merge(
					array($this->createSearchUrl($service->specialization, $service)),
					$this->_getStationUrls($service->specialization, $service),
					$this->_getDistrictUrls($service->specialization, $service)
				);
		}
	}

	/**
	 * Получает URL для специальностей
	 *
	 * @param LfSpecialization $specialization
	 *
	 * @return string[]
	 */
	protected function getSpecializationUrls(LfSpecialization $specialization) {
		if ($this->regionModel->isMoscow()) {
			$urls =
				array_merge(
					array($this->createSearchUrl($specialization)),
					$this->_getStationUrls($specialization),
					$this->_getDistrictUrls($specialization)
				);
			foreach ($specialization->services as $service) {
				$urls = array_merge($urls, $this->getServiceUrls($service));
			}
		} else {
			$urls =
				array_merge(
					array($this->createSearchUrl($specialization)),
					$this->_getCitiesUrls($specialization)
				);
		}

		return $urls;
	}

	/**
	 * Получает массив ссылок на мастеров
	 *
	 * @return string[]
	 */
	protected function getMasterUrls() {
		$urls = array();

		foreach ($this->getMasters() as $master) {
			$urls[] = $this->_getAbsoluteSiteUrl() . "/masters/" . $master->rewrite_name . "/";
		}

		return $urls;
	}

	/**
	 * Получает массив ссылок на салоны
	 *
	 * @return string[]
	 */
	protected function getSalonUrls() {
		$urls = array();

		foreach ($this->getSalons() as $salon) {
			$urls[] = $this->_getAbsoluteSiteUrl() . "/salons/" . $salon->rewrite_name . "/";
		}

		return $urls;
	}

	/**
	 * Получает все URL - адреса
	 *
	 * @return string[]
	 */
	protected function getAllUrls() {
		if ($this->regionModel->isMoscow()) {
			$urls = array_merge(
				array($this->createSearchUrl()),
				$this->_getStationUrls(),
				$this->_getDistrictUrls()
			);
		} else {
			$urls = array_merge(
				array($this->createSearchUrl()),
				$this->_getCitiesUrls()
			);
		}
		foreach (LfSpecialization::model()->findAll() as $specialization) {
			$urls = array_merge($urls, $this->getSpecializationUrls($specialization));
		}

		$urls = array_merge(
			$urls,
			$this->getMasterUrls(),
			$this->getSalonUrls()
		);

		return $urls;
	}

	protected function fileNameByIndex($index) {
		return ($index <= 0 ? 'sitemap.xml' : 'sitemap'.$index.'.xml');
	}

	/**
	 * Записывает файлы sitemap.xml
	 *
	 * @param int      $index индекс sitemap
	 * @param string[] $urls  URL-адреса страниц
	 *
	 * @return string название файла
	 */
	protected function writeSitemapToFile($index, $urls) {
		$fileName = $this->fileNameByIndex($index);

		$folder =
			Yii::app()->basePath .
			DIRECTORY_SEPARATOR .
			".." .
			DIRECTORY_SEPARATOR .
			"sitemaps" .
			DIRECTORY_SEPARATOR .
			$this->_getFolderName() .
			DIRECTORY_SEPARATOR;

		$file = $folder . $fileName;

		$fh = fopen($file, 'w+');
		fwrite($fh, "<?xml version=\"1.0\" encoding=\"utf8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">");
		foreach ($urls as $url) {
			fwrite($fh, "\t<url>\n\t\t<loc>$url</loc>\n\t</url>\n");
		}
		fwrite($fh, "</urlset>");
		fclose($fh);

		return $fileName;
	}

	/**
	 * Создает файлы sitemap.xml
	 *
	 * @return void
	 */
	public function createSitemap()
	{
		$urlChunks = array_chunk($this->getAllUrls(), self::PAGE_SIZE);
		$mapUrls = array();

		foreach ($urlChunks as $index => $urls) {
			$mapUrls[] = $this->_getAbsoluteSitemapUrl() . $this->writeSitemapToFile($index + 1, $urls);
		}

		$this->writeSitemapToFile(0, $mapUrls);
	}

	/**
	 * Получает абсолютный URL к папке где sitemap.xml
	 *
	 * @return string
	 */
	private function _getAbsoluteSitemapUrl()
	{
		return $this->_getAbsoluteSiteUrl() . "/sitemaps/" . $this->_getFolderName() . "/";
	}

	/**
	 * Получает название папки с sitemap.xml
	 *
	 * @return string
	 */
	private function _getFolderName()
	{
		if ($this->regionModel->isMoscow()) {
			return "moscow/";
		}

		return str_replace(".", "", $this->regionModel->prefix);
	}

	/**
	 * Получает абсолютный URL к корню сайта
	 *
	 * @return string
	 */
	private function _getAbsoluteSiteUrl()
	{
		return $this->regionModel->getIndexUrl();
	}

	/**
	 * Получает критерии для поиска мастеров и салонов
	 *
	 * @return CDbCriteria
	 */
	private function _getCriteria()
	{
		$criteria = new CDbCriteria;

		$list = array();
		foreach ($this->regionModel->activeCities as $city) {
			$list[] = $city->id;
		}

		$criteria->addInCondition("t.city_id", $list);

		return $criteria;
	}

	/**
	 * Получает массив URL-адресов городов
	 *
	 * @param LfSpecialization $specialization модель специальности
	 *
	 * @return string[]
	 */
	private function _getCitiesUrls(LfSpecialization $specialization = null) {
		$urls = array();

		foreach ($this->regionModel->activeCities as $city) {
			$urls[] = $this->createSearchUrl($specialization, null, false, null, null, null, $city);
		}

		return $urls;
	}
}