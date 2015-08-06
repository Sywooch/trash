<?php

namespace dfs\docdoc\listInterface;


/**
 * Class ListInterface
 *
 * @package dfs\docdoc\listInterface
 */
abstract class ListInterface
{
	/**
	 * Ошибки при формировании запроса
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Параметры поиска
	 *
	 * @var array
	 */
	protected $params = [];

	/**
	 * Дефолтные scopes
	 *
	 * @var array
	 */
	protected $scopes = [];

	/**
	 * Параметры сортировки
	 *
	 * @var array
	 */
	protected $sorting = [];

	/**
	 * Текущая сортировка
	 *
	 * @var string
	 */
	protected $sort = null;

	/**
	 * Сортировка по-умолчанию
	 *
	 * @var string
	 */
	protected $sortDefault = null;

	/**
	 * Направление сортировки (asc | desc)
	 *
	 * @var string
	 */
	protected $sortDirection = null;

	/**
	 * Лимит на страницу
	 *
	 * @var int
	 */
	protected $limit = 10;

	/**
	 * Максимальный лимит на выборку
	 *
	 * @var int
	 */
	protected $maxLimit = 1000;

	/**
	 * Сдвиг для выборки
	 *
	 * @var int | null
	 */
	protected $offset = null;

	/**
	 * Текущая страница выборки
	 *
	 * @var int
	 */
	protected $page = 1;

	/**
	 * Время кеширования запроса
	 *
	 * @var int | null
	 */
	protected $cacheDuration = null;

	/**
	 * Общее количество по заданным параметрам
	 *
	 * @var int
	 */
	protected $count = 0;

	/**
	 * Найденные записи
	 *
	 * @var \CActiveRecord[]
	 */
	protected $items = [];

	/**
	 * Список идентификаторов найденных записей
	 *
	 * @var array | null
	 */
	protected $itemIds = null;


	public function __construct()
	{
		$this->init();
	}

	/**
	 * Наличие ошибок
	 *
	 * @return bool
	 */
	public function hasErrors()
	{
		return !empty($this->errors);
	}

	/**
	 * Текущие ошибки
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Текущая сортировка
	 *
	 * @return string
	 */
	public function getSort()
	{
		return $this->sort;
	}

	/**
	 * Направление сортировки
	 *
	 * @return string
	 */
	public function getSortDirection()
	{
		return $this->sortDirection;
	}

	/**
	 * Параметры сортировки
	 *
	 * @return array
	 */
	public function getSortingParams()
	{
		return $this->sorting;
	}

	/**
	 * Лимит выборки на страницу
	 *
	 * @return int
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * Максимальный лимит выборки
	 *
	 * @return int
	 */
	public function getMaxLimit()
	{
		return $this->maxLimit;
	}

	/**
	 * С какого элемента начинается выборка
	 *
	 * @return int
	 */
	public function getOffset()
	{
		return $this->offset;
	}

	/**
	 * Текущая страница выборки
	 *
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * Общее количество записей
	 *
	 * @return int
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * Найденные записи
	 *
	 * @return \CActiveRecord[]
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Идентификаторы найденных записей
	 *
	 * @return array
	 */
	public function getItemIds()
	{
		if ($this->itemIds === null) {
			$this->itemIds = [];
			foreach ($this->items as $item) {
				$this->itemIds[] = $item->getPrimaryKey();
			}
		}

		return $this->itemIds;
	}

	/**
	 * Количество страниц
	 *
	 * @return int
	 */
	public function getPageCount()
	{
		return $this->limit ? ceil($this->count / $this->limit) : 1;
	}

