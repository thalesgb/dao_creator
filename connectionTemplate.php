<?php
$templateConnection =  "<?php
namespace Dao;

use PDO;

class Connection {

    /**
     * @author Daniel Malaquias
     * @param Função de callback que de fato executará as ações no banco, recebendo a conexão
     * @return Resultado da função de callback recebida
     */
    public static function query(\$function) {
        \$connectstr_dbhost = '{$_POST['host']}';
        \$connectstr_dbname = '{$_POST['banco']}';
        \$connectstr_dbusername = '{$_POST['user']}';
        \$connectstr_dbpassword = '{$_POST['password']}';
       
        \$result = null;
        \$conn = null;
        try {
            \$conn = new PDO(\"mysql:host=\$connectstr_dbhost;dbname=\$connectstr_dbname;\", \"\$connectstr_dbusername\", \"\$connectstr_dbpassword\", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false, PDO::MYSQL_ATTR_INIT_COMMAND => \"SET NAMES utf8\"]);
            // Executa/armazena resultado do callback passando a conexão
            \$result = \$function(\$conn);
        } catch (PDOException \$e) {
            throw \$e;
        } finally {
            \$conn = null;
        }
        return \$result;
    }

}
";