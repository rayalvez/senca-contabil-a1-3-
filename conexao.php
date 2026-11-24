<?php
// Configuração padrão do MySQL local do XAMPP.
function conectar() {
    $host = '127.0.0.1';
    $banco = 'senca_contabil';
    $usuario = 'root';
    $senha = '';

    return new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
}
