<?php
require_once("core/loader.php");
//pr($_SESSION);die;
if(isset($_POST['unse']) && $_POST['unse'] == 'yes'){
    
    $post_str = $_POST['frmdata'];
    parse_str($post_str, $post_data);        
    
    $order_number   =   getRand($dbHandle);
    $suburb_id      =   (int)$post_data['SuburbId'];
    $outlet_id      =   (int)mysqli_real_escape_string($dbHandle, trim($post_data['outlet_id']));
    $total_amount   =   mysqli_real_escape_string($dbHandle, trim($post_data['total_amount']));
    
    $voucher_for = mysqli_real_escape_string($dbHandle, trim($post_data['voucher_for']));
    $full_name = mysqli_real_escape_string($dbHandle, ucwords(trim($post_data['full_name'])));
    $mobile = mysqli_real_escape_string($dbHandle, trim($post_data['mobile']));
    $email = mysqli_real_escape_string($dbHandle, trim($post_data['email']));
    
    $recipient_name = mysqli_real_escape_string($dbHandle, ucwords(trim($post_data['recipient_name'])));
    $recipient_mobile_number = mysqli_real_escape_string($dbHandle, trim($post_data['recipient_mobile_number']));
    $recipient_email_address = mysqli_real_escape_string($dbHandle, trim($post_data['recipient_email_address']));
    
    $dispatch_date = mysqli_real_escape_string($dbHandle, trim($post_data['dispatch_date']));
    $dispatch_date = str_replace('/', '-', $dispatch_date);
    $dispatch_date   =   empty($dispatch_date) ? '' : date('Y-m-d', strtotime($dispatch_date));
    $occasion = (int)mysqli_real_escape_string($dbHandle, trim($post_data['occasion']));
    $message = mysqli_real_escape_string($dbHandle, trim($post_data['message']));
    
    $cur_datetime	=	date("Y-m-d H:i:s");
    
    $sql	=	"INSERT INTO ".TBL_ORDERS." SET order_no = '".$order_number."', outlet_id = '".$outlet_id."', "
                        . "purchased_for = '".$voucher_for."', purchaser_name = '".$full_name."', "
                        . "purchaser_mobile = '".$mobile."', purchaser_email = '".$email."', "
                        . "purchaser_suburb_id = '".$suburb_id."', receiver_name = '".$recipient_name."', "
                        . "receiver_mobile = '".$recipient_mobile_number."', receiver_email = '".$recipient_email_address."', "
                        . "voucher_dispatch_date = '".$dispatch_date."', occasion_id = '".$occasion."', "
                        . "message = '".$message."', total_amount = '".$total_amount."', submitted_amount = '".$total_amount."', "
                        . "created = '".$cur_datetime."', modified = '".$cur_datetime."' ";
    
    $ref	=	query($dbHandle, $sql);
    $order_id = insertId($dbHandle);
    
    foreach($_SESSION['XCart'] as $kk=>$CartContents){
        
        for($i=0;$i<$CartContents["Quantity"];$i++){
         
            $access_key     =	strtoupper(uniqid("VOUCHERPLUS"));


            $cn = mysqli_connect("localhost", "root", "control123") or die ("user or password wrong");
            mysqli_select_db($cn, "u157-gvms-db") or die ("DB NOT EXIST");

            $queryEXD = mysqli_query($cn, "SELECT * FROM gvms_tb_vouchers WHERE id = '".$CartContents["Id"]."'");
         $fetchEXD = mysqli_fetch_assoc($queryEXD);
         $id_= $fetchEXD["expiry_date"]; 

           /* $sqlEXD = "SELECT * FROM ".TBL_VOUCHERS." WHERE id = '".$CartContents["Id"]."'" ;
            $refEXD   =   query($dbHandle, $sqlEXD);
            $occasionsEXD  =   fetchAll($refEXD, 'Object');

            $id_ = $occasionsEXD->expiry_date; */

            
            
            $sql	=	"INSERT INTO ".TBL_SOLD_VOUCHERS." SET order_no = '".$order_number."', voucher_id = '".$CartContents["Id"]."', "
                        . "price = '".$CartContents["Unit_Price"]."', access_key = '".$access_key."' , expiry_date = '".$id_."' , created = '".$cur_datetime."', modified = '".$cur_datetime."' ";
            
            $ref	=	query($dbHandle, $sql);
            $insert_id = insertId($dbHandle);
            
            $str_len = strlen($insert_id);
            $loop_var = 10 - $str_len;
            $voucher_no = '';
            
            for($j=0;$j<$loop_var;$j++){
                
                $voucher_no .= '0';
                
            }
            $voucher_no .= $insert_id;
            
            $sql	=	"UPDATE ".TBL_SOLD_VOUCHERS." SET voucher_no = '".$voucher_no."', modified = '".$cur_datetime."' "
                        . "WHERE id = '".$insert_id."' ";
            
            $ref	=	query($dbHandle, $sql);
            
        }
        
    }
    
    unset($_SESSION['XCart']);
    unset($_SESSION['XDCart']);
    
    $return = array();
    
    $return['order_id'] = $order_id;
    
    echo json_encode($return);
        
    exit();
    
}

