<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = "title_Profile";
    $btnActive = 4;
    $mesage = "";
    if (!isAuthenticated()) {
        // No debe estar aquí
        header("location:index.php");
    } else { //usuario autenticado
        if (isset($_POST['Enviado'])){
            $anarray = array();
            $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
            if(isset($_POST["lang"])) 
                $anarray["lang"]=$_POST["lang"];
            if (array_count_values($anarray)>0) {
                $auth_query = $db->update("users", $anarray, "id = '" . $_SESSION['id'] . "'");                
                $mesage = __('msb_Update', $lang);                        
            }
            $db->close();
            $lang = $_POST['lang'];
            $_SESSION["lang"] = $lang;
        } else {
            $lang = $_SESSION['lang'];            
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
                <h1><?php echo __($TitlePag, $lang)?></h1>
                <p><?php if ($mesage!='') echo $mesage?></p>
            </div>            
            <div class="row marketing">
                <div class="col-lg-4">
                    <h4 class="blockquote-reverse"><a href="profile.php"><?php echo __('tx_Personal',$lang)?></a></h4>
                    <h4 class="blockquote-reverse"><?php echo __('tx_Language',$lang)?></h4>
                    <h4 class="blockquote-reverse"><a href="#"><?php echo __('tx_Options',$lang)?></a></h4>
                </div>

                <div class="col-lg-8">
                    <p class="lead">                    
                    <form method="post" action="language.php">
                    <input type="hidden" id="Enviado" name="Enviado" value="1"></input>                    
                    <select name="lang" id="lang" >
                        <option value="es" icon="img/icons/es.png">Castellano</option>
                        <option value="en" icon="img/icons/en.png">English</option>
                    </select>
                    <br/>
                    <input type="submit" class="btn btn-lg btn-success" style="margin-top: 15px" value="<?php echo __('lb_Button',$lang)?>"></input>
                    </form>
                    </p>
                </div>
            </div>
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->
    </body>
</html>