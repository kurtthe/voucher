<?php

if(!function_exists('Humanize')){
	
	function Humanize($inputString){
	
		$replacementArray	=	array("_");
		
		return str_replace($replacementArray, " ", $inputString);
	
	}
	
}

function calculate_string( $mathString )    {
   
    $mathString = trim($mathString);     // trim white spaces
    
	$mathString	=	str_replace("&", ".", $mathString);
 
 	try{
 
    	$compute = create_function("", "return (" . $mathString . ");" );
	
	}catch(Exception $e){
		
		echo $e->getMessage();
		
	}
	
    return $compute();
}

function rand_string($length) {
    
    $str="";
    
    $chars = "subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
    $size = strlen($chars);
    
    for($i = 0;$i < $length;$i++) {
        
        $str .= $chars[rand(0,$size-1)];
        
    }
    
    return $str;
    
}

function getRand($dbHandle){
	
	$randNumber = sprintf("%010d", mt_rand(1, 10000000000));
	
        $sql = "SELECT order_no FROM ".TBL_ORDERS." WHERE order_no = '".($randNumber)."'";
        
	$ref = mysqli_query($dbHandle, $sql) or die(mysqli_error($dbHandle));
	
	if(!mysqli_num_rows($ref)){
																								
		return $randNumber;
																								
	}else{
		
		return getRand();
		
	}
	
}
?>