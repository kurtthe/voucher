<?php

function setFlash($var, $value){
	
	if(!empty($var)){
	
		$_SESSION[$var]	=	$value;
		
	}
	
}

function getFlash($var){
	
	$return		=	false;;
	
	if(isset($_SESSION[$var])){
		
		$return	=	$_SESSION[$var];
		
		unset($_SESSION[$var]);
		
	}	
	
	return $return;
	
}

function getErrorMessageWithCode($error_code = null){
	$message = "";
    switch ($error_code) {
        case '1001':
            return "This voucher has been redeemed.";
            break;

        case '1002':
            return "This page is no longer available.";
            break;
        
        default:
            return "You are seeing this page because server is unable to respond to your request.";
            break;
	}
}

?>