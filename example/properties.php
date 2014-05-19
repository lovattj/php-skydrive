<?php
require_once __DIR__.'/../vendor/autoload.php';
ob_start();

if (!file_exists('app-info.json')){
    exit('There is no `app-info.json` file with app credentials');
}

$credentials = json_decode(file_get_contents('app-info.json'),true);
$oneDriveAuth = new \OneDrive\Auth($credentials);

$token = \OneDrive\TokenStore::acquire_token();

if (!$token) {
	echo "<div>";
	echo "<img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
	echo "<span style='vertical-align: middle;'><a href='".$oneDriveAuth->build_oauth_url()."'>Login with SkyDrive</a></span>";
	echo "</div>";
} else {

	$sd = new \OneDrive\Manager($token);
	try {
		$response = $sd->get_file_properties($_GET['fileid']);
		echo "<h3>".$response['name']."</h3>";
		echo "Size: ".round(($response['size']/1024),2)."Kb<br>";
		echo "Created: ".$response['created_time']."<br>";
		echo "Pre-Signed URL: <a href='".$response['source']."'>Copy Link</a><br>";
		echo "Permalink: <a href='".$response['link']."'>Copy Link</a><br><br>";
		echo "<div><img src='statics/folder-icon.png' width='32px' style='vertical-align: middle;'>&nbsp;<span style='vertical-align: middle;'><a href='index.php?folderid=".$response['parent_id']."'>Back to containing folder</a></span></div>";
	} catch (Exception $e) {
		$errc = ($e->getMessage());
		echo "Error: ";
		switch (substr($errc,-3)) {
			case "403":
				echo "Unauthorised";
				break;
			
			case "404":
				echo "Not found";
				break;
			
			default:
				echo substr($errc,-3);
				break;
		}
		
	}

}
$content = ob_get_contents();
ob_end_clean();
require_once __DIR__."/template/index.php";