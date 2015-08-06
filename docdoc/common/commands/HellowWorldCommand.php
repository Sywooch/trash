<?php
/**
 * @author Aleksey Parshukov, <aleksey@parshukov.name>
 * Date: 3/29/14
 * Time: 9:25 AM
 *
 *         Команда для примера
 *
 */
class HellowWorldCommand extends CConsoleCommand
{
	/**
	 * @param array $args
	 *
	 * @return int|void
	 */
	public function run($args) {
		echo "Hellow World!" . PHP_EOL;

		return 0;
	}

} 