	/**
	 * Получить значение параметра
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function getParam($name)
	{
		return isset($this->params[$name]) ? $this->params[$name] :  null;
	}


	/**
	 * Установка лимита
	 *
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function setLimit($limit)
	{
		if ($limit === null) {
			$this->limit = null;
		} else {
			$limit = intval($limit);

			$this->limit = $limit > 0 && (!$this->maxLimit || $limit < $this->maxLimit) ? $limit : $this->maxLimit;
		}

		return $this;
	}

	/**
	 * Установка сдвига для выборки
	 *
	 * @param int | null $offset
	 *
	 * @return $this
	 */
	public function setOffset($offset)
	{
		if ($offset === null) {
			$this->offset = null;
		} else {
			$offset = intval($offset);

			$this->offset = $offset > 0 ? $offset : null;
		}

		return $this;
	}

	/**
	 * Установка страницы
	 *
	 * @param int $page
	 *
	 * @return $this
	 */
	public function setPage($page)
	{
		$page = intval($page);
		if ($page > 0) {
			$this->page = $page;
		}

		return $this;
	}

	/**
	 * Установка сортировки
	 *
	 * @param string $sort
	 *
	 * @return $this
	 */
	public function setSort($sort)
	{
		if (isset($this->sorting[$sort])) {
			$this->sort = $sort;
			if ($this->sortDirection === null && isset($this->sorting[$sort]['direction'])) {
				$this->sortDirection = $this->sorting[$sort]['direction'];
			}
		}

		return $this;
	}

	/**
	 * Установка порядка сортировки
	 *
	 * @param string $direction
	 *
	 * @return $this
	 */
	public function setSortDirection($direction)
	{
		switch ($direction)
		{
			case 'asc':
				$this->sortDirection = 'asc';
				break;

			case 'dsc':
			case 'desc':
				$this->sortDirection = 'desc';
				break;
		}

		return $this;
	}

	/**
	 * Установка параметров
	 *
	 * @param array $params
	 *
	 * @return $this
	 */
	public function setParams(array $params)
	{
		$this->params = $params;

		return $this;
	}

	/**
	 * Установка параметра
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setParam($name, $value)
	{
		$this->params[$name] = $value;

		return $this;
	}

	/**
	 * Кэширование запроса в секундах
	 *
	 * @param int $duration
	 *
	 * @return $this
	 */
	public function setCache($duration)
	{
		$this->cacheDuration = $duration;

		return $this;
	}


	/**
	 * Добавить элементов в выборку
	 *
	 * @param \CActiveRecord[] $items
	 *
	 * @return $this
	 */
	protected function addItems($items)
	{
		if ($items) {
			$this->items = array_merge($this->items, $items);
			$this->itemIds = null;
			$this->count += count($items);
		}

		return $this;
	}

	/**
	 * Инициализациия
	 */
	protected function init() {}


	/**
	 * Формирование параметров для поиска
	 *
	 * @return $this
	 */
	public function buildParams()
	{
		$p = $this->params;

		if (isset($p['limit'])) {
			$this->setLimit($p['limit']);
		}
		if (isset($p['count'])) {
			$this->setLimit($p['count']);
		}
		if (isset($p['start'])) {
			$this->setOffset($p['start']);
		}
		if (isset($p['page'])) {
			$this->setPage($p['page']);
		}

		if (isset($p['sort'])) {
			$this->setSort($p['sort']);
		}
		if (isset($p['order'])) {
			$this->setSort($p['order']);
		}
		if (isset($p['sortDirection'])) {
			$this->setSortDirection($p['sortDirection']);
		}

		if (!isset($this->sorting[$this->sort])) {
			$this->setSort($this->sortDefault);
		}

		return $this;
	}

	/**
	 * Формирование scopes для запроса данных
	 *
	 * @return $this
	 */
	protected function buildScopes()
	{
		if ($this->offset === null && $this->limit > 1 && $this->page > 1) {
			$this->offset = ($this->page - 1) * $this->limit;
		}

		return $this;
	}

	/**
	 * Загрузка данных
	 *
	 * @return $this
	 * @throws \CException
	 */
	public function loadData()
	{
		throw new \CException('Need implementation method loadData');
	}
}
