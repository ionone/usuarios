<?php
    error_reporting(E_ALL);
    include ("incl/data.php");
    include ("incl/functions.php");
    include ("incl/session_gestion.php");
    initiate();
    $TitlePag = "title_AddTravel";
    $btnActive = 0;
    if(!isAuthenticated()){
        header("location:index.php");
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
                <div class="row header">
                    <div class="col-lg-12">
                        <h3><?php echo __('title_AddTravel', $lang)?></h3>
                    </div>
                </div>
                <?php // Buscar los datos del grupo al que pertenece el usuario 
                $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
                $consql = "SELECT g.id, g.name FROM groups g JOIN users u ON u.idgroup = g.id WHERE u.id=".$_SESSION["id"].";";
                $resultset = $db->send($consql);
                // Se prevé que un usuario sólo pertenezca a un grupo en caso contrario cambiar por un foreach
                if (!empty($resultset)) {
                    $groupId = $resultset[0]["id"];
                    $groupName = $resultset[0]["name"];
                }
                ?>
                <div class="header">
                    <div class="mark row">
                    <?php echo $groupName;?>
                    </div>
                    <div class="row">&nbsp;</div>
                    <form method="post" action="addtravel.php">
                        <input type="hidden" name="Enviado" value="1"/>                        
                        <div class="row">
                            <div class="col-lg-6">                                
                                <h4>PASAJEROS ACOMPAÑANTES</h4>
                                    <?php                                    
                                    $resultset = $db->select("users", "id <> '" . $_SESSION['id'] . "' AND enabled=1 AND idgroup = ".$groupId);
                                    if ($resultset) {
                                        echo "<table>";
                                        foreach ($resultset as $key => $value) {
                                            echo "<tr>\n";
                                            echo "<td>\n";
                                            echo "<input type=\"checkbox\" id=\"CheckPassenger\" name=\"CheckPassenger[]\" onchange=\"javascript:changeBack()\" value=\"" . $value['id'] . "\"/></td>\n";
                                            echo "<td>";
                                            if ($value['picture']!="") {
                                                echo "<img height=\"30\" src=\"images/" . $value['picture'] . "\" />";
                                            }
                                            echo "</td>\n";
                                            echo "<td>" . $value['name'] . "</td>\n";
                                            echo "</tr>\n";
                                        }
                                        echo "</table>";
                                    }
                                    ?>                                
                            </div>
                            <div class="col-lg-6 mark">
                                <h4>FECHA Y HORA DEL TRAYECTO</h4>
                                <table width="100%">
                                        <tr>
                                            <td colspan="4" id="cont" width="100%"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input style="text-align: center; margin-top: 10px" readonly="true" name="date" id="f_date" size="14" />
                                            </td>          
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="radio" name="hora" value="1" /> Mañana
                                                <input type="radio" name="hora" value="2" /> Tarde
                                                <input type="radio" name="hora" value="3" /> Noche
                                            </td>        
                                        </tr>
                                    </table>
                                    <script type="text/javascript">//<![CDATA[

                                        // this handler is designed to work both for onSelect and onTimeChange
                                        // events.  It updates the input fields according to what's selected in
                                        // the calendar.
                                        function updateFields(cal) {
                                            var date = cal.selection.get();
                                            if (date) {
                                                date = Calendar.intToDate(date);
                                                document.getElementById("f_date").value = Calendar.printDate(date, "%Y-%m-%d");
                                            }
                                            //document.getElementById("f_hour").value = cal.getHours();
                                            //document.getElementById("f_minute").value = cal.getMinutes();
                                        }
                                        ;

                                        var hoy = new Date();
                                        var dia = hoy.getDate();
                                        dia = dia.toString();
                                        if (dia.length == 1)
                                            dia = "0" + dia;
                                        var mes = hoy.getMonth() + 1;
                                        mes = mes.toString();
                                        if (mes.length == 1)
                                            mes = "0" + mes;
                                        var anno = hoy.getFullYear();
                                        var valor = anno.toString() + mes + dia;
                                        //document.write(valor);
                                        Calendar.setup({
                                            cont: "cont",
                                            showTime: 0,
                                            onSelect: updateFields,
                                            onTimeChange: updateFields
                                        });

                                        //]]></script>                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">                                
                                    <h4>CREAR TRAYECTO</h4>
                                    <input type="submit" class="btn btn-success" value="<?php echo __('bt_NewTravel',$lang)?>"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>          
            <?php include ("incl/footer.php")?>            
        </div> <!-- /container -->        
    </body>
</html>