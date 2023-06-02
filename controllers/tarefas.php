<?php

$exibir_tabela = true;

$tem_erros = false;
$erros_validacao = [];

$tarefa = new Tarefa();
$tarefa->setPrioridade(1);

if (tem_post()) {
    if (array_key_exists('nome', $_POST) && strlen($_POST['nome']) > 0) {
        $tem_erros = false;
        unset($erros_validacao);
        $tarefa->setNome($_POST['nome']);
    } else {
        $tem_erros = true;
        $erros_validacao['nome'] = 'O nome da tarefa é obrigatório!';
    }

    if (array_key_exists('descricao', $_POST)) {
        $tem_erros = false;
        unset($erros_validacao);
        $tarefa->setDescricao($_POST['descricao']);
    } else {
        $tarefa['descricao'] = '';
    }

    if (array_key_exists('prazo', $_POST) && strlen($_POST['prazo']) > 0) {
        if (validar_data(traduz_data_para_exibir($_POST['prazo']))) {
            $tem_erros = false;
            unset($erros_validacao);
            $tarefa->setPrazo(traduz_data_br_para_objeto(traduz_data_para_exibir($_POST['prazo'])));
        } else {
            $tem_erros = true;
            $erros_validacao['prazo'] = 'O prazo não é uma data válida!';
        }
    }

    $tarefa->setPrioridade($_POST['prioridade']);

    if (array_key_exists('concluida', $_POST)) {
        $tarefa->setConcluida(true);
    } else {
        $tarefa->setConcluida(false);
    }

    if (!$tem_erros) {
        $repositorio_tarefas->salvar($tarefa);

        if (isset($_POST['lembrete']) && $_POST['lembrete'] == '1') {
            enviar_email($tarefa);
        }

        header('Location: index.php?rota=tarefas');
        die();
    }
}

$tarefas = $repositorio_tarefas->buscar();

require __DIR__ . "/../views/template.php";