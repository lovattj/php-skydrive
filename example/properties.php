<?php
@session_start();
require_once "header.inc.php";
require_once "../functions.inc.php";
if (!isset($_SESSION['access_token'])) {
	echo "<div>";
	echo "<img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
	echo "<span style='vertical-align: middle;'><a href='".build_oauth_url()."'>Login with SkyDrive</a></span>";
	echo "</div>";
} else {

$response = get_file_properties($_SESSION['access_token'], $_GET['fileid']);

echo "<h3>".$response['name']."</h3>";
echo "Size: ".round(($response['size']/1024),2)."Kb<br>";
echo "Created: ".$response['created_time']."<br>";
echo "Permalink: <a href='".$response['link']."'>Copy Link</a><br><br>";

echo "<div><img src='statics/folder-icon.png' width='32px' style='vertical-align: middle;'>&nbsp;<span style='vertical-align: middle;'><a href='index.php?folderid=".$response['parent_id']."'>Back to containing folder</a></span></div>";

}
require_once "footer.inc.php";
?>