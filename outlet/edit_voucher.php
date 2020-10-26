<?php
require_once("../core/loader.php");

if(empty($_SESSION['OUTLET'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveVoucher'])){
    
    $outlet_id          =   (int)$_POST['outlet_id'];
    $item_id            =   (int)$_POST['item_id'];
    
    $title              =   mysqli_real_escape_string($dbHandle, ucwords(trim($_POST['title'])));
    $description        =   mysqli_real_escape_string($dbHandle, trim($_POST['description']));
    $actual_price       =   mysqli_real_escape_string($dbHandle, trim($_POST['actual_price']));
    $sale_price         =   mysqli_real_escape_string($dbHandle, trim($_POST['sale_price']));
    $voucher_order      =   (int)mysqli_real_escape_string($dbHandle, trim($_POST['voucher_order']));
    $max_redemptions    =   (int)mysqli_real_escape_string($dbHandle, trim($_POST['max_redemptions']));
    $voucher_status     =   (int)$_POST['voucher_status'];
    $months_of_expiration =   $_POST['months_of_expiration'];
    
    $cur_datetime	=	date("Y-m-d H:i:s");
    
    $sql	=	"UPDATE ".TBL_VOUCHERS." SET title = '".$title."', "
            . "description = '".$description."', actual_price = '".$actual_price."', "
            . "sale_price = '".$sale_price."', voucher_order = '".$voucher_order."', voucher_status = '".$voucher_status."', "
            . "max_redemptions = '".$max_redemptions."', "
            . "months_of_expiration = '".$months_of_expiration."', "
            . "modified = '".$cur_datetime."' WHERE id = '".$item_id."'";

    //echo $sql;die;
    $ref	=	query($dbHandle, $sql);
    
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
                                @unlink($_SERVER['DOCUMENT_ROOT'].UPLOAD_LOGO_DIR.$_POST['old_file_name']);
                                
                                $update_sql = "UPDATE ".TBL_VOUCHERS." SET voucher_image_file = '".$logo_image_file."' WHERE id = '".$item_id."'";
                                query($dbHandle, $update_sql);

                        }


                }

            }

        }
        
    }
    
    $flash->success('Voucher updated successfully.');
    header("Location:browse_vouchers.php");
    exit;
    
}

$id = (int)mysqli_real_escape_string($dbHandle, $_GET['id']);

$sql = "SELECT id,business_name FROM ".TBL_OUTLETS." WHERE id = '".$_SESSION['OUTLET']['Id']."'";

$ref	=	query($dbHandle, $sql);

$outlet		=	fetchOne($ref, 'Object');
//echo "<pre>";
//pr($outlet);die;

$sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE id = '".$id."' AND outlet_id = '".$outlet->id."'";

$ref	=	query($dbHandle, $sql);

$voucher		=	fetchOne($ref, 'Object');

if(!$voucher){
    
    $flash->warning('Voucher not found.');
    header("Location:browse_vouchers.php");
    exit;
    
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
            Edit Voucher
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
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="editVoucherFrm" enctype="multipart/form-data">
              
                          <input type="hidden" name="outlet_id" value="<?php echo $outlet->id; ?>">
                          <input type="hidden" name="item_id" value="<?php echo $voucher->id; ?>">
                          <input type="hidden" name="old_file_name" value="<?php echo $voucher->voucher_image_file; ?>">
                          
                      <div class="box-body">
                          
                        <div class="form-group required">
                            <label class="control-label">Title</label>
                            <input type="text" required="required" class="form-control" name="title" placeholder="" value="<?php echo $voucher->title; ?>">
                        </div>
                        
                        <div class="form-group required">
                            <label class="control-label">Description</label>
                            <textarea class="form-control" required="required" rows="3" name="description"><?php echo $voucher->description; ?></textarea>
                        </div> 
                         
                        <div class="form-group">
                            <label>Upload Image</label>
                            <input name="voucher_logo" type="file" id="">
                            <?php
                            if(!empty($voucher->voucher_image_file)){
                            ?>
                            <div style="margin-top:5px;"><image src="../timthumb.php?src=uploads/voucher_logo/<?php echo $voucher->voucher_image_file; ?>&w=130&h=133" /></div>
                            <?php
                            }
                            ?>
                        </div>  
                          
                        <div class="form-group required">
                            <label class="control-label">Actual Price</label>
                            <input type="text" required="required" class="form-control" name="actual_price" placeholder="" value="<?php echo $voucher->actual_price; ?>" style="width:25%;">
                        </div>
                          
                        <div class="form-group">
                            <label>Sale Price</label>
                            <input type="text" class="form-control" name="sale_price" id="" placeholder="" value="<?php echo $voucher->sale_price; ?>" style="width:25%;">
                        </div>
                        
                        <div class="form-group">
                            <label>Order</label>
                            <input type="text" class="form-control" name="voucher_order" id="" placeholder="eg. 1,2,3.." value="<?php echo $voucher->voucher_order; ?>" style="width:25%;">
                        </div>  
                        <div class="form-group">
                            <label>Maximum number of redepmtions <a href="#" data-toggle="tooltip" title="Maximum number of times the voucher can be redeemed."><i class="fa fa-question-circle"></i></a></label>
                            <input type="number" class="form-control" name="max_redemptions" id="" placeholder="Example: 1" value="<?php echo $voucher->max_redemptions; ?>" style="width:25%;">
                        </div> 

                        <div class="form-group">
                            <label>Voucher Expiration</label>
                            <select name="months_of_expiration" class="form-control" style="width:25%;">
                                <option value="1" <?php if($voucher->months_of_expiration == 1) echo 'selected'; ?>>1 Months</option>
                                <option value="3" <?php if($voucher->months_of_expiration == 3) echo 'selected'; ?>>3 Months</option>
                                <option value="6" <?php if($voucher->months_of_expiration == 6) echo 'selected'; ?>>6 Months</option>
                                <option value="9" <?php if($voucher->months_of_expiration == 9) echo 'selected'; ?>>9 Months</option>
                                <option value="12" <?php if($voucher->months_of_expiration == 12) echo 'selected'; ?>>1 Year</option>
                                <option value="24" <?php if($voucher->months_of_expiration == 24) echo 'selected'; ?>>2 Years</option>
                                <option value="36" <?php if($voucher->months_of_expiration == 36) echo 'selected'; ?>>3 Years</option>
                                <option value="48" <?php if($voucher->months_of_expiration == 48) echo 'selected'; ?>>4 Years</option>
                            </select>
                        </div>
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Voucher Status</label>
                            <select name="voucher_status" class="form-control">
                                <option value="1" <?php if($voucher->voucher_status == 1) echo 'selected'; ?>>Active</option>
                                <option value="0" <?php if($voucher->voucher_status == 0) echo 'selected'; ?>>Inactive</option>
                            </select>
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
    
    $("#editVoucherFrm").validate({

        submitHandler: function(form) {
                                
            $('input[type=submit]').attr('disabled', 'disabled');
            $('input[type=submit]').val("Saving..");
            form.submit();

        }

    });
  
});

</script>