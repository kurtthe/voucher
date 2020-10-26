<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_GET['itemid']) && !empty($_GET['itemid'])){
    
    $id = (int)$_GET['itemid'];
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE id = '".$id."'";

    $ref	=	query($dbHandle, $sql);
    $outlet	=	fetchOne($ref, 'Object');
    
    $sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$outlet->suburb_id."'";
    $ref	=	query($dbHandle, $sql);
    $suburb	=	fetchOne($ref, 'Object');
    
    $ot_arr = explode(' ', strtolower($outlet->business_name));
    $ot = urlencode(implode('-', $ot_arr));
    $access_url = BASE_URL.'/vouchers.php?ot='.$ot.'&suburb='.urlencode(strtolower($suburb->suburb));
    
    $return = array();
    
    $return['business_name'] = ucWords($outlet->business_name);
    $return['access_url'] = $access_url;
    $return['email'] = $outlet->email;
    $return['password'] = $outlet->outlet_password;
    
    echo json_encode($return);
    exit;
    
}

$sql = "SELECT id,business_name,account_status FROM ".TBL_OUTLETS." WHERE 1=1";

$ref	=	query($dbHandle, $sql);

$outlets = fetchAll($ref, 'Object');

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
            Browse Outlets
            <small>Outlet Manager</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="add_outlet.php"><i class="fa fa-plus-square"></i> Add Outlet</a></li>
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
                        <th width="25%">Business Name</th>
                        <th width="20%">Email</th>
                        <th width="20%">Paypal Account</th>
                        <th width="10%">Status</th>
                        <th width="25%">Action</th>
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

<div class="modal fade" id="accessUrlModal" role="dialog">
    <div class="modal-dialog">
    
      
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="outletInfoHeader">Outlet Info</h4>
        </div>
        <div class="modal-body" id="outletInfoBody">
          <div class="form-group">
            <label for="recipient-name" class="form-control-label">Access URL</label>
            <p style="font-size:16px;">Alok Ranjan</p>
          </div>
          <div class="form-group">
            <label for="message-text" class="form-control-label">Email</label>
            <p style="font-size:16px;">Alok Ranjan</p>
          </div>
          <div class="form-group">
            <label for="message-text" class="form-control-label">Password</label>
            <p style="font-size:16px;">Alok Ranjan</p>
          </div>  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          
        </div>
      </div>
      
    </div>
  </div>

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
                                        { "mData": "business_name", "bSearchable": true, "bRegex": false, "bSortable": true },
                                        { "mData": "email", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                        { "mData": "paypal_account", "bSearchable": true, "bRegex": false, "bSortable":true  },
                                        { "mData": "account_status", "bSearchable": false, "bRegex": false, "bSortable":false  },
                                        { "mData": "actions", "bSearchable": false, "bRegex": false, "bSortable": false  }
                                  ],
                        "ajax": "../core/datatables/admin_browse_outlets.php",
                        "bAutoWidth":false
            });
        
            $("#example1").on('click', '.access-url-modal' ,function() {
            //$(".access-url-modal").click(function() {    
                
                var itemid = parseInt($(this).attr('itemid'));
                
                $.ajax({
                    type: "GET",
                    url: "browse_outlets.php",
                    data: {'itemid':itemid},
                    success: function(result) {

                        result = jQuery.parseJSON(result);
                        
                        var htmlElement = '';
                        
                        htmlElement += '<div class="form-group"><label for="recipient-name" class="form-control-label">Access URL</label><p style="font-size:16px;">'+result.access_url+'</p></div>';
                        
                        htmlElement += '<div class="form-group"><label for="recipient-name" class="form-control-label">Email</label><p style="font-size:16px;">'+result.email+'</p></div>';
                        
                        if(result.password != ''){
                            htmlElement += '<div class="form-group"><label for="recipient-name" class="form-control-label">Password</label><p style="font-size:16px;">'+result.password+'</p></div>';
                        }
                        
                        $('#outletInfoHeader').html('');
                        $('#outletInfoHeader').html(result.business_name);
                        $('#outletInfoBody').html('');
                        $('#outletInfoBody').html(htmlElement);
                        
                        $('#accessUrlModal').modal('show');

                    }
                });
                
            });        
        
      });
    </script>