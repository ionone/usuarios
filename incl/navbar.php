<?php    
    $menu[1] = "";
    $menu[2] = "";
    $menu[3] = "";
    $menu[4] = "";
    $menu[$btnActive] = " class='active'";    
?>
<div class="header clearfix">
    <nav>
        <ul class="nav nav-pills pull-right">
            <li role="presentation" <?php echo $menu[1]?>><a href="index.php">Inicio</a></li>
            <li role="presentation" <?php echo $menu[2]?>><a href="#">About</a></li>
            <li role="presentation" <?php echo $menu[3]?>><a href="#">Contact</a></li>
            <?php
            if (isAuthenticated() != TRUE) {
            ?>
                <li role="presentation" <?php echo $menu[4]?>><a href="login.php">Login</a></li>                
            <?php } else { ?>
                <li role="presentation" <?php echo $menu[4]?>><a href="incl/logout.php">Logout</a></li>
            <?php } ?>
        </ul>
    </nav>
    <h3 class="text-muted">Ranking v2</h3>
</div>
<div class="header active">
    <?php if(isAuthenticated()) 
        echo "Bienvenido ".$_SESSION["name"];?>
</div>