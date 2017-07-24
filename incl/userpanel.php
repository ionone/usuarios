<?php

// Comprobación de seguridad
if(!isAuthenticated()) {
    echo "Error";
    exit();
}

?>
<div class="row header">
    <div class="col-lg-12">
        <h3><?php echo __('tx_PanelHeader', $lang)?></h3>
    </div>
</div>
<?php // Buscar los datos del grupo al que pertenece el usuario 
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    $consql = "SELECT g.id, g.name FROM groups g JOIN users u ON u.idgroup = g.id WHERE u.id=".$_SESSION["id"];
    $resultset = $db->send($consql);
    // Se prevé que un usuario sólo pertenezca a un grupo en caso contrario cambiar por un foreach
    if (!empty($resultset)) {
        $groupId = $resultset[0]["id"];
        $groupName = $resultset[0]["name"];
        ?>
        <div class="header">
            <!-- Gráfico General -->
            <div class="mark row">
                <h4><?php echo $groupName; ?></h4>
            </div>
            <?php
            $anarray = array();
            $i=1;
            $anarray = graphGroup($groupId);
            // función para ordenar
            foreach ($anarray as $key => $row) {
                $aux[$key] = $row['puntos'];
            }
            array_multisort($aux, SORT_DESC, $anarray);
            echo "<div class='row'>\n";
            echo    "<div class='col-lg-6'>\n";
            echo        "Nombre";
            echo    "</div>\n";
            echo    "<div class='col-lg-6'>\n";
            echo        "Puntos";
            echo    "</div>\n";            
            echo "</div>\n";
            foreach ($anarray as $key => $value){
                echo "<div class='row'>\n";
                echo    "<div class='col-lg-6 mark'>\n";                
                echo        $i++."º ";
                if($anarray[$key]["id"]==$_SESSION["id"]) echo "<span style='font-weight: bold'>";
                echo        "<a href='user.php?userid=".$anarray[$key]["id"]."'>\n";
                echo            $anarray[$key]["name"];
                echo        "</a>\n";
                if($anarray[$key]["id"]==$_SESSION["id"]) echo "</span>";
                echo    "</div>\n";
                echo    "<div class='col-lg-6 mark'>\n";
                echo        $anarray[$key]["puntos"];
                echo    "</div>\n";                
                echo "</div>\n";
            }           
            ?>
        </div>
    <?php } ?>
<?php // Menú para todos los usuarios //?>
<div class="header row">
    <div class="col-lg-6">
        <p>
            <a class="btn btn-lg btn-primary" href="addtravel.php" role="button"><?php echo __('bt_NewTravel', $lang)?></a>
        </p>
    </div>
    <div class="col-lg-6">
        <p>
            <a class="btn btn-lg btn-success" href="situation.php" role="button"><?php echo __('bt_Situation', $lang)?></a>
        </p>
    </div>    
</div>
<?php // Menú para todos los usuarios administradores//?>
<?php if($_SESSION['roles']!="[REGISTERED_USER]"){?>
<div class="header row">
    <h4 class="mark">Menú administración</h4>
    <div class="col-lg-6">
        <p>
            <a class="btn btn-lg btn-primary" href="users.php" role="button"><?php echo __('bt_Users', $lang)?></a>
        </p>
    </div>
    <div class="col-lg-6">
        <p>
            <a class="btn btn-lg btn-success" href="groups.php" role="button"><?php echo __('bt_Groups', $lang)?></a>
        </p>
    </div>    
</div>
<?php } ?>
<?php // Menú para el admin//?>
<?php if($_SESSION["roles"]=="[ADMIN_USER]"){ ?>
<div class="header row">
    <h4 class="mark">Menú super-administración</h4>
    <div class="col-lg-6">
        <p>
            <a class="btn btn-lg btn-primary" href="admin.php" role="button"><?php echo __('bt_UsersAdmin', $lang)?></a>
        </p>
    </div>
    <div class="col-lg-6">
        
    </div>
</div>
<?php } ?>