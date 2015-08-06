<?php
use dfs\docdoc\models\DoctorClinicModel;

class Schedule 
{
	private $doctorId;
	private $clinicId;
	private $timeIntervals = array();
	private $step = 60; // шаг времени записи на приём в минутах
	
	
	public function __construct($clinicId = null, $doctorId = null) {
            if(!empty($clinicId))
                $this->setClinic($clinicId);
            if(!empty($doctorId))
                $this->setDoctor($doctorId);
        }
	
	public function setDoctor($doctorId) {
		$this->doctorId = $doctorId;
	}
	
	public function setClinic($clinicId) {
		$this->clinicId = $clinicId;
	}
	
	public function setStep($step) {
		$this->step = $step;
	}
	
	
	public function getStep() {
		return $this->step;
	}
	
	
	
	
	/**
	 * Получение шага расписания врача из БД
	 */
	public function getDoctorStep() {
		$sql = " 	SELECT
						schedule_step
					FROM doctor_4_clinic 
					WHERE  
						doctor_id = ".$this->doctorId."
						AND
						type = " . DoctorClinicModel::TYPE_DOCTOR . "
						AND
						clinic_id = ".$this->clinicId;
		$result = query($sql);
		if ( num_rows($result) == 1) {
			$row = fetch_object($result);
			$this->step = $row->schedule_step;
		}
		return $this->step;
	}
	
	
	
	
	
	/**
	 * Установить запись в расписании врача (резервирование).
	 * $startDateTime = "01.01.2013 11:12" - формат даты dd.mm.YYYY H:i
	 * @return number - возвращает id записи в таблице doctor_sсhedule_on_day или  -1, если интервыал занят
	 */
	public function reserveRecord4Doctor ($startDateTime) {
		$id = 0;
		
		// конвертация даты в формат БД
		$dateTimeArr = explode(" ",$startDateTime);
		$dateArr = explode(".",$dateTimeArr[0]);
		$onDate = $dateArr[2]."-".$dateArr[1]."-".$dateArr[0];
		$timeArr = explode(":",$dateTimeArr[1]);
		
		$startTime = date("H:i", mktime($timeArr[0], $timeArr[1], 0, $dateArr[1], $dateArr[0], $dateArr[2]) ); 
		$endTime = date("H:i", mktime($timeArr[0], $timeArr[1]+$this->step, 0, $dateArr[1], $dateArr[0], $dateArr[2]));

		// checkInterval проверяет свобюоден ли данный интервал
		if ( self::checkInterval ($onDate, $startTime) ) {
			$sql = " INSERT INTO doctor_sсhedule_on_day SET
						on_date_schedule = '".$onDate."',
						doctor_id = ".$this->doctorId.",
						clinic_id = ".$this->clinicId.",
						start_time = '".$startTime."',
						end_time = '".$endTime."',
						type_state = 'reserve'";
			//echo $sql;
			$result = query($sql);
			$id = legacy_insert_id();
			
			// Очистка таблицы для быстрой выборки интервалов (табло расписания)
			self::clearSchedulePool($onDate);
		} else {
			return -1;
		}
		
		return $id;
	}
	
	
	
	
	
