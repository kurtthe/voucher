<?php
require_once("core/loader.php");
//unset($_SESSION['XCart']);
//pr($_SESSION['XCart']);die;
if(isset($_GET['ot']) && !empty($_GET['ot'])){
    
    $ot = mysqli_real_escape_string($dbHandle, urldecode(trim($_GET['ot'])));
    $ot_arr = explode('-', $ot);
    //echo $outlet_name = strtolower(implode(' ', $ot_arr));die;
    $outlet_name = strtolower(implode(' ', $ot_arr));
    
    //$sql = "SELECT * FROM ".TBL_OUTLETS." WHERE business_name = '".$outlet_name."' AND account_status = 1";
    $sql = "SELECT o.*, s.suburb, s.state, s.postcode"
            . " FROM ".TBL_OUTLETS." o "
            . " LEFT JOIN ".TBL_SUBURBS." s ON s.id = o.suburb_id"
            . " WHERE o.business_name = '".$outlet_name."' AND account_status = 1";
    
    $ref	=	query($dbHandle, $sql);

    $outlet		=	fetchOne($ref, 'Object');
    //pr($outlet);die;
    if(empty($outlet->id)){
        
        header("Location:error-page.php?error-code=1002");
        exit;
        
    }
    
    $_SESSION['FRONTPAGE']['Id'] = $outlet->id;
    
    $sql = "SELECT * FROM ".TBL_VOUCHERS." WHERE outlet_id = '".$outlet->id."' AND voucher_status = 1 ORDER BY voucher_order ASC";
    $ref	=	query($dbHandle, $sql);

    $vouchers		=	fetchAll($ref, 'Object');
    //pr($vouchers);die;
    //$sql = "SELECT * FROM ".TBL_SUBURBS." WHERE id = '".$outlet->suburb_id."'";
    //$ref	=	query($dbHandle, $sql);

    //$suburb		=	fetchOne($ref, 'Object');
    
}else{
    
    header("Location:error-page.php");
    exit;
    
}
?>

<!--========================Header Start=====================-->
    <?php
    require_once("includes/header.php");
    ?>
<!--========================Header End=====================--> 

<style>
    .padding20{
        padding: 20px;
    }
    .box{
        /*padding: 20px;
        margin-bottom: 30px;
        margin-left: 30px;*/
        background: #fff;
        border-bottom: 1px solid #73c851;
        padding:20px;
        margin-bottom: 20px;
        /*position: relative;*/
        box-shadow: #b3b3b3 0px 0px 7px;
        /*min-height: 188px;*/

    }
    
    /*@media screen and (max-width:768px){
        .box{
           width:96%;
         }
    }*/
    
    .dtls_imgarea{
        border: 1px solid #777;
        padding: 2px;
        height: auto;
        overflow: hidden;
        max-width: 100%;
        vertical-align: middle;
        display: inline-block;

    }
    .dtls_aboutarea{
        margin-top:10px;
    }
    .rdmore{
        color: #73c851;
    }
    .rdmore a:hover {
        color: #222;
    }
    .rdmore a:hover {
        text-decoration: none;
    }

    @media (max-width: 767px) {
        /* Align text to center.*/
        .box p, .box h2, .box h4 {
            text-align: center !important;
      } 
    }

    .about-us a{
        text-decoration: underline;
    }

    a.rdmore:visited, a.rdmore:visited, a.rdmore:hover, a.rdmore:active{
        color: #73c851 !important;    
    }
    

</style>

