<?php
/*

�� ��������

*/
class image {	
	public $imgId;	
	public $title;
	public $shablonList		= array();
	public $shablonPathList	= array();
	public $typeShablonList	= array();
	
	
	
	/*	���������� ��������� ��������� ������ ���� */
    function __construct() {
    }
	
	
	
	/*	��������� ������� �� id	*/
	function getImageById($id) {
		$imgId = intval($id);		 
		
		try {	
				$sql = "SELECT 
						imgId, path
					FROM ImageSRC
					WHERE imgId = $id";
				//echo $sql."<br>";
				$result = query ($sql);
				if (!$result) throw new Exception("������ ��������� ������ �� �����������");
				
				if (num_rows($result) == 1) {
					$row = fetch_object($result);
					$this -> imgId 	= $row -> imgId;
					$this -> path 	= $row -> path;
					$this -> shablonList		= array();
					$this -> shablonPathList 	= array();	
					$this -> typeShablonList 	= array();	
						
					$sql = "SELECT 
								t1.imgChildId, t1.path, t1.imgTypeId 
							FROM Image t1
							WHERE 
								t1.imgId = ".$row -> imgId."
							ORDER BY t1.imgChildId";
					//echo $sql."<br>";
					$resultAdd = query ($sql);
					if (!$resultAdd) throw new Exception("������ ����������� ������� ������");
					
					if (num_rows($resultAdd) > 0) {
						while ($rowAdd = fetch_object($resultAdd)) {
							array_push($this -> shablonList, $rowAdd -> imgChildId);
							array_push($this -> shablonPathList, $rowAdd -> path);
							array_push($this -> typeShablonList, $rowAdd -> imgTypeId);
						}
					}
					
				}
		} catch (Exception $e)
		{
			echo ($e->getMessage()."<br>");
		}
	}  

}
