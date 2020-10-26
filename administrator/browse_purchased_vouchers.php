<?php
require_once("../core/loader.php");

if(empty($_SESSION['admin'])){
    
    header("Location:login.php");
    
}

if(isset($_POST['number_of_redemptions']) && isset($_POST['sold_voucher_id'])){
    $sold_voucher_id = (int)mysqli_real_escape_string($dbHandle, trim($_POST['sold_voucher_id']));
    $number_of_redemptions = (int)mysqli_real_escape_string($dbHandle, trim($_POST['number_of_redemptions']));
    
    if($number_of_redemptions > 0){
        $cur_datetime   =   date("Y-m-d H:i:s");
        $redeem_date = date("d/m/Y H:i A", strtotime($cur_datetime));
        $status = 2;
    }else{
        $cur_datetime = '';
        $redeem_date = 'NA';
        $status = 1;
    }
    
    $sql = "UPDATE ".TBL_SOLD_VOUCHERS." SET status = '".$status."', redeem_datetime = '".$cur_datetime."', number_of_redemptions = '".$number_of_redemptions."' WHERE id = '".$sold_voucher_id."'";
    
    $ref    =   query($dbHandle, $sql);
    
    $return = array();
    
    $return['sold_voucher_id'] = $sold_voucher_id;
    $return['status'] = $status;
    $return['redeem_date'] = $redeem_date;
    echo json_encode($return);
    exit();
}

/*if(isset($_POST['number_of_redemptions']) && $_POST['redem'] == 'yes'){
    
    $sold_voucher_id = (int)mysqli_real_escape_string($dbHandle, trim($_POST['sold_voucher_id']));
    $redeem = (int)mysqli_real_escape_string($dbHandle, trim($_POST['redeem']));
    
    if($redeem == 2){
        $cur_datetime	=	date("Y-m-d H:i:s");
        $redeem_date = date("d/m/Y H:i A", strtotime($cur_datetime));
    }else{
        $cur_datetime = '';
        $redeem_date = 'NA';
    }
    
    $sql = "UPDATE ".TBL_SOLD_VOUCHERS." SET status = '".$redeem."', redeem_datetime = '".$cur_datetime."' WHERE id = '".$sold_voucher_id."'";
    
    $ref	=	query($dbHandle, $sql);
    
    $return = array();
    
    $return['sold_voucher_id'] = $sold_voucher_id;
    $return['status'] = $redeem;
    $return['redeem_date'] = $redeem_date;
    
    echo json_encode($return);
    exit();
}*/

