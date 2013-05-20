<?php
$error = true;
$_result_html = '';
$_result_fileslist = array();
$new_filename = time();
if (isset($_POST['nicename']) && !empty($_POST['nicename'])) {
    // $new_filename = getEpisodeFilename($_POST['show_title'], $_POST['show_season'], $_POST['show_episode']);
    $new_filename = pathinfo($_POST['nicename']);
    $new_filename = $new_filename['filename'];
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
        $zip_folder = md5($_POST['url']);
        $zip_target = APP_DL_FOLDER . $zip_folder . '/';
        $zip_folder_uri = APP_DL_FOLDER_URI . $zip_folder . '/';
        $unziped = unzip(APP_DL_FOLDER.$filename, $zip_target);
        if ($unziped) {
            unlink(APP_DL_FOLDER.$filename);
            if (isset($new_filename) && $dossier = opendir($zip_target)) {
                while(false !== ($fichier = readdir($dossier))) {
                    if($fichier != '.' && $fichier != '..' && $fichier != 'index.php') {
                        $fileinfo = pathinfo($fichier);
                        $infosplus = array(
                            'completepath' => $zip_folder_uri.$fileinfo['basename'],
                            'newfilename' => $new_filename . '.' . $fileinfo['extension']
                        );
                        $_result_fileslist[] = array_merge($fileinfo, $infosplus);
                        $_result_html .= '<li><a href="' . $infosplus['completepath'] . '" data-origin_filename="' . $fileinfo['basename'] . '" data-nice_filename="' . $infosplus['newfilename'] . '">' . $fileinfo['basename'] . '</a></li>' . "\r\n";
                    }
                }
            }
        }
    }
}
if (!empty($_result_html) && count($_result_fileslist) > 0) {
    $error = false;
    $_result_html = '<li class="pull-right"><a href="#" class="backfromfolder"><i class="glyphicon glyphicon-chevron-up"></i> Retour</a></li><li class="nav-header"><i class="glyphicon glyphicon-comment"></i>' . $fileinfos['basename'] . '</li>' . $_result_html;
}
echo json_encode(array('root' => array(
    'error' => $error,
    'html' => $_result_html,
    'fileslist' => $_result_fileslist
)));