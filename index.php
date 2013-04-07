<?php
    require_once('./includes/bootstrap.php');
    if (!isset($_SESSION['oauth_code'], $_SESSION['putio_oauth_access_token'])) {
        header('Location:https://api.put.io/v2/oauth2/authenticate?client_id=' . PUTIO_APPCLIENTID . '&response_type=code&redirect_uri=' . PUTIO_APP_CALLBACKURL_ENC);
      }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>fbx.io</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="./assets/css/fbx.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/app.js"></script>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a class="navbar-brand">fbx.io</a>
            <ul class="nav pull-right">
                <li>
                    <a href="./install.php" class="hasTooltip" data-placement="bottom" title="Recommencer l'installation"><i class="glyphicon glyphicon-fire"></i></a>
                </li>
                <li>
                    <a class="settings hasTooltip" title="Paramètres" data-placement="bottom" href="#modal_settings" role="button" data-toggle="modal"><i class="glyphicon glyphicon-cog"></i></a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-span-6">
                <div class="page-header">
                    <h2>Téléchargements <small>— Freebox</small></h2>
                </div>
                <div class="page-content">
                    <div id="downloads">
                        <p class="text-muted">Aucun téléchargement en cours ou terminé.</p>
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
                            <h6>Ajouter un téléchargement</h6>
                            <div class="control-group">
                                <label class="control-label">
                                    URL
                                </label>
                                <div class="controls">
                                    <input type="text" name="url" placeholder="Url de téléchargement" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">
                                    Nom du fichier
                                </label>
                                <div class="controls">
                                    <input type="text" name="file" placeholder="exemple: Movie.mkv" />
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
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="col-span-6">
                <div class="page-header">
                    <h2>Fichiers <small>— Put.io</small></h2>
                </div>
                <div class="well">
                    <ul id="files_list" class="nav nav-list">
                    <?php
                        $_apifolder_html = true;
                        require_once(APP_ROOT.'api/folder.php');
                    ?>
                    </ul>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-span-6">
                <div class="page-header">
                    <h2>Stockage <small>— Seuls les disques branchés à la Freebox Server sont affichés</small></h2>
                </div>
                <div class="page-content">
                    <div id="disks">
                        <?php
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


                                    if ($size_calc > 0) {
                                        echo '<div class="disk">';
                                            echo '<p><span class="label' . $diskLabel . '">' . $fb_disk_part['label'] . '</span><small class="pull-right text-muted"><i class="glyphicon glyphicon-hdd"></i>  ' . $total_display . '</small></p>';
                                            echo '<div class="progress"><div class="progress-bar' . $hdd_progress_class . '" style="width:' . $size_calc . '%">' . $current_display . '</div></div>';
                                        echo '</div>';
                                    }
                                }
                                echo '<hr />';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-span-6">
                <?php if (isset($bs)) {
                        $settings_subtitles_autosearchCheck = '';
                        $manual_sub_searchClass = ' on';
                        if (defined('SETTINGS_SUBTITLES_AUTOSEARCH') && SETTINGS_SUBTITLES_AUTOSEARCH == '1') {
                            $settings_subtitles_autosearchCheck = ' checked="checked"';
                            $manual_sub_searchClass = '';
                        }
                    ?>
                    <div class="manual_sub_search<?php echo $manual_sub_searchClass; ?>">
                        <i class="glyphicon glyphicon-chevron-up"></i><br />
                        <button type="button" class="btn btn-mini btn-danger" disabled="disabled">Recherche les sous-titres</button><br />
                        <i class="glyphicon glyphicon-chevron-down"></i>
                    </div>
                    <div class="page-header">
                        <h2>Sous-titres <small>— Betaseries</small></h2>
                    </div>
                    <div class="well"><ul class="nav nav-list" id="subs_list"><li class="nav-header">Aucun sous-titre</li></ul></div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Settings modal -->
    <div id="modal_settings" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Paramètres</h3>
                </div>
                <form id="form_settings" method="post" action="./api.php?bridge=settings">
                    <div class="modal-body">
                            <fieldset>
                                <h6>Sous-titres</h6>
                                <div class="control-group">
                                    <label class="checkbox">
                                        <input type="checkbox" value="1" id="settings_subtitles_autosearch" name="settings[subtitles_autosearch]"<?php echo $settings_subtitles_autosearchCheck; ?> />
                                        Activer la recherche automatique de sous-titres
                                    </label>
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