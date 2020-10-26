<?php

sleep(3);

require_once("core/loader.php");

$sql	=	"SELECT * FROM ".TBL_SMS_QUEUES." WHERE is_processed = 0";

$ref	=	query($dbHandle, $sql);

$smss = fetchAll($ref, 'Object');

foreach($smss as $sms){

        echo($sms->mobile. "@!@" . $sms->sms_text . "@!@" . '1' . "@!@" . '?' . "@!---@");

        $pdatetime	=	date("Y-m-d H:i:s");
        
        $sql = "UPDATE ".TBL_SMS_QUEUES." SET is_processed = 1, modified = '".$pdatetime."' WHERE id = '".$sms->id."'";
        
        $ref	=	query($dbHandle, $sql);

}

?>