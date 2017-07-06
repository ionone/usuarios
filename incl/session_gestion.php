<?php
session_start();
print_r($_SESSION);
function isAuthenticated() {
    if (isset($_SESSION['autentificado'])){
        return $_SESSION['autentificado'];
    } else return FALSE;    
}

function ranklogin($username, $password, $remember = FALSE, $password_hashed = TRUE) {
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    $passwd = $password_hashed ? md5($password) : $password;    
    $resultset = $db->select("users", "(name = '" . $username . "' OR email = '".$username."') AND password ='" . $passwd . "' AND enabled = 1");
    if (empty($resultset)) {
        return FALSE;
    } else {
        if ($remember) {
            //genera una nueva auth key en cada log in para que las viejas claves no pueden utilizarse varias veces
            //en caso de que "secuestren" la cookie
            $cookie_auth = rand_string() . $resultset[0]["name"];
            $auth_key = session_encrypt($cookie_auth);
            $anarray = array("auth_key" => $auth_key);
            $auth_query = $db->update("users", $anarray, "name = '" . $resultset[0]["name"] . "'");
            echo "Grabada auth key en base de datos: ".$auth_key."\n";            
            setcookie("auth_key", $auth_key, time() + 60 * 60 * 24 * 7, "/", null, FALSE, TRUE);
            echo "Grabada auth key como Cookie: ".$auth_key."\n";
        }
        session_regenerate_id(TRUE);        
        $_SESSION['name'] = $resultset[0]["name"];
        //$_SESSION['nombre'] = $resultset[0]["nombre"];
        //$_SESSION['apellidos'] = $resultset[0]["apellidos"];
        //$_SESSION['picture'] = $resultset[0]["picture"];
        $_SESSION['idusuario'] = $resultset[0]["idusuario"];
        $_SESSION['email'] = $resultset[0]["email"];
        $_SESSION['auth_key'] = $resultset[0]["auth_key"];
        //$_SESSION['roles'] = $resultset[0]["roles"];
        $_SESSION['autentificado'] = TRUE;
        $_SESSION['ultima_actividad_usuario'] = time();
        //$_SESSION['recibe_email'] = $resultset[0]["recibe_email"];
        return TRUE;
    }
}

function initiate() {
    $logged_in = FALSE;
    if (isAuthenticated()) {
        $logged_in = TRUE;
    }
    // Hay cookie definida?
    $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
    if (isset($_COOKIE['auth_key'])) {
        $auth_key = $_COOKIE['auth_key'];
    } else $auth_key = "";
    echo "Cookie: ".$auth_key."\n";
    $where = "auth_key = '".$auth_key."'";
    $auth_key_query = $db->select("users", $where); // or die("Error en: $auth_key " . mysql_error());
//    print_r($auth_key_query);
    if (isset($_COOKIE['auth_key'])) {
        $auth_key = $_COOKIE['auth_key'];
        $db = new DataBase(DB_SERVER, DB_USER, DB_PASS, DB_NAME, 1);
        if ($logged_in === FALSE) {
            // selecciona usuario de la base de datos cuya auth key coincida (las auth keys son únicas)
            $where = "auth_key = '".$auth_key."'";
            $auth_key_query = $db->select("users", $where); // or die("Error en: $auth_key " . mysql_error());
            print_r($auth_key_query);
            if (!mysql_num_rows($auth_key_query) > 0) {
                // si la clave no pertenece a ningún usuario borra cookie
                setcookie("auth_key", "", time() - 3600);
            } else {
                while ($u = mysql_fetch_array($auth_key_query)) {
                    // adelante con el login
                    ranklogin($u['name'], $u['pass'], TRUE, FALSE);                    
                }
            }
        } else {
            setcookie("auth_key", "", time() - 3600);
        }
    }
}

function logout() {
    // Es necesario borrar la auth key de la base de datos de modo que la cookie deje ser válida
    $name = $_SESSION['name'];
    setcookie("auth_key", "", time() - 3600);
    $auth_query = mysql_query("UPDATE users SET auth_key = '' WHERE name = '" . $username . "'");
    // If auth key is deleted from database proceed to unset all session variables
    session_unset();
    session_destroy();
    if ($auth_query) {           
        return TRUE;
    } else {
        return FALSE;
    }
}

// en caso de que queramos controlar que la sesión está activa
// y limitar su duración a 30 minutos
function keepalive() {

    if (!isset($_COOKIE['auth_key'])) {
        $oldtime = $_SESSION['ultima_actividad_usuario'];
        if (!empty($oldtime)) {
            $currenttime = time();
            // 30 minutos
            $timeoutlength = 30 * 600;
            if ($oldtime + $timeoutlength >= $currenttime) {
                // resetea el tiempo en que estuvo activo el usuario
                $_SESSION['ultima_actividad_usuario'] = $currenttime;
            } else {
                // Si la sessión ha estado inactiva demasiado teimpo, logout
                logout();
            }
        }
    }
}

function session_encrypt($string) {
    $salt = "encienañostodoscalvo";
    return md5($salt . $string);
}

function rand_string() {
    $length = 26;
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

?>