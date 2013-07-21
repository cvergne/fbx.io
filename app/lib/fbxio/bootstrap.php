<?php
    require('../app/lib/fbxio/functions.php');

    $req = $app->request();
    define('FULL_URI', $req->getUrl() . $req->getRootUri() . '/');

    define('CONFIG_FILENAME', md5(FULL_URI));
    define('CONFIG_FILEPATH', '../app/configs/' . CONFIG_FILENAME . '.ini');

    if (file_exists(CONFIG_FILEPATH)) {
        $_config = parse_ini_file(CONFIG_FILEPATH);
        foreach ($_config as $key => $val) {
            if (!empty($val)) {
                if (preg_match('/password/', $key)) {
                    $val = base64_decode($val);
                }
                define(strtoupper($key), $val);
            }
        }
    }
    else {
        define('INSTALL_MODE', true);
    }

    define('PUTIO_CALLBACK_URI', FULL_URI . 'putio_callback');