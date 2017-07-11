<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = "title_Index";
    $btnActive = 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <?php include ("incl/header.php")?>
    <body>
        <div class="container">
            <?php include ("incl/navbar.php")?>
            <div class="jumbotron">
            <?php if(!isAuthenticated()){?>
                <h1><?php echo __('tx_WelcomeTitle', $lang)?></h1>
                <p class="lead"><?php echo __('tx_Welcome', $lang)?></p>
                <p><a class="btn btn-lg btn-success" href="login.php" role="button"><?php echo __('bt_Login', $lang)?></a></p>            
            <?php } else { ?>
                <?php include ("incl/userpanel.php")?>
            <?php }?>
            </div>
            <!--<div class="row marketing">
                <div class="col-lg-6">
                    <h4>Subheading</h4>
                    <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

                    <h4>Subheading</h4>
                    <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

                    <h4>Subheading</h4>
                    <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
                </div>

                <div class="col-lg-6">
                    <h4>Subheading</h4>
                    <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

                    <h4>Subheading</h4>
                    <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

                    <h4>Subheading</h4>
                    <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
                </div>
            </div>-->
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>