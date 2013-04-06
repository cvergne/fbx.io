<?php
    $resp = array();
    $resultHTML = '';
    $total = 0;

    if (isset($_POST['show'], $_POST['files'])) {
        foreach ($_POST['files'] as $file) {
            $bss = json_decode($bs->getSubtitles($_POST['show'], null, null, $file), true);
            // print_r($bss);
            $subtitles = $bss['root']['subtitles'];
            $nb_subs = count($subtitles);
            $total += $nb_subs;
            if ($nb_subs > 0) {
                $resultHTML .= '<li class="nav-header">' . $file . '</li>';
                foreach ($subtitles as $sub) {
                    $quality = '';
                    if ($sub['quality'] >= 4) {
                        $quality = '<span class="label label-success pull-right"><i class="glyphicon glyphicon-thumbs-up icon-white"></i></span>';
                    }
                    else if ($sub['quality'] > 2) {
                        $quality = '<span class="label label-warning pull-right"><i class="glyphicon glyphicon-hand-right icon-white"></i></span>';
                    }
                    else {
                        $quality = '<span class="label label-danger pull-right"><i class="glyphicon glyphicon-thumbs-down icon-white"></i></span>';
                    }
                    $resultHTML .= '<li><a href="' . $sub['url'] . '" target="_blank" data-show_title="' . $sub['title'] . '" data-show_season="' . $sub['season'] . '" data-show_episode="' . $sub['episode'] . '">' . $quality . '<i class="glyphicon glyphicon-comment"></i> ' . $sub['file'] . '</a></li>';
                }
            }
        }
    }


    echo json_encode(array(
        'totalResults' => $total,
        'resultHTML' => $resultHTML
    ));
?>