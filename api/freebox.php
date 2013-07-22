<?php
if (FREEBOX_VERSION == 2) {
    if (isset($_POST['downloads'])) {
        $fbx_resp = $fbx->downloads_List();
        $fbx_resp = $fbx_resp->result;

        $totalCount = count($fbx_resp);
        $runningCount = 0;
        $finishedCount = 0;
        $result = '';
        $running = false;

        foreach ($fbx_resp as $dl) {
            if (in_array($dl->status, array('downloading', 'paused'))) {
                $running = true;
                $runningCount++;

                // Progress
                $total = $dl->size;
                $transferred = $dl->rx_bytes;
                if ($transferred > 0 && $total > 0) {
                    $current = round(($transferred / $total) * 100, 2) . '%';
                }
                else {
                    $current = 0 . '%';
                }
                if (!empty($result)) {
                    $result .= '<hr />';
                }

                // Speed
                $speed = '';
                if ($dl->rx_rate > 0) {
                    $speed = convertFileSize($dl->rx_rate, false);
                    $speed = $speed['size'] . '&nbsp;' . ucfirst($speed['unit']) . '/s';
                }

                // Progress style
                $progressBarClass = '';
                if ($dl->status == 'paused') {
                    $progressBarClass = ' progress-bar-warning';
                }

                // Write
                    // Sizes
                    $_size_transferred = convertFileSize($transferred, false);
                    $_size_total = convertFileSize($total, false);
                    $result .= '<div class="dl">
                            <p><span class="label">' . $dl->name . '</span> <a href="#" class="remove pull-right" data-type="' . $dl->type . '" data-id="' . $dl->id . '"><i class="glyphicon glyphicon-trash"></i></a> <small class="text-muted pull-right">' . $speed . '</small></p>
                            <div class="progress progress-striped active">
                                <div class="progress-bar' . $progressBarClass . '" style="width: ' . $current . '"><strong>' . $current . '</strong> <small class="opacified">( ' . $_size_transferred['size'] . ' ' . ucfirst($_size_transferred['unit']) . ' / ' . $_size_total['size'] . ' ' . ucfirst($_size_total['unit']) . ' )</small></div>
                            </div>
                        </div>';
            }
            else {
                $name = $dl->name;
                if (isset($_SESSION['fbxio_files_renamed'][$dl->id])) {
                    $name = $_SESSION['fbxio_files_renamed'][$dl->id];
                }
                $finishedCount++;
                if (!empty($result)) {
                    $result .= '<hr />';
                }
                $result .= '<div class="dl">
                            <p><span class="label label-success">' . $dl->name . '</span> <a href="#" class="remove finished pull-right" data-type="' . $dl->type . '" data-id="' . $dl->id . '"><i class="glyphicon glyphicon-ok"></i></a> <small class="label-finished pull-right">Terminé</small></p>
                        </div>';
            }
        }
        echo json_encode(array(
            'running' => $running,
            'totalResults' => $totalCount,
            'finishedCount' => $finishedCount,
            'runningCount' => $runningCount,
            'resultHTML' => $result
        ));
    }
    else if (isset($_POST['removeDownload'], $_POST['id'])) {
        try {
            $fbx_resp = $fbx->downloads_Remove($_POST['id']);
        }
        catch(Exception $e) {
            $fbx_resp = json_encode(array('error' => $e->getMessage()));
        }

        if ($fbx_resp->response && $fbx_resp->response->success) {
            unset($_SESSION['fbxio_files_renamed'][$_POST['id']]);
            echo json_encode($fbx_resp->response->result);
        }
        else {
            echo json_encode($fbx_resp);
        }
    }
    else {
        if (isset($_POST['real_url']) && !empty($_POST['real_url'])) {
            $url = $_POST['real_url'];
        }
        else if (isset($_POST['url'])) {
            $url = $_POST['url'];
        }
        $parse_url = parse_url($url);
        $pathinfo = pathinfo($parse_url['path']);
        $file = $pathinfo['basename'];

        if (isset($_POST['file']) && !empty($_POST['file'])) {
            $file = $_POST['file'];
        }
        else if (!isset($pathinfo['extension'])) {
            $file = 'file-' . date('H_i_Y_m_d') . '.fileext';
        }

        $fbx_resp = $fbx->downloads_addURL(array('download_url' => $url));
        if ($fbx_resp->success) {
            $fbx_dl_info = $fbx->downloads_Item($fbx_resp->result->id);
            if ($fbx_dl_info->success) {
                if ($db = sqlite_open(DB_FILE_PATH, 0666, $sqliteerror)) {
                    $db = sqlite_query($db,"INSERT INTO downloads (fbx_id,orig_path,new_name) VALUES ('" . sqlite_escape_string($fbx_resp->result->id) . "', '" . sqlite_escape_string(base64_encode($fbx_dl_info->result->download_dir_name . $fbx_dl_info->result->name)) . "', '" . sqlite_escape_string($file) . "')");
                }
                /*
                $_SESSION['fbxio_files_to_rename'][$fbx_dl_info->result->id] = array(
                    'dir' => $fbx_dl_info->result->download_dir,
                    'dirname' => $fbx_dl_info->result->download_dir_name,
                    'name' => $fbx_dl_info->result->name,
                    'final_name' => $file
                );
                */
            }

        }
        print_r($fbx_resp->result);
    }
} else {
    if (isset($_POST['downloads'])) {
        $fbx_resp = $fbx->download->_list();

        $totalCount = count($fbx_resp);
        $runningCount = 0;
        $finishedCount = 0;
        $result = '';
        $running = false;

        foreach ($fbx_resp as $dl) {
            if (in_array($dl['status'], array('running', 'paused'))) {
                $running = true;
                $runningCount++;

                // Progress
                $total = $dl['size'];
                $transferred = $dl['transferred'];
                if ($transferred > 0 && $total > 0) {
                    $current = round(($transferred / $total) * 100, 2) . '%';
                }
                else {
                    $current = 0 . '%';
                }
                if (!empty($result)) {
                    $result .= '<hr />';
                }

                // Speed
                $speed = '';
                if ($dl['rx_rate'] > 0) {
                    $speed = convertFileSize($dl['rx_rate'], false);
                    $speed = $speed['size'] . '&nbsp;' . ucfirst($speed['unit']) . '/s';
                }

                // Progress style
                $progressBarClass = '';
                if ($dl['status'] == 'paused') {
                    $progressBarClass = ' progress-bar-warning';
                }

                // Write
                    // Sizes
                    $_size_transferred = convertFileSize($transferred, false);
                    $_size_total = convertFileSize($total, false);
                    $result .= '<div class="dl">
                            <p><span class="label">' . $dl['name'] . '</span> <a href="#" class="remove pull-right" data-type="' . $dl['type'] . '" data-id="' . $dl['id'] . '"><i class="glyphicon glyphicon-trash"></i></a> <small class="text-muted pull-right">' . $speed . '</small></p>
                            <div class="progress progress-striped active">
                                <div class="progress-bar' . $progressBarClass . '" style="width: ' . $current . '"><strong>' . $current . '</strong> <small class="opacified">( ' . $_size_transferred['size'] . ' ' . ucfirst($_size_transferred['unit']) . ' / ' . $_size_total['size'] . ' ' . ucfirst($_size_total['unit']) . ' )</small></div>
                            </div>
                        </div>';
            }
            else {
                $finishedCount++;
                if (!empty($result)) {
                    $result .= '<hr />';
                }
                $result .= '<div class="dl">
                            <p><span class="label label-success">' . $dl['name'] . '</span> <a href="#" class="remove finished pull-right" data-type="' . $dl['type'] . '" data-id="' . $dl['id'] . '"><i class="glyphicon glyphicon-ok"></i></a> <small class="label-finished pull-right">Terminé</small></p>
                        </div>';
            }
        }

        echo json_encode(array(
            'running' => $running,
            'totalResults' => $totalCount,
            'finishedCount' => $finishedCount,
            'runningCount' => $runningCount,
            'resultHTML' => $result
        ));
    }
    else if (isset($_POST['removeDownload'], $_POST['type'], $_POST['id'])) {
        try {
            $fbx_resp = $fbx->download->remove($_POST['type'], $_POST['id']);
        }
        catch(Exception $e) {
            $fbx_resp = json_encode(array('error' => $e->getMessage()));
        }

        if (is_array($fbx_resp)) {
            echo json_encode($fbx_resp);
        }
        else {
            echo $fbx_resp;
        }
    }
    else {
        if (isset($_POST['real_url']) && !empty($_POST['real_url'])) {
            $url = $_POST['real_url'];
        }
        else if (isset($_POST['url'])) {
            $url = $_POST['url'];
        }
        $parse_url = parse_url($url);
        $pathinfo = pathinfo($parse_url['path']);
        $file = $pathinfo['basename'];

        if (isset($_POST['file']) && !empty($_POST['file'])) {
            $file = $_POST['file'];
        }
        else if (!isset($pathinfo['extension'])) {
            $file = 'file-' . date('H_i_Y_m_d') . '.fileext';
        }

        $fbx_resp = $fbx->download->http_add($file, $url);
        print_r($fbx_resp);
    }
}

?>