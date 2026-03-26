<?php
//importando o arquivo que liga o PHP ao Banco de Dados
require 'conexao.php';

//Verificação via POST
if($_SERVER['REQUEST_METHOD']=== 'POST'){
    // Pegar os dados e limpar espaços inúteis
    $autor = trim($_POST['autor']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    //Validação: verifica se nenhu campo está vazio 
    if(empty($autor)|| empty($email)|| empty($senha)){
        die("Erro! Você precisa preencher TODOS os campos!");
    }


    // Criptografando a senha antes de salvar
    $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT); // PASSWORD_DEFAULT define qual algoritmo de hashing deve ser usado quando chamamos o password hash

    try{
        //Prepared Statements: Preparação
        $sql = "INSERT INTO usuarios(autor, email, senha_hash) VALUES (:autor, :email, :senha)";
        $stmt = $pdo->prepare($sql);

        // Associando os valores reais aos "apelidos". Vamos enviar a $senhaCriptografada, não a senha pura!
        $stmt->bindParam(':autor', $autor);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaCriptografada);

        //Salvando oficialmente no Banco de Dados
        if($stmt->execute()){
            echo "Usuário Cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar usuário.";
        }
    } catch(PDOException $e){
        // verifica se o erro é de email duplicado (comum em cadastros)
       die($e->getMessage());
    }
} else{
    echo "Acesso inválido.";
}


?>