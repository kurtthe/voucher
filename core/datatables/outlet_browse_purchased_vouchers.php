<?php
require_once("../loader.php");

$outlet_id = (int)mysqli_real_escape_string($dbHandle, $_GET['id']);

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$returnArray	=	array();
		
$returnArray['draw']	=	$requestData['draw'];

$returnArray['data']	=	array();

$conditions	=	"o.outlet_id = '".$outlet_id."' ";
$conditions.=" AND sv.status <> 0 "; //dont show the unverified purchases 

$sql = "SELECT sv.* FROM ".TBL_SOLD_VOUCHERS." sv "
        . "LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id "
        . "LEFT JOIN ".TBL_ORDERS." o ON o.order_no = sv.order_no "
        . "LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id "
        . "LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.purchaser_suburb_id "
        . "WHERE ".$conditions."";
$ref	=	query($dbHandle, $sql);
$total = numRows($ref);
		
$returnArray['recordsTotal']	=	$total;

$returnArray['recordsFiltered']	=	0;
		
if(isset($requestData['search']['value']) && !empty($requestData['search']['value'])){

        //$conditions["OR"][] =	array("CONCAT(Job.title) LIKE " => '%'.$this->request->query["search"]["value"].'%');

        //$conditions["OR"][] =   array("Job.business_name LIKE " => '%'.$this->request->query["search"]["value"].'%');
        
        $conditions.=" AND ( sv.voucher_no LIKE '".$requestData['search']['value']."%' ";  
        $conditions.=" OR ot.business_name LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR v.title LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR sv.price LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR o.purchaser_name LIKE '".$requestData['search']['value']."%' ";
	$conditions.=" OR o.purchaser_email LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR o.purchaser_mobile LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR s.suburb LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR s.postcode LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR o.receiver_name LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR o.receiver_email LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR o.receiver_mobile LIKE '".$requestData['search']['value']."%' ";
        
        $conditions.=" OR o.payment_status LIKE '".$requestData['search']['value']."%' )";

}

if( !empty($requestData['columns'][1]['search']['value']) ){   
		 		
    $entered_date               =       explode( ",", $requestData['columns'][1]['search']['value'] );

    $date_from 			=	strstr( $entered_date[0], "GMT+0530",  true );

    $date_from			=	date("Y-m-d", strtotime($date_from) );

    $date_to			=	strstr( $entered_date[1], "GMT+0530",  true );

    $date_to			=	date("Y-m-d", strtotime($date_to) );

    $conditions                 .=	" AND CONCAT( DATE(o.created) ) BETWEEN '".$date_from."' AND '".$date_to."' ";
}

$orderBy =   " ORDER BY sv.id DESC, ";
		
if(isset($requestData['order']) && is_array($requestData['order'])){

    foreach($requestData['order'] as $order){

        if($requestData['columns'][$order['column']]["data"] == 'voucher_no'){

                //$orderArray["CONCAT(Job.title)"] =	strtoupper($order["dir"]);
                $orderBy .= " sv.voucher_no ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'expiry_date'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " sv.expiry_date ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'business_name'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " ot.business_name ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'title'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " v.title ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'price'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " sv.price ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'purchaser_name'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.purchaser_name ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'purchaser_email'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.purchaser_email ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'purchaser_mobile'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.purchaser_mobile ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'suburb'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " s.suburb ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'receiver_name'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.receiver_name ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'receiver_email'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.receiver_email ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'receiver_mobile'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.receiver_mobile ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'payment_status'){

                //$orderArray["Job.job_status"] =	strtoupper($order["dir"]);
                $orderBy .= " o.payment_status ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'created'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.created ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'redeem_datetime'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " sv.redeem_datetime ".strtoupper($order["dir"]);

        }else{

            //$orderArray[$this->request->query['columns'][$order['column']]["data"]] =	strtoupper($order["dir"]);
            $orderBy .= $requestData['columns'][$order['column']]["data"]." ".strtoupper($order["dir"]);

        }

    }

}
		
