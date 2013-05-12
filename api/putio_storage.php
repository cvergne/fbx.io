<?php
    // Put.io infos
    if (isset($_SESSION['oauth_code'], $_SESSION['putio_oauth_access_token'])) {
        $_putio = _get(PUTIO_API_URL . '/account/info?oauth_token=' . PUTIO_OAUTHTOKEN);
        $free_hdd_p = 100 / ($_putio['info']['disk']['size'] / $_putio['info']['disk']['avail']);
        $used_hdd_p = 100 - $free_hdd_p;
        $used_hdd_display = convertFileSize($_putio['info']['disk']['avail'], false);
        $total_display = $used_hdd_display['size'] . ' ' . ucfirst($used_hdd_display['unit']) . ' ' . (($free_hdd_p > 1) ? 'disponibles' : 'disponible');

        $hdd_progress_class = '';
        if ($free_hdd_p >= 30) {
            $hdd_progress_class = ' progress-bar-success';
        }
        else if ($free_hdd_p >= 15) {
            $hdd_progress_class = ' progress-bar-warning';
        }
        else if ($free_hdd_p >= 5) {
            $hdd_progress_class = ' progress-bar-danger';
        }
        if ($_putio['status'] == 'OK') {
            echo '<div class="progress progress-striped"><div class="progress-bar' . $hdd_progress_class . '" style="width: ' . $used_hdd_p . '%;">' . $total_display . '</div></div>';
        }
    }
