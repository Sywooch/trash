<?php
namespace dfs\docdoc\components;


final class Version
{
	/**
	 * Текущая версия
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Список доступных изображения для версий
	 *
	 * @var string[]
	 */
	private $versionList;

	/**
	 * Список изхображений
	 *
	 * @param string[] $list
	 */
	public function setImagesVersionList(array $list)
	{
		$result = [];
		foreach($list as $version) {
			$vIndex = explode('.', $version);
			$index = 0;
			foreach($vIndex as $n => $vI) {
				$index += pow(1000, 4 - $n) + (int)$vI;
			}
			$result[$index] = $version;
		}
		ksort($result);
		$this->versionList = $result;
	}

	/**
	 * Загрузка индекса с изображениями версий
	 */
	private function initImagesVersionList()
	{
		$fileList = glob(ROOT_PATH . "/development/release_logo/*.png");
		foreach($fileList as &$filename) {
			$filename = $this->clearVersion($filename);
		}
		$this->setImagesVersionList($fileList);
	}

	/**
	 * Убираел лишнее из названия версии
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	public function clearVersion($filename)
	{
		$path = explode(DIRECTORY_SEPARATOR, $filename);
		$filename = end($path);
		return substr($filename, 0, strrpos($filename, '.'));
	}

	/**
	 * Сохраняем версию
	 *
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * Последняя картинка для которой есть изображение
	 *
	 * @return string
	 */
	public function getCurrentImageIndex()
	{
		$list = $this->getImageList();
		return end($list);
	}

	/**
	 * Список изображений
	 *
	 * @return string[]
	 */
	public function getImageList()
	{
		if (is_null($this->versionList)) {
			$this->initImagesVersionList();
		}
		return $this->versionList;
	}

	/**
	 * Получить текущею версию
	 *
	 * @return string
	 */
	public function getCurrent()
	{
		if (is_null($this->version)) {
			$this->setVersion(
				file_get_contents(ROOT_PATH . '/version.txt')
			);
		}
		return $this->version;
	}

	/**
	 * Список ссылок на изображения
	 *
	 * @return string[]
	 */
	public function getImageUrlList()
	{
		$ret = [];
		foreach($this->getImageList() as $image) {
			$ret[]= $image . '.png';
		}
		return $ret;
	}

	/**
	 * @return string
	 */
	public function getImageUrl()
	{
		return $this->getCurrentImageIndex() . '.png';
	}
} 