if(isset($_POST['resend']) && $_POST['resend'] == 'yes'){
    
    $sold_voucher_id = (int)mysqli_real_escape_string($dbHandle, trim($_POST['sold_voucher_id']));
    
    $sql = "SELECT sv.id, sv.voucher_no, sv.expiry_date, ot.business_name, ot.sender_email, "
        . "v.title, sv.price, o.purchaser_name, o.purchased_for, "
        . "o.purchaser_email, o.purchaser_mobile, o.receiver_name, "
        . "o.receiver_email, o.receiver_mobile, o.payment_status, "
        . "o.created, sv.redeem_datetime, sv.access_key, sv.status "
        . "FROM ".TBL_SOLD_VOUCHERS." sv "
        . "LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id "
        . "LEFT JOIN ".TBL_ORDERS." o ON o.order_no = sv.order_no "
        . "LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id "
        . "WHERE sv.id = '".$sold_voucher_id."'";
    
    $ref	=	query($dbHandle, $sql);
    $sold_voucher		=	fetchOne($ref, 'Object');
    
    if($sold_voucher->purchased_for == 'Self'){

        $to         =   $sold_voucher->purchaser_email;
        $u_mobile = $sold_voucher->purchaser_mobile;

    }else{

        $to         =   $sold_voucher->receiver_email;
        $u_mobile = $sold_voucher->receiver_mobile;

    }
    
    $return = array();
    
    $return['sold_voucher_id'] = $sold_voucher->id;
    $return['to'] = $to;
    $return['mobile'] = $u_mobile;
    
    echo json_encode($return);
    exit();
    
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

<style>
    
    tr.highlight_pink{
        background-color: #F4C2C2 !important;
    }
    td.highlight_pink_cl {
        background-color: #F4C2C2 !important;
    }
    tr.highlight_blue{
        background-color: #89CFF0 !important;
    }
    td.highlight_blue_cl {
        background-color: #89CFF0 !important;
    }

    .tooltip-inner {
        max-width: 350px;
        /* If max-width does not work, try using width instead */
        width: 350px; 
    }
    
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Browse Purchased Vouchers
            <small>Order Manager</small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="browse_orders.php"><i class="fa fa-bars"></i> Browse Orders</a></li>
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
                            <td width="100%" colspan="19" style=" text-align: left;">
                                <input type="text" class="form-control" data-column="1" id="date-range" placeholder="Search By Date" onkeydown="return false">

                            </td>
                        </tr>  
                        <tr>
                            <th width="5%">Voucher #</th>
                            <th width="5%">Times Redeemed <a href="#" data-toggle="tooltip" title="To redeem/un-redeemed a voucher or multiple vouchers increase or decrease the number of vouchers to redeemed/un-redeemed and then press enter."><i class="fa fa-question-circle"></i></a></th>
                            <th width="5%">Expired</th>
                            <th width="5%">Expiry Date</th>
                            <th width="5%">Business Name</th>
                            <th width="5%">Title</th>
                            <th width="5%">Price</th>
                            <th width="5%">Purchaser Name</th>
                            <th width="5%">Purchaser Suburb</th>
                            <th width="5%">Purchaser Email</th>
                            <th width="5%">Purchaser Mobile</th>
                            <th width="5%">Receiver Name</th>
                            <th width="5%">Receiver Email</th>
                            <th width="5%">Receiver Mobile</th>
                            <th width="5%">Payment Status</th>
                            <th width="5%">Date Purchased</th>
                            <th width="5%">Redeemed</th>
                            <th width="5%">Date Redeemed</th>
                            <th width="5%">Status</th>
                            <th width="10%">Action</th>
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
   
    <!-- iCheck 1.0.1 -->
    <link rel="stylesheet" href="../assets/plugins/iCheck/all.css">
    <script src="../assets/plugins/iCheck/icheck.min.js"></script>
    
<div id="resend_voucher_box" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        
        <form method="post" action="resend_voucher.php" id="resendFrm">
          
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Resend Voucher</h4>
      </div>
      <div class="modal-body">
          
        <div class="row">
            
            
            <div class="col-md-12">
                
                <div class="form-group required">
                    <label class="control-label">Email</label>
                    <input type="email" required="required" class="form-control" name="uemail" id="uemail" placeholder="" value="">
                </div>
                
                <div class="form-group required">
                    <label class="control-label">Mobile</label>
                    <input type="text" required="required" class="form-control" name="umobile" id="umobile" placeholder="" value="">
                </div>

            </div>
                  
            
        </div>
          
      </div>
      <div class="modal-footer">
          <input type="submit" value="Send" class="btn btn-primary">
      </div>
            <input type="hidden" name="sold_voucher_id" id="sold_voucher_id" value="">
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    
<script>

$(function () {

    

    function updateRowToRedeemed(result){
        console.log("entro al updateRowToRedeemed con: result: "+result.sold_voucher_id);
        console.log("asd: "+'input[itemid='+result.sold_voucher_id+']');
        $('input[itemid='+result.sold_voucher_id+']').closest('tr').removeClass('highlight_pink');
        $('input[itemid='+result.sold_voucher_id+']').closest('tr').addClass('highlight_blue');
        $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(0).removeClass('highlight_pink_cl');
        $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(0).addClass('highlight_blue_cl');

        $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(15).html('Yes');
        $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(16).html(result.redeem_date);
        $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(17).html('Redeemed');
    }

    function unredeemRow(result){
        console.log("entro al unredeemRow con: result: "+result.sold_voucher_id);
        if($('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(1).html() == 'Yes'){
          
            $('input[itemid='+result.sold_voucher_id+']').closest('tr').removeClass('highlight_blue');
            $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(0).removeClass('highlight_blue_cl');
            $('input[itemid='+result.sold_voucher_id+']').closest('tr').addClass('highlight_pink');
            $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(0).addClass('highlight_pink_cl');
          
        }else{
          
            $('input[itemid='+result.sold_voucher_id+']').closest('tr').removeClass('highlight_blue');
            $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(0).removeClass('highlight_blue_cl');
          
        }

        $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(15).html('No');
        $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(16).html(result.redeem_date);
        $('input[itemid='+result.sold_voucher_id+']').closest('tr').find('td').eq(17).html('Active');
    }


    $(document).ajaxComplete(function() {

        //$(document).off();// remove all handlers so its executes just once
        $('input[name=number_of_redemptions]').off();

        var old_redemptions = 0;
        var max_redemptions = 0;
        $('input[name=number_of_redemptions]').on('focusin', function(){
            old_redemptions = parseInt($(this).val());
            max_redemptions = parseInt($($(this).parent().find('span[class=max_redemptions]')[0]).text());
            console.log("focusin: "+max_redemptions);

        }).on('change', function(){
            $("#example1_processing").html('<div>Please wait..</div>');
            $("#example1_processing").show();

            var sold_voucher_id = $(this).attr('itemid');
            var number_of_redemptions = parseInt($(this).val());

            if (number_of_redemptions > max_redemptions){
                console.log("era mayor asi que lo devolvemos al valor anterior");
                alert("Sorry, voucher redepmtion limit reached.");
                $(this).val(old_redemptions);
                $("#example1_processing").hide();
                return;
            }
            if (number_of_redemptions < 0){
                console.log("era menor q 0");
                $(this).val(0);
                number_of_redemptions = 0;
            }

            $.ajax({
                type   : "POST",
                url    : "browse_purchased_vouchers.php",
                data   : {'sold_voucher_id':sold_voucher_id, 'number_of_redemptions':number_of_redemptions},
                
                success: function(result) {
                    console.log("result: "+result);

                    result = jQuery.parseJSON(result);
                    console.log("voucher_id: "+result.sold_voucher_id);

                    if (number_of_redemptions > old_redemptions){
                        alert((number_of_redemptions-old_redemptions)+" voucher/s successfully redeemed");
                        updateRowToRedeemed(result);
                    }
                    if (number_of_redemptions < old_redemptions){
                        alert((old_redemptions-number_of_redemptions)+" voucher/s successfully un-redeemed");
                        if (number_of_redemptions == 0){
                            unredeemRow(result);
                        }
                    }

                    /*if(result.status == 2){
                        alert((number_of_redemptions-old_redemptions)+" voucher/s successfully redeemed");
                        updateRowToRedeemed(result);
                    }
                    else if(result.status == 1){
                        alert((old_redemptions-number_of_redemptions)+" voucher/s successfully un-redeemed");
                        unredeemRow(result);
                    }*/
                    $("#example1_processing").hide();
                },
                error : function(xhr, textStatus, errorThrown) {
                    alert('An error has occurred! ' + errorThrown);
                    $("#example1_processing").hide();
                }
            });

        });

        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
        
        


        /*$(".iCheck-helper").on('click', function() {
            alert("entro aca");
            $("#example1_processing").html('<div>Please wait..</div>');
            $("#example1_processing").show();
            
            var sold_voucher_id = $(this).siblings('input[type="checkbox"]').attr('itemid');
            var number_of_redemptions = $("#number_of_redemptions[itemid='"+sold_voucher_id+"']").val();

            alert("cantidad de redemptions: "+number_of_redemptions);
            
            if($('input:checkbox[name="im_'+sold_voucher_id+'"].flat-red').is(":checked")) {
                var redeem = 2;
            }else{
                var redeem = 1;
            }
            
            $.ajax({
                    type   : "POST",
                    url    : "browse_purchased_vouchers.php",
                    data   : {'redem':'yes','sold_voucher_id':sold_voucher_id, 'redeem':redeem, 'number_of_redemptions':number_of_redemptions},
                    
                    success: function(result) {
                        
                      result = jQuery.parseJSON(result);
                      //console.log(result.sold_voucher_id);
                        if(result.status == 2){
                          
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').removeClass('highlight_pink');
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').addClass('highlight_blue');
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(0).removeClass('highlight_pink_cl');
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(0).addClass('highlight_blue_cl');
                          
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(15).html('Yes');
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(16).html(result.redeem_date);
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(17).html('Redeemed');
                          
                        }else if(result.status == 1){
                          
                          if($('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(1).html() == 'Yes'){
                              
                              $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').removeClass('highlight_blue');
                              $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(0).removeClass('highlight_blue_cl');
                              $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').addClass('highlight_pink');
                              $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(0).addClass('highlight_pink_cl');
                              
                          }else{
                              
                              $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').removeClass('highlight_blue');
                              $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(0).removeClass('highlight_blue_cl');
                              
                          }
                          
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(15).html('No');
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(16).html(result.redeem_date);
                          $('input:checkbox[name="im_'+result.sold_voucher_id+'"].flat-red').closest('tr').find('td').eq(17).html('Active');
                          
                        }
                        else if(result.status == 3){ //changed the number of redemptions successfully

                        }
                        else if(result.status == 4){ //Can not redeem anymore

                        }
                        $("#example1_processing").hide();
                    },
                    error : function(xhr, textStatus, errorThrown) {
                        console.log('An error has occurred! ' + errorThrown);
                    }
                });
        
        });*/
        
        $(".ddopener").on('click', function() {
        
            var sold_voucher_id = $(this).attr('itemid');
        
            $.ajax({
                type   : "POST",
                url    : "browse_purchased_vouchers.php",
                data   : {'resend':'yes','sold_voucher_id':sold_voucher_id},

                success: function(result) {

                  result = jQuery.parseJSON(result);
                  //console.log(result.sold_voucher_id);
                  $('#uemail').val(result.to);
                  $('#umobile').val(result.mobile);
                  $('#sold_voucher_id').val(result.sold_voucher_id);

                },
                error : function(xhr, textStatus, errorThrown) {
                    console.log('An error has occurred! ' + errorThrown);
                }
            });
            
            $("#resend_voucher_box").modal('show');
        
        });
        
        $("#resendFrm").validate({

        submitHandler: function(form) {
                                
            $('input[type=submit]').attr('disabled', 'disabled');
            $('input[type=submit]').val("Sending..");
            form.submit();

        }

    });
        
    });

    var dTbl    =   $('#example1').DataTable({
                "scrollX": true,
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "aoColumns": [
    //                                        { "mData": "id", "bVisible":false, "bSearchable": false, "bRegex": false, "bSortable": false},
    //                                        { "mData": "serial_no", "bSearchable": false, "bRegex": false, "bSortable": false },
                                { "mData": "voucher_no", "bSearchable": true, "bRegex": false, "bSortable": true },
                                { "mData": "times_redeemed", "bSearchable": false, "bRegex": false, "bSortable":false  },
                                { "mData": "expired", "bSearchable": false, "bRegex": false, "bSortable": true },
                                { "mData": "expiry_date", "bSearchable": false, "bRegex": false, "bSortable": true },
                                { "mData": "business_name", "bSearchable": true, "bRegex": false, "bSortable": true },
                                { "mData": "title", "bSearchable": true, "bRegex": false, "bSortable": true },
                                { "mData": "price", "bSearchable": true, "bRegex": false, "bSortable": true },
                                { "mData": "purchaser_name", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                { "mData": "suburb", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                { "mData": "purchaser_email", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                { "mData": "purchaser_mobile", "bSearchable": true, "bRegex": false, "bSortable": true  },
                                { "mData": "receiver_name", "bSearchable": true, "bRegex": false, "bSortable":true  },
                                { "mData": "receiver_email", "bSearchable": true, "bRegex": false, "bSortable":true  },
                                { "mData": "receiver_mobile", "bSearchable": true, "bRegex": false, "bSortable":true  },
                                { "mData": "payment_status", "bSearchable": true, "bRegex": false, "bSortable":true  },
                                { "mData": "created", "bSearchable": false, "bRegex": false, "bSortable":true  },
                                { "mData": "redeemed", "bSearchable": false, "bRegex": false, "bSortable":false  },
                                { "mData": "redeem_datetime", "bSearchable": false, "bRegex": false, "bSortable":true  },
                                { "mData": "status", "bSearchable": false, "bRegex": false, "bSortable":false  },
                                { "mData": "actions", "bSearchable": false, "bRegex": false, "bSortable": false  }
                          ],
                "saveState": true,          
                "ajax": "../core/datatables/admin_browse_purchased_vouchers.php",
                "bAutoWidth":false,
                dom: 'T<"clear">lfrtip',
                deferRender: true,
                tableTools: {
                    "sSwfPath": "../assets/plugins/datatables/swf/copy_csv_xls_pdf.swf",
                    "aButtons": [
                                    {
                                        "sExtends": "csv",
                                        "sTitle": "Purchased Vouchers Details - Voucher Plus",
                                        "sButtonText": "Download CSV",
                                        "mColumns": [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18 ]
                                    }
                                ]
                },
                "createdRow": function ( row, data, index ) {
                    //console.log(data);
//                    if ( data.expired == 'Yes' ) {
//                        //$('td', row).eq(5).addClass('highlight');
//                        $(row).addClass('highlight_pink');
//                        $('td', row).eq(0).addClass('highlight_pink_cl');
//                    }
                    if ( data.redeemed == 'Yes' ) {
                        //$('td', row).eq(5).addClass('highlight');
                        $(row).addClass('highlight_blue');
                        $('td', row).eq(0).addClass('highlight_blue_cl');
                    }else if(data.expired == 'Yes'){
                        
                        $(row).addClass('highlight_pink');
                        $('td', row).eq(0).addClass('highlight_pink_cl');
                        
                    }
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