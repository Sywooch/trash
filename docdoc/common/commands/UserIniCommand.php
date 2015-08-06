<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 05.08.14
 * Time: 14:21
 */
use dfs\common\config\ConfigBuilder;

/**
 * Команда, создающая .user.ini для проекта
 * в зависимости от настроек
 *
 * Class UserIniCommand
 */
class UserIniCommand extends CConsoleCommand
{
	/**
	 * Метод по умолчанию
	 *
	 * @param string $project  имя проекта
	 * @param string $filename папка куда поместить файл
	 *
	 * @throws CException
	 */
	public function actionIndex($project, $filename)
	{
		$configBuilder = new ConfigBuilder($project);
		$params = $configBuilder->getConfig()['params'];
		$user_ini_params = isset($params['php_user_ini']) ? $params['php_user_ini'] : [];

		$user_ini_params['error_log'] = ROOT_PATH . DIRECTORY_SEPARATOR . $project . '/runtime/php_errors.log';

		if (count($user_ini_params)) {
			$text =
			$paramsStr = [];

			foreach ($user_ini_params as $k => $v) {
				if ($project == 'common') {
					$text[] = $k . '=' . escapeshellarg($v);
				} else {
					$text[] = "$k=$v";
				}
			}

			if (file_put_contents($filename, implode(PHP_EOL, $text)) === false) {
				throw new CException("Не смог записать файл $filename");
			}
		}
	}
} 
