<?php
$documentRoot = dirname(__FILE__).'/../../';
require_once $documentRoot.'include/common.php';
require_once $documentRoot.'include/croneList.php';
require_once $documentRoot.'lib/php/croneLocker.php';


$croneName = checkField($_POST['croneName'], 't');

$isAvailableGlobalToggle = new IsAvailableGlobalToggle;
$isAvailableGlobalToggle->availableGlobalToggle($croneName);


class IsAvailableGlobalToggle{
	
	private $result = array('status'=>'success', 'msg'=>array());

	public function availableGlobalToggle($croneName){
		if(!empty($croneName)){
			$crone = croneList::getConfig($croneName);
			if(!empty($crone)){
				$cronFileStatusPath = LOCK_FILE_CRONE_DIR.$crone['file'];
				$currentParam = getCroneStatusParam('isAvailableGlobal', $cronFileStatusPath);
				$isAvailableGlobal = ($currentParam != 'false')?'false':'true';

				saveCronStatusParam(array('isAvailableGlobal' => $isAvailableGlobal), $cronFileStatusPath, $croneName);

				$this->result['status'] = 'success';
				$this->result['msg'][] = 'Крон "'.$croneName.'". Глобальная доступность: '.$isAvailableGlobal.'.';
				$this->result['content'] = $isAvailableGlobal;
			}else{
				$this->result['status'] = 'fail';
				$this->result['msg'][] = 'Имя крона не зарегистрирован в системе';
			}
		}
		echo json_encode($this->result);
	}
}