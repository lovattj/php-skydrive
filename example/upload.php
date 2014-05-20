<?php
require_once __DIR__.'/../vendor/autoload.php';

include __DIR__.'/template/init_manager.php';

ob_start();

$response = $manager->uploadFile($_GET['folderid'], '/file/to/put');
// File was uploaded, return metadata.
print_r($response);


$content = ob_get_contents();
ob_end_clean();
require_once __DIR__."/template/index.php";