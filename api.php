<?php
    $file = './api/'.filter_var($_GET['bridge'], FILTER_SANITIZE_EMAIL).'.php';
    if (file_exists($file)) {
        require_once('./includes/bootstrap.php');
        require_once($file);
    }
?>