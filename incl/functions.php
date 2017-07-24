<?php


function __($str, $lang = null){
    if ( $lang != null ){
        if ( file_exists('translations/'.$lang.'.php') ){
            include('translations/'.$lang.'.php');
            if ( isset($texts[$str]) ){
                $str = $texts[$str];               
            }            
        }
    } else {
        // idioma por defecto
        include('translations/es.php');
        if ( isset($texts[$str]) ){
            $str = $texts[$str];               
        }
    }
return $str;
}
/*
  `iduser` int(11) NOT NULL,
  `dateregister` datetime NOT NULL,
  `datetime` datetime NOT NULL,
  `action`
        - CON   Conductor
        - PAS   Pasajero
        - LAB   
        - ALT   Día de Vacaciones
        
 */
function graphGroup($g){
    //     
    $arrayGroup = groupsParameters($g);
    $ptsCON = $arrayGroup[0]["CON"];
    $ptsPAS = $arrayGroup[0]["PAS"];
    $ptsVAC = $arrayGroup[0]["VAC"];
    
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    $usuarios = $db->select("users", "idgroup = ".$g. " AND enabled = TRUE");
    $tabla = array();
    $i = 0;
    foreach($usuarios as $key => $values){
        $tabla[$i]["id"] = $usuarios[$key]["id"];
        $tabla[$i]["name"] = $usuarios[$key]["name"];        
        $tabla[$i]["puntos"] = puntos($g, $tabla[$i]["id"]);
        $i++;
    }
    $db->close();
    arsort($tabla);
    return $tabla;
}

function groupsParameters ($g){
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    $grupo = $db->select("groups", "id =" .$g);
    $db->close();
    return $grupo;
}

function puntos($g, $u){
    $arrayGroup = groupsParameters($g);
    $ptsCON = $arrayGroup[0]["CON"];
    $ptsPAS = $arrayGroup[0]["PAS"];
    $ptsVAC = $arrayGroup[0]["VAC"];
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    $sql = "id =".$u;
    $puntos = $db->select("users",$sql);
    $calculo = $puntos[0]["ajuste"];
    $sql = "SELECT COUNT(actionGrab) as CONDUCTOR FROM actions WHERE actionGrab = 'CON' AND iduser =".$u;
    $puntos = $db->send($sql);
    $calculo = $calculo + ($puntos[0]["CONDUCTOR"]* $ptsCON);
    $sql = "SELECT COUNT(actionGrab) as PASAJERO FROM actions WHERE actionGrab = 'PAS' AND iduser =".$u;
    $puntos = $db->send($sql);
    $calculo = $calculo - ($puntos[0]["PASAJERO"] * $ptsPAS);
    $sql = "SELECT COUNT(actionGrab) as VACACIONES FROM actions WHERE actionGrab = 'NDP' AND iduser =".$u;
    $puntos = $db->send($sql);
    $calculo = $calculo + ($puntos[0]["VACACIONES"]* $ptsVAC);    
    $db->close();
    return $calculo;
}

function verificaremail($email){    
    $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
    if (!preg_match($pattern, $email)){ 
        return FALSE; 
    } else { 
        return TRUE; 
    } 
}

function temporal1(){
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    $resultset = $db->select("pasajeros", "pasajeros!=''");
    $p=0;
    foreach ($resultset as $key => $value){
        $p++;
        $pasajeros = $resultset[$key]["pasajeros"];
        $users = array();
        //echo "Previo: <b>".$pasajeros."</b><br/>";
        for ($i = 0; $i <= strlen($pasajeros); $i++) {
            $pos1 = strpos($pasajeros, "[");
            //echo "1.pos1 $pos1<br/>";
            //if (!is_integer($pos1))
              //  break;
            $pos2 = strpos($pasajeros, "]");
            //echo "2.pos2 $pos2<br/>";
            $res = substr($pasajeros, $pos1 + 1, $pos2 - 1);
            //echo "3. Usuario[$i] == $res<br/>";
            $users[$i] = $res;
            $pasajeros = substr($pasajeros, $pos2+1, strlen($pasajeros)- $pos2);            
            //echo "4: Resto: <b>". $pasajeros."</b> ";
            //echo "longitud: ". strlen($pasajeros)."<br/>";
        }
        //print_r($users);
        foreach ($users as $value) {
            if($value==12) $usuario = 6; // nuñez
            if($value==14) $usuario = 7; // monte
            if($value==17) $usuario = 9; // muñon
            if($value==19) $usuario = 5; // antonio
            if($value==20) $usuario = 10; // prado
            if($value==23) $usuario = 12; // cesar
            if($value==24) $usuario = 13; // gabriel
            if($value==25) $usuario = 14; // marcos
            if($value==26) $usuario = 11; // chacon
            $anarray = array();
            $anarray["iduser"] = $usuario;            
            $anarray["dateregister"] = $resultset[$key]["fecha"];
            $anarray["dateAction"] = $resultset[$key]["fecha"];
            $anarray["actionGrab"] = "PAS";
            $anarray["horario"] = $resultset[$key]["horario"];
            $update = $db->insert("actions", $anarray);
            echo " #   INSERT ". $anarray["iduser"] . " " . $anarray["dateregister"] . " " .$anarray["dateAction"] . " " . $anarray["actionGrab"] . " " .$anarray["horario"]. "<br/>\n";
        }
        echo "<br/>";
    }
}

function s_datediff( $str_interval, $dt_menor, $dt_maior, $relative=false){

       if( is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
       if( is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);

       $diff = date_diff( $dt_menor, $dt_maior, ! $relative);
      
       switch( $str_interval){
           case "y":
               $total = $diff->y + $diff->m / 12 + $diff->d / 365.25; break;
           case "m":
               $total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
               break;
           case "d":
               $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
               break;
           case "h":
               $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
               break;
           case "i":
               $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
               break;
           case "s":
               $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
               break;
          }
       if( $diff->invert)
               return -1 * $total;
       else    return $total;
   }