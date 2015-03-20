<?php
require_once "header.inc.php";
require_once "../src/functions.inc.php";

$token = \SkyDriveSDK\skydrive_tokenstore::acquire_token(); // Call this function to grab a current access_token, or false if none is available.

if (!$token) { // If no token, prompt to login. Call skydrive_auth::build_oauth_url() to get the redirect URL.
	echo "<div>";
	echo "<img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
	echo "<span style='vertical-align: middle;'><a href='".skydrive_auth::build_oauth_url()."'>Login with SkyDrive</a></span>";
	echo "</div>";
	
} else {

	$sd = new \SkyDriveSDK\skydrive($token);
	try {
		$response = $sd->put_file($_GET['folderid'], '/file/to/put');
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