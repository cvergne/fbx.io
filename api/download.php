<?php
if (isset($_POST['show_title'], $_POST['show_season'], $_POST['show_episode'])) {
    $new_filename = getEpisodeFilename($_POST['show_title'], $_POST['show_season'], $_POST['show_episode']);
}

if (isset($_POST['url']) && !empty($_POST['url'])) {
    $ext = 'zip';
    if (isset($_POST['filename']) && !empty($_POST['filename'])) {
        $fileinfos = pathinfo($_POST['filename']);
        if (isset($fileinfos['extension'])) {
            $ext = $fileinfos['extension'];
        }
    }
    $filename = md5($_POST['url']) . '.' . $ext;
    $dl = downloadFile($_POST['url'], APP_DL_FOLDER.$filename);
    if ($ext == 'zip') {
        $zip_target = APP_DL_FOLDER.md5($_POST['url']).'/';
        $unziped = unzip(APP_DL_FOLDER.$filename, $zip_target);
        if ($unziped) {
            unlink(APP_DL_FOLDER.$filename);
            if (isset($new_filename) && $dossier = opendir($zip_target)) {
                while(false !== ($fichier = readdir($dossier))) {
                    if($fichier != '.' && $fichier != '..' && $fichier != 'index.php') {
                        $fileinfos = pathinfo($fichier);
                        if (isset($fileinfos['extension'])) {
                            $ext = $fileinfos['extension'];
                        }
                        if (rename($zip_target.$fichier, APP_DL_FOLDER.$new_filename.'.'.$ext)) {
                            rmdir($zip_target);
                        }
                    }
                }
            }
        }
    }
}