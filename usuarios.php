<?php
require 'config.php';
require 'includes/auth.php';
exigirLogin();

$stmt = $pdo->query("SELECT id, nome, email, criado_em FROM usuarios ORDER BY nome");
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuários</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="topo">
            <h1>Usuários cadastrados</h1>
            <a href="logout.php" class="botao-sair">Sair</a>
        </div>

        <nav class="nav">
            <a href="dashboard.php">Minhas tarefas</a>
            <a href="usuarios.php" class="ativo">Usuários</a>
        </nav>

        <?php if (isset($_GET['erro']) && $_GET['erro'] === 'auto'): ?>
            <p class="alerta alerta-erro">
                Não é possível excluir o próprio usuário enquanto estiver logado com ele.
            </p>
        <?php endif; ?>

        <p><a href="register_usuario.php" class="link-botao">+ Novo usuário</a></p>

        <table class="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Cadastrado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['nome']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['criado_em']) ?></td>
                        <td class="acoes">
                            <a href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>
                            <a href="excluir_usuario.php?id=<?= $u['id'] ?>"
                               onclick="return confirm('Excluir este usuário? Todas as tarefas dele também serão apagadas.')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