	/**
	 * Установить запись в расписании врача (резервирование) для внешних систем.
	 * $startDateTime = "01.01.2013 11:12" - формат даты dd.mm.YYYY H:i
	 * $extStr = serialize(набор передаваемых параметров внешней системой)
	 * @return number - возвращает id записи в таблице doctor_sсhedule_on_day или  -1, если интервыал занят
	 * Пример вызова: 
         * 
	 */
	public function reserveRecord4DoctorExt ($startDateTime, $extStr = "") {
		$id = 0;
		
		// конвертация даты в формат БД
		$dateTimeArr = explode(" ",$startDateTime);
		$dateArr = explode(".",$dateTimeArr[0]);
		$onDate = $dateArr[2]."-".$dateArr[1]."-".$dateArr[0];
		$timeArr = explode(":",$dateTimeArr[1]);
		
		$startTime = date("H:i", mktime($timeArr[0], $timeArr[1], 0, $dateArr[1], $dateArr[0], $dateArr[2]) ); 
		$endTime = date("H:i", mktime($timeArr[0], $timeArr[1]+$this->step, 0, $dateArr[1], $dateArr[0], $dateArr[2]));

		// checkInterval проверяет свобюоден ли данный интервал
		if ( self::checkInterval ($onDate, $startTime) ) {
			$sql = " INSERT INTO doctor_sсhedule_on_day SET
						on_date_schedule = '".$onDate."',
						doctor_id = ".$this->doctorId.",
						clinic_id = ".$this->clinicId.",
						start_time = '".$startTime."',
						end_time = '".$endTime."',
						external_data = '".$extStr."', 
						type_state = 'reserve'";
			//echo $sql;
			$result = query($sql);
			$id = legacy_insert_id();
			
			// Очистка таблицы для быстрой выборки интервалов (табло расписания)
			self::clearSchedulePool($onDate);
		} else {
			return -1;
		}
		
		return $id;
	}
	
	
	
	
	/**
	 * Подтверждение записи на приём
	 */
	public function commitRecord4Doctor ($id, $requestId) {
		$sql = " UPDATE doctor_sсhedule_on_day SET
					type_state = 'request',
					request_id = ".intval($requestId)."
				WHERE id = ".$id;
		//echo $sql;
		$result = query($sql);
		
		return true;
	}
	
	
	
	
	/**
	 * Отмена записи
	 */
	public function cancelRecord ($recordId) {
		$id = intval($recordId);
		$onDate = "";
		
		$sql = " SELECT DATE_FORMAT(on_date_schedule, '%Y-%m-%d') as on_date FROM doctor_sсhedule_on_day WHERE id = ".$id;
		$result = query($sql);
		if ( num_rows($result) == 1) {
			$row = fetch_object($result);
			$onDate = $row->on_date;
		} else {
			return false;
		}
		
		$sql = " DELETE FROM doctor_sсhedule_on_day WHERE id = ".$id;
		//echo $sql;
		$result = query($sql);
		
		// Очистка таблицы для быстрой выборки интервалов (табло расписания)
		self::clearSchedulePool($onDate);
		
		return true;
	}
	
	
	
	
	/**
	 * Очистка пула расписания на день для в врача в клинике
	 * вызывается при изменении расписания врача
	 */
	public function clearSchedulePool ($on_date) {
		$sql = " 	DELETE FROM schedule_day_pool 
					WHERE  
						doctor_id = ".$this->doctorId."
						AND
						clinic_id = ".$this->clinicId."
						AND
						on_date_schedule = '".$on_date."'";
		$result = query($sql);
		
		return true;
	}
	
	
	
	
	/**
	 * Получение расписания врача из пула
	 * $onDate = "01.01.2013" - формат даты dd.mm.YYYY
	 */
	public function getSchedulePool ($onDate) {
		//$intervalOut = array();
		
		$sql = " 	SELECT
						time_interval_array
					FROM schedule_day_pool 
					WHERE  
						doctor_id = ".$this->doctorId."
						AND
						clinic_id = ".$this->clinicId."
						AND
						on_date_schedule = date('".convertDate2DBformat($onDate)."')";
		$result = query($sql);
		if ( num_rows($result) == 1) {
			//echo "чтение из пула "."<br>";
			
			$row = fetch_object($result);
			$doctortimeInterval = $row-> time_interval_array;
			$doctortimeInterval = unserialize($doctortimeInterval);
			
			return $doctortimeInterval;
			
		} else {
			//echo "запись в пул "."<br>";
			$intervalOut = self::getDoctorScheduleByDate($onDate);
			$sql = "REPLACE INTO schedule_day_pool SET
						time_interval_array = '".serialize($intervalOut)."',
						doctor_id = ".$this->doctorId.",
						clinic_id = ".$this->clinicId.",
						on_date_schedule = date('".convertDate2DBformat($onDate)."')";
			$result = query($sql);

			return $intervalOut;
		}
		
		return $intervalOut;
	}
	
	
	
	
	// возвращает массив времени работы клиники
	// $on_date = "01.01.2013" - формат даты dd.mm.YYYY
	/*
	public function getClinicScheduleByDate ($on_date) {
		$sqlAdd = "";
		$workTimeArray = array();
		
		$weekDay = getWeekDayNumber4Schedule($on_date);
		if ( $weekDay >=1 && $weekDay < 6 ) {$sqlAdd = " OR week_day = 0 ";} // рабочая неделя
		
		$sql = " SELECT 
					TIME_FORMAT(start_time, '%H:%i') as startTime, 
					TIME_FORMAT(end_time , '%H:%i') as endTime
				FROM clinic_schedule 
				WHERE 
					clinic_id = ".$this->clinicId." 
					AND ( week_day = ".$weekDay.$sqlAdd." ) 
				ORDER BY start_time";
		
		//echo $sql;
		
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$i = 0;
			while ($row = fetch_object($result)) {
				$workTimeArray[$i] = array("startTime"=>$row ->startTime, "endTime"=>$row ->endTime);
				$i++;
			}
		}
			
		return $workTimeArray;
	}
	*/
	
	
	
