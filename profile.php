<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = "title_Profile";
    $btnActive = 4;
    $mesage = "";
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    if (!isAuthenticated()) {
        // No debe estar aquí
        header("location:index.php");
    } else { //usuario autenticado
        if (isset($_POST['Enviado'])){
            $anarray = array();            
            foreach ($_POST as $var=>$value){
                if ($var=="Enviado")
                    continue;
                if($_POST[$var]!=$_SESSION[$var]){                    
                    $anarray[$var] = $value;
                }                
            }                      
            if (!empty($anarray)) {
                $auth_query = $db->update("users", $anarray, "id = '" . $_SESSION['id'] . "'");
                $mesage = __('msb_Update', $lang);
            }            
            $name=$_POST['name'];
            $firstname=$_POST['firstname'];
            $lastname=$_POST['lastname'];
            $email=$_POST['email'];
            $_SESSION['name'] = $name;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['lastname'] = $lastname;                        
            $_SESSION['email'] = $email;
            $resultset = $db->select("users","id = ". $_SESSION['id']);
            $picture=$resultset[0]['picture'];
        } else {
            $resultset = $db->select("users","id = ". $_SESSION['id']);
            $name=$resultset[0]['name'];
            $firstname=$resultset[0]['firstname'];
            $lastname=$resultset[0]['lastname'];
            $email=$resultset[0]['email'];
            $picture=$resultset[0]['picture'];
        }
        $db->close();
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
                    <h4 class="blockquote-reverse"><?php echo __('tx_Personal',$lang)?></h4>
                    <h4 class="blockquote-reverse"><a href="language.php"><?php echo __('tx_Language',$lang)?></a></h4>
                    <h4 class="blockquote-reverse"><a href="security.php"><?php echo __('tx_Options',$lang)?></a></h4>
                </div>

                <div class="col-lg-8">
                    <p class="lead">
                    <form method="post" action="profile.php">
                    <input type="hidden" id="Enviado" name="Enviado" value="1"></input>
                    <label class="left"><?php echo __('lb_Nick',$lang)?></label><br/>
                    <input class="right" type="text" id="name" name="name" value="<?php echo $name?>"></input>                    
                    <br/>
                    <label class="left"><?php echo __('lb_FirstName',$lang)?></label><br/>
                    <input class="right" type="text" id="firstname" name="firstname" value="<?php echo $firstname?>"></input>
                    <br/>
                    <label class="left"><?php echo __('lb_LastName',$lang)?></label><br/>
                    <input class="right" type="text" id="lastname" name="lastname" value="<?php echo $lastname?>"></input>
                    <br/>
                    <label class="left"><?php echo __('lb_Email',$lang)?></label><br/>
                    <input class="right" type="text" id="email" name="email" value="<?php echo $email?>"></input>
                    <br/>
                    <?php 
                    if($picture=="") $picture = "img/null.jpg";
                    else $picture = "img/profiles/" .$picture;
                    ?>
                    <label class="left"><?php echo __('lb_Picture',$lang)?></label><br/>
                    <img src="<?php echo $picture?>" height="100" width="100" onclick="location:temp.php"/>                   
                    <a class="btn btn-default" href="temp.php"><?php echo __('bt_Picture', $lang)?></a>
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