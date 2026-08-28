-- Estrutura do banco de dados do sistema de exemplo (login, cadastro de usuários e tarefas)
-- Importe este arquivo pelo phpMyAdmin, na aba "Importar", dentro do
-- banco de dados já criado no painel de hospedagem.

CREATE TABLE IF NOT EXISTS usuarios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nome        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    senha_hash  VARCHAR(255)  NOT NULL,
    criado_em   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tarefas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT           NOT NULL,
    titulo      VARCHAR(255)  NOT NULL,
    concluida   TINYINT(1)    NOT NULL DEFAULT 0,
    criado_em   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuário de teste
-- E-mail: usuario@exemplo.com
-- Senha:  123456   (já gravada como hash, nunca em texto puro)
INSERT INTO usuarios (nome, email, senha_hash) VALUES
('Usuário Teste', 'usuario@exemplo.com', '$2b$12$FkGW7FgBp4tEltSvgCTtw.BP2nMd.mZeg0FL/jWu9VnFK28L9DBZm');

-- Algumas tarefas de exemplo já vinculadas ao usuário de teste (id = 1)
INSERT INTO tarefas (usuario_id, titulo, concluida) VALUES
(1, 'Configurar a conta na hospedagem', 1),
(1, 'Criar o banco de dados MySQL', 1),
(1, 'Configurar o deploy com GitHub Actions', 0),
(1, 'Testar o login em produção', 0);
