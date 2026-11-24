<?php
require_once 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

function responder($dados, $status = 200) {
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function campo($nome, $limite, $obrigatorio = true) {
    $valor = $_POST[$nome] ?? '';

    if (!is_string($valor)) {
        responder([
            'sucesso' => false,
            'mensagem' => 'Campo inválido: '.$nome
        ], 400);
    }

    $valor = trim($valor);

    if (($obrigatorio && $valor === '') ||
        (function_exists('mb_strlen') ?
        mb_strlen($valor, 'UTF-8') : strlen($valor)) > $limite) {

        responder([
            'sucesso' => false,
            'mensagem' => 'Verifique o campo '.$nome.'.'
        ], 400);
    }

    return $valor;
}

$acao = $_GET['acao'] ?? '';

try {
    // Listagem de serviços.
    if ($acao === 'servicos' && $_SERVER['REQUEST_METHOD'] === 'GET') {

        $pdo = conectar();

        $consulta = $pdo->query(
            'SELECT id, nome, descricao FROM servicos ORDER BY id'
        );

        responder([
            'sucesso' => true,
            'servicos' => $consulta->fetchAll()
        ]);
    }

    if (in_array($acao, ['contato', 'orcamento'], true) &&
        $_SERVER['REQUEST_METHOD'] === 'POST') {

        $nome = campo('nome', 120);
        $email = campo('email', 160);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            responder([
                'sucesso' => false,
                'mensagem' => 'E-mail inválido.'
            ], 400);
        }

        // Inserção de contato.
        if ($acao === 'contato') {

            $mensagem = campo('mensagem', 2000);
            $pdo = conectar();

            $sql = $pdo->prepare(
                'INSERT INTO contatos (nome,email,mensagem)
                 VALUES (?,?,?)'
            );

            $sql->execute([$nome, $email, $mensagem]);

            responder([
                'sucesso' => true,
                'mensagem' => 'Mensagem enviada com sucesso!'
            ], 201);
        }

        // Inserção de orçamento.
        $telefone = campo('telefone', 30);
        $endereco = campo('endereco', 180);
        $cidade = campo('cidade', 100);
        $uf = campo('uf', 2);
        $data = campo('data_desejada', 10);
        $observacoes = campo('observacoes', 2000, false);

        $servico = filter_var(
            $_POST['servico_id'] ?? '',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $dia = DateTime::createFromFormat('!Y-m-d', $data);

        $ufs = explode(
            ' ',
            'AC AL AP AM BA CE DF ES GO MA MT MS MG PA PB PR PE PI RJ RN RS RO RR SC SP SE TO'
        );

        if (!$servico ||
            !in_array($uf, $ufs, true) ||
            !$dia ||
            $dia->format('Y-m-d') !== $data ||
            $data < date('Y-m-d')) {

            responder([
                'sucesso' => false,
                'mensagem' => 'Confira o serviço, UF e a data desejada.'
            ], 400);
        }

        $pdo = conectar();

        $consulta = $pdo->prepare(
            'SELECT id FROM servicos WHERE id = ?'
        );

        $consulta->execute([$servico]);

        if (!$consulta->fetch()) {
            responder([
                'sucesso' => false,
                'mensagem' => 'Serviço não encontrado.'
            ], 400);
        }

        $sql = $pdo->prepare(
            'INSERT INTO orcamentos
            (nome,email,telefone,servico_id,endereco,cidade,uf,data_desejada,observacoes)
            VALUES (?,?,?,?,?,?,?,?,?)'
        );

        $sql->execute([
            $nome,
            $email,
            $telefone,
            $servico,
            $endereco,
            $cidade,
            $uf,
            $data,
            $observacoes
        ]);

        responder([
            'sucesso' => true,
            'mensagem' => 'Orçamento solicitado com sucesso!'
        ], 201);
    }

    responder([
        'sucesso' => false,
        'mensagem' => 'Rota ou método inválido.'
    ], 405);

} catch (PDOException $e) {

    error_log($e->getMessage());

    responder([
        'sucesso' => false,
        'mensagem' => 'Erro no banco de dados. Confira o MySQL e a importação do banco.sql.'
    ], 500);
}
