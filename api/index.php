<?php
// Define as configurações do banco de dados
$host = 'localhost';
$dbname = 'nome_do_banco_de_dados';
$user = 'usuario_do_banco_de_dados';
$password = 'senha_do_banco_de_dados';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

// Cria uma conexão com o banco de dados
try {
    $pdo = new PDO("pgsql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit;
}
// Endpoint para cadastrar um novo tipo de produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/tipo_produto') {
    // Recupera os dados do tipo de produto a ser cadastrado do corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica se todos os campos foram informados
    if (empty($data['nome']) || empty($data['perc_imposto'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Campos obrigatórios não informados.']);
        exit;
    }

    // Insere o novo tipo de produto no banco de dados
    $stmt = $pdo->prepare('INSERT INTO tipo_produto (nome, perc_imposto) VALUES (?, ?)');
    $stmt->execute([$data['nome'], $data['perc_imposto']]);

    // Retorna o ID do novo tipo de produto criado
    $id_tipo_produto = $pdo->lastInsertId();
    echo json_encode(['id_tipo_produto' => $id_tipo_produto]);
    exit;
}

// Endpoint para cadastrar um novo produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/produto') {
    // Recupera os dados do produto a ser cadastrado do corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica se todos os campos foram informados
    if (empty($data['nome']) || empty($data['preco']) || empty($data['id_tipo_produto'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Campos obrigatórios não informados.']);
        exit;
    }

    // Verifica se o tipo de produto informado existe
    $stmt = $pdo->prepare('SELECT id_tipo_produto FROM tipo_produto WHERE id_tipo_produto = ?');
    $stmt->execute([$data['id_tipo_produto']]);
    $tipo_produto = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tipo_produto) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Tipo de produto não encontrado.']);
        exit;
    }

    // Insere o novo produto no banco de dados
    $stmt = $pdo->prepare('INSERT INTO produto (nome, preco, id_tipo_produto) VALUES (?, ?, ?)');
    $stmt->execute([$data['nome'], $data['preco'], $data['id_tipo_produto']]);

    // Retorna o ID do novo produto criado
    $id_produto = $pdo->lastInsertId();
    echo json_encode(['id_produto' => $id_produto]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/^\/produto(\/(\d+))?$/', $_SERVER['REQUEST_URI'], $matches)) {
    if (isset($matches[2])) {
        // Busca o produto no banco de dados pelo seu ID
        $id_produto = $matches[2];
        $stmt = $pdo->prepare('SELECT p.*, tp.nome AS tipo_produto_nome, tp.perc_imposto AS tipo_produto_perc_imposto FROM produto p INNER JOIN tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto WHERE id_produto = ?');
        $stmt->execute([$id_produto]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o produto foi encontrado
        if (!$produto) {
            http_response_code(404);
            echo json_encode(['mensagem' => 'Produto não encontrado.']);
            exit;
        }
        // Adiciona os dados do tipo do produto no array do produto

        // Retorna o produto em formato JSON
        echo json_encode($produto);
        exit;
    } else {
        // Busca todos os produtos no banco de dados
        $stmt = $pdo->query('SELECT p.*, tp.nome AS tipo_produto_nome, tp.perc_imposto AS tipo_produto_perc_imposto FROM produto p INNER JOIN tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto');
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retorna os produtos em formato JSON
        echo json_encode($produtos);
        exit;
    }
}


// Endpoint para buscar todas as vendas realizadas
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/venda') {
    // Busca todas as vendas no banco de dados
    $stmt = $pdo->query('SELECT * FROM venda');
    $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna as vendas em formato JSON
    echo json_encode($vendas);
    exit;
}
// Endpoint para buscar todos os tipos de produto cadastrados
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/tipo_produto') {
    // Busca todos os tipos de produto no banco de dados
    $stmt = $pdo->query('SELECT * FROM tipo_produto');
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os tipos de produto em formato JSON
    echo json_encode($tipos);
    exit;
}


// Endpoint para cadastrar uma nova venda e um item de venda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/venda') {
    // Recupera os dados da venda a ser cadastrada do corpo da requisição
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica se os campos obrigatórios foram informados
    if (empty($data['id_produto']) || empty($data['quantidade']) || empty($data['valor_total']) || empty($data['valor_imposto'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Campos obrigatórios não informados.']);
        exit;
    }

    // Insere a nova venda no banco de dados
    $stmt = $pdo->prepare('INSERT INTO venda (valor_total, valor_imposto) VALUES (?, ?)');
    $stmt->execute([$data['valor_total'], $data['valor_imposto']]);

    // Retorna o ID da venda recém-criada
    $id_venda = $pdo->lastInsertId();

    // Insere o item de venda no banco de dados
    $stmt = $pdo->prepare('INSERT INTO item_venda (id_venda, id_produto, quantidade, valor_unitario, valor_imposto) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$id_venda, $data['id_produto'], $data['quantidade'], $data['valor_total'] / $data['quantidade'], $data['valor_imposto']]);

    echo json_encode(['id_venda' => $id_venda]);
    exit;
}

