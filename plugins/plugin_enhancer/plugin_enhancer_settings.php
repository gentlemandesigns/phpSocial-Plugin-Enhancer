<?php

/* Originally in phpDolphin classes.php. Copied to have access to them. */
if (!function_exists('gentlemandesigns_loadPlugins')) {
    function gentlemandesigns_loadPlugins($db) {

        $query = $db->query('SELECT * FROM `plugins` ORDER BY `id` DESC');

        while($column = $query->fetch_assoc()) {
            $result[] = array('name' => $column['name'], 'type' => $column['type']);
        }
        return $result;
    }
}
if (!function_exists('gentlemandesigns_getPlugins')) {
    function gentlemandesigns_getPlugins($db, $CONF, $lng) {
        global $CONF, $LNG, $db;

        $output = '';

        $listplugins = gentlemandesigns_loadPlugins($db);

        foreach($listplugins as $currplugin) {
            $active[] = $currplugin['name'];
        }
        
        if($handle = opendir('./'.$CONF['plugin_path'].'/')) {
            
            $allowedPlugins = array();
            // This is the correct way to loop over the directory.
            while(false !== ($plugin = readdir($handle))) {
                // Exclude ., .., and check whether the info.php file of the plugin exist
                if($plugin != '.' && $plugin != '..' && file_exists('./'.$CONF['plugin_path'].'/'.$plugin.'/info.php')) {
                    $allowedPlugins[] = $plugin;
                    include('./'.$CONF['plugin_path'].'/'.$plugin.'/info.php');
                    
                    $state = '';
                    
                    $state .= '<div class="users-button button-active button-alert button-delete-plugin"><a title="Deactivate and delete plugin folder." href="'.$CONF['url'].'/index.php?a=admin&b=plugins&settings=plugin_enhancer&dplugin='.$plugin.'&plugin_type='.$type.'&deleted=true&token_id='.$_SESSION['token_id'].'">'.$LNG['admin_ttl_delete'].'</a></div>';
                    if(in_array($plugin, $active)) {
                        $state .= '<div class="users-button button-active"><a href="'.$CONF['url'].'/index.php?a=admin&b=plugins&plugin='.$plugin.'&plugin_type='.$type.'&token_id='.$_SESSION['token_id'].'">'.$LNG['deactivate'].'</a></div>';
                        // Check if there is any settings page for the plugin
                        if(file_exists(__DIR__ .'/../'.$plugin.'/'.$plugin.'_settings.php')) {
                            $state .= '<div class="users-button button-normal"><a href="'.$CONF['url'].'/index.php?a=admin&b=plugins&settings='.$plugin.'" rel="loadpage">'.$LNG['settings'].'</a></div>';
                        }
                    } else {
                        $state .= '<div class="users-button button-normal"><a href="'.$CONF['url'].'/index.php?a=admin&b=plugins&plugin='.$plugin.'&plugin_type='.$type.'&activated=true&token_id='.$_SESSION['token_id'].'">'.$LNG['activate'].'</a></div>';
                    }
                    
                    if(file_exists('./'.$CONF['plugin_path'].'/'.$plugin.'/icon.png')) {
                        $image = '<img src="'.$CONF['url'].'/'.$CONF['plugin_path'].'/'.$plugin.'/icon.png">';
                    }  else {
                        $image = '';
                    }

                    $capab = array();
                    for ($i=0; $i < strlen($type); $i++) { 
                        switch ($type[$i]) {
                            case 'e':
                                $capab[] = 'Message event';
                                break;
                            case 'd':
                                $capab[] = 'Message delete';
                                break;
                            case '1':
                                $capab[] = 'Message event output';
                                break;
                            case '2':
                                $capab[] = 'Feed sidebar widget';
                                break;
                            case '3':
                                $capab[] = 'Profile sidebar widget';
                                break;
                            case '4':
                                $capab[] = 'Welcome page';
                                break;
                            case '5':
                                $capab[] = 'Feed page';
                                break;
                            case '6':
                                $capab[] = 'Profile page';
                                break;
                            case '7':
                                $capab[] = 'Message footer';
                                break;
                            case '8':
                                $capab[] = 'Stylesheet';
                                break;
                            case '9':
                                $capab[] = 'Javascript';
                                break;
                        }
                    }

                    $output .= '<div class="users-container plugin-container" data-name="'.$plugin.'" data-type="'.$type.'" data-installed="'.( in_array($plugin, $active)? 'active': 'inactive' ).'">
                        <div class="message-content">
                            <div class="message-inner">
                                '.$state.'
                                <div class="message-avatar">
                                    <a href="'.$url.'" target="_blank" title="'.$LNG['author_title'].'">
                                        '.$image.'
                                    </a>
                                </div>
                                <div class="message-top">
                                    <div class="message-author" rel="loadpage">
                                        <a href="'.$url.'" target="_blank" title="'.$LNG['author_title'].'">'.$name.'</a> '.$version.'
                                    </div>
                                    <div class="message-time">
                                        '.$LNG['by'].': <a href="'.$url.'" target="_blank" title="'.$LNG['author_title'].'">'.$author.'</a>
                                    </div>
                                </div>
                            </div>
                            <div class="message-inner" style="padding-top:0">
                                <div class="plugin-info-header"><strong>Plugin Details</strong></div>
                                <div class="plugin-info-content" style="overflow:hidden">
                                    <div class="page-input-container" style="padding: 0">
                                        <div class="page-input-title">Name:</div>
                                        <div class="page-input-content">'.$name.'</div>
                                    </div>
                                    <div class="page-input-container" style="padding: 0">
                                        <div class="page-input-title">Version:</div>
                                        <div class="page-input-content">'.$version.'</div>
                                    </div>
                                    <div class="page-input-container" style="padding: 0">
                                        <div class="page-input-title">Author:</div>
                                        <div class="page-input-content">'.$author.'</div>
                                    </div>
                                    <div class="page-input-container" style="padding: 0">
                                        <div class="page-input-title">URL:</div>
                                        <div class="page-input-content"><a href="'.$url.'">'.$url.'</a></div>
                                    </div>
                                    <div class="page-input-container" style="padding: 0">
                                        <div class="page-input-title">Capabilities:</div>
                                        <div class="page-input-content">'.implode(', ', $capab).'</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            }

            closedir($handle);
            return $output;
        }
    }
}
if (!function_exists('gentlemandesigns_deleteDir')) {
    function gentlemandesigns_deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                gentlemandesigns_deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}

function plugin_enhancer_settings() {
    global $CONF, $db, $LNG;

    $output = '</div><div>';

    // Delete Plugin & Folder
    if ( isset($_GET['dplugin']) && $_GET['dplugin'] && isset($_GET['plugin_type']) && $_GET['plugin_type'] && isset($_GET['deleted']) && $_GET['deleted'] == 'true' ) {
        $dplugin = htmlentities($_GET['dplugin']);
        $plugin_type = htmlentities($_GET['plugin_type']);
        if( file_exists('./'.$CONF['plugin_path'].'/'.$dplugin.'/info.php') ){
            try {
                if( $db->query("DELETE FROM `plugins` WHERE `name`='".$dplugin."' AND `type`='".$plugin_type."'") ) {
                    gentlemandesigns_deleteDir('./'.$CONF['plugin_path'].'/'.$dplugin.'/');
                    $output .= notificationBox('success', "Plugin deleted with success.");
                } else {
                    throw new InvalidArgumentException("Database could not delete the plugin.");
                }
            } catch (InvalidArgumentException $e) {
                $output .= notificationBox('error', "Error deleting the plugin.");
            }
        }
    }

    // Install Plugin
    if ( isset($_POST['plugin_install_token']) && $_POST['plugin_install_token'] ) {
        $target_dir = './'.$CONF['plugin_path'].'/';
        $target_file = $target_dir . basename($_FILES["plugin_package"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

        $tempName = $_FILES["plugin_package"]["tmp_name"];

        $zip = new ZipArchive();
        if ( $zip->open($tempName) === TRUE ) {
            if ( $zip->locateName('info.php', ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR) !== false ) {
                $info = $zip->statIndex(0);
                $folderToCheck = explode("/", $info['name']);
                if ( file_exists($target_dir.$folderToCheck[0]."/") ) {
                        $output .= notificationBox('error', "Plugin folder already exists: $folderToCheck[0]");
                } else {
                    if ( $zip->extractTo($target_dir) ) {
                        $output .= notificationBox('success', "Plugin installed with success.");
                        header('Location:'.$CONF['url'].'/index.php?a=admin&b=plugins&settings=plugin_enhancer');
                        die;
                    } else {
                        $output .= notificationBox('error', "Plugin could not be installed.");
                    }
                }

            } else {
                $output .= notificationBox('error', "Missing plugin information.");
            }
        }
    }

    $output .= '
    <div class="page-content" style="margin-top: 10px;">
        <div class="page-header">Install Plugin</div>
        <div class="page-inner">
            <form action="'.$CONF['url'].'/index.php?a=admin&b=plugins&settings=plugin_enhancer" method="POST" enctype="multipart/form-data">
                <div class="page-input-container">
                    <div class="page-input-title">Plugin archive</div>
                    <div class="page-input-content">
                        <input type="file" name="plugin_package" value="" placeholder="Drop plugin ZIP here.">
                        <div class="page-input-sub">Upload plugin ZIP archive.</div>
                    </div>
                </div>
                <div class="page-input-container">
                    <div class="page-input-content">
                        <input type="submit" name="plugin_install_token" value="Install plugin">
                    </div>
                </div>
            </form>
        </div>
    </div>';

    $output .= '
    <input type="hidden" id="update_url" value="'.$CONF['url'].'/index.php?a=admin&b=plugins&settings=plugin_enhancer&plugin_reorder=true">
    <div class="plugins-wrapper" id="plugins_container">
        ';
    $output .= gentlemandesigns_getPlugins($db,$CONF,$LNG);
    $output .= '
    </div>';

    return $output;
}

if ( isset($_GET['plugin_reorder']) && $_GET['plugin_reorder'] && isset($_POST['data'])) {
    $list = json_decode($_POST['data']);
    if( $db->query("DELETE FROM `plugins`") ){
        $error = [];
        $index = 1;
        try {
            foreach ($list as $plugin) {
                if( $plugin->active == 'active' ){
                    if( !$db->query("INSERT INTO `plugins`(`id`,`name`,`type`, `priority`) VALUES ('".$index."', '".$plugin->name."','".$plugin->value."','".$index."')") ){
                        $error[] = $db->error;
                    }
                    $index++;
                }
            }
            var_dump($error);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
    } else {
        echo $db->error;
    }
    exit;
}

?>