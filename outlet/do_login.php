<?php

require_once("../core/loader.php");

if(!empty($_SESSION['OUTLET'])){
    
    header("Location:browse_purchased_vouchers.php");
    
}

$email = mysqli_real_escape_string($dbHandle, trim($_POST['email']));
$password = mysqli_real_escape_string($dbHandle, trim($_POST['password']));

if(isset($_POST) && $email!='' && $password!=''){
 
    $sql = "SELECT id,business_name,password,psalt FROM ".TBL_OUTLETS." WHERE email = '".$email."' AND account_status = 1";
    //echo $sql;die;
    $ref	=	mysqli_query($dbHandle, $sql) or die(mysqli_error($dbHandle));
    
    $rowcount = mysqli_num_rows($ref);
    
    if($rowcount){
        
        while($data	=	mysqli_fetch_object($ref)){
			
			$datax[]	=	$data;
			
		}
        
        foreach($datax as $d){
            
            $p      =   $d->password;
            $p_salt =   $d->psalt;
            $outlet_id = $d->id;
            $full_name   =   $d->business_name;
            
        }
        
        $site_salt="ds%DSs23@dVB_his2&ff";
        
        $salted_hash = hash('sha256',$password.$site_salt.$p_salt);
        
        if($p==$salted_hash){
            
            $_SESSION['OUTLET']['Id'] = $outlet_id;
            $_SESSION['OUTLET']['Name'] = ucwords($full_name);
            $_SESSION['OUTLET']['Outlet_login_time'] = time();
            header("Location:browse_purchased_vouchers.php");
            
        }else{
            
            //echo "<h2>Username/Password is Incorrect.</h2>";
            header("Location:login.php?msg=invalid");
            
        }
    }else{
            
            //echo "<h2>Username/Password is Incorrect.</h2>";
            header("Location:login.php?msg=invalid");
            
    }
    

}

?>