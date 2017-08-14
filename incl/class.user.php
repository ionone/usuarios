<?php

class classUsers
{

    var $connection;

    public function __construct()
    {
        if (DB_PASS == NULL)
            $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_NAME);
        else
            $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME); 
        if (!$this->connection)
            throw new Exception("0000:No ha sido posible conectar con la Base de Datos [on class DataBase->construct database.php from PHPcore]:");
        $acentos = mysqli_query($this->connection, "SET NAMES 'utf8'");
        return true;
    }
}