<?php
require_once("../loader.php");


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;


$returnArray	=	array();
		
$returnArray['draw']	=	$requestData['draw'];

$returnArray['data']	=	array();

$conditions	=	"1=1 ";

$sql = "SELECT * FROM ".TBL_OUTLETS." WHERE ".$conditions."";
$ref	=	query($dbHandle, $sql);
$total = numRows($ref);
		
$returnArray['recordsTotal']	=	$total;

$returnArray['recordsFiltered']	=	0;
		
if(isset($requestData['search']['value']) && !empty($requestData['search']['value'])){

        //$conditions["OR"][] =	array("CONCAT(Job.title) LIKE " => '%'.$this->request->query["search"]["value"].'%');

        //$conditions["OR"][] =   array("Job.business_name LIKE " => '%'.$this->request->query["search"]["value"].'%');
        
        $conditions.=" AND ( business_name LIKE '".$requestData['search']['value']."%' ";    
	$conditions.=" OR email LIKE '".$requestData['search']['value']."%' ";

	$conditions.=" OR contact_person LIKE '".$requestData['search']['value']."%' )";

}
                
$orderBy =   " ORDER BY ";
		
if(isset($requestData['order']) && is_array($requestData['order'])){

    foreach($requestData['order'] as $order){

        if($requestData['columns'][$order['column']]["data"] == 'business_name'){

                //$orderArray["CONCAT(Job.title)"] =	strtoupper($order["dir"]);
                $orderBy .= " business_name ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'email'){

                //$orderArray["Job.business_name"] =	strtoupper($order["dir"]);
                $orderBy .= " email ".strtoupper($order["dir"]);

        }else if($requestData['columns'][$order['column']]["data"] == 'account_status'){

                //$orderArray["Job.job_status"] =	strtoupper($order["dir"]);
                $orderBy .= " account_status ".strtoupper($order["dir"]);

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

$sql = "SELECT * FROM ".TBL_OUTLETS." WHERE ".$conditions." ";

$ref	=	query($dbHandle, $sql);
$filtered_total = numRows($ref);

$returnArray['recordsFiltered']	= $filtered_total;

if($requestData['length'] == -1){
    
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE ".$conditions." ".$orderBy." LIMIT ".$requestData['start']." ,".$total." ";
    
}else{
    
    $sql = "SELECT * FROM ".TBL_OUTLETS." WHERE ".$conditions." ".$orderBy." LIMIT ".$requestData['start']." ,".$requestData['length']." ";
    
}
//echo $sql;die;
$ref	=	query($dbHandle, $sql);

$outlets = fetchAll($ref, 'Object');
		
$i  =   1;
                
foreach($outlets as $outlet){

    if($outlet->account_status == 1){

        $account_status   =   'Active';

    }else{

        $account_status   =   'Inactive';

    }

//    if(empty($outlet->paypal_account)){
//        $paypal_account = '<span class="text-red">Not Added</span>';
//    }else{
//        $paypal_account = '<span class="text-green">Added</span>';
//    }
    
    $editLink = '<a href="edit_outlet.php?id='.$outlet->id.'" title="Edit" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i></a>';
    
    $voucherLink = '<a href="browse_vouchers.php?id='.$outlet->id.'" title="Manage Vouchers" class="btn btn-success btn-sm"><i class="fa fa-gift"></i></a>';
    
    //$menuLink = '<a href="browse_menu_items.php?id='.$outlet->id.'" title="Manage Menu Items" class="btn btn-warning btn-sm"><i class="fa fa-cutlery"></i></a>';
    
    //$orderLink = '<a href="browse_outlet_orders.php?id='.$outlet->id.'" title="View Orders" class="btn btn-primary btn-sm"><i class="fa fa-list-alt"></i></a>';
    
    if($outlet->account_status == 1){
        
        $infoLink = '<a href="javascript:void(0);" title="View Access URL" itemid="'.$outlet->id.'" class="btn btn-warning btn-sm access-url-modal"><i class="fa fa-external-link"></i></a> ';
    
    }else{
        
        $infoLink = '';
        
    }
    
    if($outlet->account_status == 1 && $outlet->welcome_email_sent == 0){
        
        $welcomeLink = '<a href="send_welcome_email.php?id='.$outlet->id.'" title="Send Welcome Email" class="btn btn-success btn-sm access-url-modal"><i class="fa fa-envelope-o"></i></a> ';
    
    }else{
        
        $welcomeLink = '';
        
    }
    
    //$deleteLink = '<a href="#" title="Delete" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash-o"></i></a>';
    


    $returnArray["data"][]  =  array(
                                'business_name'       =>  ucwords($outlet->business_name),
                                'email'      =>  $outlet->email,
                                'account_status' => $account_status,
                                'paypal_account' => $outlet->paypal_account,
                                'actions'    =>  $editLink.'&nbsp;'.$voucherLink.'&nbsp;'.$infoLink.''.$welcomeLink                            
            );


}
		
		
die(json_encode($returnArray));
