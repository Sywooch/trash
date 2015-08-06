<?php

require_once '/RemoteAPI.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OnClinic
 *
 * @author Danis
 */
class OnClinic extends RemoteAPI
{
    
    public $id = 3;
    public $name = 'OnClinic';
    public $login = 'docdoc';
    public $password = '1f4f5v';
    public $url = '81.200.15.2';
    public $dbname = 'docdoc';
    public $log = 'onclinic_api.log';
 
/*    
    public function syncSpecialities(){
        $data = array();
        $items = $this->getSpecialities();
        
        foreach($items as $item){
            $tmp = array('id' => $item['n_spec'], 'name' => $item['name_spec']);
            array_push($data, $tmp);
        }
        if(count($data) > 0)
            $this->sync($data, 'sector');
        else
            return false;
    }
 * 
 */

    public function syncDoctors() {
        $data = array();
        $items = $this->getDoctors();
        
        $prevId = 0;
        foreach($items as $item){
            $tmp = array('id' => $item['shifr'], 'name' => $item['name_wr']);
            if($prevId != $item['shifr'])
                array_push($data, $tmp);
                
            $prevId = $tmp['id'];
        }

        if(count($data) > 0)
            $this->sync($data, 'doctor');
        else
            return false;
    }
    
    public function getClinics() {
        return $this->get('{ call docdoc_spr_fil()}');
    }
    
    public function getSpecialities() {
        return $this->get('{ call docdoc_spr_spec()}');
    }
    
    public function getDoctors($params = array()) {
        if(count($params) > 0) {
            $clinic = isset($params['clinic']) ? (int)$params['clinic'] : null;
            $spec = isset($params['spec']) ? (int)$params['spec'] : null;
            
            if(!empty($clinic) && !empty($spec))
                return $this->get("{ call docdoc_spec_wrn($clinic, $spec)}");
            
            return false;
            
        } else
            return $this->get('{ call docdoc_spr_wrn()}');
    }
    
    public function getDoctorsXML() {
        $data = $this->get('{ call docdoc_wrn_xml()}');
        
        $xml = "<root>";
        foreach($data as $item)
            $xml .= $item[0]; 	
        
        $xml .= "</root>";
        
        return $xml;
                
    }
    
    public function getSchedule($date = null) {
        $data = array();
        
        if(!empty($date))
            $date = date('d.m.Y', $date);
        else
            $date = date('d.m.Y');

        $clinicIds = $this->getClinicIds();
        foreach($clinicIds as $clinicKey => $clinicId){

                $doctorIds = $this->getDoctorIds($clinicId);
                $i = 0;
                foreach($doctorIds as $doctorKey => $doctorId){
                    $result = $this->get("{ call docdoc_wrn_availability('$date', $doctorKey, $clinicKey)}");

                    foreach($result as $resultItem){
                        $data[$doctorId]['doctorId'] = $doctorId;
                        $data[$doctorId]['clinicId'] = $clinicId;
                        $data[$doctorId]['date'] = $date;
                        if($resultItem['MINUTE_p'] == 0) $resultItem['MINUTE_p'] = '00';
                        $data[$doctorId]['times'][$i][] = $resultItem['HOUR_p'].':'.$resultItem['MINUTE_p'];
                        $timeEnd = $resultItem['HOUR_p'] * 60 + $resultItem['MINUTE_p'] + $resultItem['INTL'];
                        $hourEnd = floor($timeEnd / 60);
                        $minEnd = $timeEnd % 60;
                        if($minEnd == 0) $minEnd = '00';
                        $data[$doctorId]['times'][$i][] = $hourEnd.':'.$minEnd;
                        //$data[$doctorIds[$resultItem->doc]]['key'] = $resultItem->key;
                        $i++;
                    }

                }
                       
        }

        return $data;
    }

    public function getCell($doctorId, $clinicId, $time) {

        $cell = false;

        $date = date('d.m.Y', $time);
        $result = $this->get("{ call docdoc_wrn_availability('$date', $doctorId, $clinicId)}");
        if($result) {
            foreach($result as $resultItem) {
                //var_dump($resultItem);
                if($resultItem['HOUR_p'] == date('H',$time) && $resultItem['MINUTE_p'] == date('i',$time) && $resultItem['isBusy'] == 0)
                    $cell = $resultItem['Cod_sh'];
            }
        }

        return $cell;
    }
    
