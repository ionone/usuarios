<?php
error_reporting(E_ALL);
include ("incl/data.php");
include ("incl/functions.php");
include ("incl/session_gestion.php");
initiate();
$TitlePag = "title_User";
$btnActive = 0;
if (!isAuthenticated()) {
    header("location:index.php");
}
$db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
if (isset($_GET['userid']) && is_numeric($_GET['userid'])) {
    $usuario["id"] = $_GET['userid'];
    // Comprobar que el usuario que se pretende observar pertenece al mismo grupo que el visitante autenticado    
    $consql = "idgroup = " . $_SESSION["idgroup"] . " AND id = " . $usuario["id"];
    $resultset = $db->select("users", $consql);
    if (empty($resultset)) {
        $db->close();
        header("location:index.php");
    }
    $usuario["name"] = $resultset[0]["name"];
    $usuario["lastname"] = $resultset[0]["lastname"];
    $usuario["firstname"] = $resultset[0]["firstname"];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <?php include ("incl/header.php") ?>
    <body>
        <div class="container">
            <?php include ("incl/navbar.php") ?>
            <div class="jumbotron">            
                <div class="row header">
                    <div class="col-lg-12">
                        <h3><?php echo __('title_User', $lang) ?></h3>
                    </div>
                </div>
                <?php
                // Buscar los datos del grupo al que pertenece el usuario
                $consql = "SELECT g.id, g.name FROM groups g JOIN users u ON u.idgroup = g.id WHERE u.id=" . $_SESSION["id"] . ";";
                $resultset = $db->send($consql);
                // Se prevé que un usuario sólo pertenezca a un grupo en caso contrario cambiar por un foreach
                if (!empty($resultset)) {
                    $groupId = $resultset[0]["id"];
                    $groupName = $resultset[0]["name"];
                }
                ?>
                <div class="header">
                    <div class="mark row">
                        <?php echo $groupName . " :: " . $usuario["firstname"] . " " . $usuario["lastname"]; ?>
                    </div>
                    <div class="row">&nbsp;</div>
                    
                        <?php include 'incl/monthpanel.php';?>
                    
                    <div class="row">&nbsp;</div>
                    <div class="row">
                        <div class="col-lg-12">
                            <a class="btn btn-primary" onclick="location.href = 'index.php'"><?php echo __('bt_Ok', $lang) ?> 
                            </a>
                        </div>         
                    </div>
                </div>
            </div>
            <?php $db->close() ?>
            <?php include ("incl/footer.php") ?>            
        </div> <!-- /container -->
    </body>
</html>