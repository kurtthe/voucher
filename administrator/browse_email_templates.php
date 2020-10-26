<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

$sql = "SELECT * FROM ".TBL_EMAIL_TEMPLATES." WHERE 1=1";
    
$ref	=	query($dbHandle, $sql);

$email_templates = fetchAll($ref, 'Object');

require_once("includes/header.php");
?>
<!-- Left side column. contains the logo and sidebar -->
<?php
require_once("includes/sidebar.php");
?>
<!-- DataTables -->
<link rel="stylesheet" href="../assets/plugins/datatables/dataTables.bootstrap.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Browse Email Templates
            <small>Email Template Manager</small>
          </h1>
<!--          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
          </ol>-->
        </section>

        <!-- Main content -->
        <section class="content">
          
            <div class="row">
                <div class="col-xs-12">
                    <?php
                    // Display the messages
                    $flash->display();
                    ?>
                    <?php
                    if(isset($_GET['msg']) && $_GET['msg'] == 'succ'){
                    ?>
                    <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Email Template added successfully.</div>
                    <?php
                    }
                    ?>
                    
                    <?php
                    if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'){
                    ?>
                    <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Email Template deleted successfully.</div>
                    <?php
                    }
                    ?>
                    
                    <?php
                    if(isset($_GET['msg']) && $_GET['msg'] == 'updated'){
                    ?>
                    <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Email Template updated successfully.</div>
                    <?php
                    }
                    ?>
                    
                    <?php
                    if(isset($_GET['msg']) && $_GET['msg'] == 'notFound'){
                    ?>
                    <div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Email Template not found.</div>
                    <?php
                    }
                    ?>
                    <div class="box">
<!--                <div class="box-header">
                  <h3 class="box-title">Data Table With Full Features</h3>
                </div> /.box-header -->
                <div class="box-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th width="80%">Email Type</th>
                        <th width="20%">Action</th>
                      </tr>
                    </thead>
                    
                    <tbody>
                      <?php
                      foreach ( $email_templates as $email_template ){
                      ?>
                        <tr>
                            <td><?php echo $email_template->type; ?></td>
                            
                            <td>
                                <a href="edit_email_template.php?id=<?php echo $email_template->id; ?>" title="Edit" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i></a>
                                
                            </td>
                        </tr>  
                      <?php
                      }
                      ?>  
                    </tbody>
                    
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
                    
                </div>
            </div>
            
        </section><!-- /.content -->
      </div>

      <?php
      require_once("includes/footer.php");
      ?>

<!-- DataTables -->
    <script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
    
    <script>
      $(function () {
          
        $("#example1").DataTable({
            
            columnDefs: [
                { orderable: false, targets: -1 }
                ]
            
        });
        
      });
    </script>