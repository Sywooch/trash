<?php

	function specializationDictXML() {
	    $xml = "";
	
	    $sql = "SELECT id, name FROM sector ORDER BY name";
	    $result = query($sql);
	    if (num_rows($result) > 0) {
	        $xml .= "<SpecializationDict>";
	        while ($row = fetch_object($result)) {
	            $xml .= "<Element id='".$row->id."'>".$row->name."</Element>";
	        }
	        $xml .= "</SpecializationDict>";
	    }
	    
	    return $xml;
	}

	
	
	function getDiagnosticList ( $parent = 0 ) {
		$xml = "";

		$sql="	SELECT 
					id, name, title, parent_id
				FROM diagnostica 
				WHERE 
					parent_id = $parent 
				ORDER BY name";
		//echo $sql;
	  	$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<DiagnosticList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<Name>".$row -> name."</Name>";
				$xml .= "<Title>".$row -> title."</Title>";
				if ( $row -> parent_id == 0 ) {
					$xml .= getDiagnosticList($row -> id);
				}
				$xml .= "</Element>";
			}
			$xml .= "</DiagnosticList>";
		}
		
		return $xml;
	}
	
	
	
	function specialization4ArticleDictXML() {
	    $xml = "";
	
	    $sql = "SELECT id, name, rewrite_name FROM article_section ORDER BY name";
	    $result = query($sql);
	    if (num_rows($result) > 0) {
	        $xml .= "<Specialization4ArticleDict>";
	        while ($row = fetch_object($result)) {
	            $xml .= "<Element id='".$row->id."'>";
	            $xml .= "<Id>".$row->id."</Id>";
	            $xml .= "<Name>".$row->name."</Name>";
	            $xml .= "<Alias>".$row->rewrite_name."</Alias>";
	            $xml .= "</Element>";
	        }
	        $xml .= "</Specialization4ArticleDict>";
	    }
	    
	    return $xml;
	}
	
	
	
	function getContractList ( ) {
		$data = array();
	
		$sql = "SELECT
					t1.contract_id as Id,
					t1.title AS Name,
					t1.isClinic AS IsClinic, 
					t1.isDiagnostic AS IsDiagnostic
				FROM contract_dict t1
				ORDER BY t1.title";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result)  > 0) {
			while ($row = fetch_array($result)) {
				array_push($data, $row);
			}
		}
	
		return $data;
	}
?>
