<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = __('title_UserAdd', $lang);
    $btnActive = 0;
    $message = "";
    
    if (!isAuthenticated())
        header("location:login.php");
    //Sólo rol de administrador
    if(!strstr($_SESSION["roles"],"[ADMIN_USER]") && !strstr($_SESSION["roles"],"[GROUP_USER]"))
        header("location:index.php");    
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    // comprobar formulario
    $usuario["name"] ="";
    $usuario["firstname"] ="";
    $usuario["lastname"] ="";
    $usuario["email"] ="";
    $usuario["lang"] ="es";
    $usuario["ajuste"] ="0";
    if(isset($_POST['frmUser']) && $_POST['frmUser']==1){        
        $usuario["name"] = $_POST['nick'];
        $usuario["roles"] = "[REGISTERED_USER]";
        $usuario["firstname"] = $_POST['firstname'];
        $usuario["lastname"] = $_POST['lastname'];
        $usuario["email"] = $_POST['email'];
        $usuario["lang"] = $_POST['lang'];
        $usuario["ajuste"] = $_POST['ajuste'];
        $usuario["picture"] = '';
        $usuario["auth_key"] = '';
        $usuario["enabled"] = 1;
        $usuario["password"] = md5(date("Y-m-d H:i:s"));
        $usuario["idgroup"] = $_POST['groupId'];
        $usuario["token"] = md5($_POST['nick'].$_POST["email"]);
        //¿faltan datos?
        if(!isset($_POST['nick']) || $_POST['nick']=="") $message = "Falta Nick";
        if(!isset($_POST['firstname']) || $_POST['firstname']=="") $message .= " Falta Nombre";
        if(!isset($_POST['lastname']) || $_POST['lastname']=="") $message .= " Falta Apellido";
        if(!isset($_POST['email']) || $_POST['email']=="") $message .= " Falta Email";
        // ¿Datos correctos?
        if(!verificaremail($_POST['email'])) $message .= " Email incorrecto";
        // ¿Nick Unico?
        if($db->send("SELECT name FROM users WHERE name='".$_POST['nick']."'")) $message=" Nick en uso";
        // ¿Email Unico?
        if($db->send("SELECT email FROM users WHERE email='".$_POST['email']."'")) $message=" Email en uso";
        if($message=="") {                       
            $resultset = $db->insert("users", $usuario); 
            $message = "Se ha creado un nuevo usuario";
            
        }
        
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
                <?php if($message!="Se ha creado un nuevo usuario"){?>
                <p class="lead">                    
                    <div class="mark row">
                        <?php echo $groupName; ?>
                    </div>
                    <?php if($message!=""){?>
                    <div class="row label-danger"><?php echo $message?></div>
                    <?php } ?>
                    <form action="useradd.php" method="post">
                        <input type="hidden" name="frmUser" value="1"></input>
                        <input type="hidden" name="groupId" value="<?php echo $groupId?>"></input>
                        <div class="row">
                            <div class="well well-sm">
                                <fieldset>
                                    <legend class="text-center"><?php echo __('title_User', $lang) ?></legend>
                                    <div class='input-group col-lg-6'>
                                        <label><?php echo __('lb_Nick', $lang)?></label>
                                        <input type='text' class="form-control" name="nick" value="<?php echo $usuario["name"]?>"/>
                                    </div>
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
                                        <input type='email' class="form-control date" name="email" value="<?php echo $usuario["email"]?>"/>
                                    </div>
                                    <div class='input-group col-lg-6'>
                                        <label><?php echo __('tx_Language', $lang)?></label><br/>
                                        <select name="lang" id="lang" >
                                            <?php 
                                            $selected["es"]="";
                                            $selected["en"]="";
                                            $selected[$usuario['idioma']]=" selected ='selected'";?>
                                            <option value="es" icon="img/icons/es.png"<?php echo $selected["es"]?>>Castellano</option>
                                            <option value="en" icon="img/icons/en.png"<?php echo $selected["en"]?>>English</option>
                                        </select>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="well well-sm">
                                <fieldset>
                                    <legend class="text-center"><?php echo __('lbl_PtosTotal', $lang) ?></legend>                                    
                                    <div class='input-group col-lg-4'>
                                        <label>Puntos por Ajuste</label>
                                        <input type='text' class="form-control" name="ajuste" value="<?php echo $usuario["ajuste"]?>"/>
                                    </div>
                                </fieldset>                                              
                            </div>                                                            
                        <div class="row">&nbsp;</div>
                        
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="submit" class="btn btn-primary" value="<?php echo __('title_UserAdd', $lang) ?>"></input>
                            </div> 
                            <div class="col-lg-6">
                                <a class="btn btn-danger" onclick="location.href = 'users.php'"><?php echo __('bt_Cancel', $lang) ?> 
                                </a>
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