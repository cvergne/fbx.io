<?php
    $app->get('/api/:category/:command/', function($category, $command) use ($app) {
        if ($app->request()->isAjax()) {
            $output_format = 'html';
        }
        else {
            $output_format = 'json';
        }

        require '../app/routes/api/' . $type . '.php';

        $app->render('api/' . $output_format . '/.twig');
    });
