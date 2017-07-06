<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/session_gestion.php");   
    include ("incl/functions.php");
    initiate();
    $TitlePag = "Login";
    $btnActive = 4;
    //si el usuario ya está logado, le mandamos a su sitio
    if (isAuthenticated()) {
        header("location:index.php");
    } else { //usuario no autenticado
        $loginFailedMessage = 'Credenciales incorrectas';
        $authenticateYourSelf = 'Introduce tus credenciales';
        $mesage = "";
        if (!$credentialsProvided = (isset($_POST['email']) && isset($_POST['password']))) {
            $mesage = $authenticateYourSelf;
        } else if ($credentialsProvided) {
            $rem = 0;
            if(isset($_POST['remember-me']) && $_POST['remember-me']==1) $rem = 1;
            if (ranklogin($_POST['email'], $_POST['password'], $rem, TRUE)) {
                // Grabar actividad en la base
                $db = new DataBase();
                $resultset = $db->send("UPDATE users SET last_login = '" . date("Y-m-d H:i:s") . "' WHERE name = '" . $_SESSION['name'] . "'");
                header("location:index.php");
            } else
                $mesage = $loginFailedMessage;
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
                <h2>Sing In</h2>
                <p class="lead">                    
                    <form method="post" action="login.php">
                    <label class="left">Correo Electrónico</label><br/>
                    <input class="right" type="text" id="email" name="email"></input>
                    <br/>
                    <label class="">Contraseña</label><br/>
                    <input class="right" type="password" id="password" name="password"></input>
                    <br/>
                    <label for="remember-me">Recordar mis datos</label>
                    <input type="checkbox" value="1" id="remember-me" name="remember-me" />
                    <br/>
                    <input type="submit" class="marketing btn btn-lg btn-success panel-group" value="Iniciar Sesión"></input>
                </form>
                </p>
            </div>
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>