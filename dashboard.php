<?php
require 'config.php';
require 'includes/auth.php';
exigirLogin();

// Adicionar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $titulo = trim($_POST['titulo']);
    if ($titulo !== '') {
        $stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, titulo) VALUES (?, ?)");
        $stmt->execute([$_SESSION['usuario_id'], $titulo]);
    }
    header("Location: dashboard.php");
    exit;
}

// Marcar/desmarcar tarefa como concluída
if (isset($_GET['concluir'])) {
    $id = (int) $_GET['concluir'];
    $stmt = $pdo->prepare("UPDATE tarefas SET concluida = NOT concluida WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $_SESSION['usuario_id']]);
    header("Location: dashboard.php");
    exit;
}

// Remover tarefa
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $_SESSION['usuario_id']]);
    header("Location: dashboard.php");
    exit;
}

// Busca só as tarefas do usuário logado (nunca de outros usuários)
$stmt = $pdo->prepare("SELECT id, titulo, concluida, criado_em FROM tarefas WHERE usuario_id = ? ORDER BY criado_em DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$tarefas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="topo">
            <h1>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1>
            <a href="logout.php" class="botao-sair">Sair</a>
        </div>

        <nav class="nav">
            <a href="dashboard.php" class="ativo">Minhas tarefas</a>
            <a href="usuarios.php">Usuários</a>
        </nav>

        <h2>Minhas tarefas</h2>

        <form method="post" action="dashboard.php" class="form-tarefa">
            <input type="text" name="titulo" placeholder="Nova tarefa..." required>
            <button type="submit">Adicionar</button>
        </form>

        <ul class="lista-tarefas">
            <?php if (!$tarefas): ?>
                <li class="vazio">Nenhuma tarefa cadastrada ainda.</li>
            <?php endif; ?>

            <?php foreach ($tarefas as $t): ?>
                <li class="<?= $t['concluida'] ? 'concluida' : '' ?>">
                    <span><?= htmlspecialchars($t['titulo']) ?></span>
                    <span class="acoes">
                        <a href="dashboard.php?concluir=<?= $t['id'] ?>">
                            <?= $t['concluida'] ? 'Desmarcar' : 'Concluir' ?>
                        </a>
                        <a href="dashboard.php?excluir=<?= $t['id'] ?>"
                           onclick="return confirm('Excluir esta tarefa?')">Excluir</a>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
