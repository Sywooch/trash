<?php
namespace dfs\docdoc\front\widgets;

use CLinkPager;
use CHtml;

/**
 * Пагинатор для виджетов
 * LinkPagerWidget class file.
 */
class LinkPagerWidget extends CLinkPager
{

	/**
	 * @vars string
	 */
	public $firstPageLabel = '';
	public $lastPageLabel = '';
	public $prevPageLabel = '←';
	public $previousPageCssClass = 'dd-prev';
	public $nextPageLabel = '→';
	public $nextPageCssClass = 'dd-next';
	public $internalPageCssClass = '';
	public $selectedPageCssClass = 'dd-active';
	public $hiddenPageCssClass = 'dd-hide';
	public $header = '';

	/**
	 * @var array
	 */
	public $htmlOptions = [
		'id'    => 'dd_pager',
		'class' => 'dd-pagination',
	];

	/**
	 * Отрисовка ссылки на страницу
	 *
	 * @param string $label
	 * @param int $page
	 * @param string $class
	 * @param bool $hidden
	 * @param bool $selected
	 *
	 * @return string
	 */
	protected function createPageButton($label, $page, $class, $hidden, $selected)
	{
		$pageNumber = $label;
		if ($class == $this->previousPageCssClass) {
			$pageNumber = $this->getCurrentPage();
		} elseif ($class == $this->nextPageCssClass) {
			$pageNumber = $this->getCurrentPage() + 2;
		}

		if ($hidden || $selected) {
			$class .= ' ' . ($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
		}

		return "<li class='{$class}'><span data-page='{$pageNumber}'>{$label}</span></li>";
	}
}
