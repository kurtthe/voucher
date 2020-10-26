<?php 
require_once("core/loader.php");
require('core/functions/PaypalIPN.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);


$sql = "SELECT sandbox FROM ".TBL_SETTINGS." WHERE id = 1";

$ref    =   query($dbHandle, $sql);
$paypal_environment     =   fetchOne($ref, 'Object');

$enable_sandbox = $paypal_environment->sandbox;

//use PaypalIPN;
$ipn = new PaypalIPN();
// Use the sandbox endpoint during testing.
if ($enable_sandbox) {
    $ipn->useSandbox();
}

$verified = $ipn->verifyIPN();
if ($verified) {
    /*
     * Process IPN
     * A list of variables is available here:
     * https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNandPDTVariables/
     */
    
    $sql = "SELECT * FROM ".TBL_SETTINGS." WHERE id = 1";

    $ref    =   query($dbHandle, $sql);
    $settings       =   fetchOne($ref, 'Object');
    
    // assign posted variables to local variables
    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
    $custom = $_POST['custom'];
    $mc_fee = $_POST['mc_fee'];
    $ipn_track_id = $_POST['ipn_track_id'];
    $paymentStatus = '';
    
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE id = '".$item_number."'";

    $ref    =   query($dbHandle, $sql);
    $outlet     =   fetchOne($ref, 'Object');
    
    if($payment_status=="Completed"){
        if($receiver_email==$outlet->paypal_account){
            $paymentStatus = "Verified";
        }
        else{
            $paymentStatus = "Pending";
        }
    }
    else{
        $paymentStatus = "Pending";
    }
    
    $pdatetime  =   date("Y-m-d H:i:s");
        
    $sql    =   "UPDATE ".TBL_ORDERS." SET payment_status = '".$paymentStatus."', txn_id = '".$txn_id."', ipn_track_id = '".$ipn_track_id."', credited_amount = '".$payment_amount."', fee = '".$mc_fee."', modified = '".$pdatetime."' WHERE id = '".$custom."'";
    //throw new Exception('Exception A '.$sql);
    $ref    =   query($dbHandle, $sql);
    
    if($paymentStatus == 'Verified'){

        $sql = "SELECT * FROM ".TBL_ORDERS." WHERE id = '".$custom."'";
        $ref    =   query($dbHandle, $sql);
        $order  =   fetchOne($ref, 'Object');
        
        //fetch voucher with the order ID called $custom (shity variable naming)
        $sql = "SELECT * FROM ".TBL_SOLD_VOUCHERS." WHERE order_no = '".$order->order_no."'";
        $ref    =   query($dbHandle, $sql);
        $sold_vouchers  =   fetchAll($ref, 'Object');

        foreach($sold_vouchers as $sold_voucher){
            
            //fetch individual voucher to get the expiary months by voucher
            $sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE id = '".$sold_voucher->voucher_id."'";
            $ref    =   query($dbHandle, $sql);
            $voucher  = fetchOne($ref, 'Object');

            



            $cn = mysqli_connect("localhost", "root", "setarehsahar2") or die ("user or password wrong");
            mysqli_select_db($cn, "u157-gvms-db") or die ("DB NOT EXIST");

            $queryEXD = mysqli_query($cn, "SELECT * FROM gvms_tb_vouchers WHERE id = '".$sold_voucher->voucher_id."' ");
            $fetchEXD = mysqli_fetch_assoc($queryEXD);


             $expiry_date_exactly= $fetchEXD["expiry_date"]; 
             $expiry_date_months= $fetchEXD["months_of_expiration"]; 



                if($expiry_date_exactly =="0000-00-00") {

                    //var_dump($voucher);
            //echo "<br>";

            $currentDate = date("Y-m-d");

            //need to change this for the number of months of expiration from the voucher table 
            $expiry_days = ($voucher->months_of_expiration*30) - 1;
            $expiry_date = date("Y-m-d", strtotime($currentDate. ' + '.$expiry_days.' days'));

             $sql = "UPDATE ".TBL_SOLD_VOUCHERS." SET status = 1, expiry_date = '".$expiry_date."', modified = '".$pdatetime."' WHERE order_no = '".$order->order_no."' AND voucher_id = '".$sold_voucher->voucher_id."'";
            $ref    =   query($dbHandle, $sql);

                } else {

                    $sql = "UPDATE ".TBL_SOLD_VOUCHERS." SET status = 1, expiry_date = '".$expiry_date_exactly."', modified = '".$pdatetime."' WHERE order_no = '".$order->order_no."' AND voucher_id = '".$sold_voucher->voucher_id."'";
            $ref    =   query($dbHandle, $sql);
                }
            
            //var_dump($sql);
            //echo "<br>";
        }
        
        /*Send confirmation Email to business owner
        * 
        */
        $sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE id = 5";

        $ref    =   query($dbHandle, $sql);
        $outlet_email_template  =   fetchOne($ref, 'Object');
        
        $sql = "SELECT sv.voucher_no, sv.price, v.title FROM ".TBL_SOLD_VOUCHERS." sv LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id WHERE order_no = '".$order->order_no."' AND status = 1";

        $ref    =   query($dbHandle, $sql);
        $outlet_sold_vouchers   =   fetchAll($ref, 'Object');
        
        $outlet_subject    =   $outlet_email_template->subject;
        $outlet_body       =   $outlet_email_template->body;

        $outlet_subject =   str_replace('{{site_name}}', ucwords(mysqli_real_escape_string($dbHandle, $settings->site_name)), $outlet_subject);
        
        $outlet_subject =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $outlet_subject);

        $outlet_body       =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $outlet_body);
        
        $outlet_body       =   str_replace('{{site_name}}', ucwords(mysqli_real_escape_string($dbHandle, $settings->site_name)), $outlet_body);
        
        $outlet_body       =   str_replace('{{purchaser_name}}', ucwords(mysqli_real_escape_string($dbHandle, $order->purchaser_name)), $outlet_body);
        
        $outlet_body       =   str_replace('{{purchaser_email}}', mysqli_real_escape_string($dbHandle, $order->purchaser_email), $outlet_body);
        
        $outlet_body       =   str_replace('{{purchaser_mobile}}', mysqli_real_escape_string($dbHandle, $order->purchaser_mobile), $outlet_body);
        
        $order_details = '<table><tr><th>Voucher Number</th><th>Purchased item</th><th>Item price</th></tr>';
        
        foreach($outlet_sold_vouchers as $outlet_sold_voucher){
            $order_details .= '<tr><td>'.$outlet_sold_voucher->voucher_no.'</td><td>'.$outlet_sold_voucher->title.'</td><td>$'.$outlet_sold_voucher->price.'</td></tr>';
        }
        
        $order_details .= '</table>';
        
        $outlet_body       =   str_replace('{{order_details}}', $order_details, $outlet_body);
        
        $from       =   $outlet_email_template->from_email;
        $to         =   $outlet->email;

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:" . $from;

        try{
            mail($to,$outlet_subject,$outlet_body, $headers);
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
        
        /*Send confirmation Email/SMS to buyer
        * 
        */
        if($order->purchased_for == 'Self'){
            
            $sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE id = 2";

            $ref    =   query($dbHandle, $sql);
            $self_email_template    =   fetchOne($ref, 'Object');
            
            $sql = "SELECT sv.access_key, v.title FROM ".TBL_SOLD_VOUCHERS." sv LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id WHERE order_no = '".$order->order_no."' AND status = 1";

            $ref    =   query($dbHandle, $sql);
            $sold_vouchers  =   fetchAll($ref, 'Object');
            
            //generate links
            $vouchersUrl = "";
            foreach($sold_vouchers as $sold_voucher){
                $vouchersUrl .= "\r\n".BASE_URL.'/gift-voucher-sf.php?key='.$sold_voucher->access_key;
            }
                
            $subject    =   $self_email_template->subject;
            $body       =   $self_email_template->body;
            $subject    =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $subject);
            $subject    =   str_replace('{{voucher_name}}', ucwords(mysqli_real_escape_string($dbHandle, $sold_voucher->title)), $subject);
            $body       =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $body);
            $body       =   str_replace('{{purchaser_name}}', ucwords(mysqli_real_escape_string($dbHandle, $order->purchaser_name)), $body);
            $body   =   str_replace('{{voucher_url}}', $vouchersUrl, $body);
            $from       =   $outlet->sender_email;
            $to         =   $order->purchaser_email;

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From:" . $from;

            try{
                mail($to,$subject,$body, $headers);
            }catch(Exception $e) {
                echo 'Message: ' .$e->getMessage();
            }

            ///////////////////////////////////////////
            // sending the sms
            ///////////////////////////////////////////
            $sql = "SELECT * FROM ".TBL_SMS_TEMPLATES." WHERE id = 1";
            $ref    =   query($dbHandle, $sql);
            $self_sms_template  =   fetchOne($ref, 'Object');
            $sms_body           =       $self_sms_template->body;
            $sms_body           =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $sms_body);
            $sms_body           =   str_replace('{{purchaser_name}}', ucwords(mysqli_real_escape_string($dbHandle, $order->purchaser_name)), $sms_body);
            $sms_body   =   str_replace('{{voucher_url}}', $vouchersUrl, $sms_body);
            $sql = "INSERT INTO ".TBL_SMS_QUEUES." (mobile, sms_text, created, modified) VALUES ('".$order->purchaser_mobile."', '".$sms_body."', '".$pdatetime."', '".$pdatetime."')";
            $ref    =   query($dbHandle, $sql);
        }
        else{
            //vouchers bought for someone else                

            $sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE id = 3";

            $ref    =   query($dbHandle, $sql);
            $other_email_template   =   fetchOne($ref, 'Object');

            //generate the links within only 1 email and sms
            $sql = "SELECT sv.access_key, v.title FROM ".TBL_SOLD_VOUCHERS." sv LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id WHERE order_no = '".$order->order_no."' AND status = 1";
            $ref    =   query($dbHandle, $sql);
            $sold_vouchers  =   fetchAll($ref, 'Object');
            
            $vouchersUrl = "";
            foreach($sold_vouchers as $sold_voucher){
                $vouchersUrl .= "\r\n".BASE_URL.'/gift-voucher-sf.php?key='.$sold_voucher->access_key;
            }
            
            ///////////////////////////////////////////
            // sending the email
            ///////////////////////////////////////////
            $subject    =   $other_email_template->subject;
            $body       =   $other_email_template->body;
            $subject    =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $subject);
            $body       =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $body);
            $body       =   str_replace('{{purchaser_name}}', ucwords(mysqli_real_escape_string($dbHandle, $order->purchaser_name)), $body);
            $body       =   str_replace('{{recipient_name}}', ucwords(mysqli_real_escape_string($dbHandle, $order->receiver_name)), $body);
            $schedule_date = date('d-m-Y', strtotime($order->voucher_dispatch_date));
            $body       =   str_replace('{{schedule_date}}', $schedule_date, $body);
            $body       =   str_replace('{{voucher_url}}', $vouchersUrl, $body);
            $from       =   $outlet->sender_email;
            $to         =   $order->purchaser_email;

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From:" . $from;

            try{
                mail($to,$subject,$body, $headers);
            }catch(Exception $e) {
                echo 'Message: ' .$e->getMessage();

            }
            

            ///////////////////////////////////////////
            // sending the sms
            ///////////////////////////////////////////

            $sql = "SELECT * FROM ".TBL_SMS_TEMPLATES." WHERE id = 2";

            $ref    =   query($dbHandle, $sql);
            $other_sms_template =   fetchOne($ref, 'Object');
            $sms_body           =   $other_sms_template->body;
            $sms_body           =   str_replace('{{business_name}}', ucwords(mysqli_real_escape_string($dbHandle, $outlet->business_name)), $sms_body);
            $sms_body           =   str_replace('{{purchaser_name}}', ucwords(mysqli_real_escape_string($dbHandle, $order->purchaser_name)), $sms_body);
            $sms_body           =   str_replace('{{recipient_name}}', ucwords(mysqli_real_escape_string($dbHandle, $order->receiver_name)), $sms_body);
            $schedule_date = date('d-m-Y', strtotime($order->voucher_dispatch_date));
            $sms_body           =   str_replace('{{schedule_date}}', $schedule_date, $sms_body);
            $sms_body   =   str_replace('{{giftvoucher_url}}', $vouchersUrl, $sms_body);
            $sql = "INSERT INTO ".TBL_SMS_QUEUES." (mobile, sms_text, created, modified) VALUES ('".$order->purchaser_mobile."', '".$sms_body."', '".$pdatetime."', '".$pdatetime."')";

            $ref    =   query($dbHandle, $sql);    
        }
    }
}
// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
header("HTTP/1.1 200 OK");
