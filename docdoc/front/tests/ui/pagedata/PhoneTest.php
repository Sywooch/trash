<?php
namespace dfs\front\tests\ui\pagedata;

use dfs\common\test\selenium\DefaultTestCase;

/**
 * Class PhoneTest
 *
 * @package dfs\front\tests\ui\pagedata
 */
class PhoneTest extends DefaultTestCase
{
	/**
	 * Проверка коррекного номера телефона
	 */
	public function testContactPhoneNumber()
	{
		$this->url($this->getFrontUrl());
		$telephone = $this->byClassName("header_contact_phone", "Не удалось найти номера телефона");
		if ($this->isMobile()) {
			$this->assertEquals("(495) 223-02-96", $telephone->text());
		} else {
			$this->assertEquals("8 (495) 236-72-76", $telephone->text());
		}
	}

}