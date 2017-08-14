<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = __('title_Restored', $lang);
    $btnActive = 0;
    $message = "";
    $verify = TRUE;
    //si el usuario ya está logueado, le mandamos a su sitio
    if (isAuthenticated()) 
        header("location:index.php");
    
    if (isset($_GET['token']) && $_GET['token']!=''){
        $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
        $resultset = $db->select("tokens", "token = '".$_GET["token"]."' AND enabled = 1");
        if(empty($resultset)) die("El enlace de recuperación no es válido");
        $origen = new Datetime ($resultset[0]["fecha"]);        
        $hoy1 = date("Y-m-d H:i:s");
        $hoy2 = new DateTime (date("Y-m-d H:i:s"));
        $dif = s_datediff("d", $origen, $hoy2) + 1;
        if($dif>2) die ("El token ha caducado");
        // grabamos fecha click y deshabilitamos el token
        $update["fechaClick"] = $hoy1;
        $update["enabled"] = 0;
        $resultset1 = $db->update("tokens", $update, "id = ".$resultset[0]["id"]);
        $message="password";
    }
    
    if(isset($_POST["Enviado"]) && $_POST["Enviado"]=='2'){
        // verificar contraseñas iguales
        if(isset($_POST['pass1']) && $_POST['pass1']!=""){
            if($_POST['pass2']!=$_POST['pass1']) {
                $verify = FALSE;
                $message = __('msb_ErrorPass', $lang);
                $_GET['token'] = $_POST['Token'];
            }
            if($message=="") {
                $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
                $resultset = $db->send("SELECT users.id FROM users INNER JOIN tokens ON users.id=tokens.idUser WHERE tokens.token='". $_POST['Token']."'");
                $usuario = $resultset[0]["id"];
                $pass = md5($_POST['pass1']);
                $anarray["password"] = $pass;
                $resultset = $db->update("users", $anarray, "id = ".$usuario);
                $db->close();
                header("location:index.php");
            }
        }
    }
    
    if(isset($_POST["Enviado"]) && $_POST["Enviado"]=='1'){
        if(!verificaremail($_POST["email"])) $message = "Email incorrecto";
        $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
        $resultset = $db->select("users", "email = '".$_POST["email"]."'");
        if(empty($resultset)) $message = "El email que ha introducido no se encuentra en la base de datos";
        if($message==""){
            // Crear token
            $Uid = array();
            $Uid["token"] = hash("md2",(string)microtime());
            $Uid["idUser"] = $resultset[0]["id"];
            $Uid["fecha"] =  date("Y-m-d H:i:s");
            $Uid["enabled"] =  1;            
            $resultset1 = $db->insert("tokens", $Uid);
//            $resultset = $db->update("tokens", $Uid, "email = '". $_POST['email']."'");
            // Enviar email
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            ////dirección del remitente
            $headers .= "From: " . st_mailsend . "\r\n";
            $headers .= "Bcc: elarahal.1972@gmail.com\r\n";
            //dirección de respuesta, si queremos que sea distinta que la del remitente
            $headers .= "Reply-To: " . st_mailsend . "\r\n";
            //ruta del mensaje desde origen a destino
            $headers .= "Return-path: " . st_mailsend . "\r\n";
            $texto = "Hola " . $resultset[0]["firstname"] . " " . $resultset[0]["lastname"] . "<br/>";
            $texto .= "<br/>";
            $texto .= "Has solicitado restablecer tu contraseña como usuario de <a href='compartecoche.tk'>compartecoche.tk</a>";
            $texto .= "<br/>";
            $texto .= "Utilice el siguiente enlace, válido para las 48 próximas horas, para restaurar su contraseña: <a href='http://compartecoche.tk/restored.php?token=".$Uid["token"]."'>http://compartecoche.tk/restored.php?token=".$Uid["token"]."</a>";
            $texto .= "<br/>";
            $texto .= "Atentamente<br/>";
            $texto .= "<br/>";
            $texto .= "El Administrador<br/>";
            $texto .= "<br/>";
            $texto .= "<br/>";
            $texto .= "<br/>";
            $texto .= "PD: Esto es un correo automatico. No debes responder al mismo ya que no sera atendido por nadie.<br/>";            
            $texto .= "<br/>";
            $sended = mail($_POST['email'], "Restablecer password", $texto, $headers);
            $message = "ok";
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
                <p class="lead">
                <?php if($message==""){ ?>
                    <form method="post" action="restored.php">
                        <input type="hidden" name="Enviado" value="1"/>
                        <label class="left"><?php echo __('lb_Email',$lang)?></label><br/>
                        <input class="right" type="email" id="email" name="email"/>
                        <br/>
                        <br/>
                        <input type="submit" class="btn btn-lg btn-success" value="<?php echo __('bt_Restored',$lang)?>"/>
                    </form>
                <?php } elseif ($message=="ok") { ?>
                    <div class="row">
                        Mensaje enviado. No olvide revisar su carpeta Spam, o de Correo No Deseado
                    </div>
                    <br/><br/>
                    <div class="row">
                        <div class="btn btn-lg btn-success" onclick="location.href = 'index.php'">Aceptar</div>
                    </div>
                <?php } elseif ($message=="password" || $verify==FALSE) {?>
                    <form method="post" action="restored.php">
                        <?php if(!$verify) echo $message ?>
                        <input type="hidden" name="Enviado" value="2"/>
                        <input type="hidden" name="Token" value="<?php echo $_GET['token']?>"/>
                        <label class="left"><?php echo __('lb_NewPass1',$lang)?></label><br/>
                        <input class="right" type="password" name="pass1"/>
                        <br/>
                        <label class="left"><?php echo __('lb_NewPass2',$lang)?></label><br/>
                        <input class="right" type="password" name="pass2"/>
                        <br/>
                        <br/>
                        <input type="submit" class="btn btn-lg btn-success" value="<?php echo __('bt_Ok',$lang)?>"/>
                    </form>
                <?php } else { ?>
                    <div class="row"><?php echo $message?></div>
                <?php }  ?>
                </p>
            </div>            
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>