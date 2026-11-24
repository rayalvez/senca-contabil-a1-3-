
CREATE DATABASE IF NOT EXISTS senca_contabil
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE senca_contabil;

CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(160) NOT NULL,
    descricao TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    mensagem TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orcamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    telefone VARCHAR(30) NOT NULL,
    servico_id INT NOT NULL,
    endereco VARCHAR(180) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    uf CHAR(2) NOT NULL,
    data_desejada DATE NOT NULL,
    observacoes TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
) ENGINE=InnoDB;

INSERT IGNORE INTO servicos (id,nome,descricao) VALUES

(1,
'Contabilidade para pequenas e médias empresas',
'Escrituração contábil e fiscal, apuração de impostos, emissão de guias e relatórios gerenciais mensais.'),

(2,
'Abertura, alteração e encerramento de empresas',
'Assessoria para abertura de CNPJ, enquadramento tributário, alterações contratuais e baixa de empresas.'),

(3,
'Consultoria tributária e planejamento fiscal',
'Análise dos regimes tributários e estratégias legais para reduzir a carga de impostos.');
