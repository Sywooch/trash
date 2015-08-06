<?php
	require_once dirname(__FILE__)."/../mp3/class.mp3file.php";
	
	/**
	 *  Получение длительности mp3 файла  
	 */
	function getDuration ($filename) {
		$duration = 0;
		
		if (file_exists($filename)) {
			$m = new mp3file($filename);
			$mp3 = $m->get_metadata();

			if ( !empty($mp3['Length']) )
	    		$duration = $mp3['Length'];
		}
		
		return $duration; 
	}
?>