<!-- Body Section Start -->
<div class="bodypart">
  <div class="container">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-lg-12">
            <div class="row box">
            <?php
            if(isset($_GET['msg']) && $_GET['msg'] == 'odr_cnf'){
            ?>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> Thank you for your order, you will be confirmed by Email/SMS soon.
            </div>
            <?php
            }
            ?>
            
            <div class="col-xs-12 col-sm-4 col-md-2" style="padding-bottom: 20px;">
                <!--style="height: 190px; background-image: url(timthumb.php?src=uploads/outlet_logo/<?php echo $outlet->outlet_image_file; ?>&w=150&h=141); background-size: contain; background-position: center;"-->
                <img class="img-responsive" src="uploads/outlet_logo/<?php echo $outlet->outlet_image_file; ?>" alt="Outlet Image" style="height: auto; width: 100%;">
                <!--<div class="dtls_imgarea"><img src="timthumb.php?src=uploads/outlet_logo/<?php echo $outlet->outlet_image_file; ?>&w=150&h=141" alt="Outlet Image"></div>-->

            </div>

            <!--<div class="row">-->
                <div class="col-xs-12 col-sm-8 col-md-4">
                    <!--<div class="address_area">-->
                        <h2 style="margin:10px 0 15px 0;"><?php echo ucwords($outlet->business_name); ?></h2>
                        <p style="font-size: 1em; line-height: 1.2em; margin-bottom: 6px;">
                            <?php echo $outlet->address; ?><br>
                            <?php echo ucwords(strtolower($outlet->suburb)).', '.$outlet->state.' '.$outlet->postcode; ?>
                            
                            <h2 style="margin:0px 0 0px 0; font-size: 1.4em;">
                                <a class="strong" style="padding-top: 10px;" href="tel: <?php echo $outlet->phone; ?>">
                                    <?php echo $outlet->phone; ?>
                                </a>  
                            </h2>
                            
                        </p>
                        <?php if ($outlet->other_addresses != ""){ ?>
                            <h2 style="padding: 0; margin: 0;">
                            <a class="btn btn-primary" style="background-color: rgba(0,0,0,0.08)" data-toggle="collapse" href="#collapse" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <strong>Show more locations</strong>
                            </a>

                            <div class="collapse" id="collapse">
                              <div class="card card-body" style="padding: 5px; font-size: 0.6em;">
                                <?php 
                                    $search = "/%[^%]*%/";
                                    $string = $outlet->other_addresses;
                                    preg_match_all($search, $string, $match);

                                    foreach ($match[0] as $phone_number) {
                                        $without_symbols = str_replace("%", "", $phone_number);
                                        $string = str_replace($phone_number, '<a href="tel:'.$without_symbols.'">'.$without_symbols."</a>", $string);
                                    }

                                    echo nl2br($string);
                                ?>
                              </div>
                            </div>
                            </h2>
                        <?php } ?>
                    <!--</div>-->
                </div>
            <!--</div>-->
            
            <div class="col-xs-12 col-sm-12 col-md-6 about-us">
                <!--<div class="dtls_aboutarea">-->
                    <hr class="visible-xs">
                    <p>
                    <?php
                    if(strlen($outlet->about_us) > 450){
                        $first_part = htmlspecialchars_decode(preg_replace('/\s+?(\S+)?$/', '', substr($outlet->about_us, 0, 450)), ENT_QUOTES);
                        echo $first_part.'...';
                    }else{
                        echo htmlspecialchars_decode($outlet->about_us, ENT_QUOTES);
                    }
                    ?> 
                    <?php
                    if(strlen($outlet->about_us) > 450){
                    ?>
                        <!--<a href="javascript:void(0);" class="rdmore visible-lg visible-xl" data-toggle="popover" data-html="true" title="" data-placement="auto" data-trigger="hover" data-content="<?php echo $outlet->about_us; ?>">Read more</a>-->

                        <!-- for smaller devices -->
                        <div class="collapse" id="collapse_moreInfo">
                            <!--<?php echo $first_part; ?>-->

                            <?php echo '<p>'.str_replace($first_part, "", htmlspecialchars_decode($outlet->about_us, ENT_QUOTES))."</p>"; ?>

                            <!--<?php echo '<p style="text-size: 1em !important;">'.htmlspecialchars_decode(preg_replace('/\s+?(\S+)?$/', '', substr($outlet->about_us, strlen(preg_replace('/\s+?(\S+)?$/', '', substr($outlet->about_us, 0, 450))), strlen($outlet->about_us))))."</p>" ?>-->
                        </div>

                        <a class="rdmore" data-toggle="collapse" href="#collapse_moreInfo" aria-controls="collapseExample">
                            Read more
                        </a>

                        

                    <?php
                    }
                    ?>
                    </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-9 col-lg-9">
            <div class="row">
            <?php
            if(!empty($vouchers)){
                foreach($vouchers as $voucher){
            ?>  
            <div class="col-xs-12 col-sm-6 col-lg-4">

              <div class="productarea">
                <div class="productborder">              
                  <div class="productimg">
                      <!--<img src="assets/images/product1.jpg" alt="image">-->
                      <?php
                      if(!empty($voucher->voucher_image_file)){
                      ?>
                      <img src="uploads/voucher_logo/<?php echo $voucher->voucher_image_file;?>" alt="Product Image" />
                      <!--<img src="timthumb.php?src=uploads/voucher_logo/<?php echo $voucher->voucher_image_file; ?>&w=180&h=156" alt="Product Image" />-->
                      <?php
                      }else{
                      ?>
                      <img src="assets/images/noproduct.jpg" alt="Product Image" />
                      <?php
                      }
                      ?>
                  </div>
                  <div class="producheading" style="font-size:16px; border-left: 0px"><?php echo ucwords($voucher->title); ?></div>          
                
                <div class="rs_area">
                  <?php
                  if(!empty($voucher->sale_price)){
                  ?>  
                    <div class="product__oldamount" style="font-size:25px;font-weight: bold;"><span>WAS</span> $<?php echo $voucher->actual_price; ?></div>
                  <?php
                  }
                  ?>
                  <div class="productamount" style="font-size:25px;font-weight: bold;">
    <!--                    <span>$</span>-->
                        <?php
                        if(empty($voucher->sale_price)){
                            echo '$'.$voucher->actual_price; 
                        }else{
                            echo '$'.$voucher->sale_price;
                        }
                        ?>
                  </div>
                </div>
                <div class="productdesc" style="min-height: 90px; height: auto; margin-bottom: 0px;">
                    <?php
                    if(strlen($voucher->description) > 85){
                        //echo substr($voucher->description,0,85).'...';

                        $first_part = htmlspecialchars_decode(preg_replace('/\s+?(\S+)?$/', '', substr($voucher->description, 0, 85)), ENT_QUOTES);
                        echo $first_part.'...';

                    }else{
                        echo $voucher->description;
                    }
                    ?> 
                    
                    <?php
                    if(strlen($voucher->description) > 85){
                    ?>

                        <div class="collapse" id="collapse_voucher_moreInfo<?php echo $voucher->id?>">
                            <?php echo str_replace($first_part, "", htmlspecialchars_decode($voucher->description, ENT_QUOTES)); ?>

                            <!--<?php echo htmlspecialchars_decode(preg_replace('/\s+?(\S+)?$/', '', substr($voucher->description, strlen(preg_replace('/\s+?(\S+)?$/', '', substr($voucher->description, 0, 85))), strlen($voucher->description)))) ?>-->
                        </div>
                        <br>
                        <a class="rdmore" data-toggle="collapse" href="#collapse_voucher_moreInfo<?php echo $voucher->id?>" aria-controls="collapseExample">
                            Read more
                        </a>

                        <!--<a href="javascript:void(0);" data-toggle="popover" title="" data-placement="top" data-trigger="hover" data-content="<?php echo $voucher->description; ?>">Read More</a>-->
                    <?php
                    }
                    ?>
                </div>
                  <div class="btnarea">

                    <a href="javascript:void(0);" class="btn selectbtn" mid="<?php echo $voucher->id; ?>" data-select="<?php echo $voucher->expiry_date; ?>">Add to Basket </a>

                     <input type="hidden" name="" id="date_today_php" value="<?php echo date('Y-m-d'); ?>">
                </div>
                 </div>
                </div>
            </div>
              
            <?php
                }
            }else{
            ?>  
             <div class="col-xs-12 col-sm-6 col-lg-4"><div class="producheading">No vouchers found.</div></div>
            <?php
            }
            ?> 
            </div> 
        </div>
        <div class="col-xs-12 col-sm-3 col-lg-3">
            <form method="post" action="checkout.php" id="chkoutCrtFrm">
            <div class="orderarea" id="stickyCartBox" style="background-color: white; z-index: 999999">
                <div class="orderareaheading">Your Order</div>
                <div class="orderlistarea" id="cartArea" style="height: 315px;">
                    
