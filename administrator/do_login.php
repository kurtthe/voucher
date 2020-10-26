<?php

require_once("../core/loader.php");

if(!empty($_SESSION['admin'])){
    
    header("Location:browse_outlets.php");
    
}

$email = mysqli_real_escape_string($dbHandle, trim($_POST['email']));
$password = mysqli_real_escape_string($dbHandle, trim($_POST['password']));

if(isset($_POST) && $email!='' && $password!=''){
 
    $sql = "SELECT id,name,password,psalt FROM ".TBL_ADMIN." WHERE email = '".$email."'";
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
            $full_name   =   $d->name;
            
        }
        
        $site_salt="sdetc456F^*&_dfer";
        
        $salted_hash = hash('sha256',$password.$site_salt.$p_salt);
        
        if($p==$salted_hash){
            
            $_SESSION['admin'] = $full_name;
            header("Location:browse_outlets.php");
            
        }else{
            
            $flash->error('Username or Password is Incorrect.');
            header("Location:login.php");
            
        }
    }else{
            
            $flash->error('Username or Password is Incorrect.');
            header("Location:login.php");
            
    }
    
// $sql=$dbh->prepare("SELECT id,password,psalt FROM users WHERE username=?");
// $sql->execute(array($email));
// while($r=$sql->fetch()){
//  $p=$r['password'];
//  $p_salt=$r['psalt'];
//  $id=$r['id'];
// }
// $site_salt="subinsblogsalt";/*Common Salt used for password storing on site. You can't change it. If you want to change it, change it when you register a user.*/
// $salted_hash = hash('sha256',$password.$site_salt.$p_salt);
// if($p==$salted_hash){
//  $_SESSION['user']=$id;
//  header("Location:home.php");
// }else{
//  echo "<h2>Username/Password is Incorrect.</h2>";
// }
}

?>