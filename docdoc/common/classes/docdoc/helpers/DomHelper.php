<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 08.07.14
 * Time: 18:23
 */

namespace dfs\docdoc\helpers;

/**
 * Class DomHelper
 *
 * @package dfs\docdoc\helpers
 */
class DomHelper
{
	/**
	 * @param \SimpleXMLElement $node
	 * @param string            $attrName  название атрибута
	 * @param string            $searchVal значение атрибута
	 *
	 * @return string
	 */
	public static function searchElt($node, $attrName, $searchVal)
	{
		foreach ($node->Element as $item) {
			$attr = $item->attributes();
			if ($attr[$attrName] == $searchVal) {
				return $item;
			}
		}
		return " ";
	}
} 
