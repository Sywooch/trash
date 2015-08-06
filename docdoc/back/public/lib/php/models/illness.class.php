<?php
class Illness {
    
    public $id = null;
    public $data;

    
    
    
    public function __construct( $id = null ) {
    	$id = intval($id); 
        if ( $id > 0 ) 
            $this -> getModel($id);
    }
    
    
    
	public function setId ($id) {
		$id = intval($id);
		 
        if ( $id > 0 ) 
            $this -> id = $id;
    }
    
    
    
    
    
    /**
     * Получение модели 
     * @return array
     */
    public function getModel($id) {
    	$data = array();
    	$id = intval($id);
    	
        $sql = "SELECT 
                    t1.id AS Id, 
                    t1.name AS Name,
                    t1.full_name as FullName,
                    t1.text_desc AS Description,
                    t1.text_symptom AS Symptom,
                    t1.text_treatment As Treatment, 
					t1.text_other as Body,
					t1.rewrite_name as Alias,
					t1.title as MetaTitle,
					t1.meta_keywords As MetaKeyWd,
					t1.meta_desc As MetaDescr,

					t2.name as SectorName,
					t1.is_hidden as IsDisabled,
					t1.sector_id as SectorId
                FROM illness t1
                LEFT JOIN sector t2 ON (t1.sector_id = t2.id)
                WHERE 
                	t1.id = ".$id;
        
        $result = query($sql);
        if ( num_rows($result) == 1 ){
            $row = fetch_array($result);
            array_push($data, $row);

            $this->data = $data[0];
            $this->id = $data[0]['Id'];
        } 
                  
        return $data;
    }
    

    
    
	/**
     *
     * Изменение статьи
     */
    public function modifyData($params = array() ) {

    	if ( $this->id > 0 && count($params) > 0 ) {
    		$sqlAdd = "";
    		
    	if ( !empty($params['title'])  ) { 
    			$sqlAdd .= " name = '".$params['title']."', ";
    		} 
			
    		if ( empty($params['sectorId'])  ) {
				$sqlAdd .= " sector_id = 0, ";
			} else {
				$sqlAdd .= " sector_id = '".$params['sectorId']."', ";
			}
			
			if ( !empty($params['desc'])  ) {
				$sqlAdd .= " text_desc = '".$params['desc']."', ";
			} else {
				$sqlAdd .= " text_desc = '-', ";
			}
    		if ( !empty($params['symptom'])  ) {
				$sqlAdd .= " text_symptom = '".$params['symptom']."', ";
			} else {
				$sqlAdd .= " text_symptom = '-', ";
			}
    		if ( !empty($params['treatment'])  ) {
				$sqlAdd .= " text_treatment = '".$params['treatment']."', ";
			} else {
				$sqlAdd .= " text_treatment = '-', ";
			}
			
			
    		if ( !empty($params['textArticle'])  ) {
				$sqlAdd .= " text_other = '".$params['textArticle']."', ";
			}
			
    		if ( !empty($params['alias'])  ) {
				$sqlAdd .= " rewrite_name = '".$params['alias']."', ";
			}
			if ( !empty($params['fullName'])  ) {
				$sqlAdd .= " full_name = '".$params['fullName']."', ";
			}  

			
    		if ( !empty($params['metaTitle'])  ) {
				$sqlAdd .= " title = '".$params['metaTitle']."', ";
			} else {
				$sqlAdd .= " title = NULL, ";
			}
    		if ( !empty($params['metaKeyWd'])  ) {
				$sqlAdd .= " meta_keywords = '".$params['metaKeyWd']."', ";
			} else {
				$sqlAdd .= " meta_keywords = NULL, ";
			}
    		if ( !empty($params['metaDescr'])  ) {
				$sqlAdd .= " meta_desc = '".$params['metaDescr']."', ";
			} else {
				$sqlAdd .= " meta_desc = NULL, ";
			}
			
    		if ( $params['status'] == 1  ) {
				$sqlAdd .= " is_hidden = 1, ";
			} else {
				$sqlAdd .= " is_hidden = 0, ";
			}
	
			$sqlAdd = rtrim($sqlAdd, ', '); 
			
			$sql = "UPDATE `illness` SET
						".$sqlAdd."
					WHERE id = ".$this->id;
			//echo $sql;
			
			$result = query($sql);
			if (!$result) return false;
			
    		return true;
    	}
    	 
    	return false;
    }
    
    
    
    
	/**
     *
     * Создание статьи
     */
    public function create ($params = array() ) {

    	if ( count($params) > 0 ) {
    		$sqlAdd = "";
    		
    		if ( !empty($params['title'])  ) { 
    			$sqlAdd .= " name = '".$params['title']."', ";
    		} 
			
    		if ( empty($params['sectorId'])  ) {
				$sqlAdd .= " sector_id = 0, ";
			} else {
				$sqlAdd .= " sector_id = '".$params['sectorId']."', ";
			}
			
			if ( !empty($params['desc'])  ) {
				$sqlAdd .= " text_desc = '".$params['desc']."', ";
			} else {
				$sqlAdd .= " text_desc = '-', ";
			}
    		if ( !empty($params['symptom'])  ) {
				$sqlAdd .= " text_symptom = '".$params['symptom']."', ";
			} else {
				$sqlAdd .= " text_symptom = '-', ";
			}
    		if ( !empty($params['treatment'])  ) {
				$sqlAdd .= " text_treatment = '".$params['treatment']."', ";
			} else {
				$sqlAdd .= " text_treatment = '-', ";
			}
			
			
    		if ( !empty($params['textArticle'])  ) {
				$sqlAdd .= " text_other = '".$params['textArticle']."', ";
			}
			
    		if ( !empty($params['alias'])  ) {
				$sqlAdd .= " rewrite_name = '".$params['alias']."', ";
			}
			if ( !empty($params['fullName'])  ) {
				$sqlAdd .= " full_name = '".$params['fullName']."', ";
			}  

			
    		if ( !empty($params['metaTitle'])  ) {
				$sqlAdd .= " title = '".$params['metaTitle']."', ";
			} else {
				$sqlAdd .= " title = NULL, ";
			}
    		if ( !empty($params['metaKeyWd'])  ) {
				$sqlAdd .= " meta_keywords = '".$params['metaKeyWd']."', ";
			} else {
				$sqlAdd .= " meta_keywords = NULL, ";
			}
    		if ( !empty($params['metaDescr'])  ) {
				$sqlAdd .= " meta_desc = '".$params['metaDescr']."', ";
			} else {
				$sqlAdd .= " meta_desc = NULL, ";
			}
			
    		if ( $params['status'] == 1  ) {
				$sqlAdd .= " is_hidden = 1, ";
			} else {
				$sqlAdd .= " is_hidden = 0, ";
			}
	
			$sqlAdd = rtrim($sqlAdd, ', '); 
			
			$sql = "INSERT INTO `illness` SET ".$sqlAdd;
			//echo $sql;
			$result = query($sql);
			if (!$result) return false;
			
			$id = legacy_insert_id();
			
			$this->id = $id;
			
    		return $id;
    	}
    	 
    	return false;
    }
    
    
    
    
	/**
     *
     * Удаление статьи
     */
    public function delete ( ) {

    	if ( $this->id > 0 ) {
			$sql = "DELETE FROM `illness` WHERE id = ".$this->id;
			//echo $sql;
			
			$result = query($sql);
			if (!$result) return false;

    		return true;
    	}
    	 
    	return false;
    }

}
?>