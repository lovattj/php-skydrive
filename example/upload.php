<?php
require_once "header.inc.php";
require_once "functions.inc.php";

$token = skydrive_auth::acquire_token(); // Call this function to grab a current access_token, or false if none is available.

if (!$token) { // If no token, prompt to login. Call skydrive_auth::build_oauth_url() to get the redirect URL.
	echo "<div>";
	echo "<img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
	echo "<span style='vertical-align: middle;'><a href='".build_oauth_url()."'>Login with SkyDrive</a></span>";
	echo "</div>";
	
} else {

	$sd = new skydrive($token);
	try {
		$response = $sd->put_file($_GET['folderid'], '/home/jl/style.css');
		// File was uploaded, return metadata.
		print_r($response);
	} catch (Exception $e) {
		// An error occured, print HTTP status code and description.
		echo "Error: ".$e->getMessage();
		exit;
	}

}
require_once "footer.inc.php";
?>