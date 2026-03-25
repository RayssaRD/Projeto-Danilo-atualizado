<?php
        session_start();
        // PASSO 2: O Bloqueio de Segurança
// Verificamos se a chave 'id_usuario' NÃO está definida na sessão
if (!isset($_SESSION['id_usuario'])) {
    
    // 1. Prepara a resposta de erro em formato JSON
    echo json_encode([
        'status' => 'erro', 
        'mensagem' => 'Acesso Negado: Você precisa estar logado para ver isso!'
    ]);

    // 2. O EXIT é vital: ele mata o script na hora e impede que o resto 
    // do seu código (como o INSERT ou SELECT) seja executado.
    exit; 
}
        //salvar.php
        require "conexao.php";

        // Verifica se o texto chegou via POST if

        if (isset($_POST['texto']) && !empty(trim($_POST['texto'])) && isset($_POST['autor'])) {

            $texto = trim($_POST['texto']);

            $autor = trim($_POST['autor']);

            //1.SANITIZAÇÃO: o "Anti-XSS". Remove espaços e neutraliza tags HTML.

            $autor = htmlspecialchars(trim($_POST['autor']), ENT_QUOTES,'UTF-8');

            $texto = htmlspecialchars(trim($_POST['texto']), ENT_QUOTES,'UTF-8');

        // 2. FILTRO DE PALAVRAS (Missão 1)

        $proibidas = ['bobo', 'feio', 'chato'];

        $texto = str_ireplace($proibidas, '***', $texto);

        // 3. VALIDAÇÃO DE TAMANHO (Missão 2)

        $texto = $_POST['texto'];

        // Verifica se a contagem real é maior que 150
        if (mb_strlen($texto, 'UTF-8') > 150) {
            echo "Erro: Sua mensagem é muito longa!";
            exit;
        }


        // 4. PERSISTÊNCIA (O resto do seu código...)


            //2. VALIDAÇÃO: Verifica se após a limpeza, os campos não ficaram vazios

            if(empty($autor) || empty($texto)){

                // Retorna um erro amigável em formato JSON para o Front-end ler

                echo json_encode(['status'=> "erro", 'mensagem' => "Atenção: Os campos não podem estar vazios."]);

                exit;

            }

                // --- FILTRO DE PALAVRAS PROIBIDAS ---

            $palavras_proibidas = ['bobo', 'feio', 'chato', 'boboca', 'ridiculo', 'merda', 'idiota', 'ameba',]; // Adicione quantas quiser

            $substituicao = '***';

            // O str_ireplace percorre o array e substitui na string $texto

            $texto = str_ireplace($palavras_proibidas, $substituicao, $texto);

            // ------------------------------------

            // 3.PERSISTÊNCIA Prepara a query segura (Prepared Statements) para o Banco de Dados

            $stmt = $pdo->prepare("INSERT INTO mensagens (texto,autor) VALUES (:texto,:autor)");

            $stmt->bindParam(":texto", $texto);

            $stmt->bindParam(":autor", $autor);

            if($stmt->execute()) {

                echo json_encode(['status' => 'sucesso']);

            } else {

                echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno ao salvar no banco de dados.']);

            }

        } else {

                echo json_encode(['status' => 'erro', 'mensagem' => 'Requisição inválida.']);

            }

?>