if((isset($_POST['identifier'])) && ($_POST['identifier'] == hash('sha256','VPlus')) && (!empty($_SESSION['XCart']))){
    
    $outlet_id = (int)mysqli_real_escape_string($dbHandle, trim($_POST['outlet_id']));
    $total_amount = mysqli_real_escape_string($dbHandle, trim($_POST['total_amount']));
    
    $_SESSION['XDCart']['Outlet_Id'] = $outlet_id;
    $_SESSION['XDCart']['Total_Amount'] = $total_amount;
    
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE id = '".$_SESSION['XDCart']['Outlet_Id']."'";

    $ref	=	query($dbHandle, $sql);
    $outlet	=	fetchOne($ref, 'Object');
    
    $sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$outlet->suburb_id."'";
    $ref	=	query($dbHandle, $sql);
    $suburb	=	fetchOne($ref, 'Object');
    
    $ot_arr = explode(' ', strtolower($outlet->business_name));
    $ot = urlencode(implode('-', $ot_arr));
    $outlet_page_url = 'vouchers.php?ot='.$ot.'&suburb='.urlencode(strtolower($suburb->suburb));
    
    $sql = "SELECT * FROM ".TBL_SETTINGS." WHERE id = 1";

    $ref	=	query($dbHandle, $sql);
    $settings		=	fetchOne($ref, 'Object');
    
    $sql = "SELECT * FROM ".TBL_OCCASIONS." WHERE status = 1 ORDER BY title ASC";

    $ref	=	query($dbHandle, $sql);
    $occasions	=	fetchAll($ref, 'Object');
    
}elseif(!empty($_SESSION['XCart']) && !empty($_SESSION['XDCart']) && !empty($_SESSION['FRONTPAGE'])){
    
    $outlet_id = $_SESSION['XDCart']['Outlet_Id'];
    $total_amount = $_SESSION['XDCart']['Total_Amount'];
    
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE id = '".$_SESSION['XDCart']['Outlet_Id']."'";

    $ref	=	query($dbHandle, $sql);
    $outlet	=	fetchOne($ref, 'Object');
    
    $sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$outlet->suburb_id."'";
    $ref	=	query($dbHandle, $sql);
    $suburb	=	fetchOne($ref, 'Object');
    
    $ot_arr = explode(' ', strtolower($outlet->business_name));
    $ot = urlencode(implode('-', $ot_arr));
    $outlet_page_url = 'vouchers.php?ot='.$ot.'&suburb='.urlencode(strtolower($suburb->suburb));
    
    $sql = "SELECT * FROM ".TBL_SETTINGS." WHERE id = 1";

    $ref	=	query($dbHandle, $sql);
    $settings		=	fetchOne($ref, 'Object');
    
    $sql = "SELECT * FROM ".TBL_OCCASIONS." WHERE status = 1 ORDER BY title ASC";

    $ref	=	query($dbHandle, $sql);
    $occasions	=	fetchAll($ref, 'Object');
    
}else{
    
    header("Location:error-page.php");
    exit;
    
}

?>

<!--========================Header Start=====================-->
    <?php
    require_once("includes/header.php");
    ?>
<!--========================Header End=====================--> 

<!-- jQuery UI -->
<link rel="stylesheet" href="assets/plugins/jQueryUI/jquery-ui.min.css">

