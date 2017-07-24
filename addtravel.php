<?php
error_reporting(E_ALL);
include ("incl/data.php");
include ("incl/functions.php");
include ("incl/session_gestion.php");
initiate();
$TitlePag = "title_AddTravel";
$btnActive = 0;
if (!isAuthenticated()) {
    header("location:index.php");
}
$db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
// Tratamiento formulario
$message = "";
// inicializacion variables
$tramo = array("", "M", "T", "N");
if (isset($_POST['Enviado']) && $_POST['Enviado'] == 1) {
    // Comprobar que no faltan datos
    // 1º Fecha
    if (isset($_POST['date']) && $_POST['date'] != "") {
        // 2º Comprobar horario
        if (isset($_POST['hora']) && $_POST['hora'] != "") {
            $message = __('msb_Confirmed', $lang);
        } else
            $message = __('msb_ErrorHour', $lang);
    } else
        $message = __('msb_ErrorDate', $lang);
    // Comprobar que no existe un viaje anterior ya grabado para ninguno de los usuarios elegidos
    $usuarios = array();
    if (isset($_POST['CheckPassenger'])) {        
        foreach ($_POST['CheckPassenger'] as $value) {
            $usuarios[] = $value;
        }
    }
    if($message==__('msb_Confirmed', $lang)){
        $usuarios[] = $_SESSION['id'];
        $horario = $tramo[$_POST['hora']];
        foreach ($usuarios as $value){
            $sql = "SELECT u.firstname, u.lastname, u.name FROM users u INNER JOIN actions a ON u.id = a.iduser WHERE ";
            $sql .= " a.iduser = ". $value . " AND a.dateAction = '" . $_POST['date'] ."' AND a.horario = '" .$horario ."'";
            $resultset = $db->send($sql);        
            if (!empty($resultset)){
                $message = __('msb_ErrorDuplicated', $lang). $resultset[0]["firstname"]. " ". $resultset[0]["lastname"];
            }
        }
    }
    // Comprobar que no se ha grabado algún día de NO disponibilidad para alguno de los viajeros
    if($message==__('msb_Confirmed', $lang)){        
        foreach ($usuarios as $value){
            $sql = "SELECT u.firstname, u.lastname, u.name FROM users u INNER JOIN actions a ON u.id = a.iduser WHERE ";
            $sql .= " a.iduser = ". $value . " AND a.dateAction = '" . $_POST['date'] ."';";
            $resultset = $db->send($sql);
            if (!empty($resultset)){
                $message = __('msb_ErrorNDP', $lang). " => ".$resultset[0]["firstname"]. " ". $resultset[0]["lastname"];
            }
        }
    }
}

