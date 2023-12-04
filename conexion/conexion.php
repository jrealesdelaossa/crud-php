<?php
$servidor="mysql:dbname=competencia_php;host=127.0.0.1";
$usuario="root";
$clave="Jandp012999@#";

try {
    $pdo=new PDO($servidor,$usuario,$clave,array(PDO::MYSQL_ATTR_INIT_COMMAND=>"set names utf8"));
    echo "Conexion exitosa a la bd";
} catch (PDOException $e) {
    echo "Conexion fallida".$e->getMessage(); 
}


?>