<?php

include("database.php");
include("conn.php");

//devuelve una conexión a la base de datos
foreach ($_GET as $variable => $valor) {
    $_GET [$variable] = str_replace("'", "", $_GET [$variable]);
    $_GET [$variable] = str_replace("\"", "", $_GET [$variable]);
    $_GET [$variable] = str_replace("=", "", $_GET [$variable]);
}

foreach ($_POST as $variable => $valor) {
    $_POST [$variable] = str_replace("'", "", $_POST [$variable]);
    $_POST [$variable] = str_replace("\"", "", $_POST [$variable]);
    $_POST [$variable] = str_replace("=", "", $_POST [$variable]);
}