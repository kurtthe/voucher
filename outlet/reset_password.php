<?php
require_once("../core/loader.php");

if(!empty($_SESSION['OUTLET'])){
    
    header("Location:browse_purchased_vouchers.php");
    
}

if(isset($_POST) && isset($_POST['resetPasswordBtn'])){
    
    $key =     mysqli_real_escape_string($dbHandle, trim($_POST['key']));
    $new_password =     mysqli_real_escape_string($dbHandle, trim($_POST['new_password']));
    $confirm_password =     mysqli_real_escape_string($dbHandle, trim($_POST['confirm_password']));
    
    $pdatetime	=	date("Y-m-d H:i:s");
    $now = strtotime(date("Y-m-d"));
    
    if($new_password != $confirm_password){
        
        $flash->warning('New password and confirm new password not matched.');
        
        header("Location:reset_password.php?key=".$key);
        exit;
        
    }elseif(strlen($new_password) < 6){
        
        $flash->warning('Password should atleast 6 characters long.');
        
        header("Location:reset_password.php?key=".$key);
        exit;
        
    }else{
        
        $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE password_reset_key = '".$key."'";

        $ref	=	query($dbHandle, $sql);

        $data       =	fetchOne($ref, 'Object');
        
        if(!$data){
            
            $flash->error('Invalid reset key.');
        
            header("Location:reset_password.php?key=".$key);
            exit;
            
        }else if($data->account_status == 0){
            
            $flash->error('Outlet is blocked by the administrator.');
        
            header("Location:reset_password.php?key=".$key);
            exit;
            
        }else if($now > strtotime(date("Y-m-d", strtotime($data->password_reset_request_date. ' + 3 days')))){
            
            $flash->error('Password reset request date expired.');
        
            header("Location:reset_password.php?key=".$key);
            exit;
            
        }else{
            
            $p_salt = rand_string(20);
            $site_salt      =   "ds%DSs23@dVB_his2&ff";
            $salted_hash    =   hash('sha256',$new_password.$site_salt.$p_salt);

            $sql	=	"UPDATE ".TBL_OUTLETS." SET outlet_password = '', password = '".$salted_hash."', "
                            . "psalt = '".$p_salt."', password_reset_key = '', password_reset_request_date = '', "
                            . "password_update_date = '".$pdatetime."', modified = '".$pdatetime."' "
                            . "WHERE id = '".$data->id."'";

            $ref	=	query($dbHandle, $sql);

            $flash->success('Password updated successfully.');
            
            header("Location:login.php");
            exit;
            
        }
        
    }
    
}

if(empty($_GET) || empty($_GET['key'])){
    
    header("Location:login.php");
    exit;
    
}

$key =     mysqli_real_escape_string($dbHandle, trim($_GET['key']));
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Outlet | Reset Password</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../assets/plugins/iCheck/square/blue.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <b>Online</b> Order Management
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">
            <?php
            // Display the messages
            $flash->display();
            ?>
        </p>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" id="resetPasswordFrm">
            
            <input type="hidden" name="key" value="<?php echo $key; ?>">
            
          <div class="form-group has-feedback">
              <input type="password" name="new_password" id="new_password" required="required" class="form-control" placeholder="New Passsword">
            
          </div>
            
          <div class="form-group has-feedback">
              <input type="password" name="confirm_password" required="required" class="form-control" placeholder="Confirm New Passsword">
            
          </div>  
          
          <div class="row">
            <div class="col-xs-8">
<!--              <div class="checkbox icheck">
                <label>
                  <input type="checkbox"> Remember Me
                </label>
              </div>-->
            </div><!-- /.col -->
            <div class="col-xs-4">
<!--                <button name="signinBtn" type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>-->
                <button type="submit" name="resetPasswordBtn" class="btn btn-primary btn-block btn-flat">Submit</button>
            </div><!-- /.col -->
          </div>
        </form>

<!--        <div class="social-auth-links text-center">
          <p>- OR -</p>
          <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
          <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>
        </div> /.social-auth-links -->

        <a href="login.php">Back to login page</a><br>
    <!--    <a href="register.html" class="text-center">Register a new membership</a>-->

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="../assets/plugins/jQuery-2.1.4.min.js"></script>
    <!-- jQuery Validate -->
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="../assets/plugins/iCheck/icheck.min.js"></script>
    <script>
      $(function () {
          
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
        
        $("#resetPasswordFrm").validate({
            
            rules: {
               new_password: { 
                 required: true,
                    minlength: 6

               } , 

                   confirm_password: { 
                    equalTo: "#new_password",
                     minlength: 6
               }


           },
            submitHandler: function(form) {
                
                $('input[type=submit]').attr('disabled', 'disabled');
                //$('input[type=submit]').val("Saving..");
                form.submit();
                
            }
            
        });
      });
    </script>
  </body>
</html>
