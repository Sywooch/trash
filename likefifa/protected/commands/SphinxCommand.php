<?php
use dfs\common\components\console\Command;
use likefifa\components\helpers\SphinxHelper;

/**
 * Команды для работы со sphinx
 *
 * @package likefifa\commands
 */
class SphinxCommand extends Command
{
	/**
	 * Строит триграммы для исправления ошибок
	 */
	public function actionSuggest()
	{
		Yii::app()->db->createCommand()->truncateTable('lf_service_suggest');
		$file = Yii::getPathOfAlias('application.data.') . '/dicts_items';
		$output = [];
		exec('sudo -u sphinxsearch indexer specsAndServices --buildstops ' . $file . ' 500000 --buildfreqs', $output);
		foreach ($output as $row) {
			echo $row . PHP_EOL;
		}

		$lines = file($file);
		foreach ($lines as $line) {
			list ($keyword, $freq) = explode(' ', trim($line));
			if (is_numeric($keyword) || mb_strlen($keyword, 'UTF-8') < 3) {
				continue;
			}

			$trigrams = SphinxHelper::buildTrigrams($keyword);
			Yii::app()->db->createCommand()->insert(
				'lf_service_suggest',
				[
					'keyword'  => $keyword,
					'trigrams' => $trigrams,
					'freq'     => $freq,
				]
			);
		}
		exec('sudo -u sphinxsearch indexer --all --rotate');
	}
} 