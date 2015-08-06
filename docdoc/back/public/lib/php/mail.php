<?php

use dfs\docdoc\models\MailQueryModel;


require_once dirname(__FILE__) . '/errorLog.php';

function sendMessage($subj, $mailBody, $mailTo = array(), $from = "Docdoc.ru", $emailFrom = null)
{
	if (is_null($emailFrom)) {
		$emailFrom = Yii::app()->params['email']['from'];
	}

	$message = Yii::app()->mailer
		->createMessage($subj, $mailBody, 'text/html', 'utf-8')
		->setFrom($emailFrom, $from)
		->setTo($mailTo);

	try {
		$sent = Yii::app()->mailer->send($message);
	} catch (Exception $e) {
		new msgLog($e->getMessage());
		$sent = false;
	}

	if (!$sent) {
		$logMessage = "Ошибка при отправке почты. Тема: " . $subj . " Адресат: " . serialize($mailTo) . " Отправка через: " . Yii::app()->mailer->type . '"';
		new msgLog($logMessage);
		throw new Exception($logMessage);
	}
}

/**
 * Отправка сообщения из очереди
 *
 * @param int $id
 *
 * @return MailQueryModel
 * @throws Exception
 */
function sendMessageById($id)
{
	$mail = MailQueryModel::model()->findByPk($id);

	$sent = false;

	if ($mail) {
		try {
			$sent = $mail->sendMail();
		} catch (Exception $e) {
			new msgLog($e->getMessage());
		}
	}

	if (!$sent) {
		$msg = $mail ? " Тема: {$mail->subj} Адресат: {$mail->emailTo}" : '';
		$logMessage = "Ошибка при отправке почты.{$msg} Отправка через: " . Yii::app()->mailer->type . '"';
		new msgLog($logMessage);
		throw new Exception($logMessage);
	}

	return $mail;
}
