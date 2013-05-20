<?php
    if (isset($_POST['rm'], $_POST['path'])) {
        $rm_path = $_POST['path'];
        try {
            $rm = $fbx->fs->remove($rm_path);
        } catch (Exception $e) {
            echo '<div class="alert alert-error">' . $e->getMessage() . '</div>';
            $rm = false;
        }
    }
    $dl_folder = $fbx->download->config_get();
    $dl_folder = utf8_decode($dl_folder['download_dir']);
    $files = array();

    try {
        $fb_disks = $fbx->fs->_list($dl_folder, array('with_attr' => true));
        foreach ($fb_disks as $file) {
            if (substr($file['name'], 0, 1) != '.') {
                $size = convertFileSize($file['size'], false);
                $files[utf8_decode($file['name'])] = '<tr>
                        <td class="table-fs-filename"><code>' . utf8_decode($file['name']) . '</code></td>
                        <td class="table-fs-size">' . $size['size'] . '&nbsp;' . ucfirst($size['unit']) . '</td>
                        <td class="table-fs-remove"><a data-path="' . $dl_folder . '/' . utf8_decode($file['name']) . '">&times;</a></td>
                    </tr>';
            }
        }
        ksort($files);
    } catch(Exception $e) {
        $fb_disks = false;
    }
?>
<table class="table table-fs">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Poids</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
            if ($fb_disks) {
                    $nb_files = 0;
                    foreach ($files as $file) {
                        echo $file;
                        $nb_files++;
                    }
                    if ($nb_files == 0) {
                        echo '<tr><td colspan="4" class="text-center text-muted"><em>Aucun fichier dans le dossier</em></td></tr>';
                    }
            }
            else {
                echo '<tr><td colspan="4" class="alert alert-error"><strong>Une erreur est survenue</strong></td></tr>';
            }
        ?>
    </tbody>
</table>