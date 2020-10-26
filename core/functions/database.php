<?php

if(intval(ENVIRONMENT) == 1){
        
        $dbHandle = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME) or die(mysqli_error($dbHandle));

}else{
	
	$dbHandle = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME) or die(mysqli_error($dbHandle));
	
}

function query($dbHandle, $sql, $debug = 0){
	
	if($debug == 1){
		
		echo '<p>'.nl2br($sql).'</p>';
		
	}
	
	if(intval(ENVIRONMENT) == 1){
		
		$return = mysqli_query($dbHandle, $sql);
		
	}else{
		
		$return = mysqli_query($dbHandle, $sql) or die(mysqli_error($dbHandle));
		
	}
	
	return $return;
	
}

function numRows($queryHandle){
	
	return mysqli_num_rows($queryHandle);
	
}

function insertId($dbHandle){
	
	if(intval(ENVIRONMENT) == 1){
		
		return mysqli_insert_id($dbHandle);
		
	}else{
		
		return mysqli_insert_id($dbHandle);
		
	}
	
}

function fetchAll(&$queryHandle, $type='Assoc'){
	
	$return		=		array();
	
	if($type == 'Assoc' || !trim($type)){
	
		while($data	=	mysqli_fetch_assoc($queryHandle)){
			
			$return[]	=	$data;
			
		}
	
	}
	
	if($type == 'Object'){
		
		while($data	=	mysqli_fetch_object($queryHandle)){
			
			$return[]	=	$data;
			
		}
		
	}
	
	if($type == 'Array'){
		
		while($data	=	mysqli_fetch_array($queryHandle)){
			
			$return[]	=	$data;
			
		}
	}
	
	return $return;
	
}

function fetchOne(&$queryHandle, $type='Assoc'){
	
	if($type == 'Assoc' || !trim($type)){
	
		$return	=	mysqli_fetch_assoc($queryHandle);
	
	}
	
	if($type == 'Object'){
		
                $return	=	mysqli_fetch_object($queryHandle);
		
	}
	
	if($type == 'Array'){
		
                $return	=	mysqli_fetch_array($queryHandle);
                
	}
	
	return $return;
	
}

?>