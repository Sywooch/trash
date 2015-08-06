<?php
class Illness {
	public $id;	
	public $data;
	
	
	
	
	/*	Определяем начальное состояние самого себя	*/
    function __construct($alias = null) {
    	if ( !empty($alias))
            $this->getModel($alias);
    }
	
	
	
	public function getModel($alias) {
		$data = array();

		$sql = "SELECT 
                    t1.id AS Id, t1.name AS Name, t1.rewrite_name AS Alias,
                    t1.sector_id AS SectorId, t1.full_name AS FullName, t1.text_desc AS Description,
                    t1.text_symptom AS Symptom, t1.text_treatment AS Treatment, t1.text_other AS Text,
                    t1.title AS Title, t1.meta_keywords AS MetaKeyWd, t1.meta_desc AS MetaDescription,
                    t1.meta_desc AS MetaDesc,
                    t1.is_hidden AS IsHidden
                FROM illness t1
                WHERE 
                	(	t1.id = '".$alias."' 
                		OR 
                		t1.rewrite_name = '$alias'
                	) 
                	AND 
                	t1.is_hidden = 0";
		//echo $sql."<br>";
		$result = query($sql);
        if ( num_rows($result) == 1) {
            $row = fetch_array($result);
            $row['NameInGenetive'] = RussianTextUtils::wordInGenitive($row['Name']);
            $data[0] = $row;
            $this-> data = $data[0];
            $this-> id = $data[0]['Id'];
        }
    }
    
  
}
