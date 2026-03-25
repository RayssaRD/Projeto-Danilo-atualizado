<?php
// PASSO 1: A Regra da Sessão (Obrigatório ser a primeira linha!)
session_start();

// Importa a conexão com o banco
require 'conexao.php';

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

        // 3. Verificar se o usuário existe E se a senha bate
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            
            // 4. EMISSÃO DO CRACHÁ: Guardando dados na Sessão
            $_SESSION['id_usuario']   = $usuario['id'];
            $_SESSION['autor_usuario'] = $usuario['autor'];
            $_SESSION['logado']       = true;

            // 5. Retorno de Sucesso
            echo json_encode([
                'status' => 'sucesso', 
                'mensagem' => 'Login realizado! Bem-vindo, ' . $usuario['autor']
            ]);

        } else {
            // Se o e-mail não existir OU a senha estiver errada, caímos aqui
            // Dica: Mensagem genérica para não dar pistas a hackers
            echo json_encode(['status' => 'erro', 'mensagem' => 'E-mail ou senha incorretos.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no servidor.']);
    }

} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método não permitido.']);
}