<?php
use dfs\common\components\console\Command;
use dfs\docdoc\models\RequestModel;

/**
 * Очищает кэш на сайте
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 *
 */
class FillClientCommand extends Command
{
	/**
	 * @param array $args
	 *
	 * @return int|void
	 */
	public function run($args)
	{
		$this->log("Заполнение таблицы client...");

		$total = RequestModel::model()->count(['condition' => 'clientId IS NULL']);

		$requests = RequestModel::model()->findAll([
			'condition' => "clientId IS NULL AND client_phone IS NOT NULL AND client_phone<>'' ",
			'limit' => 2000
		]);

		$this->log("Всего заявок без клиентов - {$total}, выбрано для привязки к клиентам - " . count($requests));

		$n = 0;
		foreach ($requests as $r) {
			if ($r->saveClientId()) {
				$n++;
			}
		}

		$this->log("Сохранено {$n} записей из " . count($requests));
	}
}