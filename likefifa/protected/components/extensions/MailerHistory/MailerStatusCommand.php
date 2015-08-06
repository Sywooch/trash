<?php


namespace likefifa\components\extensions\MailerHistory;

use CMap;
use DateTime;
use dfs\common\components\console\Command;
use LfMaster;
use likefifa\components\system\DbCriteria;
use Yii;

/**
 * Команда для рассылки писем-уведомлений для мастеров
 *
 * @package likefifa\components\extensions\MailerHistory
 */
class MailerStatusCommand extends Command
{
	/**
	 * Диапазон минут для проверки.
	 * Команда должна запускаться один раз в указанный промежуток
	 */
	const DIFF_MINUTES = 60;

	public function run($args)
	{
		// Фиксы для правильной генерации урлов
		$_SERVER['SERVER_NAME'] = str_replace('http://', '', Yii::app()->params['baseUrl']);
		$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];

		// Письмо о незаполненности профиля (через сутки после публикации)
		$end = (new DateTime())->modify('-1 days');
		$start = clone $end;
		$start->modify('-' . self::DIFF_MINUTES . ' minutes');

		$criteria = new DbCriteria;
		$criteria->compare('t.type', MailerHistory::TYPE_PUBLISH);
		$criteria->addBetweenCondition('t.created', $start->format('Y-m-d H:i:00'), $end->format('Y-m-d H:i:59'));
		$mails = MailerHistory::model()->findAll($criteria);
		foreach ($mails as $mail) {
			$mail->getEntity()->sendPublishMail(true);
		}

		// Отправляет письмо о пустом профиле (через первые и вторые сутки после регистрации)
		$end = (new DateTime())->modify('-1 days');
		$start = clone $end;
		$start->modify('-' . self::DIFF_MINUTES . ' minutes');

		$criteria = new DbCriteria;
		$criteria->addBetweenCondition('t.created', $start->format('Y-m-d H:i:00'), $end->format('Y-m-d H:i:59'));
		$masters = LfMaster::model()->findAll($criteria);

		$end = (new DateTime())->modify('-2 days');
		$start = clone $end;
		$start->modify('-' . self::DIFF_MINUTES . ' minutes');
		$criteria = new DbCriteria;
		$criteria->addBetweenCondition('t.created', $start->format('Y-m-d H:i:00'), $end->format('Y-m-d H:i:59'));
		$masters = CMap::mergeArray($masters, LfMaster::model()->findAll($criteria));

		/** @var LfMaster[] $masters */
		foreach($masters as $master) {
			$master->sendEmptyProfile();
		}
	}
} 