<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveOT'])){
    
    $item_id            =   $_POST['item_id'];
    $body               =   trim($_POST['body']);
    $pdatetime          =   date("Y-m-d H:i:s");
    
    $sql	=	"UPDATE ".TBL_SMS_TEMPLATES." SET body = '".$body."', "
                        . "modified = '".$pdatetime."' "
                        . "WHERE id = '".$item_id."'";

    //echo $sql;die;
    $ref	=	query($dbHandle, $sql);
    
    if($ref){
        
        header("Location:browse_sms_templates.php?msg=updated");
        exit;
        
    }
    
}

$id = (int)mysqli_real_escape_string($dbHandle, $_GET['id']);
$sql = "SELECT * FROM ".TBL_SMS_TEMPLATES." WHERE id = '".$id."'";

$ref	=	query($dbHandle, $sql);

$data		=	fetchOne($ref, 'Object');
//echo "<pre>";
//pr($data);die;

if(!$data){
    
    header("Location:browse_sms_templates.php?msg=notFound");
    
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
            Edit SMS Template
            <small>SMS Template Manager</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="browse_sms_templates.php"><i class="fa fa-bars"></i> Browse SMS Template</a></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <!-- Info boxes -->
          <div class="row">
            
              <div class="col-md-12">
                  
                  <div class="box box-primary">
                      
                      <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="editTemplateFrm">
                      
                          <input type="hidden" name="item_id" value="<?php echo $data->id; ?>">
                          
                      <div class="box-body">
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">SMS Type</label>
                            <span><?php echo $data->type; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="exampleInputEmail1">Body</label>
                            <textarea name="body" required="required" rows="5" class="form-control"><?php echo $data->body; ?></textarea>
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
  
  $("#editTemplateFrm").validate({
      
      submitHandler: function(form) {
          
            $('input[type=submit]').attr('disabled', 'disabled');
            $('input[type=submit]').val("Saving..");
            form.submit();

        }
      
  });
  
});

</script>