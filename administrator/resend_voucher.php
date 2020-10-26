<?php

require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}
//pr($_POST);die;
$id = (int)mysqli_real_escape_string($dbHandle, $_POST['sold_voucher_id']);
$to = mysqli_real_escape_string($dbHandle, $_POST['uemail']);
$u_mobile = mysqli_real_escape_string($dbHandle, $_POST['umobile']);

if(!$id){
    
    $flash->error('Gift voucher not found.');
    header("Location:browse_purchased_vouchers.php");
    
}

//$sql = "SELECT * FROM ".TBL_SOLD_VOUCHERS." sv LEFT JOIN WHERE id = '".$id."'";
$sql = "SELECT sv.id, sv.voucher_no, sv.expiry_date, ot.business_name, ot.sender_email, "
        . "v.title, sv.price, o.purchaser_name, o.purchased_for, "
        . "o.purchaser_email, o.purchaser_mobile, o.receiver_name, "
        . "o.receiver_email, o.receiver_mobile, o.payment_status, "
        . "o.created, sv.redeem_datetime, sv.access_key, sv.status "
        . "FROM ".TBL_SOLD_VOUCHERS." sv "
        . "LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id "
        . "LEFT JOIN ".TBL_ORDERS." o ON o.order_no = sv.order_no "
        . "LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id "
        . "WHERE sv.id = '".$id."'";

$ref	=	query($dbHandle, $sql);
$sold_voucher		=	fetchOne($ref, 'Object');

if(!$sold_voucher){
    
    $flash->error('Gift voucher not found.');
    header("Location:browse_purchased_vouchers.php");
    
}

//$sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$outlet->suburb_id."'";
//$ref	=	query($dbHandle, $sql);
//$suburb	=	fetchOne($ref, 'Object');

$sql = "SELECT * FROM ".TBL_SETTINGS." WHERE id = 1";

$ref	=	query($dbHandle, $sql);
$settings		=	fetchOne($ref, 'Object');

//$login_url = BASE_URL.'/outlet/login.php';
//
//$ot_arr = explode(' ', strtolower($outlet->business_name));
//$ot = urlencode(implode('-', $ot_arr));
//$access_url = BASE_URL.'/vouchers.php?ot='.$ot.'&suburb='.urlencode(strtolower($suburb->suburb));        

/*Send welcome Email/SMS to outlet
* 
*/
$sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE id = 7";

$ref	=	query($dbHandle, $sql);
$email_template	=	fetchOne($ref, 'Object');

$subject    =   $email_template->subject;
$body       =   $email_template->body;

$subject	=   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $sold_voucher->business_name)), $subject);
$subject	=   str_replace('{{voucher_name}}', ucwords(mysqli_real_escape_string($dbHandle, $sold_voucher->title)), $subject);

$body       =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $sold_voucher->business_name)), $body);

    if($sold_voucher->purchased_for == 'Self'){

        $giftVoucherUrl = BASE_URL.'/gift-voucher-sf.php?key='.$sold_voucher->access_key;
        $from       =   $sold_voucher->sender_email;

    }else{

        $giftVoucherUrl = BASE_URL.'/gift-voucher-or.php?key='.$sold_voucher->access_key; 
        $from       =   $sold_voucher->sender_email;

    }

$body	=   str_replace('{{giftvoucher_url}}', $giftVoucherUrl, $body);

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From:" . $from;

try{

    mail($to,$subject,$body, $headers);

}catch(Exception $e) {

    echo 'Message: ' .$e->getMessage();

}

$pdatetime	=	date("Y-m-d H:i:s");

$sql = "SELECT * FROM ".TBL_SMS_TEMPLATES." WHERE id = 5";

$ref	=	query($dbHandle, $sql);
$sms_template	=	fetchOne($ref, 'Object');

$sms_body           =       $sms_template->body;

$sms_body           =	str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $sold_voucher->business_name)), $sms_body);

$sql = "INSERT INTO ".TBL_SMS_QUEUES." (mobile, sms_text, created, modified) VALUES ('".$u_mobile."', '".$sms_body."', '".$pdatetime."', '".$pdatetime."')";

$ref	=	query($dbHandle, $sql);

//Update sold vouchers table
$sql	=	"UPDATE ".TBL_SOLD_VOUCHERS." SET resend_datetime = '".$pdatetime."' "
                    . "WHERE id = '".$sold_voucher->id."'";

$ref	=	query($dbHandle, $sql);

$flash->success('Resend notification sent.');

header("Location:browse_purchased_vouchers.php");
exit;

?>