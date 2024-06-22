<?php

$db = new SQLite3('database.sqlite');

while(true){
    echo "Opções:\n";
    echo "1- Cadastrar um produto\n";
    echo "2- Listar todos os produtos\n";
    echo "3- Listar um produto pelo ID\n";
    echo "4- Atualizar um produto pelo ID\n";
    echo "5- Excluir um produto pelo ID\n";
    echo "6- Limpar tabela de produtos\n";
    echo "7- Sair\n";

    $opcaoUsuario = readLine("digite uma opção: ");
    if (!in_array($opcaoUsuario, ['1', '2', '3', '4', '5', '6', '7'])) {
        echo "Opção inválida. Por favor, tente novamente.\n";
        continue;
    }

    switch($opcaoUsuario){
        case '1' :  inserirProdutos();
        break;

        case '2' : listarTodos();
        break;

        case '3' : listarPorId();
        break;

        case '4': atualizarPorId();
        break;

        case '5': excluirPorId();
        break;

        case '6': limparTabela();
        break;

        case '7': echo "Adeus '--' ...\n";
        exit;
    }
};

function inserirProdutos(){
    $nomeProduto = readline('Qual o nome do produto?');
    $precoProduto = readline('Qual o preco do produto?');
    $dataCadastro = date('Y-m-d H:i:s');

    $db = new SQLite3('database.sqlite');

    $stmt = $db->prepare('INSERT INTO produtos (nome, preco, data_criacao) VALUES(:nome, :preco, :data_criacao)');
    $stmt->bindValue(':nome', $nomeProduto);
    $stmt->bindValue(':preco', $precoProduto);
    $stmt->bindValue(':data_criacao', $dataCadastro);
    var_dump($stmt->execute());
}

function listarTodos() {
    $db = new SQLite3('database.sqlite');
    $produtos = $db->query('SELECT * FROM produtos');
    
    while ($produto = $produtos->fetchArray(SQLITE3_ASSOC)) {
        var_dump($produto);
    }
}

function listarPorId(){
    $id = readLine('Digite o id do registro: ');

    if (!is_numeric($id)) {
        echo "ID inválido. Por favor, insira um número.\n\n";
        return;
    }

    $db = new SQLite3('database.sqlite');
    $stmt = $db->prepare('SELECT * FROM produtos WHERE id = :id');
    $stmt -> bindValue(':id', $id);
    $result = $stmt->execute();

    $produto = $result->fetchArray(SQLITE3_ASSOC);

    if ($produto) {
        var_dump($produto);
    } else {
        echo "Produto não encontrado.\n\n";
    }
}

function atualizarPorId() {
    $id = readline('Digite o ID do produto que deseja atualizar: ');

    if (!is_numeric($id)) {
        echo "ID inválido. Por favor, insira um número.\n";
        return;
    }

    $db = new SQLite3('database.sqlite');
    $stmt = $db->prepare('SELECT * FROM produtos WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    $produto = $result->fetchArray(SQLITE3_ASSOC);

    if (!$produto) {
        echo "Produto não encontrado.\n\n";
        return;
    }

    echo "Produto atual:\n";
    var_dump($produto);

    $novoNome = readline('Novo nome do produto: ');
    $novoPreco = readline('Novo preço do produto: ');

    if (!empty($novoNome)) {
        $produto['nome'] = $novoNome;
    }
    if (!empty($novoPreco) && is_numeric($novoPreco)) {
        $produto['preco'] = $novoPreco;
    } else if (!empty($novoPreco)) {
        echo "Preço inválido. A atualização foi cancelada.\n";
        return;
    }

    $dataAtualizacao = date('Y-m-d H:i:s');
    
    $stmtUpdate = $db->prepare('UPDATE produtos SET nome = :nome, preco = :preco, data_atualizacao = :data_atualizacao WHERE id = :id');
    $stmtUpdate->bindValue(':nome', $produto['nome'], SQLITE3_TEXT);
    $stmtUpdate->bindValue(':preco', $produto['preco'], SQLITE3_FLOAT);
    $stmtUpdate->bindValue(':data_atualizacao', $dataAtualizacao, SQLITE3_TEXT);
    $stmtUpdate->bindValue(':id', $id, SQLITE3_INTEGER);
    $resultado = $stmtUpdate->execute();

    if ($resultado) {
        echo "Produto atualizado com sucesso!\n\n";
    } else {
        echo "Erro ao atualizar o produto.\n\n";
    }
}

function excluirPorId() {
    $id = readline('Digite o ID do produto que deseja excluir: ');

    if (!is_numeric($id)) {
        echo "ID inválido. Por favor, insira um número.\n";
        return;
    }

    $db = new SQLite3('database.sqlite');
    $stmt = $db->prepare('DELETE FROM produtos WHERE id = :id');
    $stmt->bindValue(':id', $id);
    $result = $stmt->execute();

    if ($result) {
        echo "Produto excluído com sucesso!\n\n";
    } else {
        echo "Erro ao excluir o produto.\n\n";
    }
}

function limparTabela() {
    $db = new SQLite3('database.sqlite');
    $result = $db->exec('DELETE FROM produtos');

    if ($result) {
        echo "Todos os produtos foram excluídos com sucesso!\n\n";
    } else {
        echo "Erro ao excluir os produtos.\n";
    }
}