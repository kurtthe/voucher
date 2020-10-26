<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveOT'])){
    
    $occasion    =   mysqli_real_escape_string($dbHandle, ucfirst(trim($_POST['occasion'])));
    $wish    =   mysqli_real_escape_string($dbHandle, ucwords(trim($_POST['wish'])));
//    $status         =   (int)$_POST['status'];
    $pdatetime	=	date("Y-m-d H:i:s");
    
    $sql	=	"INSERT INTO ".TBL_OCCASIONS." (title, wish, status, created, modified)
                            VALUES ('".$occasion."', '".$wish."', 1, '".$pdatetime."', '".$pdatetime."')";

    //echo $sql;die;
    $ref	=	mysqli_query($dbHandle, $sql) or die(mysqli_error($dbHandle));
    
    if($ref){
        
        $flash->success('Occasion added successfully.');
        header("Location:browse_occasion.php");
        
    }
}

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
            Add Occasion
            <small>Occasion Manager</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="browse_occasion.php"><i class="fa fa-bars"></i> Browse Occasion</a></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <!-- Info boxes -->
          <div class="row">
            
              <div class="col-md-12">
                  
                  <div class="box box-primary">
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="addMenuCategoryFrm">
                      
                      <div class="box-body">
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Title</label>
                            <input type="text" required="required" class="form-control" name="occasion" placeholder="Title">
                        </div>
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Occasion Wish</label>
                            <input type="text" required="required" class="form-control" name="wish" placeholder="Wish">
                        </div>  
                        
<!--                        <div class="form-group">
                            <label for="exampleInputEmail1">Status</label>
                            <select name="status" class="form-control">
                                <option value="1">Enable</option>
                                <option value="0">Disable</option>
                            </select>
                        </div>-->
                          
                      </div>
                      
                      <div class="box-footer">
                            <button type="submit" name="saveOT" class="btn btn-primary">Save</button>
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
  $("#addMenuCategoryFrm").validate();
});

</script>