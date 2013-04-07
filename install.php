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
    <title>Freebox.io</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
                        <h2>Freebox</h2>

                        <!-- #freebox:IP -->
                        <div class="control-group">
                            <label class="control-label" for="form_freebox_ip">
                                Adresse IP
                            </label>
                            <div class="controls">
                                <input id="form_freebox_ip" type="text" name="freebox[ip]" value="" placeholder="Adresse IP distante de votre Freebox" />
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
                        <div class="control-group">
                            <label class="control-label" for="form_freebox_user">
                                Utilisateur
                            </label>
                            <div class="controls">
                                <input id="form_freebox_user" type="text" name="freebox[user]" value="freebox" />
                                <p class="help-block">
                                    <small class="text-muted">Normalement inchangeable, mais au cas où</small>
                                </p>
                            </div>
                        </div>

                        <!-- freebox:PASSWORD -->
                        <div class="control-group">
                            <label class="control-label" for="form_freebox_password">
                                Mot de passe
                            </label>
                            <div class="controls">
                                <input id="form_freebox_password" type="password" name="freebox[password]" value="" />
                                <p class="help-block">
                                    <small class="text-muted">Celui que vous avez défini pour accéder à la console <a href="http://mafreebox.freebox.fr/" target="_blank">http://mafreebox.freebox.fr</a></small>
                                </p>
                            </div>
                        </div>

                        <hr />

                        <h2>Put.io</h2>

                        <!-- putio:USER -->
                        <div class="control-group">
                            <label class="control-label" for="form_putio_user">
                                Utilisateur
                            </label>
                            <div class="controls">
                                <input id="form_putio_user" type="text" name="putio[user]" value="" />
                            </div>
                        </div>

                        <!-- putio:PASSWORD -->
                        <div class="control-group">
                            <label class="control-label" for="form_putio_password">
                                Mot de passe
                            </label>
                            <div class="controls">
                                <input id="form_putio_password" type="password" name="putio[password]" value="" />
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
                        <div class="control-group">
                            <label class="control-label" for="form_putio_clientid">
                                Client ID
                            </label>
                            <div class="controls">
                                <input id="form_putio_clientid" type="text" name="putio[appclientid]" value="" />
                            </div>
                        </div>

                        <!-- putio:APP_SECRET -->
                        <div class="control-group">
                            <label class="control-label" for="form_putio_appsecret">
                                Application Secret
                            </label>
                            <div class="controls">
                                <input id="form_putio_appsecret" type="text" name="putio[appsecret]" value="" />
                            </div>
                        </div>

                        <!-- putio:OAUTH_TOKEN -->
                        <div class="control-group">
                            <label class="control-label" for="form_putio_oauthtoken">
                                Oauth Token
                            </label>
                            <div class="controls">
                                <input id="form_putio_oauthtoken" type="text" name="putio[oauthtoken]" value="" placeholder="Facultatif" />
                                <p class="help-block">
                                    <small class="text-muted">Vous évite de devoir vous reconnecter à chaque fois.</small>
                                </p>
                            </div>
                        </div>

                        <hr />

                        <h2>Betaseries</h2>
                        <p class="intro">Ces champs sont à remplir uniquement si vous souhaitez utiliser la recherche de sous-titres et le nommage automatique des vidéos.</p>

                        <!-- betaseries:APIKEY -->
                        <div class="control-group">
                            <div class="controls">
                                <p class="help-block"><small>Avant de remplir ces champs, vous devez <a href="http://www.betaseries.com/api" target="_blank">demander une clé API</a> sur <strong>Betaseries</strong>.</small></p>
                            </div>
                            <label class="control-label" for="form_betaseries_apikey">
                                API Key
                            </label>
                            <div class="controls">
                                <input id="form_betaseries_apikey" type="text" name="betaseries[apikey]" value="" placeholder="facultatif" />
                            </div>
                        </div>

                        <!-- betaseries:USER -->
                        <div class="control-group">
                            <div class="controls">
                                <hr/>
                                <p class="help-block"><small class="text-muted">Les fonctions liées à votre compte ne sont pour le moment pas actives, ces informations sont donc pour le moment facultatives mais peuvent servir dans le futur</small></p>
                            </div>
                            <label class="control-label" for="form_betaseries_user">
                                Utilisateur
                            </label>
                            <div class="controls">
                                <input id="form_betaseries_user" type="text" name="betaseries[user]" value="" placeholder="facultatif" />
                            </div>
                        </div>

                        <!-- betaseries:PASSWORD -->
                        <div class="control-group">
                            <label class="control-label" for="form_betaseries_password">
                                Mot de passe
                            </label>
                            <div class="controls">
                                <input id="form_betaseries_password" type="password" name="betaseries[password]" value="" placeholder="facultatif" />
                            </div>
                        </div>

                        <hr />

                        <h2>Paramètres</h2>

                        <div class="control-group">
                            <label class="control-label">Sous-titres</label>
                            <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" value="1" name="settings[subtitles_autosearch]" />
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