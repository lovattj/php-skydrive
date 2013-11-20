<?php
@session_start();
require_once "functions.inc.php";
if (!isset($_SESSION['access_token'])) {
	echo "Error";
} else {
	$sd = new skydrive($_SESSION['access_token']);
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