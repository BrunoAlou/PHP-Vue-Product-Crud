<?php
$dbhost = 'localhost';
$dbname = 'phpvue';
$user = 'postgres';
$password = 'Hay2krri';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

try {
    $pdo = new PDO("pgsql:host={$dbhost};dbname={$dbname}", $user, $password);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/tipo_produto') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['nome']) || empty($data['perc_imposto'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Campos obrigatórios não informados.']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO tipo_produto (nome, perc_imposto) VALUES (?, ?)');
    $stmt->execute([$data['nome'], $data['perc_imposto']]);
    $id_tipo_produto = $pdo->lastInsertId();
    echo json_encode(['id_tipo_produto' => $id_tipo_produto]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/produto') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['nome']) || empty($data['preco']) || empty($data['id_tipo_produto'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Campos obrigatórios não informados.']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id_tipo_produto FROM tipo_produto WHERE id_tipo_produto = ?');
    $stmt->execute([$data['id_tipo_produto']]);
    $tipo_produto = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tipo_produto) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Tipo de produto não encontrado.']);
        exit;
    }
    $stmt = $pdo->prepare('INSERT INTO produto (nome, preco, id_tipo_produto) VALUES (?, ?, ?)');
    $stmt->execute([$data['nome'], $data['preco'], $data['id_tipo_produto']]);

    $id_produto = $pdo->lastInsertId();
    echo json_encode(['id_produto' => $id_produto]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/^\/produto(\/(\d+))?$/', $_SERVER['REQUEST_URI'], $matches)) {
    if (isset($matches[2])) {
        $id_produto = $matches[2];
        $stmt = $pdo->prepare('SELECT p.*, tp.nome AS tipo_produto_nome, tp.perc_imposto AS tipo_produto_perc_imposto FROM produto p INNER JOIN tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto WHERE id_produto = ?');
        $stmt->execute([$id_produto]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            http_response_code(404);
            echo json_encode(['mensagem' => 'Produto não encontrado.']);
            exit;
        }

        echo json_encode($produto);
        exit;
    } else {
        $stmt = $pdo->query('SELECT p.*, tp.nome AS tipo_produto_nome, tp.perc_imposto AS tipo_produto_perc_imposto FROM produto p INNER JOIN tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto');
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($produtos);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/tipo_produto') {
    $stmt = $pdo->query('SELECT * FROM tipo_produto');
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($tipos);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/vendas') {
    $stmt = $pdo->query('SELECT * FROM venda');
    $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($vendas);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/venda') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['itens_venda'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Itens da venda não informados.']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO venda (valor_total, valor_imposto) VALUES (?, ?)');

    if ($stmt->execute([$data['valor_total'], $data['valor_imposto']])) {
        $id_venda = $pdo->lastInsertId();

        foreach ($data['itens_venda'] as $item) {
            $stmt = $pdo->prepare('INSERT INTO item_venda (id_venda, id_produto, quantidade, valor_unitario, valor_imposto) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$id_venda, $item['id_produto'], $item['quantidade'], $item['valor_unitario'], $item['valor_imposto']]);
        }

        header('Content-Type: application/json');
        echo json_encode(['id_venda' => $id_venda]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['mensagem' => 'Erro ao cadastrar venda.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/^\/venda\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches)) {
    $id_venda = $matches[1];

    $stmt = $pdo->prepare('SELECT venda.id_venda, venda.data_hora, venda.valor_total, venda.valor_imposto, item_venda.id_item_venda, item_venda.id_produto, item_venda.quantidade, item_venda.valor_unitario, item_venda.valor_imposto FROM venda INNER JOIN item_venda ON venda.id_venda = item_venda.id_venda WHERE venda.id_venda = ?');
    $stmt->execute([$id_venda]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        http_response_code(404);
        echo json_encode(['mensagem' => 'Venda não encontrada.']);
        exit;
    }

    $venda = [
        'id_venda' => $rows[0]['id_venda'],
        'data_hora' => $rows[0]['data_hora'],
        'valor_total' => $rows[0]['valor_total'],
        'valor_imposto' => $rows[0]['valor_imposto'],
        'itens_venda' => [],
    ];

    foreach ($rows as $row) {
        $item = [
            'id_item_venda' => $row['id_item_venda'],
            'id_produto' => $row['id_produto'],
            'quantidade' => $row['quantidade'],
            'valor_unitario' => $row['valor_unitario'],
            'valor_imposto' => $row['valor_imposto'],
        ];

        $venda['itens_venda'][] = $item;
    }

    header('Content-Type: application/json');
    echo json_encode($venda);
    exit;
}




