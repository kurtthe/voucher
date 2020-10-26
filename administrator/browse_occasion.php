<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

$sql = "SELECT id,title,status FROM ".TBL_OCCASIONS." WHERE 1=1";
    
$ref	=	mysqli_query($dbHandle, $sql) or die(mysqli_error($dbHandle));

$occasions = array();

while($data	=	mysqli_fetch_object($ref)){
			
        $occasions[]	=	$data;

}

if(isset($_GET['cmd']) && $_GET['cmd'] == 'delete'){
    
    $sql = "DELETE FROM ".TBL_OCCASIONS." WHERE id = '".$_GET['id']."'";
    
    $ref	=	mysqli_query($dbHandle, $sql) or die(mysqli_error($dbHandle));
    
    if($ref){
        
        $flash->success('Occasion deleted successfully.');
        header("Location:browse_occasion.php");
        
    }
}

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
            Browse Occasion
            <small>Occasion Manager</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="add_occasion.php"><i class="fa fa-plus-square"></i> Add Occasion</a></li>
          </ol>
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
                    <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Occasion added successfully.</div>
                    <?php
                    }
                    ?>
                    
                    <?php
                    if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'){
                    ?>
                    <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Occasion deleted successfully.</div>
                    <?php
                    }
                    ?>
                    
                    <?php
                    if(isset($_GET['msg']) && $_GET['msg'] == 'updated'){
                    ?>
                    <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Occasion updated successfully.</div>
                    <?php
                    }
                    ?>
                    
                    <?php
                    if(isset($_GET['msg']) && $_GET['msg'] == 'notFound'){
                    ?>
                    <div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>Occasion not found.</div>
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
                        <th width="80%">Title</th>
                        <th width="20%">Action</th>
                      </tr>
                    </thead>
                    
                    <tbody>
                      <?php
                      foreach ( $occasions as $occasion ){
                      ?>
                        <tr>
                            <td><?php echo $occasion->title; ?></td>
                            
                            <td>
                                <a href="edit_occasion.php?id=<?php echo $occasion->id; ?>" title="Edit" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i></a>
<!--                                &nbsp;
                                <a href="#" title="Delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash-o"></i></a>-->
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