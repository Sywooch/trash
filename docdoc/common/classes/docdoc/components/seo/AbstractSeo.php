<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 11.06.14
 * Time: 11:29
 */

namespace dfs\docdoc\components\seo;
use Yii,
	CComponent,
	CApplicationComponent;

abstract class AbstractSeo extends CApplicationComponent implements SeoInterface
{
	/**
	 * Парамтры, от которых зависит вывод seo-информации
	 *
	 * @var array
	 */
	public $params = array();

	public $pageTitle = "";
	public $metaKeywords = "";
	public $metaDescription = "";
	public $head = "";
	public $text = array();

	/**
	 * Название action
	 *
	 * @var string
	 */
	private $_actionName = 'defaultAction';

	const POSITION_TOP = 0;
	const POSITION_BOTTOM = 1;

	/**
	 * Добавление параметра
	 *
	 * @param string $key
	 * @param string|array $value
	 */
	function addParam($key, $value)
	{
		$this->params[$key] = $value;
	}

	/**
	 * Title страницы
	 */
	public function setTitle($pageTitle)
	{
		$this->pageTitle = $pageTitle;
	}

	/**
	 * MetaKeywords страницы
	 */
	public function setMetaKeywords($metaKeywords)
	{
		$this->metaKeywords = $metaKeywords;
	}

	/**
	 * MetaDescription страницы
	 */
	public function setMetaDescription($metaDescription)
	{
		$this->metaDescription = $metaDescription;
	}

	/**
	 * возвращает заголовок страницы
	 */
	public function setHead($head)
	{
		$this->head = $head;
	}

	/**
	 * добавление seo-текстов
	 */
	public function setSeoText($pos, $text)
	{
		$data = array(
			'Position' => $pos,
			'Text' => $text,
		);
		array_push($this->text, $data);
	}

	/**
	 * сброс сео-текстов
	 */
	public function resetSeoText()
	{
		$this->text = array();
	}

	/**
	 * Title страницы
	 * @return string
	 */
	public function getTitle()
	{
		return $this->pageTitle;
	}

	/**
	 * MetaKeywords страницы
	 * @return string
	 */
	public function getMetaKeywords()
	{
		return $this->metaKeywords;
	}

	/**
	 * MetaDescription страницы
	 * @return string
	 */
	public function getMetaDescription()
	{
		return $this->metaDescription;
	}

	/**
	 * возвращает заголовок страницы
	 * @return string
	 */
	public function getHead()
	{
		return $this->head;
	}

	/**
	 * Список seo-текстов
	 *
	 * @param bool $sortPosition
	 *
	 * @return array
	 */
	public function getSeoTexts($sortPosition = false)
	{
		if ($sortPosition) {
			$text = [];
			foreach ($this->text as $t) {
				$text[$t['Position']] = $t;
			}
			return $text;
		}

		return $this->text;
	}

	/**
	 * Получение верхнего сео-текста
	 * @return mixed
	 */
	public function getSeoTextTop()
	{
		foreach ($this->text as $item) {
			if ($item['Position'] == self::POSITION_TOP) {
				return $item['Text'];
			}
		}

		return null;
	}

	/**
	 * Получение нижнего сео-текста
	 * @return mixed
	 */
	public function getSeoTextBottom()
	{
		foreach ($this->text as $item) {
			if ($item['Position'] == self::POSITION_BOTTOM) {
				return $item['Text'];
			}
		}

		return null;
	}

	/**
	 * Установка экшн
	 *
	 * @param $action
	 */
	public function setAction($action)
	{
		$this->_actionName = $action;
	}

	/**
	 * Геттер для action
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->_actionName;
	}

	private function routeByAction()
	{
		$seo_class = 'dfs\docdoc\components\seo\\' . Yii::app()->params['siteName'] . '\\searchcontroller\\' . ucfirst($this->_actionName) . 'Seo';

		return new $seo_class;
	}

} 