	/**
	 * 
	 * Расписание клиники в зависимости от деня недели 
	 * @param int $weekDay = 0 - рабочая неделя, 1- пн, 2-вт, 7- вск
	 * 
	 */
	public function getClinicScheduleByWeekDay ($weekDay) {
		$sqlAdd = "";
		$weekDay = intval($weekDay);
		$workTimeArray = array();
		
		if ( $weekDay >=1 && $weekDay < 6 ) {$sqlAdd = " OR week_day = 0 ";} // рабочая неделя
		
		$sql = " SELECT 
					TIME_FORMAT(start_time, '%H:%i') as startTime, 
					TIME_FORMAT(end_time , '%H:%i') as endTime
				FROM clinic_schedule 
				WHERE 
					clinic_id = ".$this->clinicId." 
					AND ( week_day = ".$weekDay.$sqlAdd." ) 
				ORDER BY start_time";
		
		//echo $sql;
		
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$i = 0;
			while ($row = fetch_object($result)) {
				$workTimeArray[$i] = array("startTime"=>$row ->startTime, "endTime"=>$row ->endTime);
				//$workTimeArray[$i] = array($row ->startTime, $row ->endTime);
				$i++;
			}
		}
			
		return $workTimeArray;
	}
	
	
	
	
	
	// 	возвращает массив времени присутствия врача в клинике
	// $on_date = "01.01.2013" - формат даты dd.mm.YYYY
	/*
	public function getDoctorPresenceInClinicByDate ($on_date) {
		$sqlAdd = "";
		$workTimeArray = array();
		
		$weekDay = getWeekDayNumber4Schedule($on_date);
		if ( $weekDay >=1 && $weekDay < 6 ) {$sqlAdd = " OR week_day = 0 ";} // рабочая неделя
		
		$sql = " SELECT 
					TIME_FORMAT(start_time, '%H:%i') as startTime, 
					TIME_FORMAT(end_time , '%H:%i') as endTime
				FROM doctor_schedule_presence 
				WHERE 
					clinic_id = ".$this->clinicId."
					AND
					doctor_id = ".$this->doctorId."  
					AND ( week_day = ".$weekDay.$sqlAdd." ) 
				ORDER BY start_time";
		
		//echo $sql."<br>";
		
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$i = 0;
			while ($row = fetch_object($result)) {
				$workTimeArray[$i] = array("startTime"=>$row ->startTime, "endTime"=>$row ->endTime);
				//$workTimeArray[$i] = array($row ->startTime, $row ->endTime);
				$i++;
			}
		}
			
		return $workTimeArray;
	}
	*/
	
	
	
	/**
	 * 
	 * Возвращает массив интервалов присутствия в рача в клинике в зависимости от дня недели
	 * @param int $weekDay = 0 - рабочая неделя, 1- пн, 2-вт, 7- вск
	 */
	public function getDoctorPresenceInClinicByWeekDay ($weekDay) {
		$sqlAdd = "";
		$weekDay = intval($weekDay);
		$workTimeArray = array();
		
		if ( $weekDay >=1 && $weekDay < 6 ) {$sqlAdd = " OR week_day = 0 ";} // рабочая неделя
		
		$sql = " SELECT 
					TIME_FORMAT(start_time, '%H:%i') as startTime, 
					TIME_FORMAT(end_time , '%H:%i') as endTime
				FROM doctor_schedule_presence 
				WHERE 
					clinic_id = ".$this->clinicId."
					AND
					doctor_id = ".$this->doctorId."  
					AND ( week_day = ".$weekDay.$sqlAdd." ) 
				ORDER BY start_time";
		
		//echo $sql."<br>";
		
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$i = 0;
			while ($row = fetch_object($result)) {
				$workTimeArray[$i] = array("startTime"=>$row ->startTime, "endTime"=>$row ->endTime);
				//$workTimeArray[$i] = array($row ->startTime, $row ->endTime);
				$i++;
			}
		}
			
		return $workTimeArray;
	}
	
	
	
	
	/**
	 * 
	 * Возвращает занятость врача в клинике за дату 
	 * @param $onDate
	 */
	public function getBusyTime4DoctorInClinicByDate ($onDate) {
		$sqlAdd = "";
		$workTimeArray = array();
		
		$sql = " SELECT 
					TIME_FORMAT(start_time, '%H:%i') as startTime, 
					TIME_FORMAT(end_time , '%H:%i') as endTime
				FROM doctor_sсhedule_on_day  
				WHERE 
					clinic_id = ".$this->clinicId."
					AND
					doctor_id = ".$this->doctorId."  
					AND
					on_date_schedule = date('".convertDate2DBformat($onDate)."') 
				ORDER BY start_time";
		
		//echo $sql."<br>";
		
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$i = 0;
			while ($row = fetch_object($result)) {
				$workTimeArray[$i] = array("startTime"=>$row ->startTime, "endTime"=>$row ->endTime);
				//$workTimeArray[$i] = array($row ->startTime, $row ->endTime);
				$i++;
			}
		}
			
		return transformTimeIntervalToMinuts($workTimeArray);
	}
	
	
	
	
	
	/**
	 * 
	 * возвращает массив интервалов времени работы (присутствия) врача в клинике
	 * $onDate = "01.01.2013" - формат даты dd.mm.YYYY
	 * 
	 */ 	
	public function getDoctorPresenceByDate ($onDate) {
		$intervalOut = array();
		
		$weekDay = getWeekDayNumber4Schedule($onDate);
		
		$clinicIntervals = transformTimeIntervalToMinuts(self::getClinicScheduleByWeekDay($weekDay));
		//$doctorIntervals = transformTimeIntervalToMinuts(self::getDoctorPresenceInClinicByDate($onDate));
		$doctorIntervals = transformTimeIntervalToMinuts(self::getDoctorPresenceInClinicByWeekDay($weekDay));

		$intervalOut = splitInterval(crossInterval($clinicIntervals, $doctorIntervals));
		
		return $intervalOut;
	}
	
	
	
	
	/**
	 * 
	 * Основная функция. Выдает расписание врача на дату  
	 * @param $onDate = "01.01.2013" - формат даты dd.mm.YYYY
	 * 
	 */
	public function getDoctorScheduleByDate ($onDate) {
		$intervalOut = array();
		
		$presenceInterval 	= transformTimeIntervalInMinutsToOneLine(self::getDoctorPresenceByDate($onDate), 'PRESENCE');
		$busyInterval 		= transformTimeIntervalInMinutsToOneLine(self::getBusyTime4DoctorInClinicByDate($onDate), 'ABSENCE');
		$cross = crossLineInterval($presenceInterval, $busyInterval );

		$intervalOut = transformTimeIntervalToHHMM(transformTimeLineIntoTimeInterval($cross));
		
		return $intervalOut;
	}
        
        public function getIntervalsByDate($date) {
        
            $schedule = $this->getSchedulePool($date);
            
            return $this->normalizeInterval($schedule, $this->step);
        }
        
        /*
         * 
         * Заливка расписания для врача на определенный день недели
         * @param int $weekDay = 0 - рабочая неделя, 1- пн, 2-вт, 7- вск
         * @param array $times   array(0 => array($tmStart, $tmEnd))
         */
	public function setClinicSheduleByWeekDay ($weekDay, $times) {
            $result = query("START TRANSACTION");
            $sql = "DELETE FROM clinic_schedule 
                    WHERE clinic_id=".$this->clinicId." 
                        AND week_day = $weekDay";
            $result = query($sql);
            if (!$result) 
                setDBerror("Ошибка создания лога " . $sql);
            
            foreach($times as $time) {        
                $sql = "INSERT INTO clinic_schedule 
                        SET week_day=$weekDay, 
                            clinic_id=".$this->clinicId.", 
                            start_time='".$time[0]."', 
                            end_time='".$time[1]."'";
                $result = query($sql);
                if (!$result) 
                    setDBerror("Ошибка создания лога " . $sql);
            }
            $result = query("commit");

        }
        
        
        
        /*
         * 
         * Заливка расписания для клиники на определенный день недели
         * @param int $weekDay = 0 - рабочая неделя, 1- пн, 2-вт, 7- вск
         * @param array $times   array(0 => array($tmStart, $tmEnd))
         */
	public function setDoctorSheduleByWeekDay ($weekDay, $times) {
            $result = query("START TRANSACTION");
            $sql = "DELETE FROM doctor_schedule_presence 
                    WHERE doctor_id=".$this->doctorId." 
                        AND clinic_id=".$this->clinicId." 
                        AND week_day = $weekDay";
            $result = query($sql);
            if (!$result) 
                setDBerror("Ошибка создания лога " . $sql);
            
            foreach($times as $time) {        
                $sql = "INSERT INTO doctor_schedule_presence 
                        SET week_day=$weekDay, 
                            doctor_id=".$this->doctorId.", 
                            clinic_id=".$this->clinicId.", 
                            start_time='".$time[0]."', 
                            end_time='".$time[1]."'";
                $result = query($sql);
                if (!$result) 
                    setDBerror("Ошибка создания лога " . $sql);
            }
            $result = query("commit");

        }

        
         /**
	 * Нормализация инетвалов с шагом $step
	 */
	public function normalizeInterval ($intervals = array(), $step) {
		$intervalsOut = array();
		$step = intval($step);
		
		foreach ( $intervals as $interval ) {
			$start = convertHHMM2Minute($interval[0]);
			$end   = convertHHMM2Minute($interval[1]);
			
			$startN = $start; 
			$endN = $startN + $step;
			
			while ( $endN <= $end )
				if ( $endN <= $end ) {
					array_push($intervalsOut, array(convertMinutes2HHMM($startN), convertMinutes2HHMM($endN)));
					$startN = $endN;
					$endN = $startN + $step;
				}
		}
		
		return $intervalsOut;
	}
	
	
	
	/**
	 * 
	 * Проверяет, есть ли в наличии свободный интервал для записи к врачу
	 * @param $onDate = "01.01.2013" - формат даты dd.mm.YYYY
	 * @param $onTime = "14:45" - формат даты H:i
	 * 
	 */
	protected function checkInterval ($onDate, $onTime) {
		
		$sql = "SELECT COUNT(id) as cnt FROM doctor_sсhedule_on_day 
				WHERE 
					on_date_schedule = '".$onDate."'
					AND
					doctor_id = ".$this->doctorId."
					AND
					clinic_id = ".$this->clinicId."
					AND
					start_time = '".$onTime."'";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) > 0 ) {
			$row = fetch_object($result);
			if ( $row -> cnt == 0 ) {
				return true;
			} else {
				return false;
			}
		}
	}
	
}




