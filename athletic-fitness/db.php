

<?php

$host = "10.89.22.186";
$port = "3306";
$dbname = "athletic_fitness";
$username = "athletic_user";
$password = "123456";
try {

    $conexion = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexion->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {

    error_log($e->getMessage());
    exit("Error al conectar con la base de datos.");

}
