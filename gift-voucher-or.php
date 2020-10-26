<?php

require_once("core/loader.php");

if(!isset($_GET['key'])){
    
    header("Location:error-page.php");
    exit;
    
}

$key   =   mysqli_real_escape_string($dbHandle, trim($_GET['key']));

// http://104.237.137.121/gift-voucher-or.php?key=VOUCHERPLUS5AC51D337303F

if(empty($key)){
    
    header("Location:error-page.php");
    exit;
    
}

//$sql = "SELECT * FROM ".TBL_SOLD_VOUCHERS." WHERE access_key = '".$key."' AND status = 1";
$sql = "SELECT * FROM ".TBL_SOLD_VOUCHERS." WHERE access_key = '".$key."'";
$ref	=	query($dbHandle, $sql);
$sold_voucher		=	fetchOne($ref, 'Object');

//fetch the voucher 
$sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE id = ".$sold_voucher->voucher_id."";
$ref        =   query($dbHandle, $sql);
$voucher    =   fetchOne($ref, 'Object');

if (empty($sold_voucher) || empty($voucher)){
    header("Location:error-page.php");
    exit;
}

if($sold_voucher->number_of_redemptions < $voucher->max_redemptions){
    $sql = "SELECT v.title, v.description, o.business_name, o.message, o.address, o.phone, o.suburb_id "
            . "FROM ".TBL_VOUCHERS." v "
            . "LEFT JOIN ".TBL_OUTLETS." o ON o.id = v.outlet_id "
            . "WHERE v.id = '".$sold_voucher->voucher_id."'";
            //. "AND v.voucher_status = 1"; //removed by Matías

    $ref	=	query($dbHandle, $sql);
    $dataX		=	fetchOne($ref, 'Object');

    $sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$dataX->suburb_id."'";
    $ref	=	query($dbHandle, $sql);
    $suburb	=	fetchOne($ref, 'Object');
    
    $sql = "SELECT o.purchaser_name, o.receiver_name, o.message, oc.wish "
            . "FROM ".TBL_ORDERS." o "
            . "LEFT JOIN ".TBL_OCCASIONS." oc ON oc.id = o.occasion_id "
            . "WHERE o.order_no = '".$sold_voucher->order_no."'";

    $ref	=	query($dbHandle, $sql);
    $dataY		=	fetchOne($ref, 'Object');
    
}
elseif($sold_voucher->number_of_redemptions == $voucher->max_redemptions){
    header("Location:error-page.php?error-code=1001");
    exit;
}
else{
    header("Location:error-page.php");
    exit;
}
?>

<html lang="en">
    <head>
        <title>Gift voucher</title>
        <!-- Load paper.css for happy printing -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.2.3/paper.css">
    </head>
    
<body class="A4">
    
<style>
        .sheet{
            background:url(assets/images/boucherbg2.jpg) no-repeat; 
            background-size:cover;
        }
        
        @media print
        {
            * {-webkit-print-color-adjust:exact;}
        }
        
    </style>    
<table class="sheet" width="100%" border="0"  style="border-collapse: collapse;">

  <tr>
    <td valign="top" align="center">
    	<table width="90%" border="0" align="center">
    		<tr>
    			<td height="130"> </td>
    		</tr>
    		<tr>
    			<td align="center" valign="top">
                    <table width="500" border="0" align="center">
                        <tr>
                            <td align="center" width="100">&nbsp;</td>
                            <td align="center" style="font-size:50px; font-weight:600;font-family:serif;height:169px;">
                                <?php echo ucwords($dataY->wish); ?> <br>
                                <span style="font-weight:400;font-family:monospace; font-size: 44px;"><?php echo ucwords($dataY->receiver_name); ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
    		</tr>
    		<tr>
    			<td height="100"> </td>
    		</tr>
            <tr>
                <td align="center" valign="top">
                    <table width="100%" border="0" align="center">
                        <tr>
                            <td align="center" width="60%">
            <table width="100%" border="0" align="center">
             <tr>
                <td align="center"><img src="assets/images/giftvoucher.png" alt="image"></td>
            </tr>
            <tr>
                <td height="40"> </td>
            </tr>
            <tr>
                <td align="center" style="font-size:30px; font-weight:500;font-family:monospace;"><?php echo $dataX->title; ?></td>
            </tr>
            <tr>
                <td height="50"> </td>
            </tr>
            <tr>
                <td align="center" style="font-size:30px; font-weight:500;">@<?php echo ucwords($dataX->business_name); ?></td>
            </tr>
            <tr>
                <td height="30"> </td>
            </tr>
            <tr>
                <td valign="top" align="center" style="font-size:20px; line-height:24px; padding:0 20px;"><?php echo $dataX->description; ?></td>
            </tr>
            
            <tr>
                <td height="30"> </td>
            </tr>
            <tr>
                <td valign="top" align="center" style="font-size:24px; font-weight:600;">Condition(s) </td>
            </tr>
            <tr>
                <td height="20"> </td>
            </tr>
            <tr>
                <td valign="top" align="center" style="font-size:20px; line-height:24px"><?php echo $dataX->message; ?></td>
            </tr>
            <tr>
                <td height="30"> </td>
            </tr>

            <tr>
                <td valign="top" align="center" style="font-size:20px; line-height:24px">
                    <?php echo strtoupper($dataX->business_name); ?><br> 
                            <?php echo $dataX->address; ?>, <?php echo ucwords(strtolower($suburb->suburb)).', '.$suburb->state.' '.$suburb->postcode; ?><br> 
                            <?php echo $dataX->phone; ?>
                </td>
            </tr>
        </table>
    </td>
                            <td align="center" valign="top">
                                <table width="100%" border="0" align="center">
            <tr>
                <td align="center" style="font-size:24px; font-weight:600;font-family:sans-serif;">Voucher Number</td>
            </tr>
            <tr>
                <td height="10"></td>
            </tr>
            <tr>
                <td valign="top" align="center" style="font-size:40px; font-weight:700;"><?php echo $sold_voucher->voucher_no; ?></td>
            </tr>
            <tr>
                <td height="30"> </td>
            </tr>
            <tr>
                <td  valign="top" align="center" style="font-size:22px; font-weight:700;">FROM</td>
            </tr>
            <tr>
                <td height="30" align="center"><img src="assets/images/fromline.png" alt="image"> </td>
            </tr>
            <tr>
                <td  valign="top" align="center" style="font-size:20px; font-weight:600;"><?php echo ucwords($dataY->purchaser_name); ?></td>
            </tr>
            <tr>
                <td height="30"> </td>
            </tr>
            <tr>
                <td  valign="top" align="center" style="padding:0 20px;"><?php echo $dataY->message; ?></td>
            </tr>
            <tr>
                <td height="100"> </td>
            </tr>
            <tr>
                <td valign="top" align="center" style="font-size:20px; font-weight:600;">Expiry Date <br> <?php echo date('d M Y', strtotime($sold_voucher->expiry_date)); ?></td>
            </tr>
            <tr>
                <td height="20"> </td>
            </tr></table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>


    		
    		
    		
    		
    		
    	</table>
    </td>
  </tr>

</table>

</body>
</html>