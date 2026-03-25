<?php
require "conexao.php";

// Busca as últimas 15 linhas em ordem decrescente
$stmt = $pdo->query("
    SELECT texto,autor, DATE_FORMAT(data_envio, '%H:%i:%s') as hora FROM mensagens ORDER BY id DESC LIMIT 15
");
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Transformar o Array do PHP em um formato universal JSON
header('Content-Type: application/json');
echo json_encode($mensagens);
