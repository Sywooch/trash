<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/emailQuery.class.php";
	require_once dirname(__FILE__)."/../../lib/php/mail.php";
	require_once dirname(__FILE__)."/../../lib/php/croneLocker.php";
	require_once dirname(__FILE__).'/../../include/croneList.php';
	require_once dirname(__FILE__)."/../../lib/php/russianTextUtils.class.php";
	

	set_time_limit(60);
	$delta = 0; // сразу
	$eof = "<br>";
	
	/* Перевод заявок в ЛК из статуса записан в статус Условно дошел по прошествии времени прёма */

	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/request.class.php";
	require_once dirname(__FILE__)."/../../lib/php/croneLocker.php";
	require_once dirname(__FILE__).'/../../include/croneList.php';

	$croneName = 'croneLKNotifier';
	$crone = croneList::getConfig($croneName);
	$statusPath = LOCK_FILE_CRONE_DIR.$crone['file'];
	$status = getCroneStatusParam('', $statusPath);
	
	new commonLog($croneName.".log", "Start ".$croneName );
	
if(isset($status['isAvailableGlobal']) && $status['isAvailableGlobal'] == 'true'){
	
	// Работаем с максимальным количеством зависаний и автоматической разблокировкой
	if(!isset($status['isAvailable']) || $status['isAvailable'] != 'true'){
		if(!isset($status['maxLockedTry']) || $status['maxLockedTry'] >= croneRequestListener_MaxLockedTry){
			$status['isAvailable'] = 'true';
			$status['maxLockedTry'] = '0';
			saveCronStatusParam(array('maxLockedTry' => '0'), $statusPath, $croneName);
		}
	}
	
	if(empty($status['isAvailable']) || $status['isAvailable'] != 'false'){ // А, если не существует разрешения от предыдущего процесса, то даем его.
		saveCronStatusParam(array('isAvailable' => 'false'), $statusPath, $croneName);
		if($status['maxLockedTry'] != 0){
			saveCronStatusParam(array('maxLockedTry' => 0), $statusPath, $croneName);
		}
		new commonLog($croneName.".log", "File  ".$croneName." locked" );
	
		/*	Определение активных клиик для рассылки */
		$sql = "SELECT cl.id, st.lk_start_history_date, adm.email, adm.fname, adm.lname, adm.mname   
				FROM clinic cl
				INNER JOIN clinic_settings st ON (st.settings_id = cl.settings_id)
				LEFT JOIN admin_4_clinic a4cl ON (a4cl.clinic_id = cl.id)
				LEFT JOIN clinic_admin adm ON (a4cl.clinic_admin_id = adm.clinic_admin_id)
				WHERE
					cl.status = 3
					AND
					cl.isClinic = 'yes'
					AND
					adm.email IS NOT NULL
					AND
					st.contract_id = 1 "; // За дошедших
		
		$result = query($sql);
		if (num_rows($result) > 0) {
			new commonLog($croneName.".log", "Get ".num_rows($result)." clinics ");
			while ($row = fetch_object($result)) {
				
				/*	Для каждой клиники получаем список не подтвержденных заявок и формируем письмо */
		
				if (!empty($row-> fname) && !empty($row-> mname)) {
					$msg  = "<div>".ucfirst($row-> fname)." ".ucfirst($row-> mname).", добрый день!</div>";
				} else {
					$msg  = "<div>Добрый день!</div>";
				}
				
				$sqlAdd = "	SELECT 
								t1.req_id,
								t1.date_admission,
								t3.name as sector,
								t2.name as doctor,
								t1.client_name, 
								t1.client_phone 
							FROM request  t1
		                    LEFT JOIN doctor t2 ON (t2.id = t1.req_doctor_id)
		                    LEFT JOIN sector t3 ON (t3.id = t1.req_sector_id)
							WHERE
								t1.clinic_id = ".$row->id."
								AND
								t1.lk_status = 2
								AND
								t1.date_admission < UNIX_TIMESTAMP(now())
								AND
								t1.date_admission >= UNIX_TIMESTAMP('".$row->lk_start_history_date."') 
							ORDER BY t1.date_admission DESC
						  ";   
				$resultAdd = query($sqlAdd);
				if (num_rows($resultAdd) > 0) {
					$msg .= "<style>
								th {background-color: #67bbbc;}
								tr.odd {background-color: #eefbfc;}
							</style>";
					$msg .= "<div>Напоминаем Вам о необходимости подтвердить пациентов (".num_rows($resultAdd)."), которые были на приеме в вашей клинике.</div>";
					$msg .= "<div>Для подтверждения зайдите в личный кабинет клиники на страницу заявок (или просто перейдите по ссылке <a href='https://docdoc.ru/lk/patients'>https://docdoc.ru/lk/patients</a>) и отметьте тех пациентов, которые были на приеме у ваших врачей.</div>";
					$msg .= "<div>В случае возникновения вопросов пишите нам в техническую поддержку на почту <a href='mailto:".Yii::app()->params['email']['support']."'>".Yii::app()->params['email']['support']."</a> или через специальную форму в личном кабинете. Наши специалисты всегда готовы Вам помочь. </div>";
					$msg .= "<table width = '100%'>";
					$msg .= "<tr><th>№</th><th>Дата приёма</th><th>Пациент</th><th>Врач</th><th>Специальность</th></tr>";
					$i = 0;
					while ($rowAdd = fetch_object($resultAdd)) {
						$msg .=  ( $i % 2 == 0) ? "<tr>" : "<tr class='odd'>";  
						
						$msg .= "<td>".$rowAdd->req_id."</td>";
						$msg .= "<td>".date("d.m.y",$rowAdd->date_admission)." ".date("H:i",$rowAdd->date_admission)."</td>";
						$msg .= "<td>".mb_convert_case($rowAdd->client_name, MB_CASE_TITLE, "UTF-8")."</td>";
						$msg .= "<td>".$rowAdd->doctor."</td>";
						$msg .= "<td>".$rowAdd->sector."</td>";
						$msg .= "</tr>";
						$i++;
					}
					$msg .= "</table>";
					new commonLog($croneName.".log", "Insert  ".num_rows($resultAdd)." request(s) into mail body" );	
				
					
					$msg .= "<div>С уважением, <br/> служба поддержки <a href='http://docdoc.ru'>DocDoc</a></div>";
					
					$subj =	"[docdoc.ru] Просьба подтвердить заявки в ЛК";
					
					echo $row->email." - ".$msg."<br><br>";
					$emailToTMP = Yii::app()->params['email']['support'];
					$params = array(
						"emailTo" => $emailToTMP,
						"message" => $msg,
						"subj" => $subj
					);
					if ( !($id = emailQuery::addMessage($params)) ) {
						new commonLog($croneName.".log", "Error adding into the query for ".$row->email );
						echo "Ошибка добавления в E-mail очередь".$eof;
					} else {
						new commonLog($croneName.".log", "Email add into the query for ".$row->email );	
					}
				}
				
				
			}
		}
		saveCronStatusParam(array('isAvailable' => 'true'), $statusPath, $croneName);
		new commonLog($croneName.".log", "File  ".$croneName." unlocked");
	}else{
		echo "Заблокированно предыдущим процессом\n";
		saveCronStatusParam(array('maxLockedTry' => ++$status['maxLockedTry']), $statusPath, $croneName);
		new commonLog($croneName.".log", "File  was locked latest process");
	}
}else{
	echo "Заблокированно администратором системы\n";
	new commonLog($croneName.'.log', 'File  was locked system administrator');
}
		
new commonLog($croneName.".log", " " );
new commonLog($croneName.".log", " " );



?>