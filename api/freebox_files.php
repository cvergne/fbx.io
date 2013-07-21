<?php
    if (FREEBOX_VERSION == 2) {
        $dl_folder = $fbx->downloads_getConfiguration();
        if ($dl_folder->success) {
            $dl_folder = $dl_folder->result->download_dir;
            $raw_files = array();
            $final_files = array();

            $fb_disks = $fbx->fs_listFiles($dl_folder);
            if ($fb_disks->success) {
                foreach ($fb_disks->result as $file) {
                    if (substr($file['name'], 0, 1) != '.') {
                        $original_filename = utf8_decode($file['name']);
                        $fileinfos = pathinfo($original_filename);
                        if (!isset($raw_files[$fileinfos['filename']])) {
                            $raw_files[$fileinfos['filename']] = array();
                        }
                        if (!in_array($fileinfos['extension'], $_config_freebox_subtitles_extensions)) {
                            $raw_files[$fileinfos['filename']]['file'] = $file;
                        }
                        else if(!isset($raw_files[$fileinfos['filename']]['sub']) && in_array($fileinfos['extension'], $_config_freebox_subtitles_extensions)) {
                            $raw_files[$fileinfos['filename']]['sub'] = $file;
                        }
                    }
                }
                ksort($raw_files);
                foreach ($raw_files as $file) {
                    $sub_state_class = '';
                    $file_state_class = '';
                    if (isset($file['file'], $file['sub'])) {
                        $size = convertFileSize($file['file']['size'], false);
                        if ($size['size'] == 0) {
                            $file_state_class = ' class="unfinished"';
                        }
                        $subsize = convertFileSize($file['sub']['size'], false);
                        if ($subsize['size'] == 0) {
                            $sub_state_class = ' unfinished';
                        }
                        $final_files[utf8_decode($file['file']['name'])] = '<tr>
                                <td class="table-fs-filename"><code' . $file_state_class . '>' . utf8_decode($file['file']['name']) . '</code><br /><span class="text-muted">+</span>&nbsp;<code class="alt' . $sub_state_class . '">' . utf8_decode($file['sub']['name']) . '</code></td>
                                <td class="table-fs-size"><span' . $file_state_class . '>' . $size['size'] . '&nbsp;' . ucfirst($size['unit']) . '</span><br/><small class="text-muted' . $sub_state_class . '">' . $subsize['size'] . '&nbsp;' . ucfirst($subsize['unit']) . '</small></td>
                                <td class="table-fs-remove"><a data-path="' . $dl_folder . '/' . utf8_decode($file['file']['name']) . '"><i class="glyphicon glyphicon-trash"></i></a></td>
                            </tr>';
                    }
                    else if (isset($file['sub'])) {
                        $size = convertFileSize($file['sub']['size'], false);
                        if ($size['size'] == 0) {
                            $sub_state_class = ' unfinished';
                        }
                        $final_files[utf8_decode($file['sub']['name'])] = '<tr>
                                <td class="table-fs-filename"><code class="alt' . $sub_state_class . '">' . utf8_decode($file['sub']['name']) . '</code></td>
                                <td class="table-fs-size"><small class="text-muted' . $sub_state_class . '">' . $size['size'] . '&nbsp;' . ucfirst($size['unit']) . '</small></td>
                                <td class="table-fs-remove"><a data-path="' . $dl_folder . '/' . utf8_decode($file['sub']['name']) . '"><i class="glyphicon glyphicon-trash"></i></a></td>
                            </tr>';
                    }
                    else if (isset($file['file'])) {
                        $size = convertFileSize($file['file']['size'], false);
                        if ($size['size'] == 0) {
                            $file_state_class = ' class="unfinished"';
                        }
                        $final_files[utf8_decode($file['file']['name'])] = '<tr>
                                <td class="table-fs-filename"><code' . $file_state_class . '>' . utf8_decode($file['file']['name']) . '</code></td>
                                <td class="table-fs-size"><span' . $file_state_class . '>' . $size['size'] . '&nbsp;' . ucfirst($size['unit']) . '</span></td>
                                <td class="table-fs-remove"><a data-path="' . $dl_folder . '/' . utf8_decode($file['file']['name']) . '"><i class="glyphicon glyphicon-trash"></i></a></td>
                            </tr>';
                    }
                }
            }
        }
    } else {
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
        $raw_files = array();
        $final_files = array();

        try {
            $fb_disks = $fbx->fs->_list($dl_folder, array('with_attr' => true));
            foreach ($fb_disks as $file) {
                if (substr($file['name'], 0, 1) != '.') {
                    $original_filename = utf8_decode($file['name']);
                    $fileinfos = pathinfo($original_filename);
                    if (!isset($raw_files[$fileinfos['filename']])) {
                        $raw_files[$fileinfos['filename']] = array();
                    }
                    if (!in_array($fileinfos['extension'], $_config_freebox_subtitles_extensions)) {
                        $raw_files[$fileinfos['filename']]['file'] = $file;
                    }
                    else if(!isset($raw_files[$fileinfos['filename']]['sub']) && in_array($fileinfos['extension'], $_config_freebox_subtitles_extensions)) {
                        $raw_files[$fileinfos['filename']]['sub'] = $file;
                    }
                }
            }
            ksort($raw_files);
            foreach ($raw_files as $file) {
                $sub_state_class = '';
                $file_state_class = '';
                if (isset($file['file'], $file['sub'])) {
                    $size = convertFileSize($file['file']['size'], false);
                    if ($size['size'] == 0) {
                        $file_state_class = ' class="unfinished"';
                    }
                    $subsize = convertFileSize($file['sub']['size'], false);
                    if ($subsize['size'] == 0) {
                        $sub_state_class = ' unfinished';
                    }
                    $final_files[utf8_decode($file['file']['name'])] = '<tr>
                            <td class="table-fs-filename"><code' . $file_state_class . '>' . utf8_decode($file['file']['name']) . '</code><br /><span class="text-muted">+</span>&nbsp;<code class="alt' . $sub_state_class . '">' . utf8_decode($file['sub']['name']) . '</code></td>
                            <td class="table-fs-size"><span' . $file_state_class . '>' . $size['size'] . '&nbsp;' . ucfirst($size['unit']) . '</span><br/><small class="text-muted' . $sub_state_class . '">' . $subsize['size'] . '&nbsp;' . ucfirst($subsize['unit']) . '</small></td>
                            <td class="table-fs-remove"><a data-path="' . $dl_folder . '/' . utf8_decode($file['file']['name']) . '"><i class="glyphicon glyphicon-trash"></i></a></td>
                        </tr>';
                }
                else if (isset($file['sub'])) {
                    $size = convertFileSize($file['sub']['size'], false);
                    if ($size['size'] == 0) {
                        $sub_state_class = ' unfinished';
                    }
                    $final_files[utf8_decode($file['sub']['name'])] = '<tr>
                            <td class="table-fs-filename"><code class="alt' . $sub_state_class . '">' . utf8_decode($file['sub']['name']) . '</code></td>
                            <td class="table-fs-size"><small class="text-muted' . $sub_state_class . '">' . $size['size'] . '&nbsp;' . ucfirst($size['unit']) . '</small></td>
                            <td class="table-fs-remove"><a data-path="' . $dl_folder . '/' . utf8_decode($file['sub']['name']) . '"><i class="glyphicon glyphicon-trash"></i></a></td>
                        </tr>';
                }
                else if (isset($file['file'])) {
                    $size = convertFileSize($file['file']['size'], false);
                    if ($size['size'] == 0) {
                        $file_state_class = ' class="unfinished"';
                    }
                    $final_files[utf8_decode($file['file']['name'])] = '<tr>
                            <td class="table-fs-filename"><code' . $file_state_class . '>' . utf8_decode($file['file']['name']) . '</code></td>
                            <td class="table-fs-size"><span' . $file_state_class . '>' . $size['size'] . '&nbsp;' . ucfirst($size['unit']) . '</span></td>
                            <td class="table-fs-remove"><a data-path="' . $dl_folder . '/' . utf8_decode($file['file']['name']) . '"><i class="glyphicon glyphicon-trash"></i></a></td>
                        </tr>';
                }
            }
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
                if ($fb_disks !== false) {
                        if (count($final_files) > 0) {
                            foreach ($final_files as $file) {
                                echo $file;
                            }
                        }
                        else {
                            echo '<tr><td colspan="4" class="text-center text-muted"><em>Aucun fichier dans le dossier</em></td></tr>';
                        }
                }
                else {
                    echo '<tr><td colspan="4" class="alert alert-error"><strong>Une erreur est survenue</strong></td></tr>';
                }
            ?>
        </tbody>
    </table>
<?php
    }