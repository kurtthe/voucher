<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveOT'])){
    
    
    $name              =   mysqli_real_escape_string($dbHandle, ucwords(trim($_POST['name'])));
    $email          =   mysqli_real_escape_string($dbHandle, trim($_POST['email']));
    
    $pdatetime	=	date("Y-m-d H:i:s");
    
    $sql	=	"UPDATE ".TBL_ADMIN." SET name = '".$name."', "
                        . "email = '".$email."', "
                        . "modified = '".$pdatetime."' "
                        . "WHERE id = 1";

    //echo $sql;die;
    $ref	=	query($dbHandle, $sql);
    
    if($ref){
        
        $flash->success('Profile updated successfully.');
        header("Location:profile.php");
        exit;
        
    }
}

$sql = "SELECT * FROM ".TBL_ADMIN." WHERE id = 1";

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
            Profile
            <small>Profile Manager</small>
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
                  <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Profile updated successfully.</div>
                  <?php
                  }
                  ?>
                  
                  <div class="box box-primary">
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="editProfileFrm">
                          
                      <div class="box-body">
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Name</label>
                            <input type="text" required="required" class="form-control" name="name" placeholder="" value="<?php echo $data->name; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email</label>
                            <input type="text" required="required" class="form-control" name="email" placeholder="" value="<?php echo $data->email; ?>">
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
  $("#editProfileFrm").validate();
});

</script>