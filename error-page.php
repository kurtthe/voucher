<?php
require_once("core/loader.php");

?>

<!--========================Header Start=====================-->
    <?php
    require_once("includes/header.php");
    ?>
<!--========================Header End=====================-->
<style>
    .box{
        margin-bottom: 30px;
        background: #fff;
        position: relative;
        box-shadow: #B3B3B3 0px 0px 7px;
        box-sizing: border-box;
    }
    .padd_20{
        padding: 20px !important;
    }
</style>
<!-- Body Section Start -->
<div class="bodypart">
    <div class="container">
        
        <div class="box padd_20" style="border-bottom:1px solid red;">
            <!--<h1 style="color: red;">Error Page</h1>-->
            <p class="text-center"><?php echo (isset($_GET["error-code"])) ? getErrorMessageWithCode($_GET["error-code"]) : getErrorMessageWithCode() ?></p>
        </div>
        
    </div>
</div>

<!-- Body Section End --> 

<!--========================Footer Start=====================-->
    <?php
    require_once("includes/footer.php");
    ?>
<!--========================Footer End=====================-->