$limit	=   (isset($requestData['length'])?$requestData['length']:10);

if(isset($requestData['start']) && ($requestData['start']) == 0){

        $page = 1;

}else if(isset($requestData['start']) && ($requestData['start']) > 0){

        $page	=	($requestData['start']/$limit+1);

}else{

        $page = 1;

}

$sql = "SELECT sv.* FROM ".TBL_SOLD_VOUCHERS." sv "
        . "LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id "
        . "LEFT JOIN ".TBL_ORDERS." o ON o.order_no = sv.order_no "
        . "LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id "
        . "LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.purchaser_suburb_id "
        . "WHERE ".$conditions."";

$ref	=	query($dbHandle, $sql);
$filtered_total = numRows($ref);

$returnArray['recordsFiltered']	= $filtered_total;

if($requestData['length'] == -1){
    
    $sql = "SELECT sv.id, sv.voucher_no, sv.expiry_date, ot.business_name, "
        . "v.title, sv.price, o.purchaser_name, "
        . "CONCAT_WS(', ', LOWER(s.suburb), CONCAT_WS(' ', s.state, s.postcode)) as suburb, "
        . "o.purchaser_email, o.purchaser_mobile, o.receiver_name, "
        . "o.receiver_email, o.receiver_mobile, o.payment_status, "
        . "o.created, sv.redeem_datetime, sv.status, sv.number_of_redemptions, "
        . "v.max_redemptions "
        . "FROM ".TBL_SOLD_VOUCHERS." sv "
        . "LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id "
        . "LEFT JOIN ".TBL_ORDERS." o ON o.order_no = sv.order_no "
        . "LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id "
        . "LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.purchaser_suburb_id "
        . "WHERE ".$conditions." ".$orderBy." LIMIT ".$requestData['start']." ,".$total." ";
    
}else{
    
    $sql = "SELECT sv.id, sv.voucher_no, sv.expiry_date, ot.business_name, "
        . "v.title, sv.price, o.purchaser_name, "
        . "CONCAT_WS(', ', LOWER(s.suburb), CONCAT_WS(' ', s.state, s.postcode)) as suburb, "
        . "o.purchaser_email, o.purchaser_mobile, o.receiver_name, "
        . "o.receiver_email, o.receiver_mobile, o.payment_status, "
        . "o.created, sv.redeem_datetime, sv.status, sv.number_of_redemptions, "
        . "v.max_redemptions "
        . "FROM ".TBL_SOLD_VOUCHERS." sv "
        . "LEFT JOIN ".TBL_VOUCHERS." v ON v.id = sv.voucher_id "
        . "LEFT JOIN ".TBL_ORDERS." o ON o.order_no = sv.order_no "
        . "LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id "
        . "LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.purchaser_suburb_id "
        . "WHERE ".$conditions." ".$orderBy." LIMIT ".$requestData['start']." ,".$requestData['length']." ";
    
}
//echo $sql;die;
$ref	=	query($dbHandle, $sql);

$sold_vouchers = fetchAll($ref, 'Object');
		
$i  =   1;
                
