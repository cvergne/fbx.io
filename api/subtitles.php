<?php
    $resp = array();
    $resultHTML = '';
    $total = 0;

    if (isset($_POST['show'], $_POST['files'], $_POST['cleanfiles'])) {
        foreach ($_POST['files'] as $k => $file) {
            $bss = json_decode($bs->getSubtitles($_POST['show'], null, null, $file), true);
            // print_r($bss);
            $subtitles = $bss['root']['subtitles'];
            $nb_subs = count($subtitles);
            $total += $nb_subs;
            if ($nb_subs > 0) {

                if (isset($_POST['cleanfiles'][$k]) && !empty($_POST['cleanfiles'][$k])) {
                    $nice_filename = 'data-nice_filename="' . $_POST['cleanfiles'][$k] . '" ';
                }
                else {
                    $nice_filename = '';
                }
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
                    $resultHTML .= '<li><a href="' . $sub['url'] . '" target="_blank" ' . $nice_filename . 'data-show_title="' . $sub['title'] . '" data-show_season="' . $sub['season'] . '" data-show_episode="' . $sub['episode'] . '">' . $quality . '<i class="glyphicon glyphicon-comment"></i> ' . $sub['file'] . '</a></li>';
                }
            }
        }
    }


    echo json_encode(array(
        'totalResults' => $total,
        'resultHTML' => $resultHTML
    ));
?>