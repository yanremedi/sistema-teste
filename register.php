<?php
require 'config.php';
require 'includes/auth.php';

if (estaLogado()) {
    header("Location: dashboard.php");
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome              = trim($_POST['nome'] ?? '');
    $email             = trim($_POST['email'] ?? '');
    $senha             = $_POST['senha'] ?? '';
    $confirmar_senha   = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = "Preencha todos os campos.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não conferem.";
    } else {
        // Garante que o e-mail ainda não está cadastrado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = "Já existe uma conta cadastrada com esse e-mail.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $hash]);

            // Já loga o usuário recém-criado e manda direto para o painel
            $_SESSION['usuario_id']   = (int) $pdo->lastInsertId();
            $_SESSION['usuario_nome'] = $nome;
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar conta</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container container-estreito">
        <h1>Criar conta</h1>

        <?php if ($erro): ?>
            <p class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="post" action="register.php" class="form">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required autofocus>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <label for="confirmar_senha">Confirmar senha</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>

            <button type="submit">Criar conta</button>
        </form>

        <p class="dica">
            Já tem conta? <a href="login.php">Entrar</a>.
        </p>
    </div>
</body>
</html>
