<?php
$host = "localhost";
$db = "mural_db";
$user = "root";
$pass = "alunolab";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8",$user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["erro" => "Falha na conexão: ".$e->getMessage()]));
}