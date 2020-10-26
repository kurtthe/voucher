<?php
require_once("../core/loader.php");

if(empty($_SESSION['OUTLET'])){
    
    header("Location:login.php");
    
}

$sql = "SELECT id,business_name FROM ".TBL_OUTLETS." WHERE id = '".$_SESSION['OUTLET']['Id']."'";

$ref	=	query($dbHandle, $sql);

$outlet		=	fetchOne($ref, 'Object');

if(isset($_GET['cmd']) && $_GET['cmd'] == 'delete'){
    
//    $sql = "DELETE FROM ".TBL_OUTLET_TYPES." WHERE id = '".$_GET['id']."'";
//    
//    $ref	=	mysqli_query($dbHandle, $sql) or die(mysqli_error($dbHandle));
//    
//    if($ref){
//        
//        header("Location:browse_outlet_type.php?msg=deleted");
//        
//    }
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
            Browse Vouchers
            <small>Voucher Manager</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="add_voucher.php"><i class="fa fa-plus-square"></i> Add Voucher</a></li>
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
                    
                    <div class="box">
<!--                <div class="box-header">
                  <h3 class="box-title">Data Table With Full Features</h3>
                </div> /.box-header -->
                <div class="box-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th width="30%">Title</th>
                        <th width="10%">Actual Price</th>
                        <th width="10%">Sale Price</th>
                        <th width="10%">Max Number of Redemptions</th>
                        <th width="10%">Order</th>
                        <th width="10%">Status</th>
                        <th width="10%">Months of Expiration</th>
                        <th width="10%">Expiry Date</th>
                        <th width="15%">Action</th>
                      </tr>
                    </thead>
                    
                    <tbody>
                        
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

            var dTbl    =   $('#example1').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "pagingType": "full_numbers",
                        "aoColumns": [
//                                        { "mData": "id", "bVisible":false, "bSearchable": false, "bRegex": false, "bSortable": false},
//                                        { "mData": "serial_no", "bSearchable": false, "bRegex": false, "bSortable": false },
                                        { "mData": "title", "bSearchable": true, "bRegex": false, "bSortable": true },
                                        { "mData": "actual_price", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                        { "mData": "sale_price", "bSearchable": true, "bRegex": false, "bSortable":true  },
                                        { "mData": "max_redemptions", "bSearchable": false, "bRegex": false, "bSortable":true  },
                                        { "mData": "voucher_order", "bSearchable": false, "bRegex": false, "bSortable":true  },
                                        { "mData": "voucher_status", "bSearchable": false, "bRegex": false, "bSortable":false  },
                                        { "mData": "months_of_expiration", "bSearchable": true, "bRegex": false, "bSortable": true },
                                        { "mData": "expiry_date", "bSearchable": false, "bRegex": false, "bSortable": true },
                                        { "mData": "actions", "bSearchable": false, "bRegex": false, "bSortable": false  }
                                  ],
                        "ajax": "../core/datatables/outlet_browse_vouchers.php?id="+<?php echo $outlet->id; ?>,
                        "bAutoWidth":false
            });
        
                    
        
      });
    </script>