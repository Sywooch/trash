<?php

use dfs\docdoc\models\DoctorClinicModel;

/**
 * Description of RemoteAPI
 *
 * @author Danis
 */
class RemoteAPI {
    
    public $id = 1;
    public $name = '';
    public $login = '';
    public $password = '';
    public $url = '';
	
    public function __construct($id = null) {
        if(!empty($id))
            $this->loadAPI($id);

    }
    
    protected function sync($data, $modelName = 'doctor') {
        $modelId = $modelName.'_id';
        
        $ids = array();
        foreach($data as $item)
            $ids[] = "'".$item['id']."'";
        $ids = implode(",", $ids);
        
        query("START TRANSACTION");
        
        // Delete old models
        if(!empty($ids)){
            $sql = "DELETE FROM ".$modelName."_4_remote_api 
                    WHERE ".$modelName."_api_id NOT IN (".$ids.")
                        AND api_id=".$this->id;
            $result = query($sql); 
        }
        
        echo "\r\n";
        echo "-- Replace ".$modelName."s for partner #".$this->id."-".$this->name."\r\n";
        echo "Ids: ";
        
        foreach($data as $item) {
            $sql = "SELECT id
                    FROM $modelName
                    WHERE LOWER(name) LIKE '%".mb_strtolower(trim($item['name']))."%'";
            //echo $sql;
            $result = query($sql);
            if(num_rows($result) == 1) {
                $model = fetch_object($result);
                $sql = "REPLACE ".$modelName."_4_remote_api
                        VALUES (".$model->id.", '".$item['id']."', ".$this->id.")";
                query($sql);
                echo $item['id'].' ';
            }
        }
        
        query("COMMIT");
        
        return true;
        
    }
    

    protected function sync1($data, $modelName = 'doctor') {
        $msg = '';
        
        $modelId = $modelName.'_id';
        
        foreach($data as $item){
            $ids[] = "'".$item['id']."'";
            $names[] = "'".trim($item['name'])."'";
            $idArr[$item['name']] = $item['id'];
        }
        $ids = implode(",", $ids);
        $names = mb_strtolower(implode(",", $names));

        $result = query("START TRANSACTION");
        
        // Delete old models
        if(!empty($ids)){
            $sql = "SELECT ".$modelId." 
                    FROM ".$modelName."_4_remote_api 
                    WHERE ".$modelName."_api_id NOT IN (".$ids.")
                        AND api_id=".$this->id;
            $result = query($sql);
            $amount = num_rows($result);
            if($amount > 0) {
                $ids = array();
                while($row = fetch_object($result))
                    $ids[] = $row->$modelId;
                $ids = implode(',', $ids);
                $sql = "DELETE FROM ".$modelName."_4_remote_api 
                        WHERE api_id=".$this->id." AND ".$modelId." IN (".$ids.")";
                $result = query($sql);
                $msg .= '-- Deleted '.$amount.' '.$modelName.'(s) - Ids: '.$ids;
            }  
        }

        // Insert new models
        if(!empty($names)){
            $sql = "SELECT t1.id, t1.name 
                    FROM ".$modelName." t1,  ".$modelName."_4_remote_api t2 
                    WHERE
                    	t2.".$modelId." = t1.id
                    	AND 
                    	lower(t1.name) IN (".$names.")
                        AND 
                        t2.".$modelId." IS NULL";
            echo $sql;
            $result = query($sql);
            $amount = num_rows($result);
            if($amount > 0) {
                $ids = array();
                while($row = fetch_object($result)) {
                    $sqlArr[] = "(".$row->id.",'".$idArr[$row->name]."',".$this->id.")";
                    $ids[] = $row->id;
                }
                $sqlAdd = implode(',', $sqlArr);
                $ids = implode(',', $ids);

                $sql = "INSERT INTO ".$modelName."_4_remote_api VALUES ".$sqlAdd;
                $result = query($sql);
                $msg .= '-- Inserted '.$amount.' new '.$modelName.'(s) - Ids: '.$ids;
            }
        }
        
        if($msg == '')
            $msg = '-- No actions for '.$modelName;
      
        $result = query("COMMIT");
        
        echo $msg;
        $this->addLog($msg, $modelName);
        
        return true;
    }

    public function getClinicIds() {
        $data = array();
        
        $sql = "SELECT clinic_id, clinic_api_id AS id 
                FROM clinic_4_remote_api 
                WHERE api_id=".$this->id;
        
        $result = query($sql);
        while($row = fetch_object($result))
            $data[$row->id] = $row->clinic_id;
        
        return $data;       
    }
    
    public function getDoctorIds($clinicId = null) {
        $data = array();
        
        if(!empty($clinicId))
            $sql = "SELECT t1.doctor_id, t1.doctor_api_id AS id 
                    FROM doctor_4_remote_api t1
                    INNER JOIN doctor_4_clinic t2 ON t2.doctor_id=t1.doctor_id and t2.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                    WHERE t2.clinic_id=".$clinicId." AND t1.api_id=".$this->id;
        else
            $sql = "SELECT doctor_id, doctor_api_id AS id 
                    FROM doctor_4_remote_api 
                    WHERE api_id=".$this->id;
        //echo $sql;
        $result = query($sql);
        while($row = fetch_object($result))
            $data[$row->id] = $row->doctor_id;
        
        return $data;       
    }

    public function getRequestParams($id) {
        $data = array();

        $sql = "SELECT
                    t1.req_id AS id, t1.clientId, t1.req_doctor_id AS doctorId, t1.clinic_id AS clinicId,
                    t2.first_name AS fName, t2.last_name AS lName, t2.middle_name AS mName,
                    t1.client_phone AS phone, t1.date_admission AS date,
                    t3.doctor_api_id AS doctorApiId, t4.clinic_api_id AS clinicApiId
                FROM request t1
                LEFT JOIN client t2 ON t2.clientId=t1.clientId
                LEFT JOIN doctor_4_remote_api t3 ON t3.doctor_id=t1.req_doctor_id
                LEFT JOIN clinic_4_remote_api t4 ON t4.clinic_id=t1.clinic_id
                WHERE t1.req_id=".$id;
        $result = query($sql);
        if(num_rows($result) == 1)
            $data = fetch_array($result);

        return $data;
    }
    
    protected function loadAPI($id) {
        $sql = "SELECT * FROM remote_api WHERE api_id=".$id;
        $result = query($sql);
        
        if(num_rows($result) == 1) {
            $api = fetch_object($result);
            $this->id = $api->api_id;
            $this->name = $api->name;
            $this->login = $api->login;
            $this->password = $api->password;
            $this->url = $api->url;
        }
    }
    
    protected function addLog($mess, $model = 'doctor') {
        $file_path = $_SERVER['DOCUMENT_ROOT'].'/log/sync_'.$model.'.log';
        $mess = str_replace("\r\n",'',$mess);
        $mess = str_replace("\n",'',$mess); 
        $mess = str_replace("\t",' ',$mess); 
        $text =  date("d.m.Y H:i:s ")." ".$mess."\r\n";
        $handle = fopen($file_path, "a");
        @flock ($handle, LOCK_EX);
        fwrite ($handle, $text);
        @flock ($handle, LOCK_UN);
        fclose($handle);
        return true;
    }
    
    /*
     * Установить ошибку
     * @param string $errorCode
     * @return boolean
     */
    protected function setError ($error) {
        $this->addLog('Error: '.$error);
 
        return false;
        
    }
    
}

?>
