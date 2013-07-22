<?php
    require_once('./includes/bootstrap.php');
    $ping = new Ping('api.put.io', 5);
    $putio_latency = $ping->ping('fsockopen');
    if (!isset($_SESSION['oauth_code'], $_SESSION['putio_oauth_access_token'])) {
        if ($putio_latency) {
            header('Location:https://api.put.io/v2/oauth2/authenticate?client_id=' . PUTIO_APPCLIENTID . '&response_type=code&redirect_uri=' . PUTIO_APP_CALLBACKURL_ENC);
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
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/app.js"></script>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <ul class="nav pull-right">
                <li>
                    <a href="./install.php?restart" class="hasTooltip" data-placement="bottom" title="Recommencer l'installation"><i class="glyphicon glyphicon-repeat"></i></a>
                </li>
                <li>
                    <a class="settings hasTooltip" title="Options" data-placement="bottom" href="#modal_settings" role="button" data-toggle="modal"><i class="glyphicon glyphicon-cog"></i></a>
                </li>
            </ul>
            <a class="navbar-brand"><img src="./assets/img/icon.png" width="16" height="16" /> fbx.io</a>
        </div>
        <div class="row">
            <div class="col-span-6">
                <div class="page-header">
                    <h2><i class="titleico glyphicon glyphicon-tasks"></i> Téléchargements <small>— Freebox</small> <a id="remove_all_downloads" href="#" class="remove finished pull-right"><i class="glyphicon glyphicon-ok"></i></a></h2>
                </div>
                <div class="page-content">
                    <div id="downloads">
                        <p class="text-muted text-center"><small><em>Chargement en cours</em></small></p>
                        <!-- Current downloads goes here -->
                        <!-- <div class="dl">
                            <p><span class="label">Glee.mkv</span></p>
                            <div class="progress progress-striped active">
                                <div class="progress-bar" style="width: 40%">40%</div>
                            </div>
                        </div> -->
                    </div>
                    <div class="alert alert-info" id="log" style="display:none"></div>
                    <hr />
                    <form id="form_freebox_adddownload" class="form-horizontal">
                        <input type="hidden" name="real_url" />
                        <fieldset>
                            <div class="accordion" id="addDownloadSection">
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle text-danger" data-toggle="collapse" data-parent="#addDownloadSection" href="#addDownloadForm"><i class="glyphicon glyphicon-plus"></i> Ajouter un téléchargement</a>
                                    </div>
                                    <div id="addDownloadForm" class="accordion-body collapse">
                                        <div class="control-group">
                                            <label class="control-label">
                                                URL
                                            </label>
                                            <div class="controls">
                                                <input type="text" name="url" placeholder="Url de téléchargement" autocorrect="off" autocapitalize="off" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">
                                                Nom du fichier
                                            </label>
                                            <div class="controls">
                                                <input type="text" name="file" placeholder="exemple: Movie.mkv" autocorrect="off" autocapitalize="off" />
                                                <p class="help-block guess<?php echo (defined('SETTINGS_FILENAME_GUESSOPTION')) ? '' : ' hidden'; ?>">
                                                    <a href="#" class="api_guess_filename"><small>Deviner le nom de fichier</small></a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button type="reset" class="btn">
                                                Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary pull-right">
                                                Envoyer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="page-header">
                    <h2><i class="titleico glyphicon glyphicon-file"></i> Fichiers <small>— Put.io</small></h2>
                </div>
                <div class="putio well">
                    <?php
                        $settings_putio_hidespace = _settingBool('SETTINGS_PUTIO_HIDESPACE', 'off', 'on', '');
                        if ($putio_latency) {
                            echo '<ul id="files_list" class="nav nav-list">';
                                $_apifolder_html = true;
                                require_once(APP_ROOT.'api/folder.php');
                            echo '</ul>';
                            echo '<div class="putio_space' . $settings_putio_hidespace['class'] . '">';
                                if ($settings_putio_hidespace['state'] !== true) {
                                    require_once(APP_ROOT.'api/putio_storage.php');
                                }
                            echo '</div>';
                        }
                        else {
                            echo '<ul id="files_list" class="nav nav-list"></ul><div class="alert alert-warning">Put.io n\'est pas accessible pour le moment.</div>';
                        }
                    ?>
                </div>

                <?php if (isset($bs)) {
                        $manual_sub_search = _settingBool('SETTINGS_SUBTITLES_AUTOSEARCH');
                    ?>
                    <div class="page-header">
                        <div class="pull-right manual_sub_search<?php echo $manual_sub_search['class']; ?>">
                            <button type="button" class="btn btn-mini btn-danger" disabled="disabled">Recherche les sous-titres</button><br />
                        </div>
                        <h2><i class="titleico glyphicon glyphicon-align-center"></i> Sous-titres <small>— Betaseries</small></h2>
                    </div>
                    <div class="well"><ul class="nav nav-list" id="subs_list"><li class="nav-header">Aucun sous-titre</li></ul><ul class="nav nav-list" id="subs_folder"></ul></div>
                <?php } ?>
            </div>
            <div class="col-span-6">
                <div class="page-header">
                    <h2><i class="titleico glyphicon glyphicon-hdd"></i> Stockage <small>— Seuls les disques branchés à la Freebox Server sont affichés</small></h2>
                </div>
                <div class="page-content">
                    <div id="disks">
                        <?php
                            if (FREEBOX_VERSION == 2) {
                                $fb_disks = $fbx->storage_diskList();
                                if ($fb_disks->success) {
                                    foreach ($fb_disks->result as $fb_disk) {
                                        $diskLabel = '';
                                        if ($fb_disk->type == 'internal') {
                                            $diskLabel = ' label-info';
                                        }
                                        foreach ($fb_disk->partitions as $fb_disk_part) {
                                                $total_hdd = $fb_disk_part->free_bytes + $fb_disk_part->used_bytes;
                                                $size_calc = round(($fb_disk_part->used_bytes / $total_hdd) * 100, 2);
                                                $free_hdd = round(100 - round($size_calc));
                                                $total_hdd_display = convertFileSize($total_hdd, 'go');
                                                $used_hdd_display = convertFileSize($fb_disk_part->used_bytes, 'go');
                                                $total_display = $used_hdd_display . ' Go <span class="opacified">/</span> ' . $total_hdd_display . ' Go';
                                                $current_display = convertFileSize($fb_disk_part->free_bytes, 'go') . ' <small>Go libres</small>';

                                                $hdd_progress_class = '';
                                                if ($free_hdd >= 30) {
                                                    $hdd_progress_class = ' progress-bar-success';
                                                }
                                                else if ($free_hdd >= 15) {
                                                    $hdd_progress_class = ' progress-bar-warning';
                                                }
                                                else if ($free_hdd >= 5) {
                                                    $hdd_progress_class = ' progress-bar-danger';
                                                }


                                            if ($used_hdd_display > 0) {
                                                echo '<div class="disk">';
                                                    echo '<p><span class="label' . $diskLabel . '">' . $fb_disk_part->label . '</span><small class="pull-right text-muted"><i class="glyphicon glyphicon-hdd"></i>  ' . $total_display . '</small></p>';
                                                    echo '<div class="progress progress-striped"><div class="progress-bar' . $hdd_progress_class . '" style="width:' . $size_calc . '%">' . $current_display . '</div></div>';
                                                echo '</div>';
                                            }
                                        }
                                        echo '<hr />';
                                    }

                                }
                            } else {
                                $fb_disks = $fbx->storage->_list();
                                foreach ($fb_disks as $fb_disk) {
                                    $diskLabel = '';
                                    if ($fb_disk['type'] == 'internal') {
                                        $diskLabel = ' label-info';
                                    }
                                    foreach ($fb_disk['partitions'] as $fb_disk_part) {
                                            $total_hdd = $fb_disk_part['free_bytes'] + $fb_disk_part['used_bytes'];
                                            $size_calc = round(($fb_disk_part['used_bytes'] / $total_hdd) * 100, 2);
                                            $free_hdd = round(100 - round($size_calc));
                                            $total_hdd_display = convertFileSize($total_hdd, 'go');
                                            $used_hdd_display = convertFileSize($fb_disk_part['used_bytes'], 'go');
                                            $total_display = $used_hdd_display . ' Go <span class="opacified">/</span> ' . $total_hdd_display . ' Go';
                                            $current_display = convertFileSize($fb_disk_part['free_bytes'], 'go') . ' <small>Go libres</small>';

                                            $hdd_progress_class = '';
                                            if ($free_hdd >= 30) {
                                                $hdd_progress_class = ' progress-bar-success';
                                            }
                                            else if ($free_hdd >= 15) {
                                                $hdd_progress_class = ' progress-bar-warning';
                                            }
                                            else if ($free_hdd >= 5) {
                                                $hdd_progress_class = ' progress-bar-danger';
                                            }


                                        if ($used_hdd_display > 0) {
                                            echo '<div class="disk">';
                                                echo '<p><span class="label' . $diskLabel . '">' . $fb_disk_part['label'] . '</span><small class="pull-right text-muted"><i class="glyphicon glyphicon-hdd"></i>  ' . $total_display . '</small></p>';
                                                echo '<div class="progress progress-striped"><div class="progress-bar' . $hdd_progress_class . '" style="width:' . $size_calc . '%">' . $current_display . '</div></div>';
                                            echo '</div>';
                                        }
                                    }
                                    echo '<hr />';
                                }
                            }
                        ?>
                    </div>
                </div>
                <?php
                    if (FREEBOX_VERSION == 2) {
                        $fbx_dl_folder = $fbx->downloads_getConfiguration();
                        $dl_folder = '';
                        if ($fbx_dl_folder->success) {
                            $dl_folder = base64_decode($fbx_dl_folder->result->download_dir);
                        }
                    } else {
                        $dl_folder = $fbx->download->config_get();
                        $dl_folder = utf8_decode($dl_folder['download_dir']);
                    }
                ?>
                <div class="page-header">
                    <h2><i class="titleico glyphicon glyphicon-download-alt"></i> Freebox NAS <small>— <?php echo $dl_folder; ?></small></h2>
                </div>
                <div id="target-freebox-fs"></div>
            </div>
        </div>
    </div>

    <!-- Settings modal -->
    <div id="modal_settings" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Options</h3>
                </div>
                <form id="form_settings" method="post" action="./api.php?bridge=settings">
                    <div class="modal-body">
                            <fieldset>
                                <!--
                                <h6>Nommage des fichiers</h6>
                                <div class="control-group">
                                    <label class="control-label">
                                        Renommer auto. les fichiers
                                    </label>
                                    <div class="controls">
                                        <label class="radio">
                                            <input type="radio" value="1" id="settings_filename_auto_1" name="settings[filename_auto]"<?php _settingCheck('SETTINGS_FILENAME_AUTO', 1); ?> />
                                            Avant le téléchargement
                                            <p class="help-block"><small class="text-muted">ne fonctionnera que sur les fichiers put.io, et ralentira l'ajout d'un téléchargement</small></p>
                                        </label>
                                        <label class="radio">
                                            <input type="radio" value="2" id="settings_filename_auto_2" name="settings[filename_auto]"<?php _settingCheck('SETTINGS_FILENAME_AUTO', 2); ?> />
                                            Au téléchargement
                                            <p class="help-block"><small class="text-muted">fonctionnera pour tous les fichiers, mais empêchera le nom personnalisé si un nom automatique est trouvé</small></p>
                                        </label>
                                        <label class="radio">
                                            <input type="radio" value="3" id="settings_filename_auto_3" name="settings[filename_auto]"<?php _settingCheck('SETTINGS_FILENAME_AUTO', 3); ?> />
                                            Au cas par cas
                                            <p class="help-block"><small class="text-muted">vous devrez cocher la case dans le formulaire de téléchargement à chaque fois que vous souhaiterez l'utiliser</small></p>
                                        </label>
                                        <label class="radio">
                                            <input type="radio" value="0" id="settings_filename_auto_0" name="settings[filename_auto]"<?php _settingCheck('SETTINGS_FILENAME_AUTO', 0, true); ?> />
                                            Désactivé
                                        </label>
                                    </div>
                                </div>
                                -->
                                <h6>Nommage des fichiers</h6>
                                <div class="control-group">
                                    <div class="controls">
                                        <label class="checkbox">
                                            <input type="checkbox" value="1" id="settings_filename_autoputio" name="settings[filename_autoputio]"<?php _settingCheck('settings_filename_autoputio', 1); ?> />
                                            Activer le renommage automatique sur les fichiers Put.io
                                        </label>
                                    </div>
                                    <div class="controls">
                                        <label class="checkbox">
                                            <input type="checkbox" value="1" id="settings_filename_guessoption" name="settings[filename_guessoption]"<?php _settingCheck('settings_filename_guessoption', 1); ?> />
                                            Activer l'option de recherche automatique de nom de fichier
                                        </label>
                                    </div>
                                </div>
                                <h6>Put.io</h6>
                                <div class="control-group">
                                    <div class="controls">
                                        <label class="checkbox">
                                            <input type="checkbox" value="1" id="settings_putio_hidespace" name="settings[putio_hidespace]"<?php echo $settings_putio_hidespace['checked']; ?> />
                                            Masquer l'espace disponible
                                        </label>
                                    </div>
                                </div>
                                <h6>Sous-titres</h6>
                                <div class="control-group">
                                    <div class="controls">
                                        <label class="checkbox">
                                            <input type="checkbox" value="1" id="settings_subtitles_autosearch" name="settings[subtitles_autosearch]"<?php echo $manual_sub_search['checked']; ?> />
                                            Activer la recherche automatique de sous-titres
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning" aria-hidden="true" name="options_save">OK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>