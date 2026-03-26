<?php
session_start();
require "conexao.php";

// 1. Segurança: Verifica se está logado e se o ID do destinatário foi enviado
if (!isset($_SESSION['id_usuario']) || !isset($_GET['contato_id'])) {
    echo json_encode([]);
    exit;
}

$meu_id = $_SESSION['id_usuario'];
$contato_id = $_GET['contato_id']; // O ID do amigo que clicamos no Front-end

try {
    // 2. A LÓGICA DE OURO: 
    // "Traga mensagens onde (Eu mandei pra Ele) OU (Ele mandou pra Mim)"
    $sql = "SELECT remetente_id, mensagem, DATE_FORMAT(data_envio, '%H:%i') as hora 
            FROM chat_privado 
            WHERE (remetente_id = :meu_id AND destinatario_id = :contato_id)
               OR (remetente_id = :contato_id AND destinatario_id = :meu_id)
            ORDER BY data_envio ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':meu_id', $meu_id);
    $stmt->bindParam(':contato_id', $contato_id);
    $stmt->execute();

    $conversa = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($conversa);

} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}