<style>
    .takcartSummary{
        background-color: #fbfbfb;
        border: 1px solid #ebebeb;
        padding-top: 20px;
        padding-bottom:15px;
        padding-left: 10px;
        padding-right: 20px;
        font-size: 14px;
        margin-bottom:40px;
    }
    .crtTitle{
        color: #3f3e3b;
        font-size: 20px;
        line-height: 24px;
        margin: 0;
        padding-bottom: 10px;
    }
    .smCrtType{
        color: #26bf22;
    }
    .chkoutFrmCnt{
        color: #797876;
        padding-bottom:15px;
        margin-bottom:40px;
    }
    .header2{
        color: #3f3e3b;
        font-size: 24px;
        line-height: 24px;
        margin: 0;
        padding-bottom: 10px;
    }
    .header3{
        color: #3f3e3b;
        font-size: 18px;
        line-height: 24px;
        margin: 0;
        padding-bottom: 10px;
    }
    .required{
        color: red;
    }
    .ui-autocomplete {
        max-height: 205px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        z-index: 1051 !important;
    }
    /* IE 6 doesn't support max-height
     * we use height instead, but this forces the menu to always be this tall
     */
    * html .ui-autocomplete {
        height: 205px;
    }
</style>

<!-- Body Section Start -->
<div class="bodypart">
    <div class="container">
        
        <div class="row">
            
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="gotoChkoutFrm">
                
                <input type="hidden" name="SuburbId" id="SuburbId" value="">
                <input type="hidden" name="SuburbName" id="SuburbName" value="">
                <input type="hidden" name="outlet_id" value="<?php echo $_SESSION['XDCart']['Outlet_Id']; ?>">
                <input type="hidden" name="total_amount" value="<?php echo $_SESSION['XDCart']['Total_Amount']; ?>">
                
            <div class="col-xs-12 col-sm-7 col-lg-8">
                
                <div class="row">
                        <div class="col-md-12">
                            <div class="header2" style="border-bottom: 1px solid #e1e1e1;">Checkout</div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;">
                        <div class="col-md-12">
                            <div class="header3" style="">You are purchasing the item(s) for</div>
                        </div>
                    </div>
                
<!--                <div class="row">
                        <div class="col-md-12">
                            <div><input type="radio" class="flat-red" checked="checked" name="aa"> Yourself</div>
                        </div>
                        <div class="col-md-12">
                            <div><input type="radio" class="flat-red" name="aa"> Someone Else</div>
                        </div>
                    </div>-->
                
                <div class="row" style="margin-top:10px;">
                        <div class="col-md-4">
                            
                        </div>
                        <div class="col-md-8">
                            <div><input type="radio" class="flat-red" checked="checked" name="voucher_for" value="self"> Yourself</div>
                            <div><input type="radio" class="flat-red" name="voucher_for" value="other"> Someone else (GIFT VOUCHER)</div>
                        </div>
                       
                    </div>
                
                    <div class="row" style="margin-top:10px;" id="selfVoucherName">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Your Name</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="text" name="full_name" id="full_name" required="required" class="form-control" value="" placeholder="Your name"></div>
                        </div>
                    </div>

