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
            </div><a name="about"></a>
            <div class="row marketing">
                <div class="col-lg-6">
                    <h4><?php echo __('tit_cab1', $lang)?></h4>
                    <p><?php echo __('tit_text1', $lang)?></p>

                    <h4><?php echo __('tit_cab2', $lang)?></h4>
                    <p><?php echo __('tit_text2', $lang)?></p>
                    
                </div>

                <div class="col-lg-6">
                    <h4><?php echo __('tit_cab3', $lang)?></h4>
                    <p><?php echo __('tit_text3', $lang)?></p>

                    <h4><?php echo __('tit_cab4', $lang)?></h4>
                    <p><?php echo __('tit_text4', $lang)?></p>                   
                </div>
            </div>
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>