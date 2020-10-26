<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveOT'])){
    
    
    $current_password       =   mysqli_real_escape_string($dbHandle, trim($_POST['current_password']));
    $new_password           =   mysqli_real_escape_string($dbHandle, trim($_POST['new_password']));
    $confirm_password       =   mysqli_real_escape_string($dbHandle, trim($_POST['confirm_password']));
    
    $pdatetime	=	date("Y-m-d H:i:s");
    
    $sql = "SELECT * FROM ".TBL_ADMIN." WHERE id = 1";

    $ref	=	query($dbHandle, $sql);

    $data       =	fetchOne($ref, 'Object');
    
    $p      =   $data->password;
    $p_salt =   $data->psalt;
    
    $site_salt      =   "sdetc456F^*&_dfer";
    $salted_hash    =   hash('sha256',$current_password.$site_salt.$p_salt);
    
    if($new_password != $confirm_password){
        
        $flash->warning('Confirm password not matched.');
        header("Location:change_password.php");
        exit;
        
    }elseif(strlen($new_password) < 8){
        
        $flash->warning('New password should not less than 8 characters.');
        header("Location:change_password.php");
        exit;
        
    }elseif($p != $salted_hash){
        
        $flash->warning('Current password is wrong.');
        header("Location:change_password.php");
        exit;
        
    }else{
        
        $p_salt = rand_string(20);
        $salted_hash    =   hash('sha256',$new_password.$site_salt.$p_salt);
        
        $sql	=	"UPDATE ".TBL_ADMIN." SET password = '".$salted_hash."', "
                        . "psalt = '".$p_salt."', "
                        . "modified = '".$pdatetime."' "
                        . "WHERE id = 1";
        
        $ref	=	query($dbHandle, $sql);
        
        $flash->success('Password updated successfully.');
        header("Location:change_password.php");
        exit;
        
    }
    
}

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
            Change Password
            <small>Password Manager</small>
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
                if(isset($_GET['msg']) && $_GET['msg'] == 'notMatched'){
                ?>
                <div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Confirm password not matched.</div>
                <?php
                }
                ?>
                
                <?php
                if(isset($_GET['msg']) && $_GET['msg'] == 'shortPassword'){
                ?>
                <div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>New password should not less than 8 characters.</div>
                <?php
                }
                ?>
                
                <?php
                if(isset($_GET['msg']) && $_GET['msg'] == 'wrongPassword'){
                ?>
                <div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Current password is wrong.</div>
                <?php
                }
                ?>
                  
                <?php
                if(isset($_GET['msg']) && $_GET['msg'] == 'updated'){
                ?>
                <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Password updated successfully.</div>
                <?php
                }
                ?>
                  
                  <div class="box box-primary">
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="editPasswordFrm">
                          
                      <div class="box-body">
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Current Password</label>
                            <input type="password" required="required" class="form-control" name="current_password" placeholder="" value="">
                        </div>
                        
                        <div class="form-group">
                            <label for="exampleInputEmail1">New Password</label>
                            <input type="password" required="required" class="form-control" name="new_password" placeholder="" value="">
                        </div>
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Confirm New Password</label>
                            <input type="password" required="required" class="form-control" name="confirm_password" placeholder="" value="">
                        </div>  
                          
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
  $("#editPasswordFrm").validate();
});

</script>