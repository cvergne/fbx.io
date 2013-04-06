<?php
    require_once('./includes/bootstrap.php');

    if (isset($_GET['code'])) {
        $_SESSION['oauth_code'] = $_GET['code'];

        $resp = _get('https://api.put.io/v2/oauth2/access_token?client_id=' . PUTIO_APPCLIENTID . '&client_secret=' . PUTIO_APPSECRET . '&grant_type=authorization_code&redirect_uri=' . PUTIO_APP_CALLBACKURL_ENC . '&code=' . $_GET['code']);

        if (isset($resp['access_token'])) {
            $_SESSION['putio_oauth_access_token'] = $resp['access_token'];
            header('Location: ./');
        }


    }
    else {
        echo 'error';
    }
?>