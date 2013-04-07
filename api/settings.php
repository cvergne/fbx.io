<?php
    $configFile = new IniParser(CONFIG_FILE_PATH);
    $configFile = $configFile->parse();

    $configFile['settings'] = array();

    if (isset($_POST['settings'])) {
        foreach($_POST['settings'] as $key => $val) {
            $configFile['settings']['settings_'.$key] = $val;
        }
    }

    $ini_content = '; configuration file for: ' . $root_uri . "\r\n";
    foreach ($configFile as $section => $entries) {
        $ini_content .= "\r\n[" . $section . "]\r\n";
        foreach ($entries as $key => $val) {
            $ini_content .= $key . " = \"" . $val . "\"\r\n" ;
        }
    }
    if ($conf_file = fopen(CONFIG_FILE_PATH, 'w+')) {
        if (fwrite($conf_file, $ini_content)) {
            $result = 'success';
        }
        else {
            $result = 'write_error';
        }
        fclose($conf_file);
    }
    else {
        $result = 'file_not_writeable';
    }
    echo json_encode(array('result' => $result));