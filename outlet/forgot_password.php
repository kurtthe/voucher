<?php
require_once("../core/loader.php");

if(!empty($_SESSION['OUTLET'])){
    
    header("Location:browse_purchased_vouchers.php");
    
}

if(isset($_POST) && isset($_POST['forgotPasswordBtn'])){
    
    $email =     mysqli_real_escape_string($dbHandle, trim($_POST['email']));
    
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE email = '".$email."' and account_status = 1";

    $ref	=	query($dbHandle, $sql);

    if(numRows($ref)){
        
        $data       =	fetchOne($ref, 'Object');
    
        $outlet_id      =   $data->id;
        $outlet_email =   $data->email;
        
        $reset_key = strtoupper(uniqid(rand_string(5)));
        
        $request_date = date("Y-m-d h:i:s");
        
        $updateSQL = "UPDATE ".TBL_OUTLETS." SET password_reset_key = '".$reset_key."', password_reset_request_date = '".$request_date."' WHERE id = '".$outlet_id."'";
        
        query($dbHandle, $updateSQL);
        
        $resetUrl = BASE_URL.'/outlet/reset_password.php?key='.$reset_key;
        
        
        $sql = "SELECT * FROM ".TBL_SETTINGS." WHERE id = 1";

        $ref	=	query($dbHandle, $sql);
        $settings		=	fetchOne($ref, 'Object');
        
        /*Send reset password Email to user
        * 
        */

        $sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE id = 6";

        $ref	=	query($dbHandle, $sql);
        $email_template	=	fetchOne($ref, 'Object');

        $subject    = $email_template->subject;
        $body       = $email_template->body;

        $subject	=   str_replace('{{site_name}}', $settings->site_name, $subject);
        
        $body	=   str_replace('{{business_forgoturi}}', $resetUrl, $body);

        $body       =   str_replace('{{site_name}}', $settings->site_name, $body);
    
        $from       =   $email_template->from_email;
        $to         =   $outlet_email;

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:" . $from;

        try{

            mail($to,$subject,$body, $headers);

        }catch(Exception $e) {

            echo 'Message: ' .$e->getMessage();

        }
        
        $flash->success('Password reset link sent to your email address.');
        
        header("Location:forgot_password.php");
        exit;
       
        
    }else{
        
        $flash->error('Email not found.');
        
        header("Location:forgot_password.php");
        exit;
        
    }
    
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Outlet | Forgot Password</title>
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
        <b>Voucher</b> Plus
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">
            <?php
            // Display the messages
            $flash->display();
            ?>
        </p>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" id="OutletForgotFrm">
          <div class="form-group has-feedback">
              <input name="email" required="required" type="email" class="form-control" placeholder="Email">
            
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
                <button type="submit" name="forgotPasswordBtn" class="btn btn-primary btn-block btn-flat">Submit</button>
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
        
        $("#OutletForgotFrm").validate({
            
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
