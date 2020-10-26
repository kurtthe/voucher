<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST) && isset($_POST['saveOT'])){
    
    $item_id            =   $_POST['item_id'];
    $subject              =   mysqli_real_escape_string($dbHandle, trim($_POST['subject']));
    $body        =   trim($_POST['body']);
    $from_email      =   mysqli_real_escape_string($dbHandle, trim($_POST['from_email']));
    $pdatetime          =   date("Y-m-d H:i:s");
    
    $sql	=	"UPDATE ".TBL_EMAIL_TEMPLATES." SET subject = '".$subject."', "
                        . "body = '".$body."', "
                        . "from_email = '".$from_email."', "
                        . "modified = '".$pdatetime."' "
                        . "WHERE id = '".$item_id."'";

    //echo $sql;die;
    $ref	=	query($dbHandle, $sql);
    
    if($ref){
        
        $flash->success('Email Template updated successfully.');
        header("Location:browse_email_templates.php");
        exit;
        
    }
    
}

$id = (int)mysqli_real_escape_string($dbHandle, $_GET['id']);
$sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE id = '".$id."'";

$ref	=	query($dbHandle, $sql);

$data		=	fetchOne($ref, 'Object');
//echo "<pre>";
//pr($data);die;

if(!$data){
    
    $flash->warning('Email Template not found.');
    header("Location:browse_email_templates.php");
    
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
            Edit Email Template
            <small>Email Template Manager</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="browse_email_templates.php"><i class="fa fa-bars"></i> Browse Email Template</a></li>
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
                            <label for="exampleInputEmail1">Subject</label>
                            <input type="text" required="required" class="form-control" name="subject" placeholder="" value="<?php echo $data->subject; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="exampleInputEmail1">Body</label>
                            <textarea name="body" required="required" id="editor1" rows="10" class="form-control"><?php echo $data->body; ?></textarea>
                        </div>  
                          
                        <div class="form-group">
                            <label for="exampleInputEmail1">Sender Email</label>
                            <input type="text" required="required" class="form-control" name="from_email" placeholder="" value="<?php echo $data->from_email; ?>">
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

<!-- CK Editor -->
<script src="https://cdn.ckeditor.com/4.4.3/standard/ckeditor.js"></script>

<script>
    
$(function () {
  
  CKEDITOR.replace('editor1');
  
  $("#editTemplateFrm").validate({
      
      submitHandler: function(form) {
          
            $('input[type=submit]').attr('disabled', 'disabled');
            $('input[type=submit]').val("Saving..");
            form.submit();

        }
      
  });
  
});

</script>