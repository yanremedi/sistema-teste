<?php
require 'config.php';
require 'includes/auth.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? 0);

// Evita que o usuário logado exclua a própria conta por engano
if ($id === (int) ($_SESSION['usuario_id'] ?? 0)) {
    header("Location: usuarios.php?erro=auto");
    exit;
}

// A chave estrangeira em "tarefas" está com ON DELETE CASCADE,
// então as tarefas desse usuário são apagadas junto automaticamente.
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id]);

header("Location: usuarios.php");
exit;
