<?php


namespace likefifa\components\system\admin;

use CWidget;
use CHtml;

/**
 * Class YbBox
 *
 * @package booster.widgets.grouping
 */
class YbBox extends CWidget
{
	/**
	 * @var mixed
	 * Box title
	 * If set to false, a box with no title is rendered
	 */
	public $title = '';

	/**
	 * @var string
	 * The class icon to display in the header title of the box.
	 * @see <http://twitter.github.com/bootstrap/base-css.html#icon>
	 */
	public $headerIcon;

	/**
	 * @var string
	 * Box Content
	 * optional, the content of this attribute is echoed as the box content
	 */
	public $content = '';

	/**
	 * @var array
	 * box HTML additional attributes
	 */
	public $htmlOptions = array();

	/**
	 * @var array
	 * box header HTML additional attributes
	 */
	public $htmlHeaderOptions = array();

	/**
	 * @var array
	 * box content HTML additional attributes
	 */
	public $htmlContentOptions = array();

	/**
	 * @var array the configuration for additional header buttons. Each array element specifies a single button
	 * which has the following format:
	 * <pre>
	 * array(
	 * array(
	 * 'class' => 'bootstrap.widgets.TbButton',
	 * 'label' => '...',
	 * 'size' => '...',
	 * ...
	 * ),
	 * array(
	 * 'class' => 'bootstrap.widgets.TbButtonGroup',
	 * 'buttons' => array( ... ),
	 * 'size' => '...',
	 * ),
	 * ...
	 * )
	 * </pre>
	 */
	public $headerButtons = array();

	/**
	 *### .init()
	 *
	 * Widget initialization
	 */
	public function init()
	{
		if (isset($this->htmlOptions['class'])) {
			$this->htmlOptions['class'] = 'box ' . $this->htmlOptions['class'];
		} else {
			$this->htmlOptions['class'] = 'box';
		}

		if (isset($this->htmlContentOptions['class'])) {
			$this->htmlContentOptions['class'] = 'box-content ' . $this->htmlContentOptions['class'];
		} else {
			$this->htmlContentOptions['class'] = 'box-content';
		}

		if (!isset($this->htmlContentOptions['id'])) {
			$this->htmlContentOptions['id'] = $this->getId();
		}

		if (isset($this->htmlHeaderOptions['class'])) {
			$this->htmlHeaderOptions['class'] = 'box-header ' . $this->htmlHeaderOptions['class'];
		} else {
			$this->htmlHeaderOptions['class'] = 'box-header';
		}

		echo CHtml::openTag('div', $this->htmlOptions);

		$this->renderHeader();
		$this->renderContentBegin();
	}

	/**
	 *### .run()
	 *
	 * Widget run - used for closing procedures
	 */
	public function run()
	{
		$this->renderContentEnd();
		echo CHtml::closeTag('div') . "\n";
	}

	/**
	 *### .renderHeader()
	 *
	 * Renders the header of the box with the header control (button to show/hide the box)
	 */
	public function renderHeader()
	{
		if ($this->title !== false) {
			echo CHtml::openTag('div', $this->htmlHeaderOptions);
			if ($this->title) {
				$title = '<h2>';

				if ($this->headerIcon) {
					$title .= '<i class="' . $this->headerIcon . '"></i><span class="break"></span>';
				}
				$title .= $this->title . '</h2>';

				$this->renderButtons();
				echo $title;
			}
			echo CHtml::closeTag('div');
		}
	}

	/**
	 *### .renderButtons()
	 *
	 * Renders a header buttons to display the configured actions
	 */
	public function renderButtons()
	{
		if (empty($this->headerButtons)) {
			return;
		}

		echo '<div class="box-icon">';

		if (!empty($this->headerButtons) && is_array($this->headerButtons)) {
			foreach ($this->headerButtons as $button) {
				$showLabel = isset($button['showLabel']) ? $button['showLabel'] : true;
				echo CHtml::openTag(
					'a',
					[
						'href'        => $button['url'],
						'class'       => isset($button['class']) ? $button['class'] : false,
						'title'       => $showLabel === false ? CHtml::encode($button['label']) : false,
						'data-toggle' => $showLabel === false ? 'tooltip' : false,
					]
				);

				if (isset($button['icon'])) {
					echo '<i class="' . $button['icon'] . '"></i>';
				}

				if($showLabel == true) {
					echo $button['label'];
				}

				echo '</a>';
			}
		}

		echo '</div>';
	}

	/*
	*### .renderContentBegin()
	*
	* Renders the opening of the content element and the optional content
	*/
	public function renderContentBegin()
	{
		echo CHtml::openTag('div', $this->htmlContentOptions);
		if (!empty($this->content)) {
			echo $this->content;
		}
	}

	/*
	*### .renderContentEnd()
	*
	* Closes the content element
	*/
	public function renderContentEnd()
	{
		echo CHtml::closeTag('div');
	}
}