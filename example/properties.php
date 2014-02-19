<?php
@session_start();
require_once "header.inc.php";
require_once "../functions.inc.php";
$token = skydrive_auth::acquire_token();

if (!$token) {
	echo "<div>";
	echo "<img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
	echo "<span style='vertical-align: middle;'><a href='".build_oauth_url()."'>Login with SkyDrive</a></span>";
	echo "</div>";
} else {

	$sd = new skydrive($token);
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
require_once "footer.inc.php";
?>