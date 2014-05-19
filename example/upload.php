<?php
require_once __DIR__.'/../vendor/autoload.php';
ob_start();

include __DIR__.'/template/init_manager.php';

if (!$tokens) { // If no token, prompt to login. Call \OneDrive\Auth::build_oauth_url() to get the redirect URL.
    include __DIR__.'/template/auth_link.php';
} else {
    $response = $manager->put_file($_GET['folderid'], '/file/to/put');
    // File was uploaded, return metadata.
    print_r($response);
}

$content = ob_get_contents();
ob_end_clean();
require_once __DIR__."/template/index.php";