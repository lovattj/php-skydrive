<?php
require_once __DIR__.'/../vendor/autoload.php';

include __DIR__.'/template/init_manager.php';

//ob_start();

$response = $manager->download($_GET['fileid']);
header('Content-Type: application/octet-stream');
header('Content-Length: '.$response['properties']['size']);
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename='.$response['properties']['name']);
$stdout = fopen('php://output', 'r+');
fwrite($stdout, $response['data']);

//$content = ob_get_contents();
//ob_end_clean();
//require_once __DIR__."/template/index.php";