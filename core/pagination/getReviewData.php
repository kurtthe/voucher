<?php
if(isset($_POST['page'])){
    
    require_once("../loader.php");
    
    //Include pagination class file
    include('Pagination.php');
    
    $id = $_GET['id'];
    $start = !empty($_POST['page'])?$_POST['page']:0;
    $limit = 10;
    
    //get number of rows
    $sql = "SELECT * FROM ".TBL_USER_REVIEWS." WHERE outlet_id = '".$id."' AND review_status = 1";
    $ref	=	query($dbHandle, $sql);

    $resultNum = numRows($ref);
    $rowCount = $resultNum;
    
    //initialize pagination class
    $pagConfig = array('baseURL'=>'core/pagination/getReviewData.php?id='.$id, 'totalRows'=>$rowCount, 'currentPage'=>$start, 'perPage'=>$limit, 'contentDiv'=>'posts_content');
    $pagination =  new Pagination($pagConfig);
    
    //get rows
    //$query = $db->query("SELECT * FROM posts ORDER BY id DESC LIMIT $start,$limit");
    $sql = "SELECT r.id, r.food_rating, r.value_rating, r.speed_rating, r.overall_rating, r.comment, DATE_FORMAT(r.created, '%M, %Y') as created, CONCAT_WS(' ', u.first_name, u.last_name) as user_name FROM ".TBL_USER_REVIEWS." r LEFT JOIN ".TBL_USERS." u ON u.id = r.user_id WHERE r.outlet_id = '".$id."' AND r.review_status = 1 ORDER BY r.created DESC LIMIT $start,$limit";
    $ref	=	query($dbHandle, $sql);
    
    $reviews		=	fetchAll($ref, 'Object');
    $total_reviews = numRows($ref);
    
    if($total_reviews > 0){ ?>
        
        <?php
            foreach($reviews as $review){ 
                $postID = $review->id;
        ?>
            <div class="listing_area">
                        
                        <div class="details_featurearea">
<!--                            <h4>Overall 8.1 | Food 7.1 | Value 8 | Speed 8</h4>-->
                            <h4><?php echo 'Overall '.$review->overall_rating.' | Food '.$review->food_rating.' | Value '.$review->value_rating.' | Speed '.$review->speed_rating; ?></h4>
                            <p><?php echo $review->comment; ?></p>

                        </div>
                        <div class="details_selectarea">
                            <?php echo $review->created; ?>
                            <p><?php if(!empty($review->user_name)){ echo $review->user_name; }else{ echo 'Anonymous'; } ?></p>
                        </div>
                        
                    </div>
        <?php } ?>
        
        <div style="padding-left:10px;"><?php echo $pagination->createLinks(); ?></div>
<?php }
}
?>