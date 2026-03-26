<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso Negado!']);
    exit;
}
$autor_nome = $_SESSION['nome_usuario']; // Pega o nome que o login guardou
        //conexao
    require "conexao.php";

        // Verifica se o texto chegou via POST if

        //if (isset($_POST['texto']) && !empty(trim($_POST['texto'])) && isset($_POST['autor'])) {

           // $texto = trim($_POST['texto']);

            //$autor = trim($_POST['autor']);

            //1.SANITIZAÇÃO: o "Anti-XSS". Remove espaços e neutraliza tags HTML.

           // $autor = htmlspecialchars(trim($_POST['autor']), ENT_QUOTES,'UTF-8');

           // $texto = htmlspecialchars(trim($_POST['texto']), ENT_QUOTES,'UTF-8');//
            // Verifica se o texto chegou via POST
    if (isset($_POST['texto'])) {

        // --- A SUBSTITUIÇÃO (A SACADA DE SEGURANÇA) ---
        // Pegamos o texto do formulário, mas o NOME vem da SESSÃO (inviolável)
        $texto_puro = trim($_POST['texto']);
        $autor_nome = $_SESSION['nome_usuario']; 

        // 3. VALIDAÇÃO DE TAMANHO (BACK-END)
        // Verificamos o texto bruto ANTES de qualquer filtro
        if (mb_strlen($texto_puro, 'UTF-8') > 150) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'Atenção: Sua mensagem passou do limite de 150 caracteres.']);
            exit;
        }

            // 4. VERIFICAÇÃO DE DADOS: Agora só precisamos do 'texto' via POST
    if (isset($_POST['texto']) && !empty(trim($_POST['texto']))) {

        // A SUBSTITUIÇÃO: O autor não vem mais do formulário, vem da SESSÃO
        $autor_nome = $_SESSION['nome_usuario']; 
        $texto_bruto = $_POST['texto'];

        // 5. VALIDAÇÃO DE TAMANHO (Missão 2): No texto bruto antes de filtrar
        if (mb_strlen($texto_bruto, 'UTF-8') > 150) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'Atenção: Sua mensagem passou do limite de 150 caracteres.']);
            exit;
        }
    }

        // 6. FILTRO DE PALAVRAS (Missão 1): Suas palavras proibidas
        $palavras_proibidas = ['bobo', 'feio', 'chato', 'boboca', 'ridiculo', 'merda', 'idiota', 'ameba'];
        $texto_filtrado = str_ireplace($palavras_proibidas, '***', $texto_bruto);

        // 7. SANITIZAÇÃO (Anti-XSS): Limpando para o banco
        $texto = htmlspecialchars(trim($texto_filtrado), ENT_QUOTES, 'UTF-8');
        $autor = htmlspecialchars(trim($autor_nome), ENT_QUOTES, 'UTF-8');

        // 8. PERSISTÊNCIA: Inserindo no banco de dados
        try {
            $stmt = $pdo->prepare("INSERT INTO mensagens (texto, autor) VALUES (:texto, :autor)");
            $stmt->bindParam(":texto", $texto);
            $stmt->bindParam(":autor", $autor);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'sucesso']);
            } else {
                echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno ao salvar no banco de dados.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
        }

    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Atenção: A mensagem não pode estar vazia.']);
    }
?>