/*	################################################################	*/
/*
 *Построение XML данных
 */
/*	################################################################	*/


	
	
	// Расписаение клиники на дату
	/*
	function getClinicScheduleByDateXML ($clinicId, $on_date) {
		$xml = "";
		$clinicId = intval($clinicId);
		
		if ( $clinicId > 0) {
			$rasp = new Schedule();
			$rasp -> setClinic($clinicId);
			$scheduleArray = $rasp->getClinicScheduleByDate($on_date);
			if (count($scheduleArray) > 0 ) {
				$xml .= "<ClinicSchedule onDate=\"".$on_date."\">";
				foreach ($scheduleArray as $key => $value) {
					$xml .= "<Element>";
					$xml .= "<StartTime>".$value["startTime"]."</StartTime>";
					$xml .= "<EndTime>".$value["endTime"]."</EndTime>";
					$xml .= "</Element>";
				}
				$xml .= "</ClinicSchedule>";
			}
		}
		return $xml;
		
	}
	*/
	
	


	// Расписаение клиники по дням недели
	function getClinicScheduleXML ($clinicId) {
		$xml = "";
		$clinicId = intval($clinicId);
		
		if ( $clinicId > 0) {
			$sql ="SELECT 
					TIME_FORMAT(start_time, '%H:%i') as startTime, 
					TIME_FORMAT(end_time , '%H:%i') as endTime,
					week_day
				FROM clinic_schedule 
				WHERE 
					clinic_id = ".$clinicId."
				ORDER BY week_day, start_time";
			$result = query($sql);
			if (num_rows($result) > 0 ) {
				$xml .= "<ClinicSchedule>";
				while ($row = fetch_object($result)) {
					$xml .= "<Element weekDay=\"".$row ->week_day."\">";
					$xml .= "<StartTime>".$row ->startTime."</StartTime>";
					$xml .= "<EndTime>".$row ->endTime."</EndTime>";
					$xml .= "</Element>";
				}
				$xml .= "</ClinicSchedule>";
			}
		}
		return $xml;
		
	}
	

	
