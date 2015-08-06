<?php

function getSection($section) {
    $data = array();
    
    $sql = "SELECT 
    			id AS Id,
    			name AS Name,
    			text AS Text,
    			sector_id AS SpecId,
    			rewrite_name AS Alias
            FROM article_section
            WHERE 
            	rewrite_name = '$section'";
    $result = query($sql);
    if(num_rows($result) == 1) {
        $data = fetch_array($result);
    }
    
    return $data;
}

function getArticles($sectionId, $isMemo = 0) {
    $data = array();
    
    $sql = "SELECT 
    			id AS Id, 
    			rewrite_name AS Alias, 
    			name AS Name, 
    			description AS Description, 
    			text AS Text
            FROM article 
            WHERE 
            	article_section_id = $sectionId 
            	AND 
            	disabled = 0";
    $result = query($sql);
    while($row = fetch_array($result)) {
        array_push($data, $row);
    }
    
    return $data;
}

function getArticleByAlias($alias) {
    $data = array();
    
    $sql = "SELECT 
                id AS Id, 
                rewrite_name AS Alias, 
                name AS Name, 
                description AS Description, 
                text AS Text,
                title AS Title, 
                meta_keywords AS MetaKeywords, 
                meta_description AS MetaDescription
            FROM article 
            WHERE 
            	rewrite_name='$alias' 
            	AND 
            	disabled=0";
    $result = query($sql);
    if(num_rows($result) == 1)
        $data = fetch_array($result);
    
    return $data;
}


?>