<?php
@session_start();
require_once "header.inc.php";
require_once "functions.inc.php";
if (!isset($_SESSION['access_token'])) {
	echo "<div>";
	echo "<img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
	echo "<span style='vertical-align: middle;'><a href='".build_oauth_url()."'>Login with SkyDrive</a></span>";
	echo "</div>";
} else {
	if (empty($_POST['foldername'])) {
		echo 'Error - no new folder name specified';
	} else {
		$sd = new skydrive($_SESSION['access_token']);
		try {
			if (empty($_POST['currentfolderid'])) {
				$response = $sd->create_folder(null, $_POST['foldername'], 'Description');
			} else {
				$response = $sd->create_folder($_POST['currentfolderid'], $_POST['foldername'], 'Description');				
			}
			// Folder was created, return metadata.
			print_r($response);
		} catch (Exception $e) {
			// An error occured, print HTTP status code and description.
			echo "Error: ".$e->getMessage();
			exit;
		}		
	}


}
require_once "footer.inc.php";
?>