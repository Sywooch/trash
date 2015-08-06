<?php	   	  
require_once dirname(__FILE__)."/../../lib/php/user.class.php";
require_once dirname(__FILE__)."/../../lib/php/smsQuery.class.php";
require_once dirname(__FILE__)."/../../include/common.php";
require_once dirname(__FILE__)."/../../lib/php/croneLocker.php";
require_once dirname(__FILE__)."/../../include/croneList.php";


$user = new user();	 
$user -> checkRight4page(array('ADM'),'simple');

$croneName	= (isset($_POST["croneName"])) ? checkField ($_POST["croneName"], "t", "") : "";

$isAvailableToggle = new IsAvailableToggle;
$isAvailableToggle->availableToggle($croneName);

class IsAvailableToggle{
	
	private $result = array('status'=>'false', 'msg'=>array());

	public function availableToggle($croneName){
		if(!empty($croneName)){
			$crone = croneList::getConfig($croneName);
			if(!empty($crone)){
				$cronFileStatusPath = LOCK_FILE_CRONE_DIR.$crone['file'];

				$isAvailable = getCroneStatusParam('isAvailable', $cronFileStatusPath);
				$isAvailable = ($isAvailable != 'false')?'false':'true';
				$paramsNew['isAvailable'] = $isAvailable;
				saveCronStatusParam($paramsNew, $cronFileStatusPath, $croneName);

				$this->result['status'] = 'success';
				$this->result['msg'][] = 'Файл блокировки изменён';
				$this->result['content'] = $isAvailable;
			}else{
				$this->result['status'] = 'fail';
				$this->result['msg'][] = 'Имя крона не зарегистрирован в системе';
			}
		}
		echo json_encode($this->result);
	}
}
