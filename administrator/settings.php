<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveOT'])){
    
    
    $site_name              =   mysqli_real_escape_string($dbHandle, ucwords(trim($_POST['site_name'])));
    $contact_email          =   mysqli_real_escape_string($dbHandle, trim($_POST['contact_email']));
    $contact_phone          =   mysqli_real_escape_string($dbHandle, trim($_POST['contact_phone']));
    //$outlet_login_email     =   mysqli_real_escape_string($dbHandle, trim($_POST['outlet_login_email']));
    //$outlet_login_password  =   mysqli_real_escape_string($dbHandle, trim($_POST['outlet_login_password']));
    $default_currency  =   mysqli_real_escape_string($dbHandle, trim($_POST['default_currency']));
    $paypal_environment  =   mysqli_real_escape_string($dbHandle, trim($_POST['paypal_environment']));
    
    if($paypal_environment == 1){
        $paypal_service_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    }else{
        $paypal_service_url = 'https://www.paypal.com/cgi-bin/webscr';
    }
    //$paypal_service_url  =   mysqli_real_escape_string($dbHandle, trim($_POST['paypal_service_url']));
    //$paypal_ipn_url  =   mysqli_real_escape_string($dbHandle, trim($_POST['paypal_ipn_url']));
    
    $pdatetime	=	date("Y-m-d H:i:s");
    
    $sql	=	"UPDATE ".TBL_SETTINGS." SET site_name = '".$site_name."', "
                        . "contact_email = '".$contact_email."', "
                        . "contact_phone = '".$contact_phone."', "
                        . "default_currency = '".$default_currency."', "
                        . "sandbox = '".$paypal_environment."', "
                        . "paypal_service_url = '".$paypal_service_url."', "
                        . "modified = '".$pdatetime."' "
                        . "WHERE id = 1";

    //echo $sql;die;
    $ref	=	query($dbHandle, $sql);
    
    if($ref){
        
        $flash->success('Settings updated successfully.');
        header("Location:settings.php");
        exit;
        
    }
}

$sql = "SELECT * FROM ".TBL_SETTINGS." WHERE id = 1";

$ref	=	query($dbHandle, $sql);

$data		=	fetchOne($ref, 'Object');
//echo "<pre>";
//pr($data);die;

require_once("includes/header.php");
?>
<!-- Left side column. contains the logo and sidebar -->
<?php
require_once("includes/sidebar.php");
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Settings
            <small>Settings Manager</small>
          </h1>
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
                  
                  <?php
                  if(isset($_GET['msg']) && $_GET['msg'] == 'updated'){
                  ?>
                  <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Settings updated successfully.</div>
                  <?php
                  }
                  ?>
                  
                  <div class="box box-primary">
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="editSettingsFrm">
                          
                      <div class="box-body">
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Site Name</label>
                            <input type="text" required="required" class="form-control" name="site_name" placeholder="" value="<?php echo $data->site_name; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="exampleInputEmail1">Contact Email</label>
                            <input type="text" required="required" class="form-control" name="contact_email" placeholder="" value="<?php echo $data->contact_email; ?>">
                        </div>
                         
                        <div class="form-group">
                            <label for="exampleInputEmail1">Contact Phone</label>
                            <input type="text" required="required" class="form-control" name="contact_phone" placeholder="" value="<?php echo $data->contact_phone; ?>">
                        </div>
                          
<!--                        <div class="form-group">
                            <label for="exampleInputEmail1">Outlet Login Email</label>
                            <input type="text" required="required" class="form-control" name="outlet_login_email" placeholder="" value="<?php echo $data->outlet_login_email; ?>">
                        </div>
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Outlet Login Password</label>
                            <input type="text" required="required" class="form-control" name="outlet_login_password" placeholder="" value="<?php echo $data->outlet_login_password; ?>">
                        </div>-->
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Default Currency</label>
                            <input type="text" required="required" class="form-control" name="default_currency" placeholder="" value="<?php echo $data->default_currency; ?>">
                        </div>  
                        
                        <div class="form-group">
                            <label for="exampleInputEmail1">Paypal Environment</label>
                            <select name="paypal_environment" class="form-control">
                                <option value="1" <?php if($data->sandbox == 1){ echo 'selected'; } ?>>Sandbox</option>
                                <option value="0" <?php if($data->sandbox == 0){ echo 'selected'; } ?>>Live</option>
                            </select>
                        </div>  
                          
<!--                        <div class="form-group">
                            <label for="exampleInputEmail1">Paypal Service URL</label>
                            <input type="text" required="required" class="form-control" name="paypal_service_url" placeholder="" value="<?php //echo $data->paypal_service_url; ?>">
                        </div>-->
                          
<!--                        <div class="form-group">
                            <label for="exampleInputEmail1">Paypal IPN Verify URL</label>
                            <input type="text" required="required" class="form-control" name="paypal_ipn_url" placeholder="" value="<?php //echo $data->paypal_ipn_url; ?>">
                        </div>  -->
                          
                      </div>
                      
                      <div class="box-footer">
                            <button type="submit" name="saveOT" class="btn btn-primary">Update</button>
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
  $("#editSettingsFrm").validate();
});

</script>