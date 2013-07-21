<?php
    // Autoloader
    require_once('../app/vendor/autoload.php');

    // TWIG setup
    $twigView = new \Slim\Extras\Views\Twig();
    $twigView::$twigExtensions = array(
        'Twig_Extensions_Slim'
    );

    // APP init
    $app = new \Slim\Slim(array(
        'view' => $twigView,
        'templates.path' => './templates',
        'log.writer' => new \Slim\Extras\Log\DateTimeFileWriter(array(
            'path' => '../logs',
            'name_format' => 'Y-m-d',
            'message_format' => '%label% - %date% - %message%'
        ))
    ));

    // BOOTSTRAP (config setter)
    require_once('../app/lib/fbxio/bootstrap.php');

    // ROUTES
    require_once('../app/routes/root.php');

    // APP run
    $app->run();
?>