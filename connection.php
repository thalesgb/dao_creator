<?php

class Connection {

    /**
     * @author Daniel Malaquias
     * @param Função de callback que de fato executará as ações no banco, recebendo a conexão
     * @return Resultado da função de callback recebida
     */
    public static function query($function) {
        $connectstr_dbhost = $_POST['host'];
        $connectstr_dbname = '';
        $connectstr_dbusername = $_POST['user'];
        $connectstr_dbpassword = $_POST['password'];

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, "MYSQLCONNSTR_localdb") !== 0) {
                continue;
            }

            $connectstr_dbhost = preg_replace("/^.*Data Source=(.+?);.*$/", "\\1", $value);
            $connectstr_dbname = preg_replace("/^.*Database=(.+?);.*$/", "\\1", $value);
            $connectstr_dbusername = preg_replace("/^.*User Id=(.+?);.*$/", "\\1", $value);
            $connectstr_dbpassword = preg_replace("/^.*Password=(.+?)$/", "\\1", $value);
            $connectstr_dbhost = preg_replace('/:(\d+)/', ';port=${1}', $connectstr_dbhost);
        }
        $result = null;
        $conn = null;
        try {
            $conn = new PDO("mysql:host=$connectstr_dbhost;dbname=$connectstr_dbname;", "$connectstr_dbusername", "$connectstr_dbpassword", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            // Executa/armazena resultado do callback passando a conexão
            $result = $function($conn);
        } catch (PDOException $e) {
            throw $e;
        } finally {
            $conn = null;
        }
        return $result;
    }

}
