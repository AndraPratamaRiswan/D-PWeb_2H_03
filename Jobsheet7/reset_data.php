<?php
session_start();

// opsional 4
$_SESSION = [];
session_destroy();

header('Location: index.php');
exit;
