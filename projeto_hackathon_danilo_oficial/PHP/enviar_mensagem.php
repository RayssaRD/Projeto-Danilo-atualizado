<?php
// 1. Inicia a sessão para o PHP saber QUEM é você (o remetente)
session_start();

// 2. Conecta com o seu banco de dados
require "conexao.php";

// 3. Segurança: Se o usuário não estiver logado, ele não pode enviar mensagem
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Você precisa estar logado!']);
    exit;
}

// 4. Pega os dados que virão do formulário (do Front-end)
// Usamos o método POST por segurança
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $remetente_id   = $_SESSION['id_usuario']; // Pega o ID de quem está logado
    $destinatario_id = $_POST['destinatario_id']; // ID de quem vai receber
    $mensagem       = trim($_POST['mensagem']);  // O texto da mensagem

    // 5. Verifica se a mensagem não está vazia
    if (empty($mensagem)) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Digite uma mensagem!']);
        exit;
    }

    try {
        // 6. Prepara o comando SQL (A lógica que você validou!)
        $sql = "INSERT INTO chat_privado (remetente_id, destinatario_id, mensagem) 
                VALUES (:de, :para, :msg)";
        
        $stmt = $pdo->prepare($sql);

        // 7. Faz o "Bind" (conecta as variáveis aos buraquinhos :de, :para, :msg)
        $stmt->bindParam(':de', $remetente_id);
        $stmt->bindParam(':para', $destinatario_id);
        $stmt->bindParam(':msg', $mensagem);

        // 8. Executa!
        if ($stmt->execute()) {
            echo json_encode(['status' => 'sucesso', 'mensagem' => 'Mensagem enviada!']);
        }

    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no banco: ' . $e->getMessage()]);
    }
}