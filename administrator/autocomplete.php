<?php
require_once("../core/loader.php");

if(isset($_GET['fetch']) && $_GET['fetch'] == 'suburbs'){
    
    $search_string = $_GET['term'];
    //$search_string = 'vic';
    if(!empty($search_string)){

        $sql = "SELECT * FROM ".TBL_SUBURBS." WHERE status = 1 AND suburb LIKE '%".$search_string."%' OR postcode LIKE '%".$search_string."%' ORDER BY suburb LIMIT 0,20";

        $ref	=	query($dbHandle, $sql);

        $suburbs		=	fetchAll($ref, 'Object');

        $i=0;

        foreach($suburbs as $suburb){

            $response[$i]['id']     = $suburb->id;
            $response[$i]['label']  = ucwords(strtolower($suburb->suburb)) . ', ' . $suburb->state.' '.$suburb->postcode;
            $response[$i]['value']  = ucwords(strtolower($suburb->suburb)) . ', ' . $suburb->state.' '.$suburb->postcode;
            $i++;

        }

        die(json_encode($response));

    }
    
}

//if(isset($_GET['fetch']) && $_GET['fetch'] == 'menu_items'){
//    
//    $search_string = $_GET['term'];
//    
//    if(!empty($search_string)){
//
//        $sql = "SELECT * FROM ".TBL_MENU_ITEMS." WHERE status = 1 AND title LIKE '%".$search_string."%' ORDER BY title LIMIT 0,20";
//
//        $ref	=	query($dbHandle, $sql);
//
//        $menu_items		=	fetchAll($ref, 'Object');
//
//        $i=0;
//
//        foreach($menu_items as $menu_item){
//
//            $response[$i]['id']     = $menu_item->id;
//            $response[$i]['label']  = $menu_item->title;
//            $response[$i]['value']  = $menu_item->title;
//            $i++;
//
//        }
//
//        die(json_encode($response));
//
//    }
//    
//}
?>