<?php

namespace likefifa\test\unit\components\console;

use likefifa\components\console\Command;
use likefifa\tests\CTestCase;

/**
 * Class CommandTest
 *
 * Тестируем абстракную команду
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date   2.10.2013
 *
 * @package likefifa\test\unit\components\console
 */
class CommandTest extends CTestCase
{
	public function testLoadClassFromNamespace()
	{
		/**
		 * @var Command $command
		 */
		$command = $this
			->getMockBuilder('likefifa\components\console\Command')
			->disableOriginalConstructor()
			->getMock();
	}

} 