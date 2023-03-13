<?php

$host = 'localhost';
$dbname = 'nome_do_banco_de_dados';
$user = 'usuario_do_banco_de_dados';
$password = 'senha_do_banco_de_dados';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        INSERT INTO tipo_produto (nome, perc_imposto) VALUES
        ('Tipo 1', 10.00),
        ('Tipo 2', 5.50),
        ('Tipo 3', 15.00);
    ");

    $pdo->exec("
        INSERT INTO produto (nome, preco, id_tipo_produto) VALUES
        ('Produto 1', 100.00, 1),
        ('Produto 2', 50.00, 2),
        ('Produto 3', 200.00, 3);
    ");

    $pdo->exec("
        INSERT INTO venda (valor_total, valor_imposto) VALUES
        (500.00, 75.00),
        (1000.00, 150.00),
        (250.00, 37.50);
   