/*	################################################################	*/
/*
 *  Математика
 */
/*	################################################################	*/
	
	
	
	/**
	 * Переводит ассоциативный массив в интервалы с минутами
	 *  вход ==> array(9,10), array(13,16), array(14,20)
	 *  выход ==> array(9,10), array(13,20)
	 * @return array
	 */
	function transformTimeIntervalToMinuts ($timeIntervals = array()){
		$intervalsOut = array();

		foreach ($timeIntervals as $key => $data) {
			if ( count($data) > 1 ) {
				$startTime = explode(":", $data['startTime']);
				$endTime = explode(":", $data['endTime']);
				//$startTime = explode(":", $data[0]);
				//$endTime = explode(":", $data[1]);
				array_push($intervalsOut,array(intval($startTime[0])*60 + intval($startTime[1]), intval($endTime[0])*60 + intval($endTime[1])));
			}
		}

		return $intervalsOut;
	}
		
	
	
	
	/**
	 * 
	 * Преобразование масива инетрвалов во временную шкалу 
	 * @param array $timeIntervals
	 * @param $type = 'PRESENCE' || 'ABSENCE'
	 */
	function transformTimeIntervalInMinutsToOneLine ($timeIntervals = array(), $type = 'PRESENCE'){
		$intervalOut = array();
		$endPoint = 60*24;
                $lastPoint = 0;

		$i = 0;
		foreach ($timeIntervals as $key => $data) {
			if ( count($data) > 1 ) {
				if ($type == 'PRESENCE') {
					if ( $data[0] != 0 ) {
						$intervalOut[$i]['sign'] = 0;
						$intervalOut[$i]['point'] = 0;
						$i++;
					}
					$intervalOut[$i]['sign'] = 1;
					$intervalOut[$i]['point'] = $data[0];
					$i++;
					$intervalOut[$i]['sign'] = 0;
					$intervalOut[$i]['point'] = $data[1];
					$i++;
				} else {
					if ( $data[0] != 0 ) {
						$intervalOut[$i]['sign'] = 1;
						$intervalOut[$i]['point'] = 0;
						$i++;
					}
					$intervalOut[$i]['sign'] = -1;
					$intervalOut[$i]['point'] = $data[0];
					$i++;
					$intervalOut[$i]['sign'] = 1;
					$intervalOut[$i]['point'] = $data[1];
					$i++;
				}
				$lastPoint = $data[1];
			}
		}

		if ($lastPoint < $endPoint ) {
			if ($type == 'PRESENCE') {
				$intervalOut[$i]['sign'] = 0;
				$intervalOut[$i]['point'] = $endPoint;
			} else {
				$intervalOut[$i]['sign'] = 1;
				$intervalOut[$i]['point'] = $endPoint;
			}
		}
				
		return $intervalOut;
	}	
	
	
	
	/**
	 * 
	 * Преобразование временной шкалы в масив инетрвалов
	 * @param array $timeLine
	 */
	function transformTimeLineIntoTimeInterval ($timeLine = array()) {
		$intervalOut = array();
		$interval = array();
		$key = false;
		
		for ($i=0; $i < count($timeLine); $i++ ) {
			if ($timeLine[$i]['sign'] == 1 ) {
				$interval[0] = $timeLine[$i]['point'];
				$key = true; 
			} else if ( $key ) {
				$interval[1] = $timeLine[$i]['point'];
				$key = false; 
				array_push($intervalOut,$interval); 
			}
		}
		return $intervalOut;
	}
	
	
	
	/**
	 * 
	 * Функция пересечения интервалов 
	 * @param array $intervalOne = array( array("sign" => 0, "point" => 210), array("sign" => 1, "point" => 300), array("sign" => -1, "point" => 600) )
	 * , где элемент массива - ключевая точка;  sign = 1 - присутствие, 0 - нет информации, -1 отсутсвие; point = начало отрезка в минутах
	 * @param array $intervalTwo
	 */
	function crossLineInterval ( $intervalOne, $intervalTwo ) {
		$intervalOut = array();
		$interval = array();
		$currentSignOne = $intervalOne[0]['sign'];
		$currentSignTwo = $intervalTwo[0]['sign'];
		
		$j = 0;
		for ($i = 0; $i < count ($intervalOne); $i++ )
			$intervalOut [$i+$j] = $intervalOne[$i]['point'];
		for ($j = 0; $j < count ($intervalTwo); $j++ ) 
			$intervalOut [$i+$j] = $intervalTwo[$j]['point'];
		sort($intervalOut);
		$intervalOut = array_unique ($intervalOut);
		
		// упорядочить ключи массива
		$i = 0;
		foreach ($intervalOut as $key => $data ) {
			$intervalOutTmp[$i] = $data;
			$i++;
		}
		$intervalOut = $intervalOutTmp; 
			
		
		$i = $j = $k = 0;
	
		while ($k < count($intervalOut) ) {
			$out = 1;
			//echo $k."<br>";  
			if ( $pos = in_my_array($intervalOut[$k],$intervalOne) ) {
				$out = $out*$intervalOne[$pos-1]['sign'];
				$currentSignOne = $intervalOne[$pos-1]['sign'];
				//echo "1=".$intervalOut[$k]."  out=".$out."*".$intervalOne[$pos-1]['sign']." ";
			} else {
				$out = $out*$currentSignOne;
			}
			//echo "1 = point - ".$intervalOut[$k]."  out=".$out." currentSignOne=".$currentSignOne." <br>";
			
			
			if ( $pos = in_my_array($intervalOut[$k],$intervalTwo) ) {
				$out = $out*$intervalTwo[$pos-1]['sign'];
				$currentSignTwo = $intervalTwo[$pos-1]['sign'];
				//echo "2=".$intervalOut[$k]."  out=".$out."*".$intervalTwo[$pos-1]['sign']." ";
			} else {
				$out = $out*$currentSignTwo;
			}
			//echo "2 = point - ".$intervalOut[$k]."  out=".$out." currentSignTwo=".$currentSignTwo." <br>";
			
			$interval[$k]['sign'] = $out;
			$interval[$k]['point'] = $intervalOut[$k];
			//echo $intervalOut[$k]."-".$out."<br>";
			$k++;
			
		}
		return $interval; 
	}
	
	function in_my_array($data, $arrayIn = array()) {
		for ($i = 1; $i <= count ($arrayIn); $i++ ) {
			//if ($data == 480) {echo "i=".$i." ".$arrayIn[$i]['point']."=".$data." <br>";}
			if ( $arrayIn[$i-1]['point'] == $data ) return $i;
		}
		
		return false;
	}
	
	
	
	
	
	
	/**
	 * Переводит массив с интервалами в минутах в массив с интервалами формате чч:мм
	 *  вход ==> array(540,720), array(840,960)
	 *  выход ==> array(09:00,12:00), array(14:00,16:00)
	 * @return array
	 */
	function transformTimeIntervalToHHMM ($timeIntervals = array()){
		$intervalsOut = array();

		foreach ($timeIntervals as $key => $data) {
			if ( count($data) > 1 ) {
				//$startTime = intval($data[0] / 60).":".( ($data[0] / 60) - intval($data[0] / 60)) ;
				$startTime = vsprintf("%d:%02d",array(floor($data[0] / 60),($data[0] % 60)));
				$endTime   = vsprintf("%d:%02d",array(floor($data[1] / 60),($data[1] % 60)));
				array_push($intervalsOut,array($startTime, $endTime) );
			}
		}

		return $intervalsOut;
	}
	
	

	/**
	 * Устраняет пересечение интервалов в рамках массива
	 *  вход ==> array(9,10), array(13,16), array(14,20)
	 *  выход ==> array(9,10), array(13,20)
	 * @return array
	 */
	function splitInterval ($intervals = array()){
                $intervalsOut = array();
                
                if(count($intervals) > 0) {
                    sort($intervals);
                    

                    for ( $i=0; $i < (count($intervals)-1); $i++) {
                            if ( $intervals[$i+1][0] < $intervals[$i][1] ) {
                                    $intervals[$i+1][0] = $intervals[$i][0]; 
                            } else {
                                    array_push($intervalsOut, $intervals[$i] );
                            }
                    }
                    array_push($intervalsOut, $intervals[count($intervals)-1] );
                }
		
		return $intervalsOut;
	}	
	
	
	
	
	/**
	 * Возвращает пересечение массивов отрезков
	 * @return array
	 */
	function crossInterval ( $intFirst = array(), $intSecond = array() ) {
		$intervalOut = array();
		
		foreach ($intFirst as $interval1) {
			$s1 = $interval1[0];
			$e1 = $interval1[1];
			
			foreach ($intSecond as $interval2) {
				$s2 = $interval2[0];
				$e2 = $interval2[1];
				
				// начало 2 в отрезке 1 
				if ( $s2 > $s1  && $s2 < $e1 ) {
					if ( $e2 < $e1 ) {
						array_push($intervalOut, array($s2, $e2) );
					} else {
						array_push($intervalOut, array($s2, $e1) );
					}
				}
				
				// конец 2 в отрезке 1 
				if ( $e2 > $s1  && $e2 < $e1 ) {
					if ( $s2 < $s1 ) {
						array_push($intervalOut, array($s1, $e2) );
					} else {
						array_push($intervalOut, array($s2, $e2) );
					}
				}
				
				// отрезок 1 перекрывает отрезок 2 
				if ( $s2 <= $s1  && $e2 >= $e1 ) {
					array_push($intervalOut, array($s1, $e1) );
				}
				
			}
			
		}
		
		return $intervalOut;
	}

	
	

		
	/**
	 * Нормализация инетвалов с шагом $step
	 */
	function normalizeInterval ($intervals = array(), $step) {
		$intervalsOut = array();
		$step = intval($step);
		
		foreach ( $intervals as $interval ) {
			$start = convertHHMM2Minute($interval[0]);
			$end   = convertHHMM2Minute($interval[1]);
			
			$startN = $start; 
			$endN = $startN + $step;
			
			while ( $endN <= $end )
				if ( $endN <= $end ) {
					array_push($intervalsOut, array(convertMinutes2HHMM($startN), convertMinutes2HHMM($endN)));
					$startN = $endN;
					$endN = $startN + $step;
				}
		}
		
		return $intervalsOut;
	}	

	
	
	
	
	/**
	 * Преобразование даты
	 * $onDate = "01.01.2013" - формат даты dd.mm.YYYY
	 */
	function  getWeekDayNumber4Schedule ($onDate) {
		$dateAr = explode(".",$onDate);
		$tm = mktime(0, 0, 0, $dateAr[1], $dateAr[0], $dateAr[2]);
			
		$weekDay = date("N", $tm);
		return $weekDay; 
	}
	
	
	
	/**
	 * Преобразования формата времени
	 */
	function convertHHMM2Minute($time) {
		$newTime = explode(":", trim($time));
		if ( count($newTime) == 2 ) 
			return  intval($newTime[0])*60 + intval($newTime[1]);
		else 
			return -1; 
	}
	

	/**
	 * Преобразования формата времени
	 */
	function convertMinutes2HHMM($time) {
		return vsprintf("%02d:%02d",array(floor($time / 60),($time % 60))); 
	}
?>
