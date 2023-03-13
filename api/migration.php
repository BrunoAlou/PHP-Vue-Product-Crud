<?php

$host = 'localhost';
$dbname = 'nome_do_banco_de_dados';
$user = 'usuario_do_banco_de_dados';
$password = 'senha_do_banco_de_dados';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE tipo_produto (
            id_tipo_produto SERIAL PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            perc_imposto DECIMAL(10, 2) NOT NULL
        );
    ");

    $pdo->exec("
        CREATE TABLE produto (
            id_produto SERIAL PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            preco DECIMAL(10, 2) NOT NULL,
            id_tipo_produto INTEGER NOT NULL,
            CONSTRAINT fk_id_tipo_produto FOREIGN KEY (id_tipo_produto) REFERENCES tipo_produto(id_tipo_produto)
        );
    ");

    $pdo->exec("
        CREATE TABLE venda (
            id_venda SERIAL PRIMARY KEY,
            data_hora TIMESTAMP NOT NULL DEFAULT NOW(),
            valor_total DECIMAL(10, 2) NOT NULL,
            valor_imposto DECIMAL(10, 2) NOT NULL
        );
    ");

    $pdo->exec("
        CREATE TABLE item_venda (
            id_item_venda SERIAL PRIMARY KEY,
            id_venda INTEGER NOT NULL,
            id_produto INTEGER NOT NULL,
            quantidade INTEGER NOT NULL,
            valor_unitario DECIMAL(10, 2) NOT NULL,
            valor_imposto DECIMAL(10, 2) NOT NULL,
            CONSTRAINT fk_id_venda FOREIGN KEY (id_venda) REFERENCES venda(id_venda),
            CONSTRAINT fk_id_produto FOREIGN KEY (id_produto) REFERENCES produto(id_produto)
        );
    ");
} catch(PDOException $e) {
    echo "Erro ao executar as queries: " . $e->getMessage();
}
