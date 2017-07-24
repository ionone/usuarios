<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = __('title_UserDel', $lang);
    $btnActive = 0;
    $message = "";
    //si el usuario ya está logueado, le mandamos a su sitio
    if (!isAuthenticated())
        header("location:login.php");
    //Sólo rol de administrador
    if(!strstr($_SESSION["roles"],"[ADMIN_USER]") && !strstr($_SESSION["roles"],"[GROUP_USER]"))
        header("location:index.php");    
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    // comprobar formulario    
    if(isset($_POST['delUser']) && $_POST['delUser']==1){
        // El administrador ha confirmado la eliminación del usuario
        $resultset = $db->delete("users", "id ='".$_POST['idUser']."'");
        //$resultset = $db->send("DELETE FROM users WHERE id=".$_POST['idUser']);
        $resultset = $db->delete("actions", "iduser=".$_POST['idUser']);
        $message = "El usuario ".$_POST["lastname"]. " " .$_POST["firstname"]. " ha sido eliminado";
    }
    // comprobar que existe usuario    
    if (isset($_GET['userid']) && is_numeric($_GET['userid'])) {
        $usuario["id"] = $_GET['userid'];
        // Comprobar que el usuario que se pretende eliminar pertenece al mismo grupo que el administrador
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
        $usuario["enabled"] = $resultset[0]["enabled"];
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
                <?php if($message==""){?>
                <p class="lead">                    
                    <form method="post" action="userdel.php">
                        <input type="hidden" name="delUser" value="1"></input>
                        <input type="hidden" name="idUser" value="<?php echo $_GET['userid']?>"></input>
                        <div class="row label-danger">
                            <?php echo __('msb_Alert', $lang)?>
                            <div class="well well-sm">
                                <fieldset class="">
                                    <legend class="text-center"><?php echo __('msb_ConfirmDel', $lang) ?></legend>
                                    <div class='input-group col-lg-6'>
                                        <label><?php echo __('lb_FirstName', $lang)?></label>
                                        <input type='text' class="form-control" name="firstname" value="<?php echo $usuario["firstname"]?>" readonly/>
                                    </div>
                                    <div class='input-group col-lg-6'>
                                        <label><?php echo __('lb_LastName', $lang)?></label>
                                        <input type='text' class="form-control date" name="lastname" value="<?php echo $usuario["lastname"]?>" readonly/>
                                    </div>
                                    <div class='input-group col-lg-6'>
                                        <label><?php echo __('lb_Email', $lang)?></label>
                                        <input type='text' class="form-control date" name="email" value="<?php echo $usuario["email"]?>" readonly/>
                                    </div>
                                    <br/>
                                    <div class="col-lg-6">                                                                    
                                        <input type="submit" class="btn btn-danger" value="<?php echo __('bt_DelUser', $lang) ?>"/>
                                    </div>
                                    <div class="col-lg-6">
                                    <p>
                                        <a class="btn btn-primary" value="Cancelar" onclick="location.href = 'index.php'"><?php echo __('bt_Cancel', $lang) ?></a>
                                    </p>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </form>
                </p>
                <?php } else { ?>
                <p class="lead">
                    <div class="well well-sm">
                        <div class="row">
                        <legend class="text-center"><?php echo $message ?></legend>
                        <br/>                        
                        <div class="col-lg-12">
                            <p>
                                <a class="btn btn-primary" value="Cancelar" onclick="location.href = 'users.php'"><?php echo __('bt_Ok', $lang) ?></a>
                            </p>
                        </div>
                        </div></div>
                </p>
                    
                <?php } ?>
            </div>   
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>