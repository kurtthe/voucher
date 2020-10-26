<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveOutlet'])){
    
    
    $business_name      =   mysqli_real_escape_string($dbHandle, strtolower(trim($_POST['business_name'])));
    $abn                =   mysqli_real_escape_string($dbHandle, trim($_POST['abn']));
    $contact_person     =   mysqli_real_escape_string($dbHandle, trim($_POST['contact_person']));
    $email              =   mysqli_real_escape_string($dbHandle, trim($_POST['email']));
    $sender_email       =   mysqli_real_escape_string($dbHandle, trim($_POST['sender_email']));
    $mobile             =   mysqli_real_escape_string($dbHandle, trim($_POST['mobile']));
    $phone              =   mysqli_real_escape_string($dbHandle, trim($_POST['phone']));
    $address            =   mysqli_real_escape_string($dbHandle, trim($_POST['address']));
    $suburb_id          =   $_POST['SuburbId'];
    $other_addresses    =   mysqli_real_escape_string($dbHandle, trim($_POST['other_addresses']));
    $paypal_account     =   mysqli_real_escape_string($dbHandle, trim($_POST['paypal_account']));
    $message            =   mysqli_real_escape_string($dbHandle, trim($_POST['message']));
    $about_us           =   mysqli_real_escape_string($dbHandle, trim($_POST['about_us']));
    //$expiration         =   $_POST['expiration'];
    
    $outlet_password = rand_string(6);
    $p_salt = rand_string(20);
    $site_salt="ds%DSs23@dVB_his2&ff";
    $salted_hash = hash('sha256', $outlet_password.$site_salt.$p_salt);
    
    $cur_datetime	=	date("Y-m-d H:i:s");
    
    $sql	=	"INSERT INTO ".TBL_OUTLETS." SET business_name = '".$business_name."', "
                        . "abn = '".$abn."', contact_person = '".$contact_person."', "
                        . "email = '".$email."', sender_email = '".$sender_email."', mobile = '".$mobile."', "
                        . "phone = '".$phone."', address = '".$address."', "
                        . "other_addresses = '".$other_addresses."', "
                        . "suburb_id = '".$suburb_id."', paypal_account = '".$paypal_account."', "
                        . "message = '".$message."', about_us = '".$about_us."', "
                        //. "expiration = '".$expiration."', ".
                        . "outlet_password = '".$outlet_password."', "
                        . "password = '".$salted_hash."', psalt = '".$p_salt."', "
                        . "account_status = 1, created = '".$cur_datetime."', modified = '".$cur_datetime."' ";

    //echo $sql;die;
    $ref	=	query($dbHandle, $sql);
    
    $insert_id = insertId($dbHandle);
    
    if($ref){
        
        if(isset($_FILES['outlet_logo']) && $_FILES['outlet_logo']['name']){
		
            if($_FILES['outlet_logo']['error'] == 0){

                $imageTypes		=		array(
                                                              'jpeg',
                                                              'jpg',
                                                              'png',
                                                              'gif'
                                                              );

                $fileExtArr	=	explode(".", $_FILES['outlet_logo']['name']);
                $fileExt = end($fileExtArr);

                if(in_array(strtolower($fileExt), $imageTypes)){

                        $fileName	=	uniqid('Outlet_logo_', rand(1,1000000000000)).".".strtolower($fileExt);

                        $targetPath	=	$_SERVER['DOCUMENT_ROOT'].UPLOAD_OUTLETLOGO_DIR.$fileName;

                        $flag		=	move_uploaded_file($_FILES['outlet_logo']['tmp_name'], $targetPath);

                        if($flag){

                                $logo_image_file = $fileName;

                                $update_sql = "UPDATE ".TBL_OUTLETS." SET outlet_image_file = '".$logo_image_file."' WHERE id = '".$insert_id."'";
                                query($dbHandle, $update_sql);

                        }


                }

            }

        }
        
    }
    
    $flash->success('Outlet added successfully.');
    header("Location:browse_outlets.php");
    exit;
    
}

if(isset($_GET['otn']) && !empty($_GET['otn'])){
    
    $outlet_name        =   mysqli_real_escape_string($dbHandle, strtolower(trim($_GET['otn'])));
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE business_name = '".$outlet_name."'";
    $ref	=	query($dbHandle, $sql);
    $rowcount = numRows($ref);
    echo $rowcount;
    exit();
    
}

if(isset($_GET['gne']) && !empty($_GET['gne'])){
    
    $general_email        =   mysqli_real_escape_string($dbHandle, strtolower(trim($_GET['gne'])));
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE email = '".$general_email."'";
    $ref	=	query($dbHandle, $sql);
    $rowcount = numRows($ref);
    echo $rowcount;
    exit();
    
}

require_once("includes/header.php");
?>
<!-- Left side column. contains the logo and sidebar -->
<?php
require_once("includes/sidebar.php");
?>

