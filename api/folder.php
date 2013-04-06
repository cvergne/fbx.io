<?php
    $resp = array();
    $filesnames = array();
    $resultHTML = '';

    $uri = 'https://api.put.io/v2/files/list?oauth_token=' . PUTIO_OAUTHTOKEN;
    if (isset($_POST['parent_id'])) {
        $uri .= '&parent_id=' . $_POST['parent_id'];
    }
    $itemlist = _get($uri);
    // printr($itemlist);

    if (isset($_POST['parent_id'], $itemlist['parent']['parent_id'])) {
        $resultHTML .= '<li><a href="#" data-type="folder" data-parent_id="' . $itemlist['parent']['parent_id'] . '"><i class="glyphicon glyphicon-chevron-left"></i> Retour</a></li>';
    }

    $resultHTML .= '<li class="nav-header">' . $itemlist['parent']['name'] . '</li>';
    foreach($itemlist['files'] as $item) {
        $link = '#';
        if ($item['content_type'] == 'application/x-directory') {
            $attrs = 'data-type="folder" data-folder_id="' . $item['id'] . '"';
            $icon = '<i class="glyphicon glyphicon-folder-close"></i> ';
            $resultHTML .= '<li><a href="' . $link . '" ' . $attrs . '><strong>' . $icon . $item['name'] . '</strong></a></li>';
        }
        else {
            $attrs = 'data-type="file" data-nice_url="/files/' . $item['id'] . '/download"';
            $icon = '<i class="glyphicon glyphicon-file"></i> ';
            $link = PUTIO_DOWNLOAD_URL . '/files/' . $item['id'] . '/download?token=' . $_SESSION['putio_oauth_access_token'];
            $filesnames[] = $item['name'];
            $resultHTML .= '<li><a href="' . $link . '" ' . $attrs . '><em>' . $icon . $item['name'] . '</em></a></li>';
        }
    }

    if (!($betaseries_id = $bs->getURL(strtolower($itemlist['parent']['name'])))) {
        $betaseries_id = null;
    }

    if (isset($_apifolder_html)) {
        echo $resultHTML;
    }
    else {
        echo json_encode(array(
            'totalResults' => count($filesnames),
            'files' => $filesnames,
            'folder_name' => strtolower($itemlist['parent']['name']),
            'betaseries_id' =>  $betaseries_id,
            'resultHTML' => $resultHTML
        ));
    }
?>