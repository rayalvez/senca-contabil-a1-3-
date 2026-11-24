async function carregarServicos() {
    const lista = document.getElementById('lista-servicos');
    const select = document.getElementById('select-servico');
    const status = document.getElementById('status-servicos');

    try {
        const resposta = await fetch('api.php?acao=servicos');
        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso)
            throw new Error(dados.mensagem);

        lista.replaceChildren();
        select.replaceChildren(new Option('Selecione um serviço', ''));

        dados.servicos.forEach(servico => {
            const card = document.createElement('article');
            card.className = 'card';

            const titulo = document.createElement('h3');
            titulo.textContent = servico.nome;

            const descricao = document.createElement('p');
            descricao.textContent = servico.descricao;

            const botao = document.createElement('a');
            botao.className = 'btn';
            botao.href = '#orcamento';
            botao.textContent = 'Solicitar orçamento';

            botao.addEventListener('click', () => {
                select.value = servico.id;
            });

            card.append(titulo, descricao, botao);
            lista.append(card);

            select.add(new Option(servico.nome, servico.id));
        });

        const temServicos = dados.servicos.length > 0;
        select.disabled = !temServicos;

        document.getElementById('enviar-orcamento').disabled = !temServicos;

        status.textContent = temServicos ? '' : 'Nenhum serviço cadastrado.';

    } catch (erro) {
        status.textContent = 'Não foi possível carregar os serviços. Confira o Apache, o MySQL e o banco de dados.';
        status.className = 'erro';
    }
}

// Envia os dados do formulário sem recarregar a página.
async function enviarFormulario(evento, acao) {
    evento.preventDefault();

    const form = evento.currentTarget;
    const status = document.getElementById('status-' + acao);
    const botao = form.querySelector('button[type="submit"]');

    botao.disabled = true;
    status.className = 'status';
    status.textContent = 'Enviando...';

    try {
        const resposta = await fetch('api.php?acao=' + acao, {
            method: 'POST',
            body: new FormData(form)
        });

        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso)
            throw new Error(dados.mensagem);

        status.className = 'status ok';
        status.textContent = dados.mensagem;

        form.reset();

    } catch (erro) {
        status.className = 'status erro';
        status.textContent = erro.message || 'Falha ao enviar.';

    } finally {
        botao.disabled = false;
    }
}

function enviarContato(evento) {
    return enviarFormulario(evento, 'contato');
}

function enviarOrcamento(evento) {
    return enviarFormulario(evento, 'orcamento');
}

document.getElementById('form-contato')
    .addEventListener('submit', enviarContato);

document.getElementById('form-orcamento')
    .addEventListener('submit', enviarOrcamento);

const hoje = new Date();

const dataLocal = new Date(
    hoje.getTime() - hoje.getTimezoneOffset() * 60000
).toISOString().slice(0, 10);

document.querySelector('[name="data_desejada"]').min = dataLocal;

carregarServicos();
