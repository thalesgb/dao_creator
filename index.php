<?php
echo "<pre>";


require_once("connection.php");



if (isset($_POST['btnVAI'])) {
    
    $parametros = array();
    $query_databases = "show databases";
    $bancos = Connection::query(function(PDO $conn) use ($query_databases, $parametros) {
                $stmt = $conn->prepare($query_databases);
                $stmt->execute($parametros);
                return $stmt->fetchAll(PDO::FETCH_OBJ);
            });

    echo "<form action='' method='POST'>";
    echo "HOST <input type='text' value='{$_POST['host']}' name='host' readonly required/> ";
    echo "USUARIO <input type='text' value='{$_POST['user']}'  name='user' readonly required/>";
    echo "SENHA <input type='password' value='{$_POST['password']}'  name='password' readonly/> <br/>";
    foreach ($bancos as $item) {
        echo "<input type='radio' name='banco'  value='{$item->Database}' />{$item->Database}<br/>";
    }
    echo "<input type='submit' name='btnGO' value='GO'/>";
    echo "</form>";
}else{
    echo "<form action='' method='POST'>";
echo "HOST <input type='text' name='host' required/> ";
echo "USUARIO <input type='text' name='user' required/>";
echo "SENHA <input type='text' name='password' /> <br/>";
echo "<input type='submit' name='btnVAI' value='VAI'/>";
echo "</form>";
}
if (isset($_POST['btnGO'])) {
    $parametros = array(':banco' => $_POST['banco']);
    $query_banco = "SELECT table_name FROM information_schema.tables where table_schema = :banco;";
    $tabelas = Connection::query(function(PDO $conn) use ($query_banco, $parametros) {
                $stmt = $conn->prepare($query_banco);
                $stmt->execute($parametros);
                return $stmt->fetchAll(PDO::FETCH_OBJ);
            });


    //print_r($tabelas);
$count = 0;
    foreach ($tabelas as $item) {
        $count++;
        $nome_tabela = $item->table_name;
        $nome_dao = str_replace(' ', '', ucwords(str_replace('_', ' ', $nome_tabela))) . 'DAO';


        $parametros = array(':banco' => $_POST['banco'], ':tabela' => $nome_tabela);
        $query_colunas = "SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = :tabela AND table_schema = :banco";
        $colunas = Connection::query(function(PDO $conn) use ($query_colunas, $parametros) {
                    $stmt = $conn->prepare($query_colunas);
                    $stmt->execute($parametros);
                    return $stmt->fetchAll(PDO::FETCH_OBJ);
                });

        $array_colunas = array();

        
        /*
         *  GERA ASSINATURAS
         */
        $assinatura_update = '';
        $assinatura_insert = '';
        foreach ($colunas as $coluna) {
            $array_colunas[$coluna->COLUMN_NAME] = $coluna->COLUMN_NAME;
            if ($coluna->COLUMN_NAME == 'id') {
                $assinatura_update .= '$' . $coluna->COLUMN_NAME . ',';
            } else {
                $assinatura_insert .= '$' . $coluna->COLUMN_NAME;
                if ($coluna->IS_NULLABLE == 'YES') {
                    $assinatura_insert .= ' = null,';
                } else {
                    $assinatura_insert .= ',';
                }
            }
        }
        $assinatura_insert = mb_substr($assinatura_insert, 0, strlen($assinatura_insert) - 1);

         /*
         *  GERA PARAMETROS DOS METODOS E CAMPOS
         */
        
        $nome_colunas = implode(',', $array_colunas);

        $p_update = '$parametros = array(';
        foreach ($array_colunas as $pa) {
            $p_update .= '\':' . $pa . '\' => $' . $pa . ',';
        }
        $p_update = mb_substr($p_update, 0, strlen($p_update) - 1);
        $p_update .= ');';

        unset($array_colunas['id']);
        $campos_insert = implode(',', $array_colunas);
        $campos_insert_parametros = implode(',:', $array_colunas);

        $campos_update = '';
        $p_insert = '$parametros = array(';
        foreach ($array_colunas as $pa) {
            $p_insert .= '\':' . $pa . '\' => $' . $pa . ',';
            $campos_update .= $pa . ' = :' . $pa . ',';
        }
        $p_insert = mb_substr($p_insert, 0, strlen($p_insert) - 1);
        $p_insert .= ');';
        $campos_update = mb_substr($campos_update, 0, strlen($campos_update) - 1);

         /*
         *  GERA O ARQUIVO DAO
         */

        @include("BasicDAO.php");
        $myFile = "generated/$nome_dao.php";
        
        
        $fh = fopen($myFile, 'w') or die("can't open file");

        fwrite($fh, $template);

        fclose($fh);


        echo "<p style='color:green;'>$nome_tabela OK</p>";
    }
echo "<p style='color:blue;'>$count TABELAS GERADAS</p>";
    $myFile = "generated/connection.php";
    $fh = fopen($myFile, 'w') or die("can't open file");
    include_once('connectionTemplate.php');
    fwrite($fh, $templateConnection);

    fclose($fh);
echo "<p style='color:blue;'>ARQUIVO DE CONEXÃO GERADO</p>";
    exit();
}


