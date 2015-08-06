<?php
namespace dfs\docdoc\components\behaviors;

use dfs\docdoc\extensions\TextUtils;

/**
 * Class CaseArBehavior
 * @package dfs\docdoc\components\behaviors
 */
class CaseArBehavior extends \CBehavior
{

	/**
	 * Название в предложном падеже
	 *
	 * @param string $attr
	 * @param bool $many
	 * @return mixed
	 */
	public function inPrepositional($attr, $many = false)
	{
		return TextUtils::parseWords($this->owner->$attr, $many, 'wordPrepositional');
	}

}