$redepmtion_number = "";          
foreach($sold_vouchers as $sold_voucher){

    if($sold_voucher->status == 2){
        
        $redeemed = 'Yes';
        $status = 'Redeemed';
        /*$doUndoBtn   =   "<input type='checkbox' name='im_".$sold_voucher->id."' itemid='".$sold_voucher->id."' class='flat-red' value='2' title='Unredeem Voucher' checked>";*/
        $resendVoucher = '<a href="javascript:void(0);" itemid="'.$sold_voucher->id.'" title="Resend Voucher" class="btn btn-success btn-sm ddopener"><i class="fa fa-envelope-o"></i></a>';
        $redepmtion_number = '<p><input style="width: 50px; margin-left: 10px" type="number" length="3" itemid="'.$sold_voucher->id.'" name="number_of_redemptions" value='.$sold_voucher->number_of_redemptions.'> (max: <span class="max_redemptions">'.$sold_voucher->max_redemptions.'</span>)</p>';
        
    }elseif($sold_voucher->status == 1){
        
        $redeemed = 'No';
        $status = 'Active';
        /*$doUndoBtn   =   "<input type='checkbox' name='im_".$sold_voucher->id."' itemid='".$sold_voucher->id."' class='flat-red' value='1' title='Redeem Voucher'>";*/
        $resendVoucher = '<a href="javascript:void(0);" itemid="'.$sold_voucher->id.'" title="Resend Voucher" class="btn btn-success btn-sm ddopener"><i class="fa fa-envelope-o"></i></a>';
        
        $redepmtion_number = '<p><input style="width: 50px; margin-left: 10px" type="number" length="3" itemid="'.$sold_voucher->id.'" name="number_of_redemptions" value='.$sold_voucher->number_of_redemptions.'> (max: <span class="max_redemptions">'.$sold_voucher->max_redemptions.'</span>)</p>';
    }else{
        
        $redeemed = 'NA';
        $status = 'Inactive';
        /*$doUndoBtn = '';*/
        $resendVoucher = '';
        $redepmtion_number = '<input style="width: 50px; margin-left: 10px" type="number" length="3" itemid="'.$sold_voucher->id.'" name="number_of_redemptions" id="number_of_redemptions" value='.$sold_voucher->number_of_redemptions.' class="disabled" disabled>';
        /*add the number of redemptions */
        
    }

    if(strtotime(date("Y-m-d")) > strtotime($sold_voucher->expiry_date)){
        $expired = 'Yes';
    }else{
        $expired = 'No';
    }
    
    if($sold_voucher->expiry_date != '0000-00-00'){
        $expiry_date = date("d/m/Y", strtotime($sold_voucher->expiry_date));
    }else{
        $expiry_date = 'NA';
    }
    
    if($sold_voucher->redeem_datetime != '0000-00-00 00:00:00'){
        $redeem_datetime = date("d/m/Y H:i A", strtotime($sold_voucher->redeem_datetime));
    }else{
        $redeem_datetime = 'NA';
    }
    
    //$resendVoucher = '<a href="resend_voucher.php?id='.$sold_voucher->id.'" title="Resend Voucher" class="btn btn-success btn-sm"><i class="fa fa-envelope-o"></i></a>';
    //$resendVoucher = '<a href="javascript:void(0);" itemid="'.$sold_voucher->id.'" title="Resend Voucher" class="btn btn-success btn-sm ddopener"><i class="fa fa-envelope-o"></i></a>';
    //$doUndoLink = '<a href="#" title="View Sold Vouchers" class="btn btn-warning btn-sm"><i class="fa fa-gift"></i></a>';

    $returnArray["data"][]  =  array(
                                'voucher_no'       =>  $sold_voucher->voucher_no,
                                'expired'       =>  $expired,
                                'expiry_date'       =>  $expiry_date,
                                'business_name'       =>  ucwords($sold_voucher->business_name),
                                'title'       =>  ucwords($sold_voucher->title),
                                'price'       =>  '$'.number_format($sold_voucher->price, 2),
                                'purchaser_name'       =>  ucwords($sold_voucher->purchaser_name),
                                'suburb'      =>  ucwords($sold_voucher->suburb),
                                'purchaser_email'      =>  $sold_voucher->purchaser_email,
                                'purchaser_mobile'      =>  $sold_voucher->purchaser_mobile,
                                'receiver_name'      =>  $sold_voucher->receiver_name,
                                'receiver_email'      =>  $sold_voucher->receiver_email,
                                'receiver_mobile'      =>  $sold_voucher->receiver_mobile,
                                'payment_status' => $sold_voucher->payment_status,
                                'created'      =>  date("d/m/Y", strtotime($sold_voucher->created)),
                                'redeemed' => $redeemed,
                                'redeem_datetime' => $redeem_datetime,
                                'status' => $status,
                                'times_redeemed' => $redepmtion_number,
                                'actions'    =>  $resendVoucher//.'&nbsp'.$doUndoBtn
                            );


}
		
		
die(json_encode($returnArray));
