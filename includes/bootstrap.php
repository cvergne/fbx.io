<?php
    session_start();

    // CORE
    $root_uri = pathinfo('http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
    $root_uri = $root_uri['dirname'] . '/';
    $callbackuri = $root_uri . 'callbackurl.php';
    $_sf = pathinfo($_SERVER["SCRIPT_FILENAME"]);
    define('APP_ROOT', $_sf['dirname'] . '/');
    define('CONFIG_FILE_NAME', md5($root_uri));
    define('CONFIG_FILE_PATH', APP_ROOT.'includes/configs/'.CONFIG_FILE_NAME.'.ini');


    // Define CONF from .ini file
    if (!defined('INSTALL_MODE')) {
        if (!file_exists(CONFIG_FILE_PATH)) {
            header('Location: ./install.php');
        }
        else {
            $_config = parse_ini_file(CONFIG_FILE_PATH);
            foreach ($_config as $key => $val) {
                if (!empty($val)) {
                    if (preg_match('/password/', $key)) {
                        $val = base64_decode($val);
                    }
                    define(strtoupper($key), $val);
                }
            }
        }

    // CONF
    /* const définies depuis le conf .ini */

    // FREEBOX
    /* const:
        FREEBOX_IP: adresse ip de la freebox
        FREEBOX_USER: utilisateur freebox
        FREEBOX_PASSWORD: mot de passe freebox
    */

    // PUT.IO
        /* const:
            PUTIO_USER: utilisteur put.io
            PUTIO_PASSWORD: mot de passe put.io
            PUTIO_APPCLIENTID: app client id
            PUTIO_APPSECRET: app secret
            PUTIO_OAUTHTOKEN: oauth token
        */
    define('PUTIO_APP_CALLBACKURL_ENC', urlencode($callbackuri));
    define('PUTIO_API_URL', 'http://s01.put.io/v2');
    define('PUTIO_DOWNLOAD_URL', 'http://' . PUTIO_USER . ':' . PUTIO_PASSWORD . '@put.io/v2');
    if (isset($_SESSION['putio_oauth_access_token']) && !defined('PUTIO_OAUTHTOKEN')) {
        define('PUTIO_OAUTHTOKEN', $_SESSION['putio_oauth_access_token']);
    }

    // DL FOLDER
    define('APP_DL_FOLDER', APP_ROOT.'temp/dl/');

    // Required libs
    require_once(APP_ROOT . 'includes/functions.php');

    // BETASERIES
        /* const:
            BETASERIES_APIKEY: clé api
            BETASERIES_USER: utilisateur betaseries
            BETASERIES_PASSWORD: mot de passe betaseries
        */
    if (defined('BETASERIES_APIKEY')) {
        define('BETASERIES_URL', 'http://api.betaseries.com');
        $bs = new BetaSeries_Client(BETASERIES_URL, BETASERIES_APIKEY, BetaSeries_Client::JSON, BetaSeries_Client::LANGUAGE_VF);
    }
    // Freebox init
    $fbx = new Mafreebox('http://' . FREEBOX_IP . '/', FREEBOX_USER, FREEBOX_PASSWORD);
}