<!--                    <div class="listwrapper">
                        <div class="ordercount">
                                <a href="#"><img src="assets/images/minus.png" alt="image"/></a><span>1</span>
                            <a href="#"><img src="assets/images/plus.png" alt="image"/></a></div>
                        <div class="ordername">website provides free color tools for finding <a href="#">more...</a></div>
                        <div class="orderRs">$80</div>
                    </div>
                    <div class="listwrapper">
                        <div class="ordercount">
                                <a href="#"><img src="assets/images/minus.png" alt="image"/></a><span>1</span>
                            <a href="#"><img src="assets/images/plus.png" alt="image"/></a></div>
                        <div class="ordername">$100 Giftvoucher</div>
                        <div class="orderRs">$100</div>
                    </div>-->
                    <?php
                    $cartTotal = 0;
                    if(!isset($_SESSION['XCart']) || empty($_SESSION['XCart'])){

                        echo '<div class="listwrapper">No Items Added to Cart.</div>';

                    }else{
                    ?>

                    <?php 
                        foreach($_SESSION['XCart'] as $kk=>$CartContents){

                            $cartTotal	+= $CartContents['Amount'];
                    ?>
                        <div class="listwrapper">
                            <div class="ordercount">
                                    <a href="javascript:void(0);" class="decreaseCartItem" mid="<?php echo $CartContents["Id"]; ?>"><img src="assets/images/minus.png" alt="minus icon"/></a><span><?php echo $CartContents["Quantity"]; ?></span>
                                <a href="javascript:void(0);" class="increseCartItem" mid="<?php echo $CartContents["Id"]; ?>"><img src="assets/images/plus.png" alt="plus icon"/></a></div>
                            <div class="ordername"><?php echo $CartContents["Product"]; ?></div>
                            <div class="orderRs">$<?php echo $CartContents["Amount"]; ?></div>
                        </div>
                    <?php
                        }
                    }
                    ?>
                    
                </div>

                <div class="ordertotalamount">
                    <span class="left">Total Amount</span>
                    <span class="right" id="totalamount">
                        $<?php
                        echo number_format($cartTotal, 2);
                        ?>
                    </span>
                </div>
                <a href="javascript:void(0);" class="checkout" id="goToCheckout">Check Out</a>
            </div>
                <input type="hidden" name="outlet_id" value="<?php echo $outlet->id; ?>">
                <input type="hidden" name="total_amount" value="<?php echo $cartTotal; ?>">
                <input type="hidden" name="identifier" value="<?php echo hash('sha256','VPlus') ?>">
            </form>    
        </div>
        
    </div>
  </div>
