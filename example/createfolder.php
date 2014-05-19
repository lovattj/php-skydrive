<?php
require_once __DIR__.'/../vendor/autoload.php';

include __DIR__.'/template/init_manager.php';

ob_start();

if (empty($_POST['foldername'])) {
    echo 'Error - no new folder name specified';
} else {
    if (empty($_POST['currentfolderid'])) {
        $response = $manager->createFolder(null, $_POST['foldername'], 'Description');
    } else {
        $response = $manager->createFolder($_POST['currentfolderid'], $_POST['foldername'], 'Description');
    }
    // Folder was created, return metadata.
    print_r($response);
}


$content = ob_get_contents();
ob_end_clean();
require_once __DIR__."/template/index.php";