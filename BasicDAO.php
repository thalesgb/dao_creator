<?php
$template = "<?php

class $nome_dao extends Connection {

    public static function inserir($assinatura_insert) {
         
        $p_insert

        \$query = 'INSERT INTO $nome_tabela ($campos_insert)  VALUES(:$campos_insert_parametros);';
        return self::query(function(PDO \$conn) use (\$query, \$parametros) {
                    \$stmt = \$conn->prepare(\$query);
                    try {
                        \$conn->beginTransaction();
                        \$stmt->execute(\$parametros);
                        \$id = \$conn->lastInsertId();
                        \$conn->commit();
                    } catch (PDOExecption \$e) {
                        \$conn->rollback();
                        \$id = 0;
                        throw \$e;
                    }
                    return \$id;
                });
    }

    public static function atualizar($assinatura_update $assinatura_insert) {
        $p_update

        \$query = 'UPDATE $nome_tabela  SET $campos_update WHERE id = :id;';
        return self::query(function(PDO \$conn) use (\$query, \$parametros) {
                    \$stmt = \$conn->prepare(\$query);
                    try {
                        \$conn->beginTransaction();
                        \$stmt->execute(\$parametros);
                        \$count = \$stmt->rowCount();
                        \$conn->commit();
                    } catch (PDOExecption \$e) {
                        \$conn->rollback();
                        \$count = 0;
                        throw \$e;
                    }
                    return \$count;
                });
    }

  public static function count(\$params = []) {
        \$parametros = array();
        \$string_query = array();
         if(!empty(\$params)){
        \$keys = array_keys($params);
	        foreach(\$keys as \$n=>\$i){
	                \$parametros[':'.\$n] = \$params[\$i];
	                 array_push(\$string_query, \$i.' = :'.\$n);
	        }
    	}
        \$where = (!empty(\$string_query))? ' WHERE '.implode(' AND ',\$string_query):'';
        \$query = 'SELECT COUNT(DISTINCT id) as count FROM $nome_tabela '.\$where;
         return self::query(function(PDO \$conn) use (\$query, \$parametros) {
                    \$stmt = \$conn->prepare(\$query);
                    \$stmt->execute(\$parametros);
                    \$count = \$stmt->fetch(PDO::FETCH_OBJ);
                    return \$count->count;
                });
    }

    public static function atualizarParcial($assinatura_update \$campos) {
        \$parametros = array();
        \$parametros[':id'] = \$id;
        \$string_update = array();
        \$keys = array_keys(\$campos);
        foreach(\$keys as \$n=>\$i){
                \$parametros[':'.\$n] = \$campos[\$i];
                 array_push(\$string_update, \$i.' = :'.\$n);
        }
        
        \$query = 'UPDATE $nome_tabela  SET '.implode(\",\",\$string_update).' WHERE id = :id;';
        return self::query(function(PDO \$conn) use (\$query, \$parametros) {
                    \$stmt = \$conn->prepare(\$query);
                    try {
                        \$conn->beginTransaction();
                        \$stmt->execute(\$parametros);
                        \$count = \$stmt->rowCount();
                        \$conn->commit();
                    } catch (PDOExecption \$e) {
                        \$conn->rollback();
                        \$count = 0;
                        throw \$e;
                    }
                    return \$count;
                });
    }
    public static function deletar(\$id) {
        \$parametros = array(
            ':id' => \$id
        );

        \$query = 'DELETE FROM $nome_tabela WHERE id = :id;';
        return self::query(function(\$conn) use (\$query, \$parametros) {
                    \$stmt = \$conn->prepare(\$query);
                    \$stmt->execute(\$parametros);
                    \$count = \$stmt->rowCount();
                    return \$count;
                });
    }

    public static function listar( \$offset = 0, \$limit = 50) {
          \$parametros = array(
            ':limit' => \$limit,
            ':offset' => \$offset,
        );
        \$query = 'SELECT $nome_colunas FROM $nome_tabela LIMIT  :limit OFFSET :offset;';
        return self::query(function(PDO \$conn) use (\$query, \$parametros) {
                    \$stmt = \$conn->prepare(\$query);
                    \$stmt->execute(\$parametros);
                    return \$stmt->fetchAll(PDO::FETCH_OBJ);
                });
    }

  public static function listarTodos() {
          \$parametros = array();
        \$query = 'SELECT $nome_colunas FROM $nome_tabela;';
        return self::query(function(PDO \$conn) use (\$query, \$parametros) {
                    \$stmt = \$conn->prepare(\$query);
                    \$stmt->execute(\$parametros);
                    return \$stmt->fetchAll(PDO::FETCH_OBJ);
                });
    }
    
    public static function listarPorId(\$id) {
        \$parametros = array(
            ':id' => \$id
        );
        \$query = 'SELECT $nome_colunas FROM $nome_tabela WHERE id = :id LIMIT 1';
        return self::query(function(PDO \$conn) use (\$query, \$parametros) {
                    \$stmt = \$conn->prepare(\$query);
                    \$stmt->execute(\$parametros);
                    return \$stmt->fetch(PDO::FETCH_OBJ);
                });
    }

}

";