</div>
<!-- Body Section End --> 

<!--========================Footer Start=====================-->
    <?php
    require_once("includes/footer.php");
    ?>
<!--========================Footer End=====================-->

<!-- Bootbox 4.4.0 -->
<script src="assets/plugins/bootbox/bootbox.min.js"></script>

<script type="text/javascript">
$(function () {
    
    function detectmob() {
       if(window.innerWidth <= 800 && window.innerHeight <= 600) {
         return true;
       } else {
         return false;
       }
    }

    var loading = $("#loadingDiv");
    $(document).ajaxStart(function () {
        loading.show();
    });

    $(document).ajaxStop(function () {
        loading.hide();
    });
    
    var stickyCartTop = $('#stickyCartBox').offset().top;
    
    if (!detectmob()){
        $(window).scroll(function(){
            
            if( $(window).scrollTop() > stickyCartTop ) {
                    var x = $("#stickyCartBox").parent().width();
                    $('#stickyCartBox').css({position: 'fixed', top: '0px', width:x});
            } else {
                    $('#stickyCartBox').css({position: 'relative', top: '0px'});
            }
            
        });
    }

    
    //Popover init
    $('[data-toggle="popover"]').popover();
    
    $(".selectbtn").click(function() {
        
        var mid = $(this).attr("mid");
        var date_DB = $(this).attr("data-select");
       

        var date_today = $("#date_today_php").val();

        /* var date_today = '2019-07-08';  


        let date = new Date()

        let day = date.getDate()
        let month = date.getMonth() + 1
        let year = date.getFullYear()

        if(month < 10){
          date_today = (`${year}-0${month}-${day}`)
        }else{
          date_today = (`${year}-${month}-${day}`)
        } */

                

               /* if (date_today >  date_DB) {

                       alert(date_today + ' is later than ' + date_DB);

                } else {
                     alert(date_DB  + ' is later than ' + date_today);
                }  */


                if (date_DB == '0000-00-00' ||  date_DB == date_today ) {

    date_DB = '3000-01-01';
    
}


 
if (date_today > date_DB) {

    alert('This voucher is no longer available for purchase');

    //alert('son iguales entro');

} else {
        
        $.ajax({
            type: "POST",
            url: "core/functions/cart.php?function=add_to_cart",
            data: {'id':mid, 'action_type':'addCartItem'},
            success: function(result) {

                result = jQuery.parseJSON(result);
                
                if($('.listwrapper').length == 1 && $('.º1').html() == 'No Items Added to Cart.'){
                    $('div.listwrapper').remove();
                }
                
                if(result.similar == 0){
                    
                    var newElement = '';

                    newElement += '<div class="listwrapper"><div class="ordercount"><a href="javascript:void(0);" class="decreaseCartItem" mid="'+result.id+'"><img src="assets/images/minus.png" alt="image"/></a><span>1</span><a href="javascript:void(0);" class="increseCartItem" mid="'+result.id+'"><img src="assets/images/plus.png" alt="image"/></a></div><div class="ordername">'+result.product+'</div><div class="orderRs">$'+result.amount+'</div></div>';

                    if($('.listwrapper').length > 0){
                        $( newElement ).insertAfter( $('#cartArea div.listwrapper:last') );
                    }else{
                        $('#cartArea').prepend(newElement);
                    }
                    
                    var total_amount = parseInt($('input[name="total_amount"]').val());
                    var unit_price = parseInt(result.unitprice);
                    var total = total_amount + unit_price;
                    
                    $('input[name="total_amount"]').val(total);
                    $("#totalamount").html('$'+total.toFixed(2));

                }else{
                    
                    $('a.increseCartItem[mid="' + result.id + '"]').prev("span").html(result.quantity);
                    $('a.increseCartItem[mid="' + result.id + '"]').parent("div").next().next().html('$'+(result.amount));
                    
                    var total_amount = parseInt($('input[name="total_amount"]').val());
                    var unit_price = parseInt(result.unitprice);
                    var total = total_amount + unit_price;
                    
                    $('input[name="total_amount"]').val(total);
                    $("#totalamount").html('$'+total.toFixed(2));
                    
                }

            }
        });

    }
       //here end 
        
    });
    
    $("#cartArea").on('click', '.increseCartItem' ,function() {
       
       var mid = $(this).attr("mid");
       
       $.ajax({
                type: "POST",
                url: "core/functions/cart.php?function=add_to_cart",
                data: {'id':mid, 'action_type':'increseCartItem'},
                success: function(result) {
                    
                    result = jQuery.parseJSON(result);
                    
                    $('a.increseCartItem[mid="' + result.id + '"]').prev("span").html(result.quantity);
                    $('a.increseCartItem[mid="' + result.id + '"]').parent("div").next().next().html(result.amount);
                    
                    var total_amount = parseInt($('input[name="total_amount"]').val());
                    var unit_price = parseInt(result.unitprice);
                    var total = total_amount + unit_price;
                    
                    $('input[name="total_amount"]').val(total);
                    $("#totalamount").html('$'+total.toFixed(2));
                    
                }
            });
       
    });
    
    $("#cartArea").on('click', '.decreaseCartItem' ,function() {
       
       var mid = $(this).attr("mid");
       
       $.ajax({
                type: "POST",
                url: "core/functions/cart.php?function=add_to_cart",
                data: {'id':mid, 'action_type':'decreaseCartItem'},
                success: function(result) {
                    
                    result = jQuery.parseJSON(result);
                    
                    if(result.removeitem == 1){
                        
                        $('a.decreaseCartItem[mid="' + result.id + '"]').parent().parent().fadeOut(300, function(){ $(this).remove();});
                        
                        var total_amount = parseInt($('input[name="total_amount"]').val());
                        var unit_price = parseInt(result.unitprice);
                        var total = total_amount - unit_price;

                        $('input[name="total_amount"]').val(total);
                        $("#totalamount").html('$'+total.toFixed(2));
                        
                        var numItems = $('.listwrapper').length;
                        
                        if(numItems == 1){
                            
                            $('#cartArea').prepend('<div class="listwrapper">No Items Added to Cart.</div>');
                            
                        }
                        
                    }else{
                        
                        $('a.decreaseCartItem[mid="' + result.id + '"]').next("span").html(result.quantity);
                        $('a.decreaseCartItem[mid="' + result.id + '"]').parent("div").next().next().html(result.amount); 
                        
                        var total_amount = parseInt($('input[name="total_amount"]').val());
                        var unit_price = parseInt(result.unitprice);
                        var total = total_amount - unit_price;

                        $('input[name="total_amount"]').val(total);
                        $("#totalamount").html('$'+total.toFixed(2));
                        
                    }    
                    
                }
            });
       
    });
    
    $("#chkoutCrtFrm").on('click', '#goToCheckout' ,function() {
        
        if($('input[name="total_amount"]').val() == 0){
            bootbox.alert("<strong>Warning!</strong> Please add some item to the cart.");
            return false;
        }else{
            $("#chkoutCrtFrm").submit();
        }
        
    });    
    
});
</script>