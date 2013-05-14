<?php
    if (isset($_POST['uri'])) {
        $parse_url = parse_url($_POST['uri']);
        $pathinfo = pathinfo($parse_url['path']);
        $file = $pathinfo['basename'];

        $error = false;
        $filename = getEpisodeFilename($file);
    }
    else {
        $error = true;
        $filename = false;
    }

    header('Content-type: application/json');
    echo json_encode(array(
                'root' => array(
                    'error' => $error,
                    'filename' =>  $filename
                )
            ));
?>