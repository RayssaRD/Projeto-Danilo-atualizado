<?php
session_start();
require "conexao.php";

// 1. Segurança: Só usuários logados podem ver a lista de contatos
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([]);
    exit;
}

$meu_id = $_SESSION['id_usuario'];

try {
    // 2. Busca todos os usuários, MENOS o que está logado agora (você)
    // Selecionamos o ID e o NOME (autor) para montar a lista no Front-end
    $stmt = $pdo->prepare("SELECT id, autor FROM usuarios WHERE id != :meu_id ORDER BY autor ASC");
    $stmt->bindParam(':meu_id', $meu_id);
    $stmt->execute();
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Retorna a lista em formato JSON para o JavaScript ler
    header('Content-Type: application/json');
    echo json_encode($usuarios);

} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}