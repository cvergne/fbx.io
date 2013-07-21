<?php
    $app->get('/', function() use($app) {
        if (defined('INSTALL_MODE')) {
            $app->redirect('install');
        }

        $app->render('app.twig');
    });

    $app->map('/install(/:restart)', function($restart=null) use ($app, $req) {

        $postData = array(
            'freebox' => array(
                'user' => 'freebox'
            )
        );
        $errors = array();
        $errorsClass = array();

        // POST form
        if ($req->isPost() && $req->post('createconf') !== NULL) {
            $postData = $req->post();

            $preconfiguration = array();

            // Freebox
            $preconfiguration['freebox'] = array();
            $_postdata = $req->post('freebox');

                // IP
                $preconfiguration['freebox']['ip'] = trim($_postdata['ip']);
                if (empty($preconfiguration['freebox']['ip']) || !filter_var($preconfiguration['freebox']['ip'], FILTER_VALIDATE_IP)) {
                    $errors['form_freebox_ip'] = "[Freebox] Indiquez une adresse IP valide.";
                }

                // USER
                $preconfiguration['freebox']['user'] = trim($_postdata['user']);
                if (empty($preconfiguration['freebox']['user'])) {
                    $errors['form_freebox_user'] = "[Freebox] Indiquez votre nom d'utilisateur";
                }

                // PASSWORD
                $preconfiguration['freebox']['password'] = trim($_postdata['password']);
                if (empty($preconfiguration['freebox']['password'])) {
                    $errors['form_freebox_password'] = "[Freebox] Indiquez votre mot de passe";
                }
                else {
                    $preconfiguration['freebox']['password'] = base64_encode($preconfiguration['freebox']['password']);
                }

            // Put.io
            $preconfiguration['putio'] = array();
            $_postdata = $req->post('putio');

                // USER
                $preconfiguration['putio']['user'] = trim($_postdata['user']);
                if (empty($preconfiguration['putio']['user'])) {
                    $errors['form_putio_user'] = "[Put.io] Indiquez votre nom d'utilisateur";
                }

                // PASSWORD
                $preconfiguration['putio']['password'] = trim($_postdata['password']);
                if (empty($preconfiguration['putio']['password'])) {
                    $errors['form_putio_password'] = "[Put.io] Indiquez votre mot de passe";
                }
                else {
                    $preconfiguration['putio']['password'] = base64_encode($preconfiguration['putio']['password']);
                }

                // APP CLIENT ID
                $preconfiguration['putio']['appclientid'] = trim($_postdata['appclientid']);
                if (empty($preconfiguration['putio']['appclientid'])) {
                    $errors['form_putio_appclientid'] = "[Put.io] Indiquez le client ID";
                }

                // APP SECRET
                $preconfiguration['putio']['appsecret'] = trim($_postdata['appsecret']);
                if (empty($preconfiguration['putio']['appsecret'])) {
                    $errors['form_putio_appsecret'] = "[Put.io] Indiquez l'Application Secret";
                }

                // OAUTH TOKEN
                $preconfiguration['putio']['oauthtoken'] = trim($_postdata['oauthtoken']);

            // BETASERIES
            $preconfiguration['betaseries'] = array();
            $_postdata = $req->post('betaseries');

                // API KEY
                $preconfiguration['betaseries']['apikey'] = trim($_postdata['apikey']);

                // USER
                $preconfiguration['betaseries']['user'] = trim($_postdata['user']);

                // PASSWORD
                $preconfiguration['betaseries']['password'] = trim($_postdata['password']);
                if (!empty($preconfiguration['betaseries']['password'])) {
                    $preconfiguration['betaseries']['password'] = base64_encode($preconfiguration['betaseries']['password']);
                }

            // SETTINGS
            $preconfiguration['settings'] = array();
            $_postdata = $req->post('settings');

                if ($_postdata !== NULL) {
                    foreach ($_postdata as $key => $val) {
                        $preconfiguration['settings'][$key] = $val;
                        $postData['settings'][$key] = ' checked="checked"';
                    }
                }

            // ERRORS Class
            foreach($postData as $groupkey => $group) {
                $errorsClass[$groupkey] = array();
                if (is_array($group)){
                    foreach($group as $field_name => $field_value) {
                        if (isset($errors['form_' . $groupkey . '_' . $field_name])) {
                            $tmp_errorclass = ' has-error';
                        }
                        else {
                            $tmp_errorclass = '';
                        }
                        $errorsClass[$groupkey][$field_name] = $tmp_errorclass;
                    }
                }
            }

            // CREATE CONF
            if (count($errors) == 0) {
                $ini_content = '; configuration file for: ' . FULL_URI . "\r\n";
                foreach ($preconfiguration as $section => $entries) {
                    $ini_content .= "\r\n[" . $section . "]\r\n";
                    foreach ($entries as $key => $val) {
                        $ini_content .= $section.'_'.$key . " = \"" . $val . "\"\r\n" ;
                    }
                }
                if ($conf_file = fopen(CONFIG_FILEPATH, 'w+')) {
                    if (fwrite($conf_file, $ini_content)) {
                        $app->redirect('/');
                    }
                    fclose($conf_file);
                }
            }
        }

        // Render
        $app->render('install.twig', array(
            'restart' => $restart,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'putio_callback' => PUTIO_CALLBACK_URI,
            'postdata' => $postData,
            'errors' => $errors,
            'errors_class' => $errorsClass
        ));
    })->via('GET', 'POST');