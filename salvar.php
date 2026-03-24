<?php
require "conexao.php";

// Verifica se o texto chegou via post
if (isset($_POST['texto']) && !empty(trim($_POST['texto'])) && isset($_POST['autor']) && !empty(trim($_POST['autor']))) {
    $texto = trim($_POST['texto']);
    $autor = trim($_POST['autor']);


    //1.Sanitização: remover espaços extras e neutralizar tags html (o anti xss)
    $texto = htmlspecialchars(trim($_POST['texto']));

    //2. verificar se os campos estao vazios
    if(empty($texto)){
        echo json_encode(['status'=> "erro", 'mensagem' => "Ei! Os campos não podem estar vazios."]);
        exit;
    }

    // Inserir o registro no banco de dados
    $stmt = $pdo->prepare("INSERT INTO mensagens (texto,autor) VALUES (:texto,:autor)");
    $stmt->bindParam(":texto", $texto);
    $stmt->bindParam(":autor", $autor);

    //'1=1 OR'
    

    if($stmt->execute()) {
        echo json_encode(['status' => 'sucesso']);
    } else {
        echo json_encode(['status' => 'erro']);
    }
}