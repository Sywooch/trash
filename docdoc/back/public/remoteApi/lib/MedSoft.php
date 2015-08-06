<?php

require_once '/RemoteAPI.php';

/**
 * Description of MedSoft
 *
 * @author Danis
 */
class MedSoft extends RemoteAPI 
{
    
    public $id = 2;
    public $name = 'MedSoft';
    public $login = 'docdoc';
    public $password = 'api5';
    public $url = 'http://212.16.24.102:5005/api/v1';
    
    public function syncDoctors(){
        $data = $this->getDoctors();
        if(count($data) > 0)
            $this->sync($data, 'doctor');
        else
            return false;
    }
    
    public function syncSpecialities(){
        $data = $this->getSpecialities();
        if(count($data) > 0)
            $this->sync($data, 'sector');
        else
            return false;
    } 
    
    public function syncClinics(){
        $data = $this->getClinics();
        if(count($data) > 0)
            $this->sync($data, 'clinic');
        else
            return false;
    }
    
    public function getDoctors(){
        $data = array();
        $items = $this->send('ref/doc');
        //var_dump($items);die;
        for($i=0; $i<count($items); $i++){
            $data[$i]['id'] = (int)$items[$i]->id;
            $data[$i]['name'] = checkField($items[$i]->surname, 'h', '').' '.  checkField($items[$i]->name, 'h', '');
        }
        
        return $data;
    }
    
    public function getSpecialities(){
        $data = array();
        $items = $this->send('ref/spec');
        for($i=0; $i<count($items); $i++){
            $data[$i]['id'] = (int)$items[$i]->id;
            $data[$i]['name'] = checkField($items[$i]->name, 'h', '');
        }
        
        return $data;
    }
    
    public function getClinics(){
        $data = array();
        $items = $this->send('ref/lpu');
        for($i=0; $i<count($items); $i++){
            $data[$i]['id'] = (int)$items[$i]->id;
            $data[$i]['name'] = checkField($items[$i]->name, 'h', '');
        }
        return $data;
    }
    
    /*
    public function getClinicIds(){
        $data = array();
        $items = $this->get('ref/lpu');
        if(!empty($items)){
            foreach($items as $item)
                $data[] = $item->id;
        }

        return $data;
    }
     * 
     */
    
    public function getSpecsByClinic($id){
        $data = array();
        if(!empty($id))
            $data = $this->send('dsh/lpu/'.$id.'/spec');

        return $data;
    }
    
    public function getSchedule($date = null, $clinic = null, $spec = array()){
        $data = array();
        
        if(!empty($date))
            $date = date('d.m.y', $date);
        else
            $date = date('d.m.y', time());
        
        $doctorIds = $this->getDoctorIds();
        $clinicIds = $this->getClinicIds();
        foreach($clinicIds as $clinicId){
            if(empty($clinic) || (!empty($clinic) && $clinic == $clinicId)){
                $items = $this->getSpecsByClinic($clinicId);
                $i = 0;
                foreach($items as $item){
                    if(count($spec) == 0 || in_array($item, $spec)){
                        $result = $this->send('dsh/lpu/'.$clinicId.'/spec/'.$item.'/dates?base='.$date);
                        foreach($result as $resultItem){
                            if(isset($doctorIds[$resultItem->doc])) {
                                $data[$doctorIds[$resultItem->doc]]['doctorId'] = $doctorIds[$resultItem->doc];
                                $data[$doctorIds[$resultItem->doc]]['clinicId'] = $clinicId;
                                $data[$doctorIds[$resultItem->doc]]['date'] = $resultItem->date;
                                $data[$doctorIds[$resultItem->doc]]['times'][$i][] = $resultItem->tmstart;
                                $data[$doctorIds[$resultItem->doc]]['times'][$i][] = $resultItem->tmend;
                                $data[$doctorIds[$resultItem->doc]]['key'] = $resultItem->key;
                                $i++;
                            }
                        }
                    }
                }   
            }
        }   
        
        return $data;
    }

    public function record($requestId){

        $request = $this->getRequestParams($requestId);

        if(count($request) > 0){

            $tm = date('h:i', $request['date']);
            $times = $this->getFreeTimes($request['date'], $request['doctorId']);

            if(count($times) > 0){
                $time = array();

                foreach($times as $item){
                    if($item['time'] == $tm){
                        $time['key'] = $item['key'];
                        $time['id'] = $item['id'];
                    }
                }

                if(count($time) == 2) {
                    $data['comm'] = '';
                    $data['ref'] = $request['id'];
                    $data['patient'] = array(
                        'surname' => $request['lName'],
                        'name' => $request['fName'],
                        'patronomic' => $request['mName'],
                        'sex' => 'M',
                        'born' => '',
                        'phone' => $request['phone'],
                        'refpid' => $request['clientId']
                    );

                    $data = json_encode($data);
                    //var_dump($data);var_dump('dsh/records/'.$time['key'].'/tm/'.$time['id']);die;
                    $response = $this->send('dsh/records/'.$time['key'].'/tm/'.$time['id'], 'PUT', $data);

                    if($response->result == 'OK')
                        return true;

                }
            }
    
        }

        return false;
    }

    public function deleteRecord($requestId) {
        $response = $this->send('dsh/records/ref/'.$requestId, 'DELETE', $data);

        if($response->result == 'OK')
            return true;
    }
    
    public function getFreeTimes($date, $doctorId){
        $timeArr = array();
        
        $items = $this->getSheduleKeys($date, $doctorId);

        $i = 0;
        foreach($items as $item){
            $result = $this->get('dsh/records/'.$item);
            foreach($result->times as $time){
                $timeArr[$i]['key'] = $item;
                $timeArr[$i]['id'] = $time->id;
                $timeArr[$i]['time'] = $time->tm;
                $i++;
            }
        }
        
        return $timeArr;
    }

    public function getScheduleKeys($date, $doctorId, $spec = array()){
        $items = $this->getSchedule($date, null, $spec);
        $keys = array();
        foreach($items as $item){
            if($item['doctorId'] == $doctorId){
                $keys[] = $item['key'];
            }
        }
        
        return $keys;
    }
 
    protected function send($method, $action = "GET", $data = array()){
        $ch = curl_init();

        if($action == 'GET') {
            curl_setopt_array($ch, array(
                    CURLOPT_URL => $this->url.'/'.$method,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_HTTPHEADER => array('Content-type: application/json'),
                    CURLOPT_POST => false,
                    CURLOPT_USERPWD => $this->login.':'.$this->password
            ));
        } else {
            curl_setopt_array($ch, array(
                CURLOPT_URL => $this->url.'/'.$method,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => array('Content-type: application/json', 'X-HTTP-Method-Override: '.$action),
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_USERPWD => $this->login.':'.$this->password
            ));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }
    
}

?>
