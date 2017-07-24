<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = "title_Index";
    $btnActive = 1;
    $message = "";
    
    if(!strstr($_SESSION["roles"],"[ADMIN_USER]") && !strstr($_SESSION["roles"],"[GROUP_USER]"))
        header("location:index.php");
    
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    
    // mini formulario deshabilitar/habilitar
    if(isset($_GET['enabled']) && $_GET['enabled']!=""){
        $envio["enabled"]= 1;
        $db->update("users", $envio, "id = ". $_GET['enabled']);
    }
    if(isset($_GET['disabled']) && $_GET['disabled']!=""){
        $envio["enabled"]= 0;
        $db->update("users", $envio, "id = ". $_GET['disabled']);
    }
    // Tratamiento formulario
    if(isset($_POST['frmUser']) && $_POST['frmUser']==1){        
        $anarray = array();
        if(isset($_POST['pass1']) && $_POST['pass1']!=""){
            if($_POST['pass2']!=$_POST['pass1']) $message = __('msb_ErrorPass', $lang);
            if($message=="") {
                $pass = md5($_POST['pass1']);
                $anarray["password"] = $pass;
            }
        }
        if($message==""){
            if($_POST['firstname']=="") $message = __('msb_ErrorFirstname', $lang);        
            if($message==""){
                $anarray["firstname"] = $_POST['firstname'];
                if($_POST['lastname']=="") $message = __('msb_ErrorLastname', $lang);            
                if($message==""){
                    $anarray["lastname"] = $_POST['lastname'];
                    if($_POST['email']=="") $message = __('msb_ErrorEmail1', $lang);
                    if($message==""){
                        if(!verificaremail($_POST['email'])) $message = __('msb_ErrorEmail2', $lang);
                        if($message==""){
                            print_r($_POST);
                            $anarray["email"] = $_POST['email'];                            
                            $pti = $_POST['ptosajuste'];
                            settype ($pti, "float");
                            $anarray["ajuste"] = $pti;                            
                            $sql = implode(",", $anarray);
                            // Actualizamos los datos del usuario                                                        
                            $resultset = $db->update("users", $anarray, "id = ".$_POST['idUser']);
                            $message="Datos actualizados";
                            $_GET['userid'] = $_POST['idUser'];
                        }
                    }
                }
            }
        }
    }
    if (isset($_GET['userid']) && is_numeric($_GET['userid'])) {
        $usuario["id"] = $_GET['userid'];
        // Comprobar que el usuario que se pretende observar pertenece al mismo grupo que el visitante autenticado
        $consql = "idgroup = " . $_SESSION["idgroup"] . " AND id = " . $usuario["id"];
        if(strstr($_SESSION['roles'], "[ADMIN_USER]"))
                $consql = "id = " . $usuario["id"];
        $resultset = $db->select("users", $consql);
        if (empty($resultset)) {
            $db->close();
            header("location:index.php");
        }
        $usuario["name"] = $resultset[0]["name"];
        $usuario["lastname"] = $resultset[0]["lastname"];
        $usuario["firstname"] = $resultset[0]["firstname"];
        $usuario["email"] = $resultset[0]["email"];
        $usuario["ajuste"] = $resultset[0]["ajuste"];        
    }
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
                <?php // Buscar los datos del grupo al que pertenece el usuario 
                    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
                    $consql = "SELECT g.id, g.name FROM groups g JOIN users u ON u.idgroup = g.id WHERE u.id=".$_SESSION["id"].";";
                    $resultset = $db->send($consql);
                    // Se prevé que un usuario sólo pertenezca a un grupo en caso contrario cambiar por un foreach
                    if (!empty($resultset)) {
                        $groupId = $resultset[0]["id"];
                        $groupName = $resultset[0]["name"];
                        ?>
                        <div class="header">                    
                            <div class="mark row">
                                <h4><?php echo $groupName; ?></h4>
                            </div>
                            <?php if ($message!="" && $message!="Datos actualizados"){?>
                                <div class="row label-danger">
                                    <?php echo $message; ?>
                                </div>
                            <?php } elseif ($message=="Datos actualizados"){?>
                                <div class="row label-success">
                                    <?php echo $message; ?>
                                </div>
                            <?php } ?>
                            <div class="row">&nbsp;</div>
                            <?php if(isset($_GET["userid"]) && $_GET['userid']!=""){?>
                                <div class="mark row">
                                    <?php echo $groupName . " :: " . $usuario["name"]; ?>
                                </div>                            
                                <div class="row">&nbsp;</div>
                                <form action="users.php?userid=<?php echo $_GET['userid']?>" method="post">
                                    <input type="hidden" name="frmUser" value="1"></input>
                                    <input type="hidden" name="idUser" value="<?php echo $_GET['userid']?>"></input>
                                
                                    <div class="row">
                                        <div class="well well-sm">
                                            <fieldset>
                                                <legend class="text-center"><?php echo __('title_User', $lang) ?></legend>
                                                <div class='input-group col-lg-6'>
                                                    <label><?php echo __('lb_FirstName', $lang)?></label>
                                                    <input type='text' class="form-control" name="firstname" value="<?php echo $usuario["firstname"]?>"/>
                                                </div>
                                                <div class='input-group col-lg-6'>
                                                    <label><?php echo __('lb_LastName', $lang)?></label>
                                                    <input type='text' class="form-control date" name="lastname" value="<?php echo $usuario["lastname"]?>"/>
                                                </div>
                                                <div class='input-group col-lg-6'>
                                                    <label><?php echo __('lb_Email', $lang)?></label>
                                                    <input type='text' class="form-control date" name="email" value="<?php echo $usuario["email"]?>"/>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="well well-sm">
                                            <fieldset>
                                                <legend class="text-center"><?php echo __('lbl_PtosTotal', $lang) ?></legend>
                                                <div class='input-group col-lg-4'>
                                                    <label>Puntos Totales</label>
                                                    <input type='text' class="form-control" name="ptosuser" readonly value="<?php echo puntos($groupId, $usuario["id"])?>"/>
                                                </div>
                                                <div class='input-group col-lg-4'>
                                                    <label>Puntos por Ajuste</label>
                                                    <input type='text' class="form-control" name="ptosajuste" value="<?php echo $usuario["ajuste"]?>"/>
                                                </div>
                                            </fieldset>                                              
                                        </div>
                                    </div>

                                    <?php include 'incl/monthpanel.php';?>
                                    <div class="row">&nbsp;</div>
                                    <div class="well well-sm">
                                        <fieldset>
                                            <legend class="text-center"><?php echo __('tx_Options', $lang) ?></legend>
                                            <div class='input-group col-lg-5'>
                                                <label><?php echo __('lb_NewPass1', $lang)?></label>
                                                <input type='password' class="form-control" name="pass1"/>
                                            </div>
                                            <div class='input-group col-lg-5'>
                                                <label><?php echo __('lb_NewPass2', $lang)?></label>
                                                <input type='password' class="form-control" name="pass2"/>
                                            </div>                                            
                                        </fieldset>                                              
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <input type="submit" class="btn btn-primary" value="<?php echo __('bt_Update', $lang) ?>"></input>
                                        </div> 
                                        <div class="col-lg-6">
                                            <a class="btn btn-success" onclick="location.href = 'users.php'"><?php echo __('bt_Ok', $lang) ?> 
                                            </a>
                                        </div>         
                                    </div>
                                </form>
                            <?php } else { ?>
                            <!-- Tabla de Usuarios -->
                            <div class="row">
                                <table>
                                <?php                                
                                $resultset = $db->select("users", "idgroup=".$groupId);
                                // listado de usuarios y/o opciones
                                echo "<thead><tr>\n";
                                echo "<td>Usuario</td><td></td><td>Nombre</td><td></td><td>Apellidos</td><td></td><td>Puntos</td><td></td><td colspan='3'>Opciones</td>";
                                echo "</tr></thead>\n";
                                $i=0;
                                foreach ($resultset as $key => $value){
                                    $i++;
                                    if($i % 2 == 0 ) $styleTr = 'background-color: #fafafa;';
                                        else $styleTr = 'background-color: #afafaf; color: #fdfdfd';
                                    if($resultset[$key]["enabled"]=='0') $styleTr = 'background-color: #F6CEE3; color: red; border-top: 2px solid white; border-bottom: 2px solid white';
                                    echo "<tr height='40'>\n";
                                        echo "<td style='".$styleTr."' align='left'>\n";
                                        echo "<a href='users.php?userid=".$resultset[$key]["id"]."'>";
                                        echo $resultset[$key]["name"];
                                        echo "</a>\n";                                
                                        echo "</td>\n";
                                        echo "<td style='".$styleTr."' width='10'></td>";
                                        echo "<td style='".$styleTr."' align='left'>\n";                                    
                                        echo $resultset[$key]["firstname"];                                                                    
                                        echo "</td>\n";
                                        echo "<td style='".$styleTr."' width='10'></td>";
                                        echo "<td style='".$styleTr."' align='left'>\n";
                                        echo $resultset[$key]["lastname"];
                                        echo "</td>\n";
                                        echo "<td style='".$styleTr."' width='10'></td>";
                                        echo "<td style='".$styleTr."' align='right'>\n";
                                        echo puntos($groupId, $resultset[$key]["id"]);
                                        echo "</td>\n";
                                        echo "<td style='".$styleTr."' width='10'></td>";
                                        echo "<td style='".$styleTr."'>\n";
                                        echo "<a href='userdel.php?userid=".$resultset[$key]["id"]."'>";
                                        echo "Eliminar";
                                        echo "</a>\n";
                                        echo "</td>\n";
                                        echo "<td style='".$styleTr."' width='10'></td>";
                                        echo "<td style='".$styleTr."'>\n";
                                        if($resultset[$key]["enabled"]=='1')
                                            echo "<a href='users.php?disabled=".$resultset[$key]["id"]."' class='badge label-danger'>Deshabilitar</a>";
                                        else 
                                            echo "<a href='users.php?enabled=".$resultset[$key]["id"]."' class='badge label-success'>Habilitar</a>";
                                        echo "</td>\n";
                                    echo "</tr>\n";
                                } ?>                                    
                                </table>                                
                            </div>
                            <!-- Fin Tabla de usuarios -->
                            <div class="row">&nbsp;</div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <a class="btn btn-primary" onclick="location.href = 'useradd.php'"><?php echo __('bt_AddUser', $lang) ?>
                                        </a>
                                    </div>         
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="header">No se han hallado usuarios en el grupo</div>
                    <?php } ?>
                <?php $db->close();?>
            <?php }?>
            </div>
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>