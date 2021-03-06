<?php
require_once("../core/loader.php");

if(!empty($_SESSION['OUTLET'])){
    
    header("Location:browse_purchased_vouchers.php");
    
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Outlet | Log in</title>
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
        <b>Voucher</b> Plus Outlet
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">
            <?php
            // Display the messages
            $flash->display();
            ?>
            <?php
            if(isset($_GET['msg']) && $_GET['msg'] == 'invalid'){
                echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Username or Password is Incorrect.</div>';
            }
            if(isset($_GET['msg']) && $_GET['msg'] == 'success'){
                echo '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>You have logged out successfully.</div>';
            }
            ?>
        </p>
        <form action="do_login.php" method="post" id="OutletLoginFrm">
          <div class="form-group has-feedback">
              <input name="email" required="required" type="email" class="form-control" placeholder="Email">
            
          </div>
          <div class="form-group has-feedback">
            <input name="password" required="required" type="password" class="form-control" placeholder="Password">
            
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
                <input type="submit" name="signinBtn" class="btn btn-primary btn-block btn-flat" value="Sign In">
            </div><!-- /.col -->
          </div>
        </form>

<!--        <div class="social-auth-links text-center">
          <p>- OR -</p>
          <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>
          <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>
        </div> /.social-auth-links -->

        <a href="forgot_password.php">I forgot my password</a><br>
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
        
        $("#OutletLoginFrm").validate({
            
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
