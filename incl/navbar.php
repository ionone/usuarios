<?php    
    $menu[1] = "";
    $menu[2] = "";
    $menu[3] = "";
    $menu[4] = "";
    $menu[5] = "";
    $menu[$btnActive] = " class='active'";    
?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-THKDRQV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<script type="text/javascript">
    google_ad_client = "ca-pub-2679800710610708";
    google_ad_slot = "3896654624";
    google_ad_width = 728;
    google_ad_height = 90;
</script>
<!-- Publi1 -->
<script type="text/javascript"
src="//pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<div class="header clearfix">
    <nav>
        <ul class="nav nav-pills pull-right">
            <li role="presentation" <?php echo $menu[1]?>><a href="index.php"><?php echo __('mn_Home', $lang)?></a></li>
            <li role="presentation" <?php echo $menu[2]?>><a href="index.php#about"><?php echo __('mn_About', $lang)?></a></li>
            <li role="presentation" <?php echo $menu[3]?>><a href="#"><?php echo __('mn_Contact', $lang)?></a></li>
            <?php
            if (isAuthenticated() != TRUE) {
            ?>
                <li role="presentation" <?php echo $menu[4]?>><a href="login.php"><?php echo __('mn_Login', $lang)?></a></li>                
            <?php } else { ?>
                <li role="presentation" <?php echo $menu[4]?>><a href="profile.php"><?php echo __('mn_Profile', $lang)?></a></li>
                <li role="presentation" <?php echo $menu[5]?>><a href="logout.php"><?php echo __('mn_Logout', $lang)?></a></li>
            <?php } ?>
        </ul>
    </nav>
    <h3 class="text-muted">Ranking v2</h3>
</div>
<div class="header active">
    <?php if(isAuthenticated()) 
        echo __('tx_WelcomeTitle', $lang)." <a href='profile.php'>".$_SESSION["name"]."</a>";?>    
</div>