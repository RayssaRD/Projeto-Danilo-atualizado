<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    echo json_encode(['logado' => true, 'nome' => $_SESSION['nome_usuario']]);
} else {
    echo json_encode(['logado' => false]);
}
?>