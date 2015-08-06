<?php

namespace dfs\docdoc\models;

use Yii;
use CActiveRecord;
use CDbCriteria;
use CActiveDataProvider;

/**
 * Файл класса PageModel
 *
 * Модель для работы с таблицей "page"
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003885/card/
 * @package dfs.docdoc.models
 *
 * @property int       $id              ID
 * @property string    $url             URL
 * @property string    $title           Title
 * @property string    $h1              H1
 * @property string    $keywords        Keywords
 * @property string    $description     Description
 * @property int       $is_show         Показывать
 * @property string    $seo_text_top    SEO текст внизу
 * @property string    $seo_text_bottom SEO текст внизу
 * @property int       $id_city         Идентификатор города
 * @property int       $site            Сайт
 *
 * @property CityModel $city            Модель города
 */
class PageModel extends \CActiveRecord
{

	/**
	 * Названия флагов публикации страницы
	 *
	 * @var string[]
	 */
	public $showFlags = array(
		0 => "Нет",
		1 => "Да",
	);


	/**
	 * Возвращает статическую модель указанного класса.
	 *
	 * @param string $className название класса
	 *
	 * @return PageModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'page';
	}

	/**
	 * Получает имя первичного ключа
	 *
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('url, h1, title', 'required'),
			array('is_show, id_city, site', 'numerical', 'integerOnly' => true),
			array('url, h1, title', 'length', 'max' => 1024),
			array('keywords, description, seo_text_top, seo_text_bottom', 'safe'),
			array(
				'title, keywords, description',
				'filter',
				'filter' => 'strip_tags'
			),
			array(
				'id, url, h1, title, keywords, description, seo_text_top, seo_text_bottom, is_show',
				'safe',
				'on' => 'search'
			),
			array(
				'url',
				'dfs\docdoc\validators\StringValidator',
				'type' => "relativeUrl"
			),
		);
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array(
			"city" => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\CityModel',
				'id_city'
			)
		);
	}

	/**
	 * Возвращает подписей полей
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'id'              => 'ID',
			'url'             => 'URL',
			'h1'              => 'H1',
			'title'           => 'Title',
			'keywords'        => 'Keywords',
			'description'     => 'Description',
			'seo_text_top'    => 'SEO текст вверху',
			'seo_text_bottom' => 'SEO текст внизу',
			'is_show'         => 'Показывать',
			'id_city'         => 'Город',
			'site'            => 'Сайт',
		);
	}

	/**
	 * Получает список моделей на основе условий поиска / фильтров.
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('url', $this->url, true);
		$criteria->compare('h1', $this->h1, true);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('keywords', $this->keywords, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('seo_text_top', $this->seo_text_top, true);
		$criteria->compare('seo_text_bottom', $this->seo_text_bottom, true);
		$criteria->compare('is_show', $this->is_show);
		$criteria->compare('id_city', $this->id_city);
		$criteria->compare('site', $this->site);

		return new CActiveDataProvider(
			$this, array(
				'criteria' => $criteria,
			)
		);
	}

	/**
	 * Выборка только активной информации
	 *
	 * @return $this
	 */
	public function active()
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "is_show = 1",
			)
		);
		return $this;
	}

	/**
	 * Выборка записей для сайта
	 *
	 * @param string $site
	 * @return PageModel
	 */
	public function forSite($site)
	{
		$this->getDbCriteria()->mergeWith(array(
				'condition' => "site = :site",
				'params' => array(
					':site' => $site
				),
			));
		return $this;
	}

	/**
	 * Возвращает отсортированный по городам массив с SEO-записями для сайта $site по $url c is_show = 1
	 * @param string $site
	 * @param string $url
	 *
	 * @return PageModel[]
	 */
	public function findAllByUrl($site, $url)
	{
		$seoRecords = $this->forSite($site)
							->active()
							->findAllByAttributes(array('url' => $url));

		$sortedByCity = array();

		foreach ($seoRecords as $r) {
			$sortedByCity[$r->id_city] = $r;
		}

		return $sortedByCity;
	}



	/**
	 * Получает название флага будликации страницы
	 *
	 * @return string
	 */
	public function getShowFlag()
	{
		if (empty($this->showFlags[$this->is_show])) {
			return null;
		}

		return $this->showFlags[$this->is_show];
	}

	/**
	 * Получает сайт
	 *
	 * @return null|string
	 */
	public function getSite()
	{
		if (empty(Yii::app()->params['siteList'][$this->site])) {
			return null;
		}

		return Yii::app()->params['siteList'][$this->site];
	}

	/**
	 * Получает название города
	 *
	 * @return string
	 */
	public function getCityName()
	{
		$city = $this->city;
		if ($city) {
			return $city->title;
		}

		return null;
	}

	/**
	 * Вызывается перед валидацией
	 *
	 * @return bool
	 */
	protected function beforeValidate()
	{
		if ($this->url) {
			$urlParams = parse_url($this->url);
			if ($urlParams)
			$this->url = $urlParams["path"];
			if (!empty($urlParams["query"])) {
				$this->url .= "?" . $urlParams["query"];
			}
		}

		return parent::beforeValidate();
	}
}