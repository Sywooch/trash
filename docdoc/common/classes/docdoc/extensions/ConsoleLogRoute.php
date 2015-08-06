<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 18.07.14
 * Time: 11:28
 */

namespace dfs\docdoc\extensions;

class ConsoleLogRoute extends \CFileLogRoute
{
	protected $logFile = 'php://stdout';

	public function processLogs($logs)
	{
		$text = '';

		foreach ($logs as $log) {
			$text .= $this->formatLogMessage($log[0], $log[1], $log[2], $log[3]);
		}

		$logFile = $this->logFile;

		$file = fopen($logFile, "w");

		if ($file) {
			fwrite($file, $text);
			fclose($file);
		}
	}
} 
