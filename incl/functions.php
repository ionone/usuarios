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
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    $usuarios = $db->select("users", "idgroup = ".$g);
    $tabla = array();
    $i = 0;
    foreach($usuarios as $key => $values){
        $tabla[$i]["id"] = $usuarios[$key]["id"];
        $tabla[$i]["name"] = $usuarios[$key]["name"];
        $sql = "SELECT COUNT(action) as CONDUCTOR FROM actions WHERE action = 'CON' AND iduser =".$tabla[$i]["id"];
        $puntos = $db->send($sql);
        $calculo = $puntos[0]["CONDUCTOR"]*2;
        $sql = "SELECT COUNT(action) as PASAJERO FROM actions WHERE action = 'PAS' AND iduser =".$tabla[$i]["id"];
        $puntos = $db->send($sql);
        $calculo = $calculo - $puntos[0]["PASAJERO"];
        $sql = "SELECT COUNT(action) as VACACIONES FROM actions WHERE action = 'VAC' AND iduser =".$tabla[$i]["id"];
        $puntos = $db->send($sql);
        $calculo = $calculo + $puntos[0]["VACACIONES"]*0.5;
        $tabla[$i]["puntos"] = $calculo;
        $i++;
    }
    $db->close();
    arsort($tabla);
    return $tabla;
}