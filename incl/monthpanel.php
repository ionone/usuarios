<?php

// recuperamos variables de navegación o creamos los valores por defecto
if(!isset($_GET['m'])) {
    $month = date("n");
} else {
    $month = $_GET['m'];
    if (!is_numeric($month) OR $month<0 OR $month>12) $month = date('n');
}
if(!isset($_GET['y'])) {
    $year = date("Y");
} else {
    $year = $_GET['y'];
    if (!is_numeric($year) OR $year<2011 OR $year>2020) $year = date("Y");
}
$mes["1"]="enero";
$mes["2"]="febrero";
$mes["3"]="marzo";
$mes["4"]="abril";
$mes["5"]="mayo";
$mes["6"]="junio";
$mes["7"]="julio";
$mes["8"]="agosto";
$mes["9"]="septiembre";
$mes["10"]="octubre";
$mes["11"]="noviembre";
$mes["12"]="diciembre";
// navegación
?>
<div class="row mark">
    <?php 
    $anteriorYear = $year;
    $siguienteYear = $year;
    if($month==1) {
        $anterior = 12;
        $anteriorYear = $year - 1;
    }
    else $anterior = $month - 1;
    if($month==12) {
        $siguiente = 1;
        $siguienteYear = $year + 1;
    }
    else $siguiente = $month + 1;
    ?>
    <div class="calNameDayWeek">
        <a href="user.php?userid=<?php echo $usuario["id"]?>&m=<?php echo $anterior?>&y=<?php echo $anteriorYear?>"><</a>
    </div>
    <div class="calNameDayWeek col-lg-4">
        <?php echo $mes[$month]."' $year"?>
    </div>
    <div class="calNameDayWeek">
        <a href="user.php?userid=<?php echo $usuario["id"]?>&m=<?php echo $siguiente?>&y=<?php echo $siguienteYear?>">></a>    
    </div>
</div>
<?php // construimos mes 
    $numDayMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//    echo "<table cellpadding='2' width='100%' border='0' style='border-collapse: separate;'>";
//    echo "<tr style='background-color:blue'>";
//    for($i=1;$i<=$numDayMonth;$i++){
//        echo "<td style='padding:2px'>";
//        echo "<span style='font-size:0.8em, color: FFFFFF#'>".$i."</span>";
//        echo "</td>";
//    }
//    echo "</tr>";
//    echo "</table>";
//
    // Primer día de la semana
    $diaSemana = date("N", mktime(0, 0, 0, $month, 1, $year));    
?>
<div class="row mark">
    <div class="calNameDayWeek">
        L
    </div>
    <div class="calNameDayWeek">
        M
    </div>
    <div class="calNameDayWeek">
        M
    </div>
    <div class="calNameDayWeek">
        J
    </div>
    <div class="calNameDayWeek">
        V
    </div>
    <div class="calNameDayWeek">
        S
    </div>
    <div class="calNameDayWeek">
        D
    </div>
</div>
<?php     
    $cuadrante = $db->select("actions", "MONTH (dateAction) = ".$month. " AND YEAR (dateAction) = ". $year . " AND iduser = " .$usuario["id"]);
    $matrizmes = array();
    if(!empty($cuadrante)){
        foreach ($cuadrante as $key){
            $dateaction = explode("-", $key["dateAction"]);
            //$extraedia = settype($dateaction[2], "integer"); // captura el día de la fecha msqli
            $extraedia = $dateaction[2]; // captura el día de la fecha msqli
            $extraedia = intval($extraedia); // captura el día de la fecha msqli
            
            $matrizmes[$extraedia][0] = $key["horario"];
            $matrizmes[$extraedia][1] = $key["actionGrab"];        
        }
    }
    $valor = 1;
    $dia = 1;
    //print_r($matrizmes);
    $arrayGroup = groupsParameters($groupId);
    $ptsCON = $arrayGroup[0]["CON"];
    $ptsPAS = $arrayGroup[0]["PAS"];
    $ptsVAC = $arrayGroup[0]["VAC"];
    while($dia<=$numDayMonth){
        echo "<div class='row mark'>\n";        
        for($c=1;$c<=7;$c++) {            
            if($valor>=$diaSemana){
                if(!isset($matrizmes[$dia][0]))
                    echo "<div class='calNameDayWeek hd'>$dia</div>\n";
                else {
                    $text1 = $matrizmes[$dia][0];                    
                    if($matrizmes[$dia][1] == "CON")
                            $text2 = "<div class='calNameDayWeek sp' title='CONDUCTOR: + $ptsCON puntos'>$text1</div>\n";
                    elseif($matrizmes[$dia][1] == "PAS")
                            $text2 = "<div class='calNameDayWeek lp' title='PASAJERO: - $ptsPAS puntos'>$text1</div>\n";
                    elseif($matrizmes[$dia][1] == "NDP")
                            $text2 = "<div class='calNameDayWeek nd' title='NO DISPONIBLE: + $ptsVAC puntos'>$dia</div>\n";
                    echo $text2;                    
                }
                $dia++;
            } else 
                echo "<div class='calNameDayWeek hd'>&nbsp;</div>\n";
            $valor++;
            if($dia>$numDayMonth) break;
        }
        echo "</div>\n";
    }
?>