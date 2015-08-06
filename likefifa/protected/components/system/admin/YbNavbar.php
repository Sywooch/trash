<?php


namespace likefifa\components\system\admin;


use CHtml;
use CWidget;


/**
 *## Bootstrap navigation bar widget.
 *
 * @package booster.widgets.navigation
 * @since   0.9.7
 */
class YbNavbar extends CWidget
{

	const CONTAINER_PREFIX = 'yii_booster_collapse_';

	// Navbar types.
	const TYPE_DEFAULT = 'default';
	const TYPE_INVERSE = 'inverse';

	// Navbar fix locations.
	const FIXED_TOP = 'top';
	const FIXED_BOTTOM = 'bottom';

	/**
	 * @var string the navbar type. Valid values are 'inverse'.
	 * @since 1.0.0
	 */
	public $type = self::TYPE_DEFAULT;

	/**
	 * @var string the text for the brand.
	 */
	public $brand;

	/**
	 * @var string the URL for the brand link.
	 */
	public $brandUrl;

	/**
	 * @var array the HTML attributes for the brand link.
	 */
	public $brandOptions = array();

	/**
	 * @var array navigation items.
	 * @since 0.9.8
	 */
	public $items = array();

	/**
	 * @var mixed fix location of the navbar if applicable.
	 * Valid values are 'top' and 'bottom'. Defaults to 'top'.
	 * Setting the value to false will make the navbar static.
	 * @since 0.9.8
	 */
	public $fixed = self::FIXED_TOP;

	/**
	 * @var boolean whether the nav span over the full width. Defaults to false.
	 * @since 0.9.8
	 */
	public $fluid = false;

	/**
	 * @var boolean whether to enable collapsing on narrow screens. Default to true.
	 */
	public $collapse = true;

	/**
	 * @var array the HTML attributes for the widget container.
	 */
	public $htmlOptions = array();

	/**
	 *### .init()
	 *
	 * Initializes the widget.
	 */
	public function init()
	{

		if ($this->brand !== false) {
			if (!isset($this->brand)) {
				$this->brand = CHtml::encode(Yii::app()->name);
			}

			if (!isset($this->brandUrl)) {
				$this->brandUrl = Yii::app()->homeUrl;
			}

			$this->brandOptions['href'] = CHtml::normalizeUrl($this->brandUrl);

			if (isset($this->brandOptions['class'])) {
				$this->brandOptions['class'] .= ' navbar-brand';
			} else {
				$this->brandOptions['class'] = 'navbar-brand';
			}
		}
	}

	/**
	 *### .run()
	 *
	 * Runs the widget.
	 */
	public function run()
	{

		echo CHtml::openTag('nav', $this->htmlOptions);

		echo '<div class="sidebar-nav nav-collapse collapse navbar-collapse" id="' . self::CONTAINER_PREFIX . $this->id . '">';
		foreach ($this->items as $item) {
			if (is_string($item)) {
				echo $item;
			} else {
				if (isset($item['class'])) {
					$className = $item['class'];
					unset($item['class']);

					$this->controller->widget($className, $item);
				}
			}
		}
		echo '</div></nav>';
	}
}
