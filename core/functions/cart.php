<?php
require_once("../loader.php");

if(isset($_GET['function']) && $_GET['function'] == 'add_to_cart'){
    
    if(isset($_POST["action_type"]) && $_POST["action_type"] == 'addCartItem'){
    
        $id = (int)mysqli_real_escape_string($dbHandle, trim($_POST['id']));
        //echo $pricetype.'-'.$priceid;die;
        $sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE id = '".$id."'";
        $ref	=	query($dbHandle, $sql);

        $ps3	=	fetchOne($ref, 'Object');
        
        $return = array();
        
        $return['product'] = $ps3->title;
        $return['id'] = $ps3->id;
        $return['similar'] = 0;
        
        
            
        //$similar = false;
        
        if(empty($ps3->sale_price)){
            
            $return['unitprice'] = $ps3->actual_price;
            $voucher_price = $ps3->actual_price;
            
        }else{
            
            $return['unitprice'] = $ps3->sale_price;
            $voucher_price = $ps3->sale_price;
            
        }
        

        if(isset($_SESSION['XCart'][$ps3->id])){

                
                $_SESSION['XCart'][$ps3->id]['Quantity']		+=	1;
                $_SESSION['XCart'][$ps3->id]['Amount']			+=	$voucher_price;
                $return['similar'] = 1;
                $return['quantity'] = $_SESSION['XCart'][$ps3->id]['Quantity'];
                $return['amount'] = $_SESSION['XCart'][$ps3->id]['Amount'];

        }else{
                
                $_SESSION['XCart'][$ps3->id]		=	array(
                                                                                'Product'=>$ps3->title,
                                                                                'Id'	=> $ps3->id,
                                                                                'Quantity'=>1,
                                                                                'Amount'=>$voucher_price,
                                                                                'Unit_Price'=>$voucher_price
                                                                        );
                $return['quantity'] = 1;
                $return['amount'] = $voucher_price;

        }
            
        
        
        echo json_encode($return);
        
        exit();
    
    }
    
    if(isset($_POST["action_type"]) && $_POST["action_type"] == 'increseCartItem'){
        
        $id = (int)mysqli_real_escape_string($dbHandle, trim($_POST['id']));
        
        $sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE id = '".$id."'";
        $ref	=	query($dbHandle, $sql);

        $ps3	=	fetchOne($ref, 'Object');
        
        $return = array();
        
        $return['id'] = $id;
        
        if(empty($ps3->sale_price)){
            
            $return['unitprice'] = $ps3->actual_price;
            $voucher_price = $ps3->actual_price;
            
        }else{
            
            $return['unitprice'] = $ps3->sale_price;
            $voucher_price = $ps3->sale_price;
            
        }
        
        if(isset($_SESSION['XCart'][$ps3->id])){

            $_SESSION['XCart'][$ps3->id]['Quantity']		+=	1;
            $_SESSION['XCart'][$ps3->id]['Amount']		+=	$voucher_price;
            //$_SESSION['XCart'][$ps3->id]['ActualAmount']	+=	$ps3->amount;
            $return['quantity'] = $_SESSION['XCart'][$ps3->id]['Quantity'];
            $return['amount'] = '$'.($_SESSION['XCart'][$ps3->id]['Amount']);
            $return['unitprice'] = $voucher_price;

        }
            
        
        
        echo json_encode($return);
    
        exit();
        
    }
    
    if(isset($_POST["action_type"]) && $_POST["action_type"] == 'decreaseCartItem'){
        
        $id = (int)mysqli_real_escape_string($dbHandle, trim($_POST['id']));
        
        $sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE id = '".$id."'";
        $ref	=	query($dbHandle, $sql);

        $ps3	=	fetchOne($ref, 'Object');
        
        $return = array();
        
        $return['id'] = $id;
        //$return['removekey'] = 0;
        //$return['removeitemsize'] = 0;
        $return['removeitem'] = 0;
        //$return['preference'] = 0;
        
        if(empty($ps3->sale_price)){
            
            $return['unitprice'] = $ps3->actual_price;
            $voucher_price = $ps3->actual_price;
            
        }else{
            
            $return['unitprice'] = $ps3->sale_price;
            $voucher_price = $ps3->sale_price;
            
        }
        
        if(isset($_SESSION['XCart'][$ps3->id]) && $_SESSION['XCart'][$ps3->id]['Quantity'] > 1){

                    $_SESSION['XCart'][$ps3->id]['Quantity']		-=	1;
                    $_SESSION['XCart'][$ps3->id]['Amount']		-=	$voucher_price;
                    //$_SESSION['XCart'][$ps3->id]['ActualAmount']	+=	$ps3->amount;
                    $return['quantity'] = $_SESSION['XCart'][$ps3->id]['Quantity'];
                    $return['amount'] = '$'.($_SESSION['XCart'][$ps3->id]['Amount']);
                    $return['unitprice'] = $voucher_price;
                    

        }else if(isset($_SESSION['XCart'][$ps3->id]) && $_SESSION['XCart'][$ps3->id]['Quantity'] == 1){

                    //$return['quantity'] = $_SESSION['XCart'][$ps3->id]['Quantity'];
                    //$return['amount'] = '$'.($_SESSION['XCart'][$ps3->id]['Amount']);
                    //$return['unitprice'] = $voucher_price;

                    unset($_SESSION['XCart'][$ps3->id]);
                    $return['removeitem'] = 1;

                    


                    //$_SESSION['XCart'][$ps3->id]['Amount']			+=	$ps3->amount;
                    //$_SESSION['XCart'][$ps3->id]['ActualAmount']	+=	$ps3->amount;

        }
        
        echo json_encode($return);
    
        exit();
        
    }
    
    exit();
    
}
?>