// Tratamiento formulario confirmación
if (isset($_POST['Enviado']) && $_POST['Enviado'] == 2) {    
    // Comprobamos, antes de grabar que no exista ya
    $horario = $tramo[$_POST['hora']];
    $sql = "iduser=" . $_SESSION['id'] . " AND dateAction ='" . $_POST['date'] . "' AND horario = '" . $horario . "' AND actionGrab = 'CON';";
    $resultset = $db->select("actions", $sql);
    if (!$resultset) {
        // grabar accion conductor
        $fechagrabacion = date("Y-m-d H:i:s");
        //$sql = "INSERT INTO actions VALUES (DEFAULT, " . $_SESSION['id'] . ", '" . $fechagrabacion . "', '" . $_POST['date'] . "', 'CON', '" . $horario . "');";
        $anarray["iduser"] = $_SESSION['id'];
        $anarray["dateregister"] = "$fechagrabacion";
        $anarray["dateAction"] = $_POST['date'];
        $anarray["actionGrab"] = "CON";
        $anarray["horario"] = "$horario";
        $resultset = $db->insert("actions", $anarray);
        //$resultset = $db->send($sql);
        // grabar accion pasajero
        if (isset($_POST['CheckPassenger'])) {
            $anarray2 = array();
            foreach ($_POST['CheckPassenger'] as $value) {
                $anarray2["iduser"] = $value;
                $anarray2["dateregister"] = "$fechagrabacion";
                $anarray2["dateAction"] = $_POST['date'];
                $anarray2["actionGrab"] = "PAS";
                $anarray2["horario"] = "$horario";
                $resultset = $db->insert("actions", $anarray2);//INSERT INTO actions VALUES (DEFAULT, " . $value . ", '" . $fechagrabacion . "', '" . $_POST['date'] . "', 'PAS', '" . $horario . "');");
                //$resultset = $db->send("INSERT INTO actions VALUES (DEFAULT, " . $value . ", '" . $fechagrabacion . "', '" . $_POST['date'] . "', 'PAS', '" . $horario . "');");
            }
        }
        $db->close();
        header("location:index.php");
    } else
        $message = __('msb_ErrorDuo', $lang);
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
                        <h3><?php echo __('title_AddTravel', $lang) ?></h3>
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
                        <?php echo $groupName; ?>
                    </div>
                    <div class="row">&nbsp;</div>
                    <?php if ($message != __('msb_Confirmed', $lang)) { ?>
                        <div class="row">
                            <h2 style="color:red;"><?php echo $message; ?></h2>
                        </div>
                    <div class="row">
                        <form method="post" action="addtravel.php">
                            <input type="hidden" name="Enviado" value="1"/>

                            <div class="col-lg-12">
                                
                                <div class="col-lg-12">
                                    <div class="well well-sm">
                                        <fieldset>
                                            <legend class="text-center"><?php echo __('lbl_Passenger', $lang) ?></legend>
                                            <div class='input-group date'>
                                                <?php
                                                    $resultset = $db->select("users", "id <> '" . $_SESSION['id'] . "' AND enabled=1 AND idgroup = " . $groupId);
                                                    if ($resultset) {
                                                        foreach ($resultset as $key => $value) {
                                                            ?>
                                                            <label class="checkbox-inline">
                                                                <input type="checkbox" value="<?php echo $value['id'] ?>" name="CheckPassenger[]"/> <span class="badge label-info"><?php echo $value['firstname']. " ". $value['lastname'] ?></span>
                                                            </label>
                                                        <?php
                                                        }
                                                    }?>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="col-lg-12">                                    
                                    <div class="well well-sm">
                                        <fieldset>
                                            <legend class="text-center">Fecha del Trayecto</legend>
                                            <div class='input-group date'>
                                            <input type='text' class="form-control" id='divMiCalendario1' name="date" readonly/>
                                            </div>
                                        </fieldset>          
                                    </div>
                                </div>

<!--
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                            </span>-->                                
                                
                                <div class="col-md-12">
                                    <div class="well well-sm">
                                        <fieldset>
                                            <legend class="text-center">Horario del Trayecto</legend>
                                            <div class='input-group date'>
                                            <label class="checkbox-inline">
                                            <input type="radio" id="checkboxEnLinea1" value="1" name="hora"/> Mañana
                                            </label>
                                            <label class="checkbox-inline">
                                            <input type="radio" id="checkboxEnLinea2" value="2" name="hora"/> Tarde
                                            </label>
                                            <label class="checkbox-inline">
                                            <input type="radio" id="checkboxEnLinea3" value="3" name="hora"/> Noche
                                            </label>
                                            </div>
                                        </fieldset>          
                                    </div>
                                </div>
                            <div class="col-lg-12">
                                <div class="well well-sm">
                                <fieldset>
                                    <legend class="text-center">Crear Trayecto</legend>
                                    <div class="col-lg-6">                                                                    
                                        <input type="submit" class="btn btn-primary" value="<?php echo __('bt_NewTravel', $lang) ?>"/>
                                    </div>
                                    <div class="col-lg-6">
                                    <p>
                                        <a class="btn btn-danger" value="Cancelar" onclick="location.href = 'index.php'"/><?php echo __('bt_Cancel', $lang) ?></a>
                                    </p>
                                    </div>
                                </fieldset>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="well well-sm">
                                <fieldset>
                                    <legend class="text-center">Ayuda</legend>
                                    <div class="pull-left panel"><?php echo __('txt_Help1', $lang)?></div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    
                    <?php } else { ?>
                
                    <h2><?php echo $message; ?></h2>
                    <form method="post" action="addtravel.php">
                        <input type="hidden" name="Enviado" value="2"/>
                        <div class="row">
                            <div class="col-lg-6">
                                <h4>Fecha y Horario</h4>
                                <h2><?php echo $_POST['date'] ?></h2>                                            
                                <?php $h = array("mañana", "tarde", "noche") ?>
                                <h2><?php echo $h[$_POST['hora'] - 1] ?></h2>                        
                            </div>
                            <div class="col-lg-6">
                                <h4>Pasajeros</h4>
                                <?php
                                if (isset($_POST['CheckPassenger'])) {
                                    $i = 0;
                                    $j = 0;
                                    foreach ($_POST['CheckPassenger'] as $value) {
                                        $resultset = $db->select("users", "id = '" . $value . "'");
                                        if ($i == 3) {
                                            $j++;   // fila
                                            $i = 0;   // col
                                        ?>                                            
                                        <?php } ?>
                                        <div class="4u">                                    
                                            <div class="thumbnail">                                    
                                                <p>Pasajero <?php echo ++$i; ?></p>
                                                <a href="#"><img src="images/<?php echo $resultset[0]['picture'] ?>" height="150" /></a>
                                                <h2><?php echo $resultset[0]['name'] ?></h2>
                                            </div>
                                        </div>                                                                
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <input type="hidden" name="Enviado" value="2">
                        <input type="hidden" name="date" value="<?php echo $_POST['date'] ?>">
                        <input type="hidden" name="hora" value="<?php echo $_POST['hora'] ?>">
                        <?php
                        if (isset($_POST['CheckPassenger'])) {
                            foreach ($_POST['CheckPassenger'] as $value) {
                            ?>
                                <input type="hidden" name="CheckPassenger[]" value="<?php echo $value ?>">
                                <?php
                            }
                        }?>
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="submit" class="btn btn-success" value="<?php echo __('bt_Ok', $lang) ?>"/>
                            </div>
                            <div class="col-lg-6">
                                <input type="button" class="btn btn-danger" value="<?php echo __('bt_Cancel', $lang) ?>" onclick="location.href = 'addtravel.php'"/>
                            </div>
                        </div>
                    </form>
            </div>
                <?php } ?>
            </div>
        </div>
<?php $db->close() ?>
<?php include ("incl/footer.php") ?>            
    </div> <!-- /container -->        
</body>
</html>