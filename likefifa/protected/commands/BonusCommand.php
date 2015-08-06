<?php

use dfs\common\components\console\Command;
use dfs\modules\payments\models\PaymentsAccount;
use dfs\modules\payments\models\PaymentsOperations;

/**
 * Class BonusCommand
 *
 * Дарит бонус на счёт мастера
 *
 * @package likefifa\commands
 */
class BonusCommand extends Command
{
	/**
	 * @throws \Exception
	 * @param array $args command line parameters for this command.
	 * @return int|void
	 */
	public function run($args)
	{
		$masterId = isset($args[0]) ? (int)$args[0] : null;
		if (!$masterId) {
			$this->log("master_id not found.", CLogger::LEVEL_ERROR);
			return 1;
		}

		$amount = isset($args[1]) ? floor((int)$args[1]) : null;
		if (!$amount) {
			$this->log("Mo amount.", CLogger::LEVEL_ERROR);
			return 2;
		}

		$master = LfMaster::model()->findByPk($masterId);
		if (!$master) {
			$this->log("master with id #{$masterId} not found.", CLogger::LEVEL_ERROR);
			return 1;
		}

		PaymentsAccount::model()
			->findByPk(PaymentsAccount::BONUS_ID)
			->creditAmount(
				$master->getAccount(),
				$amount,
				false,
				PaymentsOperations::TYPE_BONUS,
				"Поступил бонус на сумму {$amount} рублей"
			)
		;

		$this->log("Отправлен бонус на сумму: {$amount} рублей мастеру #{$masterId}.");
	}

	public function getHelp()
	{
		return <<<EOD
USAGE
  yiic bonus [master_id] [amount]

DESCRIPTION

EXAMPLES

EOD;
	}
} 