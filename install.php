<?php
    define('INSTALL_MODE', true);
    require_once('./includes/bootstrap.php');

    if (isset($_POST['createconf'])) {
        if (isset($_POST['freebox'], $_POST['putio'], $_POST['betaseries'])) {
            $preconfiguration = array();
            $errors = array();
            // Freebox
            $preconfiguration['freebox'] = array();

                // IP
                $preconfiguration['freebox']['ip'] = trim($_POST['freebox']['ip']);
                if (empty($preconfiguration['freebox']['ip']) || !filter_var($preconfiguration['freebox']['ip'], FILTER_VALIDATE_IP)) {
                    $errors['form_freebox_ip'] = "Indiquez une adresse IP valide.";
                }

                // USER
                $preconfiguration['freebox']['user'] = trim($_POST['freebox']['user']);
                if (empty($preconfiguration['freebox']['user'])) {
                    $errors['form_freebox_user'] = "Indiquez votre nom d'utilisateur";
                }

                // PASSWORD
                $preconfiguration['freebox']['password'] = trim($_POST['freebox']['password']);
                if (empty($preconfiguration['freebox']['password'])) {
                    $errors['form_freebox_password'] = "Indiquez votre mot de passe";
                }
                else {
                    $preconfiguration['freebox']['password'] = base64_encode($preconfiguration['freebox']['password']);
                }

            // Put.io
            $preconfiguration['putio'] = array();

                // USER
                $preconfiguration['putio']['user'] = trim($_POST['putio']['user']);
                if (empty($preconfiguration['putio']['user'])) {
                    $errors['form_putio_user'] = "Indiquez votre nom d'utilisateur";
                }

                // PASSWORD
                $preconfiguration['putio']['password'] = trim($_POST['putio']['password']);
                if (empty($preconfiguration['putio']['password'])) {
                    $errors['form_putio_password'] = "Indiquez votre mot de passe";
                }
                else {
                    $preconfiguration['putio']['password'] = base64_encode($preconfiguration['putio']['password']);
                }

                // APP CLIENT ID
                $preconfiguration['putio']['appclientid'] = trim($_POST['putio']['appclientid']);
                if (empty($preconfiguration['putio']['appclientid'])) {
                    $errors['form_putio_clientid'] = "Indiquez le client ID";
                }

                // APP SECRET
                $preconfiguration['putio']['appsecret'] = trim($_POST['putio']['appsecret']);
                if (empty($preconfiguration['putio']['appsecret'])) {
                    $errors['form_putio_appsecret'] = "Indiquez l'Application Secret";
                }

                // OAUTH TOKEN
                $preconfiguration['putio']['oauthtoken'] = trim($_POST['putio']['oauthtoken']);

            // BETASERIES
            $preconfiguration['betaseries'] = array();

                // API KEY
                $preconfiguration['betaseries']['apikey'] = trim($_POST['betaseries']['apikey']);

                // USER
                $preconfiguration['betaseries']['user'] = trim($_POST['betaseries']['user']);

                // PASSWORD
                $preconfiguration['betaseries']['password'] = trim($_POST['betaseries']['password']);
                if (!empty($preconfiguration['betaseries']['password'])) {
                    $preconfiguration['betaseries']['password'] = base64_encode($preconfiguration['betaseries']['password']);
                }

            // SETTINGS
            $preconfiguration['settings'] = array();
                if (isset($_POST['settings'])) {
                    foreach ($_POST['settings'] as $key => $val) {
                        $preconfiguration['settings'][$key] = $val;
                    }
                }


            // CREATE CONF
            if (count($errors) == 0) {
                $ini_content = '; configuration file for: ' . $root_uri . "\r\n";
                foreach ($preconfiguration as $section => $entries) {
                    $ini_content .= "\r\n[" . $section . "]\r\n";
                    foreach ($entries as $key => $val) {
                        $ini_content .= $section.'_'.$key . " = \"" . $val . "\"\r\n" ;
                    }
                }
                if ($conf_file = fopen(CONFIG_FILE_PATH, 'w+')) {
                    if (fwrite($conf_file, $ini_content)) {
                        header('Location: ' . $root_uri);
                    }
                    fclose($conf_file);
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>fbx.io</title>
    <link rel="icon" type="image/png" href="./assets/img/favicon.png" />

    <!-- Mobile part -->
    <meta name="viewport" content="initial-scale=1.0" />

    <!-- iOS Part -->
    <meta name="apple-mobile-web-app-title" content="fbx.io" />
    <link rel="apple-touch-icon" href="./assets/img/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link href="./assets/img/apple-touch-start-640x1096.png" media="(device-height: 568px)" rel="apple-touch-startup-image" />
    <link href="./assets/img/apple-touch-start-640x920.png" sizes="640x920" media="(device-height: 480px)" rel="apple-touch-startup-image" />

    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="./assets/css/fbx.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body class="setup">
    <div class="container">
        <div class="row">
            <div class="col-span-6 col-offset-3">
                <form method="post" action="./install.php" class="form-horizontal">
                    <fieldset>
                        <legend><h1>Configuration initiale</h1></legend>

                        <?php
                            if (isset($errors) && count($errors) > 0) {
                                echo '<div class="alert alert-error alert-noclose"><ul>';
                                foreach ($errors as $error_id => $error_text) {
                                    echo '<li><a href="#' . $error_id . '">' . $error_text . '</a></li>';
                                }
                                echo '</ul></div>';
                            }
                        ?>
                        <h2>Freebox</h2>

                        <!-- #freebox:IP -->
                        <div class="control-group<?php _setupIsInError('form_freebox_ip'); ?>">
                            <label class="control-label" for="form_freebox_ip">
                                Adresse IP
                            </label>
                            <div class="controls">
                                <input id="form_freebox_ip" class="input-with-feedback" type="text" name="freebox[ip]" value="<?php echo (isset($_POST['freebox']['ip'])) ? $_POST['freebox']['ip'] : ''; ?>" placeholder="Adresse IP distante de votre Freebox" autocorrect="off" autocapitalize="off" />
                                <?php if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') { ?>
                                <p class="help-block">
                                    <small><i class="glyphicon glyphicon-question-sign"></i> Cliquez sur <a class="remote_addr"><?php echo $_SERVER['REMOTE_ADDR']; ?></a> si vous êtes actuellement sur votre Freebox.</small>
                                </p>
                                <script type="text/javascript">
                                    $('.remote_addr').on('click', function(ev) {  ev.preventDefault(); $('#form_freebox_ip').val(this.innerText); })
                                </script>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- freebox:USER -->
                        <div class="control-group<?php _setupIsInError('form_freebox_user'); ?>">
                            <label class="control-label" for="form_freebox_user">
                                Utilisateur
                            </label>
                            <div class="controls">
                                <input id="form_freebox_user" class="input-with-feedback" type="text" name="freebox[user]" value="<?php echo (isset($_POST['freebox']['user'])) ? $_POST['freebox']['user'] : 'freebox'; ?>" autocapitalize="off" />
                                <p class="help-block">
                                    <small class="text-muted">Normalement inchangeable, mais au cas où</small>
                                </p>
                            </div>
                        </div>

                        <!-- freebox:PASSWORD -->
                        <div class="control-group<?php _setupIsInError('form_freebox_password'); ?>">
                            <label class="control-label" for="form_freebox_password">
                                Mot de passe
                            </label>
                            <div class="controls">
                                <input id="form_freebox_password" class="input-with-feedback" type="password" name="freebox[password]" value="<?php echo (isset($_POST['freebox']['password'])) ? $_POST['freebox']['password'] : ''; ?>" autocorrect="off" autocapitalize="off" />
                                <p class="help-block">
                                    <small class="text-muted">Celui que vous avez défini pour accéder à la console <a href="http://mafreebox.freebox.fr/" target="_blank">http://mafreebox.freebox.fr</a></small>
                                </p>
                            </div>
                        </div>

                        <hr />

                        <h2>Put.io</h2>

                        <!-- putio:USER -->
                        <div class="control-group<?php _setupIsInError('form_putio_user'); ?>">
                            <label class="control-label" for="form_putio_user">
                                Utilisateur
                            </label>
                            <div class="controls">
                                <input id="form_putio_user" class="input-with-feedback" type="text" name="putio[user]" value="<?php echo (isset($_POST['freebox']['ip'])) ? $_POST['putio']['user'] : ''; ?>" autocapitalize="off" />
                            </div>
                        </div>

                        <!-- putio:PASSWORD -->
                        <div class="control-group<?php _setupIsInError('form_putio_password'); ?>">
                            <label class="control-label" for="form_putio_password">
                                Mot de passe
                            </label>
                            <div class="controls">
                                <input id="form_putio_password" class="input-with-feedback" type="password" name="putio[password]" value="<?php echo (isset($_POST['putio']['password'])) ? $_POST['putio']['password'] : ''; ?>" />
                                <p class="help-block">
                                    <small class="text-muted">Le nom d'utilisateur et le mot de passe sont utilisés pour que la Freebox puisse accéder aux téléchargements, celle-ci ne gérant pas pour le moment les téléchargements en https.</small>
                                </p>
                            </div>
                        </div>

                        <!-- putio:CALLBACK -->
                        <div class="control-group">
                            <div class="controls">
                                <hr />
                                <p class="help-block"><small>Avant de remplir ces champs, vous devez <a href="https://put.io/v2/oauth2/register" target="_blank">créer un accès API</a> sur <strong>Put.io</strong>.<br />Vous devrez lui fournir l'url de Callback suivante :</small></p>
                                <input id="form_putio_callback" type="text" readonly="readonly" value="<?php echo $callbackuri; ?>" />
                                <p class="help-block help-exclam"><small><i class="glyphicon glyphicon-exclamation-sign"></i> Attention, toute nouvelle configuration nécessite un nouvel accès API</small></p>
                            </div>
                        </div>

                        <!-- putio:CLIENT_ID -->
                        <div class="control-group<?php _setupIsInError('form_putio_clientid'); ?>">
                            <label class="control-label" for="form_putio_clientid">
                                Client ID
                            </label>
                            <div class="controls">
                                <input id="form_putio_clientid" class="input-with-feedback" type="text" name="putio[appclientid]" value="<?php echo (isset($_POST['putio']['appclientid'])) ? $_POST['putio']['appclientid'] : ''; ?>" autocorrect="off" autocapitalize="off" />
                            </div>
                        </div>

                        <!-- putio:APP_SECRET -->
                        <div class="control-group<?php _setupIsInError('form_putio_appsecret'); ?>">
                            <label class="control-label" for="form_putio_appsecret">
                                Application Secret
                            </label>
                            <div class="controls">
                                <input id="form_putio_appsecret" class="input-with-feedback" type="text" name="putio[appsecret]" value="<?php echo (isset($_POST['putio']['appsecret'])) ? $_POST['putio']['appsecret'] : ''; ?>" autocorrect="off" autocapitalize="off" />
                            </div>
                        </div>

                        <!-- putio:OAUTH_TOKEN -->
                        <div class="control-group<?php _setupIsInError('form_putio_oauthtoken'); ?>">
                            <label class="control-label" for="form_putio_oauthtoken">
                                Oauth Token
                            </label>
                            <div class="controls">
                                <input id="form_putio_oauthtoken" class="input-with-feedback" type="text" name="putio[oauthtoken]" value="<?php echo (isset($_POST['putio']['oauthtoken'])) ? $_POST['putio']['oauthtoken'] : ''; ?>" placeholder="Facultatif" autocorrect="off" autocapitalize="off" />
                                <p class="help-block">
                                    <small class="text-muted">Vous évite de devoir vous reconnecter à chaque fois.</small>
                                </p>
                            </div>
                        </div>

                        <hr />

                        <h2>Betaseries</h2>
                        <p class="intro">Ces champs sont à remplir uniquement si vous souhaitez utiliser la recherche de sous-titres et le nommage automatique des vidéos.</p>

                        <!-- betaseries:APIKEY -->
                        <div class="control-group<?php _setupIsInError('form_betaseries_apikey'); ?>">
                            <div class="controls">
                                <p class="help-block"><small>Avant de remplir ces champs, vous devez <a href="http://www.betaseries.com/api" target="_blank">demander une clé API</a> sur <strong>Betaseries</strong>.</small></p>
                            </div>
                            <label class="control-label" for="form_betaseries_apikey">
                                API Key
                            </label>
                            <div class="controls">
                                <input id="form_betaseries_apikey" class="input-with-feedback" type="text" name="betaseries[apikey]" value="<?php echo (isset($_POST['betaseries']['apikey'])) ? $_POST['betaseries']['apikey'] : ''; ?>" placeholder="facultatif" autocorrect="off" autocapitalize="off" />
                            </div>
                        </div>

                        <!-- betaseries:USER -->
                        <div class="control-group<?php _setupIsInError('form_betaseries_user'); ?>">
                            <div class="controls">
                                <hr/>
                                <p class="help-block"><small class="text-muted">Les fonctions liées à votre compte ne sont pour le moment pas actives, ces informations sont donc pour le moment facultatives mais peuvent servir dans le futur</small></p>
                            </div>
                            <label class="control-label" for="form_betaseries_user">
                                Utilisateur
                            </label>
                            <div class="controls">
                                <input id="form_betaseries_user" class="input-with-feedback" type="text" name="betaseries[user]" value="<?php echo (isset($_POST['betaseries']['user'])) ? $_POST['betaseries']['user'] : ''; ?>" placeholder="facultatif" autocapitalize="off" />
                            </div>
                        </div>

                        <!-- betaseries:PASSWORD -->
                        <div class="control-group<?php _setupIsInError('form_betaseries_password'); ?>">
                            <label class="control-label" for="form_betaseries_password">
                                Mot de passe
                            </label>
                            <div class="controls">
                                <input id="form_betaseries_password" class="input-with-feedback" type="password" name="betaseries[password]" value="<?php echo (isset($_POST['betaseries']['password'])) ? $_POST['betaseries']['password'] : ''; ?>" placeholder="facultatif" autocorrect="off" autocapitalize="off" />
                            </div>
                        </div>

                        <hr />

                        <h2>Options</h2>

                        <div class="control-group">
                            <label class="control-label">Put.io</label>
                            <div class="controls">
                                <label class="checkbox">
                                    <input type="checkbox" value="1" name="settings[putio_hidespace]"<?php echo (isset($_POST['settings']['putio_hidespace'])) ? ' checked="checked"' : ''; ?> />
                                    Désactiver l'affichage de l'espace utilisé/restant sur Put.io
                                </label>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">Sous-titres</label>
                            <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" value="1" name="settings[subtitles_autosearch]"<?php echo (isset($_POST['settings']['subtitles_autosearch'])) ? ' checked="checked"' : ''; ?> />
                                Activer la recherche automatique de sous-titres
                                <p class="help-block">
                                    <small class="text-muted">Vous devez remplir les paramètres concernant Betaseries pour utiliser cette option</small>
                                </p>
                            </label>
                        </div>

                        <hr />

                        <div class="form-actions">
                            <button type="submit" name="createconf" class="btn btn-primary">
                                Créer la configuration
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</body>
</html>