    public function reserve($cell) {

        if($cell) {
            $result = $this->set("{ call docdoc_cell_reservation($cell)}");

            if($result === false)
                return true;
        }
        
        return false;
    }
    
    public function confirm($cell, $patient) {
        $cell = isset($cell) ? (int)$cell : 0;
        $patient = isset($patient) ? (string)$patient : '';
        
        if(!$cell) {
            $result = $this->set("{ call docdoc_cell_commit($cell, '$patient')}");
            if($result == 0)
                return true;
        }
        
        return false;
    }
    
    public function createPatient($data = array()) {
        if(!empty($data)) {
            $name = isset($data['name']) ? trim($data['name']) : null;
            $phone = isset($data['phone']) ? trim($data['phone']) : null;
            $sex = isset($data['sex']) ? (int)$data['sex'] : 1;
            $birthday = isset($data['birthday']) ? trim($data['birthday']) : null;
            
            if(!empty($name) && !empty($phone) && !empty($birthday)) {

                $dbConn = sqlsrv_connect($this->url, array( 'Database'=> $this->dbname, "UID"=> $this->login, "PWD"=> $this->password, "CharacterSet"=>"UTF-8", "LoginTimeout"=>20));
                if($dbConn) {
                    $outparam = str_repeat("\0", 200);

                    $params = array(
                        array('test82', SQLSRV_PARAM_IN),
                        array('89261234567', SQLSRV_PARAM_IN),
                        array(1, SQLSRV_PARAM_IN),
                        array($birthday, SQLSRV_PARAM_IN),
                        array(&$outparam, SQLSRV_PARAM_OUT)
                    );
                    $queryString = "{call docdoc_nokont_insert(?,?,?,?,?)}";
                    $result = sqlsrv_query($dbConn, $queryString, $params);
                    $result = sqlsrv_query($dbConn, $queryString, $params);
                    if ($result === false) {
                        echo "Error in executing statement";
                        die(print_r(sqlsrv_errors(), true));
                    }
                    //$result = sqlsrv_next_result($result);

                    return $outparam;

                }
            }
        }
        
        return false;
    }
    
    public function book($requestId) {

        $request = $this->getRequestParams($requestId);

        if(!empty($request)) {
            $patient = array(
                'name' =>  $request['lName'].' '.$request['fName'].' '.$request['mName'],
                'phone' =>  $request['phone'],
                'sex' => 1,
                'birthday' => '01.01.1980',
            );
            $patientId = $this->createPatient($patient);

            if($patientId) {
                $cell = $this->getCell($request['doctorApiId'], $request['clinicApiId'], $request['date']);
                //var_dump($cell);die;
                if($cell) {
                    $reserve = $this->reserve($cell);
                    var_dump($reserve);
                    if($reserve) {
                        var_dump($reserve);
                        var_dump($this->confirm($cell, $patientId));
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    public function cancelBook($cell) {
        $cell = isset($cell) ? (int)$cell : 0;
        
        if(!$cell) {
            $result = $this->set('{ call docdoc_cell_clear($cell)}');
            return true;
        }
        
        return false;
    }

    protected function get($queryString) {
        $params = array(
            'server' => $this->url,
            'dbname' => $this->dbname,
            'login' => $this->login,
            'password' => $this->password,
        );
        
        //$dbConn = clientODBC::connect($params);
        $dbConn = sqlsrv_connect($this->url, array( 'Database'=> $this->dbname, "UID"=> $this->login, "PWD"=> $this->password, "CharacterSet"=>"UTF-8", "LoginTimeout"=>20));
        if($dbConn) {
            $result = sqlsrv_query($dbConn, $queryString);

            if(!$result)
                return false;

            $data = array();
            while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
                array_push($data, $row);

            return $data;
            
        } else
            return false;
    }
    
    protected function set($queryString) {

        //$dbConn = clientODBC::connect($params);
        $dbConn = sqlsrv_connect($this->url, array( 'Database'=> $this->dbname, "UID"=> $this->login, "PWD"=> $this->password, "CharacterSet"=>"UTF-8", "LoginTimeout"=>20));
        if($dbConn) {

            $result = sqlsrv_query($dbConn, $queryString);

            if( $result === false ) {
                die( print_r( sqlsrv_errors(), true));
            }

            return $result;
            
        } else
            return false;
    }
    
}

?>
