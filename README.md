# Sistema de exemplo — Login, Cadastro de Usuários e Tarefas (PHP + MySQL)

Projeto simples para uso didático: tela de **login**, **CRUD completo de usuários**
(criar, listar, editar e excluir) e um **painel** protegido por sessão onde cada
usuário gerencia sua própria lista de tarefas.

Serve como base para praticar o passo a passo de publicação em hospedagem PHP/MySQL
usando **GitHub + GitHub Actions (CI/CD)** com deploy via FTP.

## Estrutura do banco de dados

Duas tabelas, relacionadas por chave estrangeira:

- **usuarios** — `id`, `nome`, `email`, `senha_hash`, `criado_em`
- **tarefas** — `id`, `usuario_id` (FK → usuarios.id), `titulo`, `concluida`, `criado_em`

O script completo está em [`database/schema.sql`](database/schema.sql).

## CRUD de usuários

| Ação   | Arquivo                  | Descrição                                                    |
|--------|---------------------------|---------------------------------------------------------------|
| Create | `register.php`            | Autocadastro público — cria a conta e já loga o usuário       |
| Create | `register_usuario.php`    | Cadastro de um novo usuário a partir da tela de gerenciamento  |
| Read   | `usuarios.php`             | Lista todos os usuários cadastrados                           |
| Update | `editar_usuario.php`       | Edita nome, e-mail e (opcionalmente) a senha de um usuário    |
| Delete | `excluir_usuario.php`      | Remove um usuário (e suas tarefas, via `ON DELETE CASCADE`)   |

> **Observação para a turma:** este projeto não tem controle de permissões
> (qualquer usuário logado pode gerenciar todos os outros). Em um sistema real,
> essa área de gerenciamento normalmente seria restrita a um perfil de
> administrador — é um ótimo próximo passo para propor como exercício.

## Estrutura de arquivos

```
sistema-login-cadastro/
├── config.php                 → conexão com o banco (usa marcadores __DB_HOST__ etc.)
├── includes/
│   └── auth.php                 → funções de sessão/login
├── login.php                    → tela de login
├── register.php                  → autocadastro público (Create)
├── register_usuario.php           → cadastro de usuário pelo painel (Create)
├── usuarios.php                    → lista de usuários (Read)
├── editar_usuario.php               → edição de usuário (Update)
├── excluir_usuario.php               → exclusão de usuário (Delete)
├── dashboard.php                      → painel protegido (lista/adiciona/conclui/exclui tarefas)
├── logout.php                          → encerra a sessão
├── index.php                            → redireciona para login ou dashboard
├── assets/
│   └── style.css                         → estilo das telas
├── database/
│   └── schema.sql                         → estrutura das tabelas + dados de teste
├── .github/workflows/
│   └── deploy.yml                          → pipeline de deploy automático via FTP
└── .gitignore
```

## Usuário de teste

Depois de importar o `schema.sql`, já existe um usuário pronto:

- **E-mail:** `usuario@exemplo.com`
- **Senha:** `123456`

A senha é armazenada como **hash** (nunca em texto puro) e verificada com `password_verify()`.

## Testando localmente (antes de publicar)

1. Suba um MySQL local (ex: XAMPP, Laragon ou WAMP) e importe `database/schema.sql`
   em um banco novo (ex: `sistema_local`).
2. Abra `config.php` e troque temporariamente os marcadores pelos dados do seu
   banco local:
   ```php
   $host   = "localhost";
   $dbname = "sistema_local";
   $user   = "root";
   $pass   = "";
   ```
   **Não faça commit dessa alteração** — antes de subir para o GitHub, desfaça e
   deixe os marcadores (`__DB_HOST__`, etc.) de volta no lugar, pois é isso que o
   GitHub Actions vai substituir automaticamente no deploy.
3. Coloque a pasta do projeto no `htdocs` do XAMPP (ou rode `php -S localhost:8000`
   dentro da pasta do projeto) e acesse `login.php` no navegador.

## Publicando na hospedagem

Este projeto já vem com o `config.php` "templatizado" e o `.github/workflows/deploy.yml`
prontos, seguindo o guia de implantação com GitHub Actions:

1. Crie o banco de dados MySQL no painel da hospedagem e importe `database/schema.sql`
   pelo phpMyAdmin.
2. Crie o repositório no GitHub e envie este projeto (`git push`).
3. Cadastre os **Secrets** do repositório (`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`,
   `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
4. Todo `git push` na branch `main` publica automaticamente o sistema no servidor.

## Pontos importantes para discutir com a turma

- **Consultas preparadas (prepared statements)**: todas as consultas usam `PDO::prepare()`
  com parâmetros (`?`), evitando SQL Injection.
- **Senha nunca em texto puro**: armazenada com `password_hash()`, verificada com
  `password_verify()`, e nunca reexibida nos formulários de edição.
- **Isolamento por usuário nas tarefas**: todas as consultas de tarefas filtram por
  `usuario_id = ?`, garantindo que um usuário nunca veja ou altere tarefas de outro.
- **Validação de e-mail único**: tanto no cadastro quanto na edição, o sistema
  verifica se o e-mail já pertence a outro usuário antes de salvar.
- **`htmlspecialchars()`**: usado sempre que dados vindos do banco (ou do próprio
  formulário reenviado) são exibidos na tela, para evitar XSS.
- **Config com marcadores**: nenhuma credencial real fica gravada no repositório —
  elas só existem nos Secrets do GitHub e são injetadas no momento do deploy.
