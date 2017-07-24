<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = "Login";
    $btnActive = 4;
    //si el usuario ya está logueado, le mandamos a su sitio
    if (isAuthenticated()) {
        header("location:index.php");
    } else { //usuario no autenticado
        $loginFailedMessage = '<h2 style="color:red">Credenciales incorrectas</h2>';
        $authenticateYourSelf = '<h2>Introduce tus credenciales</h2>';
        $mesage = "";
        if (!$credentialsProvided = (isset($_POST['email']) && isset($_POST['password']))) {
            $mesage = $authenticateYourSelf;
        } else if ($credentialsProvided) {
            $rem = 0;
            if(isset($_POST['remember-me']) && $_POST['remember-me']==1) $rem = 1;
            // Hacemos login
            if (login($_POST['email'], $_POST['password'], $rem, TRUE)) {
                // Login Ok, grabamos actividad en la base
                $db = new DataBase();
                //$resultset = $db->send("UPDATE users SET last_login = '" . date("Y-m-d H:i:s") . "' WHERE name = '" . $_SESSION['name'] . "'");                
                $anarray= array();
                $anarray["last_login"] = date("Y-m-d H:i:s");
                $resultset = $db->update("users", $anarray, "name = '" . $_SESSION['name'] . "'");
                $db->close();
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
                <?php echo $mesage?>
                <p class="lead">                    
                    <form method="post" action="login.php">
                    <label class="left"><?php echo __('lb_Email',$lang)?></label><br/>
                    <input class="right" type="text" id="email" name="email"></input>
                    <br/>
                    <label class=""><?php echo __('lb_Pass',$lang)?></label><br/>
                    <input class="right" type="password" id="password" name="password"></input>
                    <br/>
                    <label for="remember-me"><?php echo __('lb_Remember',$lang)?></label>
                    <input type="checkbox" value="1" id="remember-me" name="remember-me" />
                    <br/>
                    <input type="submit" class="marketing btn btn-lg btn-success panel-group" value="<?php echo __('bt_Login',$lang)?>"></input>
                </form>
                </p>
            </div>
            <div class="jumbotron">             
                <p class="alert-link">
                    <ul>
                        <li style="text-align: left">
                            <a href="restored.php"><?php echo __('tx_remember', $lang)?></a>
                        </li>
                        <li style="text-align: left">
                            <a href="register.php"><?php echo __('tx_register', $lang)?></a>
                        </li>
                    </ul>
                </p>                
            </div>
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>