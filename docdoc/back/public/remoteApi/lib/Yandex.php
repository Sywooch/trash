<?php

require_once 'RemoteAPI.php';

/**
 * Description of Yandex
 *
 * @author Danis
 */
class Yandex extends RemoteAPI{
    
    public $id = 1;
    
    public $url = 'http://api.booking.yandex.ru/api';
    public $login = "docdocltd";
    public $password = "esa5XKGZc6bFWCUsjG9V7VjKEnsQGrVVfbXgV49SOso";

    protected $_status = array(
        0 => 'ACCEPTED',
        1 => 'USER_NOTIFIED',
        2 => 'APPROVED',
        3 => 'COME',
//        4 => 'REJECTED',
        5 => 'CANCELLED_BY_USER',
        6 => 'ACCEPTED',
        7 => 'USER_NOTIFIED',
        8 => 'CANCELLED_BY_ORGANIZATION',
        9 => 'COME'
    );
    
    /*
     * Обновить статус бронирования
     * @param array $params
     * @return boolean
     */
    public function updateBookStatus($params) {
        if(isset($params['id']) && isset($this->_status[$params['status']])){
            $data['method'] = 'updateBookStatus';
            $data['params'] = array($params['id'], $this->_status[$params['status']]);
            //var_dump($data['params']);
            return $this->send($data);
        }
        
        return false;
    }
    
    /*
     * Сообщить занятые слоты
     * @param array $params
     * @return boolean
     */
    public function reportSlots($params) {
        if(isset($params['doctorId']) && isset($params['startTime']) && isset($params['step'])){
            $data['method'] = 'reportSlots';
            $slotId = $params['doctorId'].'-'.$params['startTime'];
            $endTime = $params['startTime'] + 60 * $params['step'];
            $startTime = date('Y-m-d',$params['startTime']).'T'.date('Y-m-d',$params['startTime']);
            $endTime = date('Y-m-d',$endTime).'T'.date('Y-m-d',$endTime);
            $data['params'] = array(array(
                array(
                    "resourceId" => $params['doctorId'], 
                    "slotId"     => $slotId,
                    "attributes" => array(
                        "from"  => $startTime,
                        "to"    => $endTime,
                    )
                )
            ));
            //echo $data;
            return $this->send($data);
        }
        
        return false;
    }
    
    /*
     * Отправка запроса
     * @param array $data
     * @return array
     */
    protected function send($data){
        $dataArray = array(
            "jsonrpc" => "2.0",
            "method" => $data['method'],
            "params" => $data['params'],
            "id" => 1
        );
        $data = json_encode($dataArray);
        echo $data;//die;
        $ch = curl_init();
        curl_setopt_array($ch, array(
                CURLOPT_URL => $this->url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_HTTPHEADER => array('Content-type: application/json'),
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_USERPWD => $this->login.':'.$this->password
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        
        $response = json_decode($response);
        
        $msg = '[Partner #'.$this->id.'] Request: '.$data.'. ';
        if(isset($response->result) && $response->result == 1){
            $msg .= 'Response: OK.';
            $result = true;
        } elseif(isset($response->error)){
            $msg .= 'Response: '.$response->error->message.'.';
            $result = false;
        } else {
            $msg .= 'Response: Service is not available.';
            $result = false;
        }
        
        $log = new commonLog('yandex_api.log', $msg);

        return $result;
    }
}

?>