<!--                <div class="row" style="margin-top:10px; display:none;" id="otherVoucherName">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Your Name</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="text" name="full_name" required="required" class="form-control" value="" placeholder="Your name will appear on the Gift Voucher as the sender"></div>
                        </div>
                    </div>    -->
                
                <div class="row" style="margin-top:10px;">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Mobile Number</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="text" name="mobile" required="required" class="form-control" value="" placeholder="Mobile number"></div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Email</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="email" name="email" required="required" class="form-control" value="" placeholder="Email address"></div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Suburb or Postcode</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="text" id="suburbAuto" required="required" class="form-control" value="" placeholder="Suburb or Postcode"></div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;display:none;" id="recipientName">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Recipient Name</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="text" name="recipient_name" required="required" class="form-control" value="" placeholder="Recipient name"></div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;display:none;" id="recipientMobile">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Mobile number</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="text" name="recipient_mobile_number" required="required" class="form-control" value="" placeholder="Mobile number"></div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;display:none;" id="recipientEmail">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Email address (theirs or yours)</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="text" name="recipient_email_address" required="required" class="form-control" value="" placeholder="Recipient's email address or yours"></div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;display:none;" id="recipientDispatchDate">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Gift voucher dispatch date</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><input type="text" name="dispatch_date" id="datePick" required="required" class="form-control" value="" placeholder="Dispatch date"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="pull-right" style="padding-top:5px;">(Recipient will receive gift voucher on this date via sms & email)</div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;display:none;" id="recipientOccasion">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Occasion</div>
                        </div>
                        <div class="col-md-8">
                            <div class="">
                                <select name="occasion" required="required" class="form-control">
                                    <option value="">Select occasion</option>
                                    <?php
                                    foreach($occasions as $occasion){
                                    ?>
                                    <option value="<?php echo $occasion->id; ?>"><?php echo $occasion->title; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                
                <div class="row" style="margin-top:10px;display:none;" id="recipientMessage">
                        <div class="col-md-4">
                            <div class="" align="center" style="padding-top:5px;"><span class="required">*</span>Message</div>
                        </div>
                        <div class="col-md-8">
                            <div class=""><textarea name="message" class="form-control" required="required" rows="3" placeholder="This message appears on the Gift Voucher"></textarea></div>
                        </div>
                    </div>

                <div class="row" style="margin-top:10px;">
                        <div class="col-md-12">
                            <div align="">
<!--                                <button class="btn btn-danger btn-lg pull-right">Order Now</button>-->
                                    <input type="button" name="orderNowBtn" id="orderNowBtn" value="Order Now" class="btn btn-danger btn-lg pull-right">
                            </div>
                        </div>
                    </div>    
            </div>
            </form>
              
            <form action="<?php echo $settings->paypal_service_url; ?>" name="paypal_form" id="paypalFrm" method="post">
            
                <input type="hidden" name="business" value="<?php echo $outlet->paypal_account; ?>" />
			
                <input type="hidden" name="notify_url" value="<?php echo BASE_URL; ?>/paypal_ipn.php" />

                <input type="hidden" name="cmd" value="_xclick" />

                <input type="hidden" name="custom" id="customOrderId" value="<?php //echo $order_id; ?>" />

                <input type="hidden" name="amount" value="<?php echo number_format($total_amount, 2); ?>" />

                <input type="hidden" name="item_name" value='Payment towards Voucher Plus' />

                <input type="hidden" name="cbt" value="Return to Voucher Plus" />

                <input type="hidden" name="item_number" value="<?php echo $outlet_id; ?>" />

                <input type="hidden" name="return" value="<?php echo BASE_URL.'/'.$outlet_page_url.'&msg=odr_cnf'; ?>" />

                <input type="hidden" name="cancel_return" value="<?php echo BASE_URL.'/'.$outlet_page_url; ?>" />

                <input type="hidden" name="currency_code" value="<?php echo $settings->default_currency;; ?>">
                
            </form>
            
            <div class="col-xs-12 col-sm-5 col-lg-4">
                <div class="orderarea">
                    
                    <div class="orderareaheading">Your Order Summary</div>
                    
                    <?php
                    $cartTotal = 0;
                    if(!isset($_SESSION['XCart']) || empty($_SESSION['XCart'])){

                        echo '<div class="row" style="padding-bottom: 10px;padding-top:10px;">No Items Added to Cart.</div>';

                    }else{
                    ?>
                    
                    <?php 
                        foreach($_SESSION['XCart'] as $kk=>$CartContents){

                            $cartTotal	+= $CartContents['Amount'];
                    ?>
                    
                    <div class="row" style="padding-bottom: 10px;padding-top:10px;">
                        <div class="col-md-7">
                            <div class=""><?php echo $CartContents["Product"]; ?></div>
                        </div>
                        <div class="col-md-2">
                            <div class=""><?php echo $CartContents["Quantity"]; ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="">$<?php echo $CartContents["Amount"]; ?></div>
                        </div>
                    </div>
                    
                    <?php
                        }
                    }
                    ?>
                    
<!--                    <div class="row" style="padding-top:10px;">
                        <div class="col-md-12">
                            <div class="" style="float:left;font-weight: bold;">Total</div>
                            <div class="" style="float:right;font-weight: bold;">$<?php echo $cartTotal;  ?></div>
                        </div>
                    </div>-->
                    <div class="ordertotalamount">
                    <span class="left">Total Amount</span>
                    <span class="right" id="totalamount">
                        $<?php
                        echo number_format($cartTotal, 2);
                        ?>
                    </span>
                </div>
                </div>    
            </div>
            
        </div>
        
    </div>
</div>

<!-- Body Section End --> 

<!--========================Footer Start=====================-->
    <?php
    require_once("includes/footer.php");
    ?>
<!--========================Footer End=====================-->

<!-- iCheck 1.0.1 -->
<link rel="stylesheet" href="assets/plugins/iCheck/all.css">
<script src="assets/plugins/iCheck/icheck.min.js"></script>

<!-- jQuery Validate -->
<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.min.js"></script>
<!-- jQuery UI -->
<script src="assets/plugins/jQueryUI/jquery-ui.min.js"></script>

<!-- datepicker -->
<link rel="stylesheet" href="assets/plugins/datepicker/datepicker3.css">
<script src="assets/plugins/datepicker/bootstrap-datepicker.js"></script>

<script type="text/javascript">
$(function () {
    
    $("#suburbAuto").autocomplete({
		source: "administrator/autocomplete.php?fetch=suburbs",
		minLength: 2,
                autoFocus: true
	});
    
    $("#suburbAuto").autocomplete({
		select: function(event, ui) {
			selected_id = ui.item.id;
                        selected_value = ui.item.value;
			$('#SuburbId').val(selected_id);
                        $('#SuburbName').val(selected_value);
		}
    });
    
    $("#suburbAuto").autocomplete({
            open: function(event, ui) {
                    $('#SuburbId').val('');
                    $('#SuburbName').val('');
            }
    });
    
    $( "#suburbAuto" ).blur(function() {
            
            if($('#SuburbId').val() == ''){
                $("#suburbAuto").val('');
            }    
            if($('#suburbAuto').val() != $('#SuburbName').val()){
                $("#suburbAuto").val('');
                $("#SuburbName").val('');
            }
            
    });
    
    
    var nowDate = new Date();
    var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
    
    //Datepicker
    $('#datePick').datepicker({
        format: "dd/mm/yyyy",
        todayHighlight: true,
        startDate: today
    });
    
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });
    
    $(".iCheck-helper").click(function() {
        
        var rad_val = $('input[name="voucher_for"]:checked').val();
        
        if(rad_val == 'self'){
        
            //$("#selfVoucherName").show();
            $("#full_name").attr("placeholder", "Your name");
            //$("#otherVoucherName").hide();
            $('input[name="recipient_name"]').val('');
            $('input[name="recipient_mobile_number"]').val('');
            $('input[name="recipient_email_address"]').val('');
            $('input[name="dispatch_date"]').val('');
            $('select[name="occasion"]').val('');
            $('textarea[name="message"]').val('');
            
            $("#recipientName").hide();
            $("#recipientMobile").hide();
            $("#recipientEmail").hide();
            $("#recipientDispatchDate").hide();
            $("#recipientOccasion").hide();
            $("#recipientMessage").hide();
        
        }else{
            
            $("#full_name").attr("placeholder", "Your name will appear on the Gift Voucher as the sender");
            //$("#otherVoucherName").show();
            //$("#selfVoucherName").hide();
            $("#recipientName").show();
            $("#recipientMobile").show();
            $("#recipientEmail").show();
            $("#recipientDispatchDate").show();
            $("#recipientOccasion").show();
            $("#recipientMessage").show();
            
        }
        
    });
    
    $("#orderNowBtn").click(function() {
       
        if(!$("#gotoChkoutFrm").valid()){
            return false;
        }else{
            
            $('input[type=button]').attr('disabled', 'disabled');
            
            $.ajax({
            type: "POST",
            url: "checkout.php",
            data: {'unse':'yes','frmdata':$('#gotoChkoutFrm').serialize()},
            success: function(result) {

                result = jQuery.parseJSON(result);
                
                $('#customOrderId').val(result.order_id);
                $('#paypalFrm').submit();

            }
        });
            
        }
       
    });
    
});
</script>