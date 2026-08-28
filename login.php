<?php
require 'config.php';
require 'includes/auth.php';

// Se já está logado, não faz sentido ver a tela de login de novo
if (estaLogado()) {
    header("Location: dashboard.php");
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = "Preencha e-mail e senha.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nome, email, senha_hash FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // password_verify compara a senha digitada com o hash guardado no banco.
        // Nunca comparamos senha em texto puro diretamente.
        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = "E-mail ou senha inválidos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container container-estreito">
        <h1>Entrar no sistema</h1>

        <?php if ($erro): ?>
            <p class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="post" action="login.php" class="form">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required autofocus>

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>

        <p class="dica">
            Ainda não tem conta? <a href="register.php">Cadastre-se aqui</a>.
        </p>
    </div>
</body>
</html>
