<?php
require 'includes/auth.php';

header("Location: " . (estaLogado() ? "dashboard.php" : "login.php"));
exit;
