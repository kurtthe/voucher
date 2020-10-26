<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

$id = (int)mysqli_real_escape_string($dbHandle, $_GET['id']);

$sql = "SELECT * FROM ".TBL_ORDERS." WHERE id = '".$id."'";

$ref	=	query($dbHandle, $sql);
$order		=	fetchOne($ref, 'Object');
//pr($data);die;
if(!$order){
    
    header("Location:browse_orders.php?msg=notFound");
    
}

$sql = "SELECT * FROM ".TBL_OUTLETS." WHERE id = '".$order->outlet_id."'";

$ref	=	query($dbHandle, $sql);
$outlet		=	fetchOne($ref, 'Object');

$sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$order->purchaser_suburb_id."'";
$ref	=	query($dbHandle, $sql);
$purchaser_suburb	=	fetchOne($ref, 'Object');

$sql = "SELECT sv.price, sv.voucher_no, v.title FROM ".TBL_SOLD_VOUCHERS." sv LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id WHERE order_no = '".$order->order_no."'";

$ref	=	query($dbHandle, $sql);
$sold_vouchers	=	fetchAll($ref, 'Object');

if($order->purchased_for == 'Other'){
    
    $sql = "SELECT * FROM ".TBL_OCCASIONS." WHERE id = '".$order->occasion_id."'";

    $ref	=	query($dbHandle, $sql);
    $occasion		=	fetchOne($ref, 'Object');
    
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
<link rel="stylesheet" href="../assets/plugins/datatables/dataTables.bootstrap.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Order Details
            <small>Order Manager</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="browse_orders.php"><i class="fa fa-bars"></i> Back To Orders</a></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          
            <div class="row">
                <div class="col-xs-12">
                    
                    <div class="box">
<!--                <div class="box-header">
                  <h3 class="box-title">Data Table With Full Features</h3>
                </div> -->
                <div class="box-body">
                  
                    <div class="row">
                        
                        <div class="col-sm-6">
                            <table class="table table-bordered table-striped">
                                
                                <thead>
                                  <tr>
                                      <td width="40%"><h4>Order Details</h4></td>
                                    <td width="60%"></td>
                                  </tr>
                                </thead>
                                
                                <tbody>
                                    <tr><td><strong>Order #</strong></td><td><?php echo $order->order_no; ?></td></tr>
                                    <tr><td><strong>Date</strong></td><td><?php echo date("d/m/Y h : i A", strtotime($order->created)); ?></td></tr>
                                    <tr><td><strong>Status</strong></td><td><?php echo $order->payment_status; ?></td></tr>
                                    <tr><td><strong>Type</strong></td><td><?php echo $order->purchased_for; ?></td></tr>
                                    
                                    <tr><td><strong>Submitted Amount</strong></td><td>$<?php echo number_format($order->submitted_amount, 2); ?></td></tr>
                                    <tr><td><strong>Credited Amount</strong></td><td>$<?php echo number_format($order->credited_amount, 2); ?></td></tr>
                                    
                                    
                                    <?php
                                    if($order->payment_status == 'Verified'){
                                    ?>
                                    <tr><td><strong>Paypal Fee</strong></td><td><?php echo $order->fee; ?></td></tr>
                                    <tr><td><strong>Paypal Txn Id</strong></td><td><?php echo $order->txn_id; ?></td></tr>
                                    <?php
                                    }
                                    ?>
                                    
                                </tbody>
                                
                            </table>
                        </div><!-- /.col -->
                        
                        <div class="col-sm-6">
                            <table class="table table-bordered table-striped">
                                
                                <thead>
                                  <tr>
                                    <td width="40%"><h4>Voucher Details</h4></td>
                                    <td width="30%"></td>
                                    <td width="30%"></td>
                                  </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($sold_vouchers as $sold_voucher){
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $sold_voucher->title; ?></strong></td>
                                        <td><?php echo $sold_voucher->voucher_no; ?></td>
                                        <td>$<?php echo $sold_voucher->price; ?></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                        </div><!-- /.col -->
                        
                    </div>
                    
                    <div class="row">
                        
                        <div class="col-sm-6">
                            
                            <table class="table table-bordered table-striped">
                                <thead>
                                  <tr>
                                      <td width="30%"><h4>Contact Details</h4></td>
                                    <td width="70%"></td>
                                  </tr>
                                </thead>

                                <tbody>
                                    <tr><td><strong>Purchaser Name</strong></td><td><?php echo $order->purchaser_name; ?></td></tr>
                                    <tr><td><strong>Purchaser Email</strong></td><td><?php echo $order->purchaser_email; ?></td></tr>
                                    <tr><td><strong>Purchaser Mobile</strong></td><td><?php echo $order->purchaser_mobile; ?></td></tr>
                                    <tr><td><strong>Purchaser Suburb</strong></td><td><?php echo ucwords(strtolower($purchaser_suburb->suburb)).', '.$purchaser_suburb->state.' '.$purchaser_suburb->postcode; ?></td></tr>
                                </tbody>

                            </table>
                            
                        </div>
                        
                        
                        
                        
                    </div>
                    
                    <div class="row">
                        
                        <?php
                        if($order->purchased_for == 'Other'){
                        ?>
                        <div class="col-sm-6">
                            <table class="table table-bordered table-striped">
                                <thead>
                                  <tr>
                                      <td width="30%"><h4>Receiver Details</h4></td>
                                    <td width="70%"></td>
                                  </tr>
                                </thead>

                                <tbody>
                                    <tr><td><strong>Receiver Name</strong></td><td><?php echo $order->receiver_name; ?></td></tr>
                                    <tr><td><strong>Receiver Email</strong></td><td><?php echo $order->receiver_email; ?></td></tr>
                                    <tr><td><strong>Receiver Mobile</strong></td><td><?php echo $order->receiver_mobile; ?></td></tr>
                                    <tr><td><strong>Dispatch Date</strong></td><td><?php echo date('d M Y', strtotime($order->voucher_dispatch_date)); ?></td></tr>
                                    <tr><td><strong>Occasion</strong></td><td><?php echo $occasion->title; ?></td></tr>
                                    <tr><td><strong>Message</strong></td><td><?php echo $order->message; ?></td></tr>
                                </tbody>

                            </table>
                        </div>
                        <?php
                        }
                        ?>
                        
                        <div class="col-sm-6">
                            <table class="table table-bordered table-striped">
                                <thead>
                                  <tr>
                                      <td width="40%"><h4>Outlet Details</h4></td>
                                    <td width="60%"></td>
                                  </tr>
                                </thead>

                                <tbody>
                                    <tr><td><strong>Outlet Name</strong></td><td><?php echo ucwords($outlet->business_name); ?></td></tr>
                                    <tr><td><strong>Email</strong></td><td><?php echo $outlet->email; ?></td></tr>
                                    <tr><td><strong>Mobile</strong></td><td><?php echo $outlet->phone; ?></td></tr>
                                    
                                </tbody>

                            </table>
                        </div>
                        
                    </div>
                    
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
          
        
      });
    </script>