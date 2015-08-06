<?php
	function getArticleListXML ($params=array()) {
		$xml = "";
		$addJoin = "";
		$startPage = 1;
		$sqlAdd = " 1 = 1 ";
		$step = 100;
		$withPager = true;

		if (count($params) > 0) {

			if	( isset($params['withPager']) )  {
				$withPager = $params['withPager'];
			}
			
			if	( isset($params['name']) && !empty ($params['name'])  )  {
				$sqlAdd .= " AND LOWER(t1.name) LIKE  '%".strtolower($params['name'])."%' ";
			}
			if	( isset($params['status']) && $params['status'] != '' )  {
				if ($params['status'] == 1 ) 
					$sqlAdd .= " AND t1.disabled = ".$params['status']." ";
				else 
					$sqlAdd .= " AND t1.disabled = 0 ";
			}
			if	( isset($params['section']) && !empty ($params['section'])  )  {
				if ( $params['section'] == -1 )
					$sqlAdd .= " AND t1.article_section_id = 0 ";
				else 	
					$sqlAdd .= " AND t1.article_section_id = ".$params['section']." ";
			}
			
			if ( isset($params['sortBy']) )  {
				switch ($params['sortBy']) {
					case 'crDate'		: $sortBy= " t1.created ";break;
					case 'title'		: $sortBy= " name ";break;
					case 'id'		: $sortBy= " t1.id ";break;
					default:break;
				}
				if (isset($params['sortType']) && $params['sortType'] == 'asc')  {
					$sqlSort = " ORDER BY ".$sortBy." ASC";
				} else {
					$sqlSort = " ORDER BY ".$sortBy." DESC";
				}
			} else {
					$sqlSort = " ORDER BY t1.id";
			}
		}

		
		$sql = "SELECT 
	    			t1.id, 
	    			t1.name, 
	    			t1.rewrite_name,
	    			t1.disabled as status,
	    			t2.sector_id,
	    			t2.name as section_name
	    		FROM article t1
	    		LEFT JOIN article_section t2 ON (t2.id = t1.article_section_id)
	    		WHERE ".$sqlAdd.$sqlSort;

		//echo $sql;
		if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
		if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];

		if ( $withPager ) {
			list($sql, $str) = pager( $sql, $startPage, $step, "artlist");
			$xml .= $str;
		}
		//echo $str."<br/>";

		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<ArticleList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				//$xml .= "<CrDate>".$row -> crDate."</CrDate>";
				$xml .= "<Name><![CDATA[".$row -> name."]]></Name>";
				$xml .= "<Alias><![CDATA[".$row -> rewrite_name."]]></Alias>";
				$xml .= "<SectorId>".$row -> sector_id."</SectorId>";
				$xml .= "<SectionName>".$row -> section_name."</SectionName>";
				
				$xml .= "<Status>".$row -> status."</Status>";
				$xml .= "</Element>";
			}
			$xml .= "</ArticleList>";
		}
		return $xml;
	}
	
	
	
	
	function getIllnessListXML ($params=array()) {
		$xml = "";
		$addJoin = "";
		$startPage = 1;
		$sqlAdd = " 1 = 1 ";
		$step = 100;
		$withPager = true;

		if (count($params) > 0) {

			if	( isset($params['withPager']) )  {
				$withPager = $params['withPager'];
			}
			
			if	( isset($params['name']) && !empty ($params['name'])  )  {
				$sqlAdd .= " AND LOWER(t1.name) LIKE  '%".strtolower($params['name'])."%' ";
			}
			if	( isset($params['status']) && $params['status'] != '' )  {
				if ($params['status'] == 1 ) 
					$sqlAdd .= " AND t1.is_hidden = ".$params['status']." ";
				else 
					$sqlAdd .= " AND t1.is_hidden = 0 ";
			}
			if	( isset($params['sector']) && !empty ($params['sector'])  )  {
				if ( $params['sector'] == -1 )
					$sqlAdd .= " AND t1.sector_id = 0 ";
				else 	
					$sqlAdd .= " AND t1.sector_id = ".$params['sector']." ";
			}
			
			if ( isset($params['sortBy']) )  {
				switch ($params['sortBy']) {
					case 'title'		: $sortBy= " name ";break;
					case 'id'		: $sortBy= " t1.id ";break;
					default:break;
				}
				if (isset($params['sortType']) && $params['sortType'] == 'asc')  {
					$sqlSort = " ORDER BY ".$sortBy." ASC";
				} else {
					$sqlSort = " ORDER BY ".$sortBy." DESC";
				}
			} else {
					$sqlSort = " ORDER BY t1.id";
			}
		}

		
		$sql = "SELECT 
	    			t1.id, 
	    			t1.name, 
	    			t1.rewrite_name,
	    			t1.is_hidden as status,
	    			t1.sector_id,
	    			t2.name as sector_name
	    		FROM illness t1
	    		LEFT JOIN sector t2 ON (t2.id = t1.sector_id)
	    		WHERE ".$sqlAdd.$sqlSort;

		//echo $sql;
		if ( isset($params['step']) && intval($params['step']) > 0 ) $step = $params['step'];
		if ( isset($params['startPage']) && intval($params['startPage']) > 0 ) $startPage = $params['startPage'];

		if ( $withPager ) {
			list($sql, $str) = pager( $sql, $startPage, $step, "illness_list");
			$xml .= $str;
		}
		//echo $str."<br/>";

		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<IllnessList>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"".$row -> id."\">";
				$xml .= "<Name><![CDATA[".$row -> name."]]></Name>";
				$xml .= "<Alias><![CDATA[".$row -> rewrite_name."]]></Alias>";
				$xml .= "<SectorId>".$row -> sector_id."</SectorId>";
				$xml .= "<SectorName>".$row -> sector_name."</SectorName>";
				$xml .= "<Status>".$row -> status."</Status>";
				$xml .= "</Element>";
			}
			$xml .= "</IllnessList>";
		}
		return $xml;
	}



	function getStatusDictXML () {
		$xml = "";


		$xml .= "<StatusDict>";
		$xml .= "<Element id=\"0\">Показывается</Element>";
		$xml .= "<Element id=\"1\">Блокировка</Element>";
		$xml .= "</StatusDict>";
		
		return $xml;
	}
?>