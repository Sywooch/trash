<?php
class Article {
    
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
                    t1.description AS Description,
					t1.text as Body,
					t1.rewrite_name as Alias,
					t1.title as MetaTitle,
					t1.meta_keywords As MetaKeyWd,
					t1.meta_description As MetaDescr,
					t1.article_section_id As SectionId,
					t2.name as SectionName,
					t1.disabled as IsDisabled
                FROM article t1
                LEFT JOIN article_section t2 ON (t1.article_section_id = t2.id)
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
			
			if ( empty($params['sectionId'])  ) {
				$sqlAdd .= " article_section_id = 0, ";
			} else {
				$sqlAdd .= " article_section_id = '".$params['sectionId']."', ";
			}
			
			if ( !empty($params['textDesc'])  ) {
				$sqlAdd .= " description = '".$params['textDesc']."', ";
			} else {
				$sqlAdd .= " description = NULL, ";
			}
			
    		if ( !empty($params['textArticle'])  ) {
				$sqlAdd .= " text = '".$params['textArticle']."', ";
			}
			
    		if ( !empty($params['alias'])  ) {
				$sqlAdd .= " rewrite_name = '".$params['alias']."', ";
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
				$sqlAdd .= " meta_description = '".$params['metaDescr']."', ";
			} else {
				$sqlAdd .= " meta_description = NULL, ";
			}
			
    		if ( $params['status'] == 1  ) {
				$sqlAdd .= " disabled = 1, ";
			} else {
				$sqlAdd .= " disabled = 0, ";
			}
	
			$sqlAdd = rtrim($sqlAdd, ', '); 
			
			$sql = "UPDATE `article` SET
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
			
			if ( empty($params['sectionId'])  ) {
				$sqlAdd .= " article_section_id = 0, ";
			} else {
				$sqlAdd .= " article_section_id = '".$params['sectionId']."', ";
			}
			
			if ( !empty($params['textDesc'])  ) {
				$sqlAdd .= " description = '".$params['textDesc']."', ";
			} else {
				$sqlAdd .= " description = NULL, ";
			}
			
    		if ( !empty($params['textArticle'])  ) {
				$sqlAdd .= " text = '".$params['textArticle']."', ";
			}
			
    		if ( !empty($params['alias'])  ) {
				$sqlAdd .= " rewrite_name = '".$params['alias']."', ";
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
				$sqlAdd .= " meta_description = '".$params['metaDescr']."', ";
			} else {
				$sqlAdd .= " meta_description = NULL, ";
			}
			
    		if ( $params['status'] == 1  ) {
				$sqlAdd .= " disabled = 1, ";
			} else {
				$sqlAdd .= " disabled = 0, ";
			}
	
			$sqlAdd = rtrim($sqlAdd, ', '); 
			
			$sql = "INSERT INTO `article` SET ".$sqlAdd;
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
			$sql = "DELETE FROM `article` WHERE id = ".$this->id;
			//echo $sql;
			
			$result = query($sql);
			if (!$result) return false;

    		return true;
    	}
    	 
    	return false;
    }

}
?>