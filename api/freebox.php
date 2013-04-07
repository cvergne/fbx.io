<?php
if (isset($_POST['downloads'])) {
    $fbx_resp = $fbx->download->_list();

    $totalCount = count($fbx_resp);
    $result = '';
    $running = false;

    foreach ($fbx_resp as $dl) {
        if (in_array($dl['status'], array('running', 'paused'))) {
            $running = true;

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
                if (($speed = convertFileSize($dl['rx_rate'])) < 1) {
                    $speed = convertFileSize($dl['rx_rate'], 'ko') . ' Ko/s';
                }
                else {
                    $speed .= ' Mo/s';
                }
            }

            // Progress style
            $progressBarClass = '';
            if ($dl['status'] == 'paused') {
                $progressBarClass = ' progress-bar-warning';
            }

            // Write
            $result .= '<div class="dl">
                        <p><span class="label">' . $dl['name'] . '</span> <a href="#" class="remove pull-right" data-type="' . $dl['type'] . '" data-id="' . $dl['id'] . '"><i class="glyphicon glyphicon-trash"></i></a> <small class="text-muted pull-right">' . $speed . '</small></p>
                        <div class="progress progress-striped active">
                            <div class="progress-bar' . $progressBarClass . '" style="width: ' . $current . '"><strong>' . $current . '</strong> <small class="opacified">( ' . convertFileSize($transferred) . ' Mo / ' . convertFileSize($total) . ' Mo )</small></div>
                        </div>
                    </div>';
        }
        else {
            if (!empty($result)) {
                $result .= '<hr />';
            }
            $result .= '<div class="dl">
                        <p><span class="label">' . $dl['name'] . '</span> <a href="#" class="remove finished pull-right" data-type="' . $dl['type'] . '" data-id="' . $dl['id'] . '"><i class="glyphicon glyphicon-ok"></i></a></p>
                        <div class="progress progress-striped">
                            <div class="progress-bar progress-bar-success" style="width: 100%">Terminé</div>
                        </div>
                    </div>';
        }
    }

    echo json_encode(array(
        'running' => $running,
        'totalResults' => $totalCount,
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

?>