<?php
$relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
header('Location: ' . $relativePath . '/public/index.php');
exit;
?>