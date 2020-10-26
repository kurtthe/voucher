<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveOT'])){
    
    $item_id = $_POST['item_id'];
    $occasion    =   mysqli_real_escape_string($dbHandle, ucfirst(trim($_POST['occasion'])));
    $wish    =   mysqli_real_escape_string($dbHandle, ucwords(trim($_POST['wish'])));
    //$status         =   (int)$_POST['status'];
    $pdatetime	=	date("Y-m-d H:i:s");
    
    $sql	=	"UPDATE ".TBL_OCCASIONS." SET title = '".$occasion."', wish = '".$wish."', "
                        . "modified = '".$pdatetime."' "
                        . "WHERE id = '".$item_id."'";

    //echo $sql;die;
    $ref	=	query($dbHandle, $sql);
    
    if($ref){
        
        $flash->success('Occasion updated successfully.');
        header("Location:browse_occasion.php");
        exit;
        
    }
}

$id = (int)mysqli_real_escape_string($dbHandle, $_GET['id']);
$sql = "SELECT id,title,wish,status FROM ".TBL_OCCASIONS." WHERE id = '".$id."'";

$ref	=	query($dbHandle, $sql);

$data		=	fetchOne($ref, 'Object');
//echo "<pre>";
//pr($data);die;

if(!$data){
    
    $flash->warning('Occasion not found.');
    header("Location:browse_occasion.php");
    
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
            Edit Occasion
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
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="editMenuCategoryFrm">
                      
                          <input type="hidden" name="item_id" value="<?php echo $data->id; ?>">
                          
                      <div class="box-body">
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Title</label>
                            <input type="text" required="required" class="form-control" name="occasion" placeholder="Title" value="<?php echo $data->title; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="exampleInputEmail1">Occasion Wish</label>
                            <input type="text" required="required" class="form-control" name="wish" placeholder="Wish" value="<?php echo $data->wish; ?>">
                        </div>  
                          
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
  $("#editMenuCategoryFrm").validate();
});

</script>