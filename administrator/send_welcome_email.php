<?php

require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

$id = (int)mysqli_real_escape_string($dbHandle, $_GET['id']);

if(!$id){
    
    $flash->error('Outlet not found.');
    header("Location:browse_outlets.php");
    
}

$sql = "SELECT * FROM ".TBL_OUTLETS." WHERE id = '".$id."'";

$ref	=	query($dbHandle, $sql);
$outlet		=	fetchOne($ref, 'Object');

if(!$outlet){
    
    $flash->error('Outlet not found.');
    header("Location:browse_outlets.php");
    
}

$sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$outlet->suburb_id."'";
$ref	=	query($dbHandle, $sql);
$suburb	=	fetchOne($ref, 'Object');

$sql = "SELECT * FROM ".TBL_SETTINGS." WHERE id = 1";

$ref	=	query($dbHandle, $sql);
$settings		=	fetchOne($ref, 'Object');

$login_url = BASE_URL.'/outlet/login.php';

$ot_arr = explode(' ', strtolower($outlet->business_name));
$ot = urlencode(implode('-', $ot_arr));
$access_url = BASE_URL.'/vouchers.php?ot='.$ot.'&suburb='.urlencode(strtolower($suburb->suburb));        

/*Send welcome Email/SMS to outlet
* 
*/
$sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE id = 1";

$ref	=	query($dbHandle, $sql);
$email_template	=	fetchOne($ref, 'Object');

$subject = $email_template->subject;
$body = $email_template->body;

$subject	=   str_replace('{{outlet.name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $subject);

$body       =   str_replace('{{outlet.name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $body);

$body       =   str_replace('{{outlet.email}}', $outlet->email, $body);

$body       =   str_replace('{{outlet.password}}', $outlet->outlet_password, $body);

$body       =   str_replace('{{outlet.loginurl}}', $login_url, $body);

$body       =   str_replace('{{outlet.accessurl}}', $access_url, $body);

$body       =   str_replace('{{sitename}}', $settings->site_name, $body);

$from       =   $email_template->from_email;
$to         =   $outlet->email;

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From:" . $from;

try{

    mail($to,$subject,$body, $headers);

}catch(Exception $e) {

    echo 'Message: ' .$e->getMessage();

}

$pdatetime	=	date("Y-m-d H:i:s");
    
$sql	=	"UPDATE ".TBL_OUTLETS." SET welcome_email_sent = 1 "
                    . "WHERE id = '".$outlet->id."'";

$ref	=	query($dbHandle, $sql);

$flash->success('Welcome email sent.');

header("Location:browse_outlets.php");
exit;

?>