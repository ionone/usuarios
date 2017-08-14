<?php
    include ("data.php");    
    // read input stream
    $data = file_get_contents("php://input");     
    // filtering and decoding code adapted from
        // http://stackoverflow.com/questions/11843115/uploading-canvas-context-as-image-using-ajax-and-php?lq=1
    // Filter out the headers (data:,) part.
    $filteredData=substr($data, strpos($data, ",")+1);
    // Need to decode before saving since the data we received is already base64 encoded
    $decodedData=base64_decode($filteredData);

    // store in server
    $fic_name = 'snapshot'.rand(1000,9999).'.png';
    $fp = fopen('../img/profiles/'.$fic_name, 'wb');
    $ok = fwrite($fp, $decodedData);
    fclose( $fp );
    if($ok) {        
        // actualizamos la base de datos
        $user = $_GET['user'];
        $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
        $anarray = array();
        $anarray["picture"] = $fic_name;
        $picture = $db->update("users", $anarray, "token = '" . $user . "'");        
        $db->close();        
        //echo $fic_name;
    }
    else
        echo "ALgoVaMal";
?>