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
            $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
            // Comprobamos datos recibidos
            $where = "id = ".$_SESSION["id"]. " AND password = '". md5($_POST["pass"])."'";
            $resultset = $db->select("users", $where);
            if (empty($resultset)) {
                $mesage = __('msb_BadPass', $lang);
            } else {
                if ($_POST['newpass1']!=$_POST['newpass2']){
                    $mesage = __('msb_DistintPass', $lang);
                } else {
                    // TODO comprobar requisitos
                    $anarray = array("password" => md5($_POST['newpass1']));
                    $send_sql = $db->update("users", $anarray, $where);
                    $mesage = __('msb_Update', $lang);
                }
            }
            $db->close();            
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
                    <h4 class="blockquote-reverse"><a href="language.php"><?php echo __('tx_Language',$lang)?></a></h4>
                    <h4 class="blockquote-reverse"><?php echo __('tx_Options',$lang)?></h4>
                </div>

                <div class="col-lg-8">
                    <p class="lead">
                    <form method="post" action="security.php">
                    <input type="hidden" id="Enviado" name="Enviado" value="1"></input>
                    <label class="left"><?php echo __('lb_Pass1',$lang)?></label><br/>
                    <input class="right" type="password" id="pass" name="pass" value=""></input>
                    <br/>
                    <label class="left"><?php echo __('lb_NewPass1',$lang)?></label><br/>
                    <input class="right" type="password" id="newpass1" name="newpass1" value=""></input>
                    <br/>
                    <label class="left"><?php echo __('lb_NewPass2',$lang)?></label><br/>
                    <input class="right" type="password" id="newpass2" name="newpass2" value=""></input>
                    <br/>                    
                    <input type="submit" class="btn btn-lg btn-success" style="margin-top: 15px" value="<?php echo __('bt_Update',$lang)?>"></input>
                    </form>
                    </p>
                </div>
            </div>
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>