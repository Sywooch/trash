<?php

	class CLinkPagerCustom extends CLinkPager
	{
		public $linkHtmlOptions = array();
		/**
		 * Creates a page button.
		 * You may override this method to customize the page buttons.
		 * @param string $label the text label for the button
		 * @param integer $page the page number
		 * @param string $class the CSS class for the page button.
		 * @param boolean $hidden whether this page button is visible
		 * @param boolean $selected whether this page button is selected
		 * @return string the generated button
		 */
		protected function createPageButton($label,$page,$class,$hidden,$selected)
		{
			$linkHtmlOptions = array();
			if(strpos($class, 'first') !== false || strpos($class, 'last') !== false || $hidden){
				return '';
			}
			
			if($selected){
				$linkHtmlOptions['class'] = $this->linkHtmlOptions['class'] .' '.$this->linkHtmlOptions['selected'];	
				$class.=' '. $this->selectedPageCssClass;
			}

			//unset($this->linkHtmlOptions['selected']);
			return '<li class="'.$class.'">'.CHtml::link($label,$this->createPageUrl($page), $linkHtmlOptions).'</li>';
		}
	}