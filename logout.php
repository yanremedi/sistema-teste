<?php
require 'includes/auth.php';

session_unset();
session_destroy();

header("Location: login.php");
exit;
