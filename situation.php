<?php
error_reporting(E_ALL);
include ("incl/data.php");
include ("incl/functions.php");
include ("incl/session_gestion.php");
initiate();
$TitlePag = "title_Situation";
$btnActive = 0;
if (!isAuthenticated()) {
    header("location:index.php");
}
$db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
// Tratamiento formulario
$message = "";
if (isset($_POST['Enviado']) && $_POST['Enviado'] == 1) {    
    // Comprobar que no faltan datos
    // Fechas
    if (isset($_POST['dateinit']) && $_POST['dateinit'] != "") {
        // Comprobar fecha fin
        if (isset($_POST['dateend']) && $_POST['dateend'] != "") {
            // ahora comprobamos incongruencias
            // fecha de fin anterior a fecha de inicio
            $arrayGroup = groupsParameters($_SESSION["idgroup"]);
            $diasNDP = $arrayGroup[0]["NDP"];            
            $ini = new DateTime($_POST["dateinit"]);
            $fin = new DateTime($_POST["dateend"]);
            if($ini>=$fin) {
                $message = __('msb_DateNull', $lang);
            }else{  
                $dif = s_datediff("d", $ini, $fin) + 1;
                if ($dif<$diasNDP) {
                    $message = __('msb_DateInvalid', $lang). " $diasNDP días";
                } else
                    $message = __('msb_Confirmed', $lang);   
            }
        } else
            $message = __('msb_ErrorDateEnd', $lang);
    } else
        $message = __('msb_ErrorDateInit', $lang);
}

// Tratamiento formulario confirmación
if (isset($_POST['Enviado']) && $_POST['Enviado'] == 2) {
    // Comprobamos antes de grabar que no exista ya algún día del tramo con algo
    $sql = "SELECT * FROM actions WHERE iduser=" . $_SESSION['id'] . " AND dateAction BETWEEN '" . $_POST['dateinit'] . "'  AND '" . $_POST['dateend'] . "'";
    $resultset = $db->send($sql);
    //echo $sql;
    if (!$resultset) {
        // grabar accion conductor
        $fechagrabacion = date("Y-m-d H:i:s");
        $ini = new DateTime($_POST["dateinit"]);
        $fin = new DateTime($_POST["dateend"]);
        $dif = s_datediff("d", $ini, $fin) + 1;
        $anarray = array();
        for($i=0;$i<$dif;$i++){
            $grab = $ini->format("Y-m-d");
            //$anarray["id"] = false;
            $anarray["iduser"] = $_SESSION['id'];
            $anarray["dateregister"] = "$fechagrabacion";
            $anarray["dateAction"] = "$grab";
            $anarray["actionGrab"] = "NDP";
            $anarray["horario"] = "";
            //print_r($anarray);
            //$sql = "INSERT INTO actions VALUES (DEFAULT, " . $_SESSION['id'] . ", '" . $fechagrabacion . "', '" . $grab . "', 'NDP', '');";
            //echo $sql."<br/>";
            $resultset = $db->insert("actions", $anarray);
            $ini = date_add($ini, date_interval_create_from_date_string("1 day"));            
        }                                
        $db->close();
        header("location:index.php");
    } else
        $message = __('msb_ErrorNDP', $lang);
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
                        <h3><?php echo __('title_Situation', $lang) ?></h3>
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
                        <h4><?php echo $groupName; ?></h4>
                    </div>
                    <div class="row">&nbsp;</div>
                    <?php if ($message != __('msb_Confirmed', $lang)) { ?>
                        <div class="row">
                            <h2 style="color:red;"><?php echo $message; ?></h2>
                        </div>
                        <div class="row">
                            <form method="post" action="situation.php">
                                <input type="hidden" name="Enviado" value="1"/>

                                <div class="col-lg-12">

                                    <div class="col-lg-12">
                                        <div class="well well-sm">

                                            <fieldset>
                                                <legend class="text-center">Fecha inicio</legend>
                                                <div class='input-group date' id=''>
                                                    <input type='text' id="divMiCalendario1" class="form-control" name = "dateinit" readonly/>                      
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">                                    
                                        <div class="well well-sm">
                                            <fieldset>
                                                <legend class="text-center">Fecha fin</legend>
                                                <div class='input-group date' id=''>
                                                    <input type='text' id="divMiCalendario2" class="form-control" name = "dateend" readonly/>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="well well-sm">
                                            <fieldset>
                                                <legend class="text-center">Crear Situación</legend>
                                                <div class="col-lg-6">                                                                    
                                                    <input type="submit" class="btn btn-primary" value="<?php echo __('bt_Situation', $lang) ?>"/>
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
                                    <div class="pull-left panel"><?php echo __('txt_Help2', $lang) ?></div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <h2><?php echo $message; ?></h2>
                    <form method="post" action="situation.php">
                        <input type="hidden" name="Enviado" value="2"/>
                        <div class="row">
                            <div class="col-lg-6">
                                <h4>Fecha Inicio</h4>
                                <h2><?php echo $_POST['dateinit'] ?></h2>
                            </div>
                            <div class="col-lg-6">
                                <h4>Fecha Fin</h4>
                                <h2><?php echo $_POST['dateend'] ?></h2>                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 mark">
                                <h4><?php echo "Total días de No Disponibilidad: $dif días"?></h4>
                            </div>
                        </div>
                        <div class="row">&nbsp;</div>
                        <input type="hidden" name="Enviado" value="2"/>
                        <input type="hidden" name="dateinit" value="<?php echo $_POST['dateinit'] ?>"/>
                        <input type="hidden" name="dateend" value="<?php echo $_POST['dateend'] ?>"/>
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="submit" class="btn btn-success" value="<?php echo __('bt_Ok', $lang) ?>"/>
                            </div>
                            <div class="col-lg-6">
                                <input type="button" class="btn btn-danger" value="<?php echo __('bt_Cancel', $lang) ?>" onclick="location.href = 'situation.php'"/>
                            </div>
                        </div>
                    </form>
                                    <?php } ?>
            </div>
        </div>
                                    <?php $db->close() ?>
                                    <?php include ("incl/footer.php") ?>            
                                    </div> <!-- /container -->
    </body>
</html>