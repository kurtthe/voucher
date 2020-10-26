<?php
require_once("core/loader.php");

if(!isset($_GET['key'])){
    
    header("Location:error-page.php");
    exit;
    
}

$key   =   mysqli_real_escape_string($dbHandle, trim($_GET['key']));

if(empty($key)){
    
    header("Location:error-page.php");
    exit;
    
}

//$sql = "SELECT * FROM ".TBL_SOLD_VOUCHERS." WHERE access_key = '".$key."' AND status = 1";
$sql = "SELECT * FROM ".TBL_SOLD_VOUCHERS." WHERE access_key = '".$key."'";

$ref    =   query($dbHandle, $sql);
$sold_voucher       =   fetchOne($ref, 'Object');

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
            //. " AND v.voucher_status = 1";
        
    $ref	=	query($dbHandle, $sql);
    $data		=	fetchOne($ref, 'Object');
    
    $sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$data->suburb_id."'";
    $ref	=	query($dbHandle, $sql);
    $suburb	=	fetchOne($ref, 'Object');
    
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
            background:url(assets/images/boucherbg1.jpg) no-repeat; 
            background-size:cover;
        }
        
        @media print
        {
            * {-webkit-print-color-adjust:exact;}
        }
    </style>    
<table class="sheet" width="100%" border="0" style="border-collapse: collapse;">

  <tr>
    <td valign="top" align="center">
    	<table width="60%" border="0" align="center">
    		<tr>
    			<td height="100"> </td>
    		</tr>
    		<tr>
    			<td align="center"><img src="assets/images/heading1.png" alt="image"></td>
    		</tr>
    		<tr>
    			<td height="50"> </td>
    		</tr>
    		<tr>
    			<td align="center" style="font-size:24px; font-weight:600;"><?php echo $data->title; ?></td>
    		</tr>
    		<tr>
    			<td height="50"> </td>
    		</tr>
    		<tr>
    			<td align="center" style="font-size:24px; font-weight:600;">@<?php echo ucwords($data->business_name); ?></td>
    		</tr>
    		<tr>
    			<td height="30"> </td>
    		</tr>
    		<tr>
    			<td valign="top" align="center" style="font-size:20px; line-height:24px; padding:0 20px;"><?php echo $data->description; ?></td>
    		</tr>
    		<tr>
    			<td height="60"> </td>
    		</tr>
    		<tr>
    			<td align="center" style="font-size:24px; font-weight:600;">Voucher Number</td>
    		</tr>
    		<tr>
    			<td height="10"></td>
    		</tr>
    		<tr>
    			<td valign="top" align="center" style="font-size:40px; font-weight:700;"><?php echo $sold_voucher->voucher_no; ?></td>
    		</tr>
    		<tr>
    			<td height="50"> </td>
    		</tr>
    		<tr>
    			<td valign="top" align="center" style="font-size:24px; font-weight:600;">Condition(s) </td>
    		</tr>
    		<tr>
    			<td height="20"> </td>
    		</tr>
    		<tr>
    			<td valign="top" align="center" style="font-size:20px; line-height:24px"><?php echo $data->message; ?></td>
    		</tr>
    		<tr>
    			<td height="60"> </td>
    		</tr>
    		<tr>
    			<td valign="top" align="center" style="font-size:24px; font-weight:600;">Expiry Date: <?php echo date('d M Y', strtotime($sold_voucher->expiry_date)); ?></td>
    		</tr>
    		<tr>
    			<td height="20"> </td>
    		</tr>
    		<tr>
    			<td valign="top" align="center" style="font-size:20px; line-height:24px">
                            <?php echo strtoupper($data->business_name); ?><br> 
                            <?php echo $data->address; ?>, <?php echo ucwords(strtolower($suburb->suburb)).', '.$suburb->state.' '.$suburb->postcode; ?><br> 
                            <?php echo $data->phone; ?>
                        </td>
    		</tr>
    		
    	</table>
    </td>
  </tr>

</table>
</body>
</html>