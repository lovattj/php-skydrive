<?php
require_once "../src/functions.inc.php";
$token = skydrive_tokenstore::acquire_token();

if (!$token) {
	echo "Error";
} else {
	$sd = new skydrive($token);
	try {
		$response = $sd->download($_GET['fileid']);
		ob_end_clean();  
		header('Content-Type: application/octet-stream');
		header('Content-Length: '.$response[0]['properties']['size']);
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.$response[0]['properties']['name']);		
		$stdout = fopen('php://output', 'r+');
		fwrite($stdout, $response[0]['data']);
	} catch (Exception $e) {
		// An error occured, print HTTP status code and description.
		echo "Error: ".$e->getMessage();
		exit;
	}
}
require_once "footer.inc.php";
?>