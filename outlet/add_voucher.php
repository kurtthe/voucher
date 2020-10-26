<?php
require_once("../core/loader.php");

if(empty($_SESSION['OUTLET'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveVoucher'])){
    
    $outlet_id          =   $_POST['outlet_id'];
    $title              =   mysqli_real_escape_string($dbHandle, ucwords(trim($_POST['title'])));
    $description        =   mysqli_real_escape_string($dbHandle, trim($_POST['description']));
    $actual_price       =   mysqli_real_escape_string($dbHandle, trim($_POST['actual_price']));
    $sale_price         =   mysqli_real_escape_string($dbHandle, trim($_POST['sale_price']));
    $voucher_order      =   (int)mysqli_real_escape_string($dbHandle, trim($_POST['voucher_order']));
    $max_redemptions    =   (int)mysqli_real_escape_string($dbHandle, trim($_POST['max_redemptions']));


 if($_POST['expiry_date']=="") {

      $expiry_date = NULL;
       $months_of_expiration =  $_POST['months_of_expiration'];

    } else {

      $date_ = str_replace('/', '-', $_POST['expiry_date']);
      $expiry_date  =  date('Y-m-d', strtotime($date_));
      $months_of_expiration = '';
    }


    
    $cur_datetime	=	date("Y-m-d H:i:s");
    
    $sql	=	"INSERT INTO ".TBL_VOUCHERS." SET outlet_id = '".$outlet_id."', title = '".$title."', "
                        . "description = '".$description."', actual_price = '".$actual_price."', "
                        . "sale_price = '".$sale_price."', voucher_order = '".$voucher_order."', voucher_status = 1, "
                        . "months_of_expiration = '".$months_of_expiration."', "
                        . "max_redemptions = '".$max_redemptions."', "
                        . "created = '".$cur_datetime."', modified = '".$cur_datetime."' , expiry_date = '".$expiry_date."' ";

    //echo $sql;die;
    $ref	=	query($dbHandle, $sql);
    
    $insert_id = insertId($dbHandle);
    
    if($ref){
        
        if(isset($_FILES['voucher_logo']) && $_FILES['voucher_logo']['name']){
		
            if($_FILES['voucher_logo']['error'] == 0){

                $imageTypes		=		array(
                                                              'jpeg',
                                                              'jpg',
                                                              'png',
                                                              'gif'
                                                              );

                $fileExtArr	=	explode(".", $_FILES['voucher_logo']['name']);
                $fileExt = end($fileExtArr);

                if(in_array(strtolower($fileExt), $imageTypes)){

                        $fileName	=	uniqid('Voucher_logo_', rand(1,1000000000000)).".".strtolower($fileExt);

                        $targetPath	=	$_SERVER['DOCUMENT_ROOT'].UPLOAD_LOGO_DIR.$fileName;

                        $flag		=	move_uploaded_file($_FILES['voucher_logo']['tmp_name'], $targetPath);

                        if($flag){

                                $logo_image_file = $fileName;

                                $update_sql = "UPDATE ".TBL_VOUCHERS." SET voucher_image_file = '".$logo_image_file."' WHERE id = '".$insert_id."'";
                                query($dbHandle, $update_sql);

                        }


                }

            }

        }
        
    }
    
    $flash->success('Voucher added successfully.');
    header("Location:browse_vouchers.php");
    exit;
    
}

$sql = "SELECT id,business_name FROM ".TBL_OUTLETS." WHERE id = '".$_SESSION['OUTLET']['Id']."'";

$ref	=	query($dbHandle, $sql);

$outlet		=	fetchOne($ref, 'Object');
//echo "<pre>";
//pr($outlet);die;

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

#res_date {

  font-size: 15pt;
  color: black;
  font-weight: bold;
  margin-left: 5%;
  display: none;

}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Add Voucher
            <small>Voucher Manager</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="browse_vouchers.php"><i class="fa fa-bars"></i> Browse Vouchers</a></li>
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
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="addVoucherFrm" enctype="multipart/form-data">
              
                          <input type="hidden" name="outlet_id" value="<?php echo $outlet->id; ?>">
                          
                      <div class="box-body">
                          
                        <div class="form-group required">
                            <label class="control-label">Title</label>
                            <input type="text" required="required" class="form-control" name="title" placeholder="" value="">
                        </div>
                        
                        <div class="form-group required">
                            <label class="control-label">Description</label>
                            <textarea class="form-control" required="required" rows="3" name="description"></textarea>
                        </div> 
                         
                        <div class="form-group">
                            <label>Upload Image</label>
                            <input name="voucher_logo" type="file" id="">
                        </div>  
                          
                        <div class="form-group required">
                            <label class="control-label">Actual Price</label>
                            <input type="text" required="required" class="form-control" name="actual_price" placeholder="" value="" style="width:25%;">
                        </div>
                          
                        <div class="form-group">
                            <label>Sale Price</label>
                            <input type="text" class="form-control" name="sale_price" id="" placeholder="" value="" style="width:25%;">
                        </div>
                        
                        <div class="form-group">
                            <label>Order</label>
                            <input type="text" class="form-control" name="voucher_order" id="" placeholder="eg. 1,2,3.." value="" style="width:25%;">
                        </div>  
                        <div class="form-group">
                            <label>Maximum number of redepmtions <a href="#" data-toggle="tooltip" title="Maximum number of times the voucher can be redeemed."><i class="fa fa-question-circle"></i></a></label>
                            <input type="number" class="form-control" name="max_redemptions" id="" placeholder="Example: 1" value="1" style="width:25%;">
                        </div>  
                          
                        <div class="form-group">
                            <label>
                              Voucher Expiration
                              <a href="#" data-toggle="tooltip" title="The NSW Government is introducing a mandatory minimum expiry period of 3 years for most gift cards and gift vouchers sold to a consumer in NSW, starting 31 March 2018. For information and exclusions visit The NSW Fair Trading website."><i class="fa fa-question-circle"></i></a> 
                            </label> <label id="label1" style="float: right; margin-right: 75%;"><span id="title1">Choose Exact Date </span><span id="title2" style="display: none;">Choose Mounths </span><input type="checkbox" name="" id="check_date"></label>
                            <select name="months_of_expiration" class="form-control" required="required" style="width:25%;" id="select_epx_da">
                                <option value="1">1 Month</option>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="9">9 Months</option>
                                <option value="12">1 Year</option>
                                <option value="24">2 Years</option>
                                <option value="36" selected>3 Years</option>
                                <option value="48">4 Years</option>
                            </select>

                            <input type="text" class="form-control"   placeholder="02/10/2021" id="expiry_date" name="expiry_date" style="width:25%; display: none;">

                            <p id="res_date"></p>
                            <input type="hidden" name="months_of_expiration_calculate" id="result_end">
                        </div> 

                      </div>
                      
                      <div class="box-footer">
                            <button type="submit" name="saveVoucher" class="btn btn-primary">Save</button>
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

<script>
    
$(function () {
    
    $("#addVoucherFrm").validate({

        submitHandler: function(form) {
                                
            $('input[type=submit]').attr('disabled', 'disabled');
            $('input[type=submit]').val("Saving..");
            form.submit();

        }

    });
  
});

$( "#expiry_date" ).datepicker({
  dateFormat: 'dd/mm/yy',//check change
  changeMonth: true,
  changeYear: true,
  clearBtn: true,
    minDate: 0
    
});

</script>


<script type="text/javascript" src="../test/app.js"></script>

<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.js" data-modules="effect effect-bounce effect-blind effect-bounce effect-clip effect-drop effect-fold effect-slide"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />


