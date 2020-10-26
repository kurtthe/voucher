<?php
require_once("../loader.php");

$outlet_id = (int)mysqli_real_escape_string($dbHandle, $_GET['id']);

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$returnArray	=	array();
		
$returnArray['draw']	=	$requestData['draw'];

$returnArray['data']	=	array();

$conditions	=	"outlet_id = '".$outlet_id."' ";

$sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE ".$conditions."";
$ref	=	query($dbHandle, $sql);
$total = numRows($ref);
		
$returnArray['recordsTotal']	=	$total;

$returnArray['recordsFiltered']	=	0;
		
if(isset($requestData['search']['value']) && !empty($requestData['search']['value'])){

        //$conditions["OR"][] =	array("CONCAT(Job.title) LIKE " => '%'.$this->request->query["search"]["value"].'%');

        //$conditions["OR"][] =   array("Job.business_name LIKE " => '%'.$this->request->query["search"]["value"].'%');
        
        $conditions.=" AND ( title LIKE '".$requestData['search']['value']."%' ";    
	$conditions.=" OR actual_price LIKE '".$requestData['search']['value']."%' ";

	$conditions.=" OR sale_price LIKE '".$requestData['search']['value']."%' )";

}
                
$orderBy =   " ORDER BY ";
		
if(isset($requestData['order']) && is_array($requestData['order'])){

    foreach($requestData['order'] as $order){

        if($requestData['columns'][$order['column']]["data"] == 'title'){

                //$orderArray["CONCAT(Job.title)"] =	strtoupper($order["dir"]);
                $orderBy .= " title ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'actual_price'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " actual_price ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'sale_price'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " sale_price ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'account_status'){

                //$orderArray["Job.job_status"] =	strtoupper($order["dir"]);
                $orderBy .= " account_status ".strtoupper($order["dir"]);

        } else if($requestData['columns'][$order['column']]["data"] == 'months_of_expiration'){

                //$orderArray["Job.job_status"] =   strtoupper($order["dir"]);
                $orderBy .= " months_of_expiration ".strtoupper($order["dir"]);     

        } else if($requestData['columns'][$order['column']]["data"] == 'expiry_date'){

                //$orderArray["Job.job_status"] =   strtoupper($order["dir"]);
                $orderBy .= " expiry_date ".strtoupper($order["dir"]);  

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

$sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE ".$conditions." ";

$ref	=	query($dbHandle, $sql);
$filtered_total = numRows($ref);

$returnArray['recordsFiltered']	= $filtered_total;

if($requestData['length'] == -1){
    
    $sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE ".$conditions." ".$orderBy." LIMIT ".$requestData['start']." ,".$total." ";
    
}else{
    
    $sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE ".$conditions." ".$orderBy." LIMIT ".$requestData['start']." ,".$requestData['length']." ";
    
}
//echo $sql;die;
$ref	=	query($dbHandle, $sql);

$vouchers = fetchAll($ref, 'Object');
		
$i  =   1;
                
foreach($vouchers as $voucher){

    if($voucher->voucher_status == 1){

        $voucher_status   =   'Active';

    }else{

        $voucher_status   =   'Inactive';

    }

//    if(empty($outlet->paypal_account)){
//        $paypal_account = '<span class="text-red">Not Added</span>';
//    }else{
//        $paypal_account = '<span class="text-green">Added</span>';
//    }
    
    $editLink = '<a href="edit_voucher.php?id='.$voucher->id.'" title="Edit" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i></a>';
    
    //$voucherLink = '<a href="browse_vouchers.php?id='.$outlet->id.'" title="Manage Vouchers" class="btn btn-success btn-sm"><i class="fa fa-gift"></i></a>';
    
    //$menuLink = '<a href="browse_menu_items.php?id='.$outlet->id.'" title="Manage Menu Items" class="btn btn-warning btn-sm"><i class="fa fa-cutlery"></i></a>';
    
    //$orderLink = '<a href="browse_outlet_orders.php?id='.$outlet->id.'" title="View Orders" class="btn btn-primary btn-sm"><i class="fa fa-list-alt"></i></a>';
    
    if($voucher->voucher_status == 1){
        
        //$infoLink = '<a href="javascript:void(0);" title="View Access URL" itemid="'.$outlet->id.'" class="btn btn-warning btn-sm access-url-modal"><i class="fa fa-external-link"></i></a> ';
    
    }else{
        
        //$infoLink = '';
        
    }


    if($voucher->expiry_date == NULL or $voucher->expiry_date == '0000-00-00'){
        
        $expiry_date = 'NA';
    }else{
        $expiry_date = date("d/m/Y", strtotime($voucher->expiry_date));
    }
    
//    if($outlet->account_status == 1 && $outlet->welcome_email_sent == 0){
//        
//        $welcomeLink = '<a href="send_welcome_email.php?id='.$outlet->id.'" title="Send Welcome Email" class="btn btn-success btn-sm access-url-modal"><i class="fa fa-envelope-o"></i></a> ';
//    
//    }else{
//        
//        $welcomeLink = '';
//        
//    }
    
    //$deleteLink = '<a href="#" title="Delete" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash-o"></i></a>';
    


    if($voucher->months_of_expiration == 0 ){
        
        $months_of_expiration = 'NA';
    } else {

         $months_of_expiration =  $voucher->months_of_expiration;
    }

   




    $returnArray["data"][]  =  array(
                                'title'       =>  ucwords($voucher->title),
                                'actual_price'      =>  $voucher->actual_price,
                                'sale_price'      =>  $voucher->sale_price,
                                'max_redemptions' => $voucher->max_redemptions,
                                'voucher_order' => $voucher->voucher_order,
                                'voucher_status' => $voucher_status,
                                'months_of_expiration' => $months_of_expiration,
                                'expiry_date' => $expiry_date,
                                'actions'    =>  $editLink                            
            );


}
		
		
die(json_encode($returnArray));
