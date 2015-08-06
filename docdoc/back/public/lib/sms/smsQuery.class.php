<?php

// Остановка рассылки SMS очереди
function stopSMSquery($reason)
{
	file_put_contents(LOCK_FILE, "SMS:false");
	$log = new logger();
	$log->setLog(0, 'SMSER', $reason);
}

// Запуск рассылки SMS очереди
function startSMSquery()
{
	file_put_contents(LOCK_FILE, "SMS:true");
	$log = new logger();
	$log->setLog(0, 'SMSst', "Ручной запуск SMS очереди");
}

// Проверка очереди на включенное состояние ( "SMS:true" или "SMS:false")
function checkSMSquery()
{
	if (file_exists(LOCK_FILE)) {
		$str = file_get_contents(LOCK_FILE);
		if ($str == "SMS:true") {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
