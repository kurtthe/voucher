<?php
require_once("../loader.php");

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$returnArray	=	array();
		
$returnArray['draw']	=	$requestData['draw'];

$returnArray['data']	=	array();

$conditions	=	"1=1 ";

$sql = "SELECT o.* FROM ".TBL_ORDERS." o LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.purchaser_suburb_id WHERE ".$conditions."";
$ref	=	query($dbHandle, $sql);
$total = numRows($ref);
		
$returnArray['recordsTotal']	=	$total;

$returnArray['recordsFiltered']	=	0;
		
if(isset($requestData['search']['value']) && !empty($requestData['search']['value'])){

        //$conditions["OR"][] =	array("CONCAT(Job.title) LIKE " => '%'.$this->request->query["search"]["value"].'%');

        //$conditions["OR"][] =   array("Job.business_name LIKE " => '%'.$this->request->query["search"]["value"].'%');
        
        $conditions.=" AND ( o.order_no LIKE '".$requestData['search']['value']."%' ";  
        $conditions.=" OR ot.business_name LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR o.purchaser_name LIKE '".$requestData['search']['value']."%' ";
	$conditions.=" OR o.purchaser_email LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR o.purchaser_mobile LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR s.suburb LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR s.postcode LIKE '".$requestData['search']['value']."%' ";
        $conditions.=" OR o.purchased_for LIKE '".$requestData['search']['value']."%' ";
        
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

$orderBy =   " ORDER BY ";
		
if(isset($requestData['order']) && is_array($requestData['order'])){

    foreach($requestData['order'] as $order){

        if($requestData['columns'][$order['column']]["data"] == 'order_no'){

                //$orderArray["CONCAT(Job.title)"] =	strtoupper($order["dir"]);
                $orderBy .= " o.order_no ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'created'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.created ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'submitted_amount'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.submitted_amount ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'business_name'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " ot.business_name ".strtoupper($order["dir"]);

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

        }else if($requestData['columns'][$order['column']]["data"] == 'purchased_for'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " o.purchased_for ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'payment_status'){

                //$orderArray["Job.job_status"] =	strtoupper($order["dir"]);
                $orderBy .= " o.payment_status ".strtoupper($order["dir"]);

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

$sql = "SELECT o.* FROM ".TBL_ORDERS." o LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.purchaser_suburb_id WHERE ".$conditions." ";

$ref	=	query($dbHandle, $sql);
$filtered_total = numRows($ref);

$returnArray['recordsFiltered']	= $filtered_total;

if($requestData['length'] == -1){
    
    $sql = "SELECT o.*, ot.business_name, CONCAT_WS(', ', LOWER(s.suburb), CONCAT_WS(' ', s.state, s.postcode)) as suburb FROM ".TBL_ORDERS." o LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.purchaser_suburb_id WHERE ".$conditions." ".$orderBy." LIMIT ".$requestData['start']." ,".$total." ";
    
}else{
    
    $sql = "SELECT o.*, ot.business_name, CONCAT_WS(', ', LOWER(s.suburb), CONCAT_WS(' ', s.state, s.postcode)) as suburb FROM ".TBL_ORDERS." o LEFT JOIN ".TBL_OUTLETS." ot ON ot.id = o.outlet_id LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.purchaser_suburb_id WHERE ".$conditions." ".$orderBy." LIMIT ".$requestData['start']." ,".$requestData['length']." ";
    
}
//echo $sql;die;
$ref	=	query($dbHandle, $sql);

$orders = fetchAll($ref, 'Object');
		
$i  =   1;
                
foreach($orders as $order){

//    if(empty($order->payment_option)){
//        $payment_option = 'None';
//    }else{
//        $payment_option = ucfirst($order->payment_option);
//    }

    $viewLink = '<a href="order_details.php?id='.$order->id.'" title="View Details" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>';
    //$vouchersLink = '<a href="#" title="View Sold Vouchers" class="btn btn-warning btn-sm"><i class="fa fa-gift"></i></a>';
    

    $returnArray["data"][]  =  array(
                                'order_no'       =>  $order->order_no,
                                'created'       =>  date("d/m/Y", strtotime($order->created)),
                                'submitted_amount'       =>  '$'.number_format($order->submitted_amount, 2),
                                'business_name'       =>  ucwords($order->business_name),
                                'purchaser_name'       =>  ucwords($order->purchaser_name),
                                'purchaser_email'      =>  $order->purchaser_email,
                                'purchaser_mobile'      =>  $order->purchaser_mobile,
                                'suburb'      =>  ucwords($order->suburb),
                                'purchased_for'      =>  $order->purchased_for,
                                'payment_status' => $order->payment_status,
                                'actions'    =>  $viewLink
                            );


}
		
		
die(json_encode($returnArray));
