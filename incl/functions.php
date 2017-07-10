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