<style>
.form-group.required .control-label:before { 
    color: red;
   content: "*";
   position: absolute;
   margin-left: -8px;
}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Add Outlet
            <small>Outlet Manager</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="browse_outlets.php"><i class="fa fa-bars"></i> Browse Outlets</a></li>
          </ol>  
        </section>

        <!-- Main content -->
        <section class="content">
          <!-- Info boxes -->
          <div class="row">
            
              <div class="col-md-12">
                  
                  <?php
                    // Display the messages
                    $flash->display();
                  ?>
                  
                  <div class="box box-primary">
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="addOutletFrm" enctype="multipart/form-data">
                        
                        <input type="hidden" name="SuburbId" id="SuburbId" value="">
                        <input type="hidden" name="SuburbName" id="SuburbName" value="">
              
                      <div class="box-body">
                          
                        <div class="form-group required">
                            <label class="control-label">Business Name</label>
                            <input type="text" required="required" class="form-control" name="business_name" id="business_name" placeholder="" value="">
                        </div>
                        
                        <div class="form-group required">
                            <label class="control-label">ABN</label>
                            <input type="text" required="required" class="form-control" name="abn" placeholder="" value="">
                        </div>
                         
                        <div class="form-group required">
                            <label class="control-label">Contact Person</label>
                            <input type="text" required="required" class="form-control" name="contact_person" placeholder="" value="">
                        </div>
                          
                        <div class="form-group required">
                            <label class="control-label">Actual Email</label>
                            <input type="email" required="required" class="form-control" name="email" id="general_email" placeholder="" value="">
                        </div>
                          
                        <div class="form-group required">
                            <label class="control-label">Sender Email</label>
                            <input type="email" required="required" class="form-control" name="sender_email" id="sender_email" placeholder="" value="">
                        </div>  
                          
                        <div class="form-group required">
                            <label class="control-label">Mobile</label>
                            <input type="text" required="required" class="form-control" name="mobile" placeholder="" value="">
                        </div>
                          
                        <div class="form-group required">
                            <label class="control-label">Telephone Number</label>
                            <input type="text" required="required" class="form-control" name="phone" placeholder="" value="">
                        </div>  
                          
                        <div class="form-group required">
                            <label class="control-label">Address</label>
                            <input type="text" required="required" class="form-control" name="address" placeholder="" value="">
                        </div>  
                        
                        <div class="form-group required">
                            <label class="control-label">Suburb</label>
                            <input type="text" class="form-control" placeholder="Suburb or Postcode" id="suburbAuto" required="required">
                        </div>  

                        <div class="form-group">
                            <label class="control-label">Other Addresses. <span class="text-muted">One per line. Enter Phone numbers between '%' signs.</span></label>
                            <textarea class="form-control" id="other_addresses" name="other_addresses" placeholder="58 Smith Street NSW 2112 %02 9768 4455%" value=""></textarea> 
                        </div> 
                        
                        <div class="form-group required">
                            <label class="control-label">Upload Logo</label>
                            <input name="outlet_logo" required="required" type="file" id="">
                        </div>  
                        
                        <div class="form-group required">
                            <label class="control-label">About Us</label>
                            <textarea class="form-control" id="editor1" required="required" rows="10" name="about_us"></textarea>
                        </div>  
                          
                        <div class="form-group required">
                            <label class="control-label">Paypal Account</label>
                            <input type="text" required="required" class="form-control" name="paypal_account" placeholder="" value="">
                        </div>  
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Terms and Conditions</label>
                            <textarea class="form-control" rows="3" name="message"></textarea>
                        </div>
                          
                        <!--<div class="form-group">
                            <label>Voucher Expiration</label>
                            <select name="expiration" class="form-control" required="required">
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="9">9 Months</option>
                                <option value="12">1 Year</option>
                                <option value="24">2 Years</option>
                                <option value="36">3 Years</option>
                                <option value="48">4 Years</option>
                            </select>
                        </div>  -->
                          

                          
                      </div>
                      
                      <div class="box-footer">
                            <button type="submit" name="saveOutlet" class="btn btn-primary">Save</button>
                      </div>
                          
                      </form>    
                      
                  </div>
                  
              </div>
            
            
          </div><!-- /.row -->

          

          <!-- Main row -->
          <!-- /.row -->
        </section><!-- /.content -->
      </div>

      <?php
      require_once("includes/footer.php");
      ?>

<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>

<script>
    
/*$('textarea[placeholder]').each(function() {
    var placeholder = $(this).attr('placeholder').replace(/\\n/g, '\n');
    var numberOfLineBreaks = (placeholder.match(/\n/g)||[]).length;
    if (numberOfLineBreaks > 0) {
        $(this).removeAttr('placeholder');
        if($(this).val() ==='') {
            $(this).val(placeholder);
        }
        
        $(this).focus(function(){
            if($(this).val() === placeholder){
                $(this).val('');
            }
        });
        
        $(this).blur(function(){
            if($(this).val() ===''){
                $(this).val(placeholder);
            }    
        });
    }
});*/

$(function () {
    
    CKEDITOR.replace('editor1');
    
    $("#suburbAuto").autocomplete({
		source: "autocomplete.php?fetch=suburbs",
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
    
    $("#business_name").blur(function() {
        
            if($("#business_name").val() != ''){
                
                $.ajax({
                    type: "GET",
                    url: "add_outlet.php",
                    data: {'otn':$("#business_name").val()},
                    success: function(result) {
                        
                        if(result == 1){
                            alert('Outlet with same name already exist, try other name.');
                            $("#business_name").val('');
                            $("#business_name").focus();
                        }
                        
                    }
                });
                
            }    
            
    });
    
    $("#general_email").blur(function() {
        
            if($("#general_email").val() != ''){
                
                $.ajax({
                    type: "GET",
                    url: "add_outlet.php",
                    data: {'gne':$("#general_email").val()},
                    success: function(result) {
                        
                        if(result == 1){
                            alert('Email already associated with other outlet, try a different email.');
                            $("#general_email").val('');
                            $("#general_email").focus();
                        }
                        
                    }
                });
                
            }    
            
    });
    
    $("#addOutletFrm").validate({

        submitHandler: function(form) {
                                
            $('input[type=submit]').attr('disabled', 'disabled');
            $('input[type=submit]').val("Saving..");
            form.submit();

        }

    });
  
});

</script>