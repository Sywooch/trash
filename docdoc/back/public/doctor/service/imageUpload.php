<?php
	require_once dirname(__FILE__)."/../../include/common.php";
	require_once dirname(__FILE__)."/../../lib/php/imgLib.php";
	require_once dirname(__FILE__)."/../../lib/php/validate.php";
	

	$doctorId	= (isset($_REQUEST['id'])) ? checkField($_REQUEST['id'], "i", 0) : '0';

/*	Settings	*/
$allowedExtensions = array("jpg","gif", "png", "tif");	
$sizeLimit = 5 * 1024 * 1024;				// max file size in bytes
define ("widthPrv",550);	 // Ширина превью
define ("heightPrv",748);	 // Высота превью
define ("widthMin",160);	 // Ширина превью
define ("heightMin",218);	 // Высота превью



define ("SRCpath","src/");	 // Путь для исходника


class qqUploadedFileXhr {
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

		chmod($path, FILE_MODE);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}


class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}


class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;
	private $doctorId;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760, $doctorId = 0){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
        
        $this->doctorId = $doctorId;
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
	
	
	function resizeFile ( $filename, $ext ) {
		if ( !img_resize_2(Path4Upload.SRCpath.$filename.".".$ext, Path4Upload."doctor/".$this->doctorId.".jpg", widthPrv, heightPrv, widthMin, heightMin, 90) ) {
			return false;
		}
		return true;
	}
   
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    
    
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
    	if (!is_writable($uploadDirectory)){
            return array('error' => "Ошибка на сервере. Директория недоступна.");
        }
        
        if (!$this->file){
            return array('error' => 'Файл не передан');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'Файл пустой');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'Файл превышает допустимый размер');
        }
        
		/*	Имя файла - timestamp	*/
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        $ext = $pathinfo['extension'];
		//$filename = date("YmdHis")."_".$this->doctorId;
		$filename = $this->doctorId;

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'Файл другого типа. Разрешено выкладывать только '. $these . '.');
        }
        
        if(!$replaceOldFile){
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
 			
		
        if ($this->file->save($uploadDirectory.SRCpath.$filename.'.'.$ext)){

        	if ( !$this->resizeFile ( $filename, $ext) ) {
				return array('error'=> 'Не удалось сжать файл: '.Path4Upload.SRCpath.$filename.".jpg");
			}
		
			return array('success'=>true, 'fileNewNаme'=>Path4Upload.SRCpath.$filename.".".$ext);
        } else {
            return array('error'=> 'Не получилось сохранить файл. Загрузка прервана');
        }
        
    }    
}


	$uploader = new qqFileUploader($allowedExtensions, $sizeLimit, $doctorId);
	$result = $uploader->handleUpload(Path4Upload);
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
?>