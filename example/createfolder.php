<?php
require_once __DIR__.'/../vendor/autoload.php';
ob_start();

include __DIR__.'/template/init_manager.php';

if (!$tokens) { // If no token, prompt to login. Call \OneDrive\Auth::build_oauth_url() to get the redirect URL.
    include __DIR__.'/template/auth_link.php';
} else {

	if (empty($_POST['foldername'])) {
		echo 'Error - no new folder name specified';
	} else {
		try {
			if (empty($_POST['currentfolderid'])) {
				$response = $manager->createFolder(null, $_POST['foldername'], 'Description');
			} else {
				$response = $manager->createFolder($_POST['currentfolderid'], $_POST['foldername'], 'Description');
			}
			// Folder was created, return metadata.
			print_r($response);
		} catch (Exception $e) {
			// An error occured, print HTTP status code and description.
			echo "Error: ".$e->getMessage();

		}		
	}


}
$content = ob_get_contents();
ob_end_clean();
require_once __DIR__."/template/index.php";