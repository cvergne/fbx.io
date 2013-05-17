<?php
    $resp = array();
    $filesnames = array();
    $resultHTML = '';

    $uri = 'https://api.put.io/v2/files/list?oauth_token=' . PUTIO_OAUTHTOKEN;
    if (isset($_POST['parent_id']) && !empty($_POST['parent_id'])) {
        $uri .= '&parent_id=' . $_POST['parent_id'];
    }
    $itemlist = _get($uri);

    if (isset($_POST['parent_id'], $itemlist['parent']['parent_id'])) {
        $resultHTML .= '<li><a href="#" data-type="folder" data-parent_id="' . $itemlist['parent']['parent_id'] . '"><i class="glyphicon glyphicon-chevron-left"></i> Retour</a></li>';
    }

    $resultHTML .= '<li class="nav-header">' . $itemlist['parent']['name'] . '</li>';
    foreach($itemlist['files'] as $item) {
        $link = '#';
        $size = convertFileSize($item['size'], false);
        if ($item['content_type'] == 'application/x-directory') {
            $attrs = 'data-type="folder" data-folder_id="' . $item['id'] . '"';
            $icon = '<i class="glyphicon glyphicon-folder-close"></i> ';
            $resultHTML .= '<li><a data-size="' . $item['size'] . '" href="' . $link . '" ' . $attrs . '><small class="pull-right text-muted">' . $size['size'] . ' ' . ucfirst($size['unit']) . '</small><strong>' . $icon . '<span>' . $item['name'] . '</span>' . '</strong></a></li>';
        }
        else {
            if (defined('SETTINGS_FILENAME_AUTOPUTIO')) {
                $nice_filename = 'data-nice_filename="' . getEpisodeFilename($item['name']) . '" ';
            }
            else {
                $nice_filename = '';
            }
            $attrs = 'data-type="file" ' . $nice_filename . 'data-nice_url="/files/' . $item['id'] . '/download"';
            $icon = '<i class="glyphicon glyphicon-file"></i> ';
            $link = PUTIO_DOWNLOAD_URL . '/files/' . $item['id'] . '/download?token=' . $_SESSION['putio_oauth_access_token'];
            $filesnames[] = $item['name'];
            $resultHTML .= '<li><a data-size="' . $item['size'] . '" href="' . $link . '" ' . $attrs . '><small class="pull-right text-muted">' . $size['size'] . ' ' . ucfirst($size['unit']) . '</small><em>' . $icon . '<span>' . $item['name'] . '</span>' . '</em></a></li>';
        }
    }


    if (isset($_apifolder_html)) {
        echo $resultHTML;
    }
    else {
        if (!isset($bs) || (empty($itemlist['parent']['parent_id']) && $itemlist['parent']['parent_id'] !== 0) || !($betaseries_id = $bs->getURL(strtolower($itemlist['parent']['name'])))) {
            $betaseries_id = null;
        }
        echo json_encode(array(
            'currentFolderID' => $itemlist['parent']['id'],
            'totalResults' => count($filesnames),
            'files' => $filesnames,
            'folder_name' => strtolower($itemlist['parent']['name']),
            'betaseries_id' =>  $betaseries_id,
            'resultHTML' => $resultHTML
        ));
    }
?>