<?php
// PASSO 1: A Regra da Sessão (Obrigatório ser a primeira linha!)
session_start();

// Importa a conexão com o banco
require "conexao.php";

// Verifica se os dados vieram via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Receber os dados do formulário
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Validação básica de campos vazios
    if (empty($email) || empty($senha)) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Preencha todos os campos!']);
        exit;
    }

    try {
        // 2. Buscar o usuário apenas pelo E-mail
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Pegamos o resultado (fetch)
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
    
            // 4. EMISSÃO DO CRACHÁ: Agora usando 'autor' como você definiu
           // Dentro do login.php, onde o login dá certo:
            $_SESSION['id_usuario']   = $usuario['id'];
            $_SESSION['nome_usuario'] = $usuario['autor']; // O nome que veio da coluna 'autor' // Pega da coluna 'autor' do banco
            $_SESSION['logado']       = true;
        
            echo json_encode([
                'status' => 'sucesso', 
                'mensagem' => 'Login realizado! Bem-vindo, ' . $usuario['autor']
            ]);
        
        } else {
            echo json_encode(['status' => 'erro', 'mensagem' => 'E-mail ou senha incorretos.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no servidor.']);
    }

} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
}