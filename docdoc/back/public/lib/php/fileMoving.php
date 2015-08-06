<?php
	// Перенос файла на FTP сервер
	

	function moveFileToFTP ($file4Moving, $params = array()) {
		
		if ( !empty($file4Moving) && file_exists($file4Moving) ) {
			if ( isset($params['ftpMove']) && isset($params['ftpConnId']) && isset($params['ftpPath']) ) {
				if (ftp_chdir($params['ftpConnId'], $params['ftpPath'])) {
					$fp = fopen($file4Moving, 'r');
					ftp_fput($params['ftpConnId'], $file4Moving, $fp, FTP_ASCII);
					fclose($fp);
				}
			}
				
			if ( isset($params['withDelete']) )
				unlink($file4Moving);
		}
	}	
?>