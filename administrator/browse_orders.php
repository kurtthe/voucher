<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

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
<link rel="stylesheet" href="../assets/plugins/datatables/jquery.dataTables.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="../assets/plugins/datatables/dataTables.tableTools.css">
<link rel="stylesheet" href="../assets/plugins/daterangepicker/daterangepicker-bs3.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Browse Orders
            <small>Order Manager</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="#"><i class="fa fa-bars"></i> Browse Outlets</a></li>
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
                  <table id="example1" class="table table-bordered table-striped display nowrap">
                    <thead>
                        <tr>
                            <td width="100%" colspan="13" style=" text-align: left;">
                                <input type="text" class="form-control" data-column="1" id="date-range" placeholder="Search By Date" onkeydown="return false">

                            </td>
                        </tr>  
                        <tr>
                          <th width="10%">Order #</th>
                          <th width="10%">Date</th>
                          <th width="5%">Amount</th>
                          <th width="5%">Outlet</th>
                          <th width="10%">Purchaser Name</th>
                          <th width="20%">Purchaser Email</th>
                          <th width="10%">Purchaser Mobile</th>
                          <th width="10%">Purchaser Suburb</th>
                          <th width="4%">Type</th>
                          <th width="4%">Status</th>
                          <th width="12%">Action</th>
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
    <script src="../assets/plugins/datatables/dataTables.tableTools.js"></script>
    <script src="../assets/plugins/daterangepicker/moment.min.js"></script>
    <script src="../assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="../assets/plugins/datatables/shCore.js"></script>
    <script src="../assets/plugins/datatables/demo.js"></script>
    
    
<script>
$(function () {

    var dTbl    =   $('#example1').DataTable({
                "scrollX": true,
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "aoColumns": [
    //                                        { "mData": "id", "bVisible":false, "bSearchable": false, "bRegex": false, "bSortable": false},
    //                                        { "mData": "serial_no", "bSearchable": false, "bRegex": false, "bSortable": false },
                                { "mData": "order_no", "bSearchable": true, "bRegex": false, "bSortable": true },
                                { "mData": "created", "bSearchable": true, "bRegex": false, "bSortable": true },
                                { "mData": "submitted_amount", "bSearchable": false, "bRegex": false, "bSortable": true },
                                { "mData": "business_name", "bSearchable": false, "bRegex": false, "bSortable": true },
                                { "mData": "purchaser_name", "bSearchable": true, "bRegex": false, "bSortable": true },
                                { "mData": "purchaser_email", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                { "mData": "purchaser_mobile", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                { "mData": "suburb", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                { "mData": "purchased_for", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                { "mData": "payment_status", "bSearchable": true, "bRegex": false, "bSortable":true  },
                                { "mData": "actions", "bSearchable": false, "bRegex": false, "bSortable": false  }
                          ],
                "saveState": true,          
                "ajax": "../core/datatables/admin_browse_orders.php",
                "bAutoWidth":false,
                "order": [[ 1, "desc" ]],
                dom: 'T<"clear">lfrtip',
                deferRender: true,
                tableTools: {
                    "sSwfPath": "../assets/plugins/datatables/swf/copy_csv_xls_pdf.swf",
                    "aButtons": [
                                    {
                                        "sExtends": "csv",
                                        "sTitle": "Payment Details - Voucher Plus",
                                        "sButtonText": "Download CSV",
                                        "mColumns": [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ]
                                    }
                                ]
                }
                
            });
                    
    $('#date-range').daterangepicker({ 
		
            format      : 'DD/MM/YYYY',

            maxDate 	: '<?php echo date("d-m-Y"); ?>'
            
    });  
    
    $(".applyBtn").click( function(){
            
            var i               =	$('#date-range').attr('data-column'); 
			
            var start_date 	= 	$('#date-range').data('daterangepicker').startDate;

            var end_date	=	$('#date-range').data('daterangepicker').endDate;

            var dateRange       = 	[ start_date, end_date ];

            dTbl.columns(i).search(dateRange).draw();

            
    });
        
});
</script>