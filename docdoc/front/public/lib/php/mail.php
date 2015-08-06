<?php

function sendMessage($subj, $mailBody, $mailTo = array(), $from = "Docdoc.ru", $emailFrom = null)
{
	$sent = 0;

	if (is_null($emailFrom)) {
		$emailFrom = Yii::app()->params['email']['from'];
	}

	$message = Yii::app()->mailer
		->createMessage($subj, $mailBody, 'text/html', 'utf-8')
		->setFrom($emailFrom, $from)
		->setTo($mailTo);

	try {
		$sent = Yii::app()->mailer->send($message);
	} catch (Exception $e) {}

	return $sent;
}
