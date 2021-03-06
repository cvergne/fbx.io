<?php
    function class_autoloader($classname) {
        require_once(APP_ROOT.'includes/'.strtolower($classname).'.php');
    }
    spl_autoload_register('class_autoloader');

    function printr($n) {
        echo '<pre>';
        print_r($n);
        echo '</pre>';
    }

    function _get($url) {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        ));

        $resp = curl_exec($ch);

        curl_close($ch);

        return json_decode($resp, true);
    }

    function _post($url, $params=array()) {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params
        ));

        $resp = curl_exec($ch);

        curl_close($ch);

        return json_decode($resp, true);
    }

    function convertFileSize($bytes, $to='mo') {
        if ($to) {
            switch($to)
            {
                case 'ko':
                        return round(($bytes / 1024), 2);
                    break;

                case 'mo':
                        return round(($bytes / 1024)/1024, 2);
                    break;

                case 'go':
                        return round(($bytes / 1024)/1024/1024, 2);
                    break;
            }
        }
        else {
            $fs = round(($bytes / 1024), 2);
            $unit = 'ko';
            if ($fs >= 1024) {
                $fs = round(($bytes / 1024) / 1024, 2);
                $unit = 'mo';
                if ($fs >= 1024) {
                    $fs = round(($bytes / 1024) / 1024 / 1024, 2);
                    $unit = 'go';
                }
            }
            return array(
                'size' => $fs,
                'unit' => $unit
            );
        }
    }

    function downloadFile ($url, $path) {
        $newfname = $path;
        $file = fopen ($url, "rb");
        if ($file) {
            $newf = fopen ($newfname, "wb");

            if ($newf) {
                while(!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
                }
            }
        }

        if ($file) {
            fclose($file);
        }

        if ($newf) {
            fclose($newf);
        }
    }

    function unzip($file, $to){
        $zip = new ZipArchive;
        $zip->open($file);
        $zip->extractTo($to);
        $zip->close();

        return(true);
    }

    function mkdirr($pn,$mode=null) {
      if(is_dir($pn)||empty($pn)) return true;
      $pn=str_replace(array('/', ''),DIRECTORY_SEPARATOR,$pn);

      if(is_file($pn)) {trigger_error('mkdirr() File exists', E_USER_WARNING);return false;}

      $next_pathname=substr($pn,0,strrpos($pn,DIRECTORY_SEPARATOR));
      if(mkdirr($next_pathname,$mode)) {if(!file_exists($pn)) {return mkdir($pn,$mode);} }
      return false;
    }

    function emptydir($dir, $delete = false) {
        $dossier = $dir;
        $dir = opendir($dossier);
        while($file = readdir($dir)) {
            if(!in_array($file, array(".", ".."))){
                if(is_dir("$dossier/$file")) {
                    emptydir("$dossier/$file", true);
                } else {
                    unlink("$dossier/$file");
                }
            }
        }
        closedir($dir);

        if($delete == true) {
            rmdir("$dossier/$file");
        }
    }

    function getEpisodeFilename($filename, $with_ext=true) {
        if (preg_match("'^(.+)\.S([0-9]+)E([0-9]+).*$'i",$filename,$n)) {
            $fileinfo = pathinfo($filename);
            $name = preg_replace("'\.'"," ",$n[1]);
            $season = intval($n[2],10);
            $episode = intval($n[3],10);

            $new_filename = getEpisodeInfos($name, $season, $episode);
            if ($with_ext) {
                $new_filename .= '.' . $fileinfo['extension'];
            }

            return $new_filename;
        }

        return $filename;
    }

    function getEpisodeInfos($show_title, $season, $episode, $title=null) {
        global $bs;
        if (empty($show_title) || empty($season) || empty($episode)) {
            return false;
        }
        if (empty($title) && isset($bs)) {
            $s = $bs->getURL($show_title);
            $s = json_decode($bs->getEpisode($s, $season, $episode), true);
            if ($s['root']['code'] != '1' || count($s['root']['seasons']) == 0 || count($s['root']['seasons'][0]['episodes']) == 0) {
                return false;
            }
            $title = $s['root']['seasons'][0]['episodes'][0]['title'];
        }
        $show_title = ucfirst(trim(preg_replace('/\((.*)\)/', '', filter_var($show_title, FILTER_SANITIZE_STRING))));

        $episode_title = ucfirst(preg_replace("/\!|\"|@|'|\#/", '', $title));

        if ($episode < 10) {
            $episode = '0'.$episode;
        }

        return $show_title . ' - ' . $season . 'x' . $episode . ' - ' . $episode_title;
    }

    function _settingCheck($key, $val, $or_undefined=false, $echo=true, $trueval=' checked="checked"', $falseval='') {
        $key = strtoupper($key);
        if (defined($key)) {
            if (constant($key) == $val) {
                $is_true = true;
            }
            else {
                $is_true = false;
            }
        }
        else {
            if ($or_undefined) {
                $is_true = true;
            }
            else {
                $is_true = false;
            }
        }

        if ($echo) {
            if ($is_true) {
                echo $trueval;
            }
            else {
                echo $falseval;
            }
        }
        else {
            if ($is_true) {
                return $trueval;
            }
            else {
                return $falseval;
            }
        }
    }

    function _settingBool($key, $on= 'on', $off='off', $default='off') {
        $key = strtoupper($key);
        if (defined($key)) {
            if (constant($key) == '0') {
                $setting_input_checked = ' checked="checked"';
                $setting_target_class = ' ' . $off;
                $setting_state = true;
            }
            else if (constant($key) == '1') {
                $setting_input_checked = ' checked="checked"';
                $setting_target_class = ' ' . $on;
                $setting_state = true;
            }
        }
        else {
            $setting_input_checked = '';
            $setting_target_class = ' ' . $default;
            $setting_state = false;
        }
        return array(
            'checked' => $setting_input_checked,
            'class' => $setting_target_class,
            'state' => $setting_state
        );
    }

    function _setupIsInError($id) {
        global $errors;
        if (isset($errors[$id])) {
            echo ' has-error';
        }
        else {
            echo '';
        }
    }