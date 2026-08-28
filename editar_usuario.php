<?php
require 'config.php';
require 'includes/auth.php';
exigirLogin();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: usuarios.php");
    exit;
}

$erro    = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? ''; // opcional: só troca se for preenchida

    if ($nome === '' || $email === '') {
        $erro = "Nome e e-mail são obrigatórios.";
    } else {
        // Confere se o e-mail já pertence a outro usuário
        $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $check->execute([$email, $id]);

        if ($check->fetch()) {
            $erro = "Já existe outro usuário cadastrado com esse e-mail.";
        } elseif ($senha !== '' && strlen($senha) < 6) {
            $erro = "A nova senha deve ter pelo menos 6 caracteres.";
        } else {
            if ($senha !== '') {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $upd  = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, senha_hash = ? WHERE id = ?");
                $upd->execute([$nome, $email, $hash, $id]);
            } else {
                $upd = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
                $upd->execute([$nome, $email, $id]);
            }

            $usuario['nome']  = $nome;
            $usuario['email'] = $email;
            $sucesso = "Dados atualizados com sucesso.";

            // Se o usuário editado é o que está logado, atualiza o nome exibido na sessão
            if ($id === (int) ($_SESSION['usuario_id'] ?? 0)) {
                $_SESSION['usuario_nome'] = $nome;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar usuário</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container container-estreito">
        <h1>Editar usuário</h1>

        <?php if ($erro): ?>
            <p class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <?php if ($sucesso): ?>
            <p class="alerta alerta-sucesso"><?= htmlspecialchars($sucesso) ?></p>
        <?php endif; ?>

        <form method="post" action="editar_usuario.php?id=<?= $usuario['id'] ?>" class="form">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

            <label for="senha">Nova senha (deixe em branco para manter a atual)</label>
            <input type="password" id="senha" name="senha">

            <button type="submit">Salvar alterações</button>
        </form>

        <p class="dica"><a href="usuarios.php">&larr; Voltar para a lista de usuários</a></p>
    </div>
</body>
</html>
