<?php
require_once("../loader.php");

if(isset($_GET['function']) && $_GET['function'] == 'receiver_notify'){
    
    $current_time = date('H:i a');
    
    $time1 = "8:00 am";
    $time2 = "22:00 pm";
    $date1 = DateTime::createFromFormat('H:i a', $current_time);
    $date2 = DateTime::createFromFormat('H:i a', $time1);
    $date3 = DateTime::createFromFormat('H:i a', $time2);
    
    if ($date1 > $date2 && $date1 < $date3){
    
    $sql = "SELECT ot.business_name, ot.sender_email, o.purchaser_name, o.receiver_name, o.receiver_email, o.receiver_mobile, o.voucher_dispatch_date, s.id as sid, s.access_key, s.order_no, v.title "
            . "FROM ".TBL_ORDERS." o "
            . "LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id "
            . "INNER JOIN ".TBL_SOLD_VOUCHERS." s ON s.order_no = o.order_no AND s.status = 1 AND s.notified = 0 "
            . "LEFT JOIN ".TBL_VOUCHERS." v ON v.id = s.voucher_id "
            . "WHERE o.purchased_for = 'Other' AND o.payment_status = 'Verified' ORDER BY s.id";
    
    $ref	=	query($dbHandle, $sql);
    $xrows	=	fetchAll($ref, 'Object');
    //pr($xrows);die;
    $sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE id = 4";

    $ref	=	query($dbHandle, $sql);
    $receiver_email_template	=	fetchOne($ref, 'Object');
    
    $sql = "SELECT * FROM ".TBL_SMS_TEMPLATES." WHERE id = 3";

    $ref	=	query($dbHandle, $sql);
    $receiver_sms_template	=	fetchOne($ref, 'Object');
    
    $pdatetime	=	date("Y-m-d H:i:s");
    $orderNum = '';
    
    foreach($xrows as $xrow){
        
        $now = strtotime(date("Y-m-d"));
        
        $voucher_dispatch_date = strtotime($xrow->voucher_dispatch_date);
        
        if($now >= $voucher_dispatch_date){
        
            $subject    =   $receiver_email_template->subject;
            $body       =   $receiver_email_template->body;

            $subject	=   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->business_name)), $subject);
            
            $subject	=   str_replace('{{purchaser_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->purchaser_name)), $subject);
            
            $subject	=   str_replace('{{voucher_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->title)), $subject);

            $body       =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->business_name)), $body);

            $body       =   str_replace('{{recipient_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->receiver_name)), $body);

            $body       =   str_replace('{{purchaser_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->purchaser_name)), $body);

            $voucherUrl = BASE_URL.'/gift-voucher-or.php?key='.$xrow->access_key;
            $body	=   str_replace('{{giftvoucher_url}}', $voucherUrl, $body);

            $from       =   $xrow->sender_email;
            $to         =   $xrow->receiver_email;

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From:" . $from;

            try{

                mail($to,$subject,$body, $headers);

            }catch(Exception $e) {

                echo 'Message: ' .$e->getMessage();

            }
            
            $sql	=	"UPDATE ".TBL_SOLD_VOUCHERS." SET notified = 1 WHERE id = '".$xrow->sid."'";

            $ref	=	query($dbHandle, $sql);
            
            if(empty($orderNum) || $orderNum != $xrow->order_no){
                
                /*

                 * Saving SMS for receiver         
                 */
                $sms_body           =       $receiver_sms_template->body;

                $sms_body           =	str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->business_name)), $sms_body);

                $sms_body           =	str_replace('{{recipient_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->receiver_name)), $sms_body);

                $sms_body           =	str_replace('{{purchaser_name}}', ucwords(mysqli_real_escape_string($dbHandle, $xrow->purchaser_name)), $sms_body);

                $voucherUrl = BASE_URL.'/gift-voucher-or.php?key='.$xrow->access_key;
                $sms_body   =   str_replace('{{giftvoucher_url}}', $voucherUrl, $sms_body);

                $sql = "INSERT INTO ".TBL_SMS_QUEUES." (mobile, sms_text, created, modified) VALUES ('".$xrow->receiver_mobile."', '".$sms_body."', '".$pdatetime."', '".$pdatetime."')";

                $ref	=	query($dbHandle, $sql);
                
                $orderNum = $xrow->order_no;
            
            }
        
        }
        
    }
    
    exit();
  
    }
}
?>