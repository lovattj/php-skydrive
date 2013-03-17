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
	if (empty($_GET['folderid'])) {
		$response = get_folder($_SESSION['access_token'], null);	// Get the root folder.
		$properties = get_folder_properties($_SESSION['access_token'], null);
	} else {
		$response = get_folder($_SESSION['access_token'], $_GET['folderid']); // Get the specified folder.
		$properties = get_folder_properties($_SESSION['access_token'], $_GET['folderid']);
	}
	echo "<div id='bodyheader'><b>".$properties['name']."</b><br>";
	if (! empty($properties['parent_id'])) {
		echo "<a href='index.php?folderid=".$properties['parent_id']."'>Up to parent folder</a>";
	}
	echo "</div>";
	echo "<br>";
	foreach ($response as $item) {		// Loop through the items in the folder and generate the list of items.
		echo "<div>";
		if ($item['type'] == 'folder' || $item['type'] =='album') {
			echo "<img src='statics/folder-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
			echo "<span style='vertical-align: middle;'><a href='index.php?folderid=".$item['id']."'>".$item['name']."</a></span>";
		} else {
			echo "<img src='statics/".$item['type']."-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
			echo "<span style='vertical-align: middle;'><a href='view.php?fileid=".$item['id']."' target='_blank'>".$item['name']."</a><br>";
			echo "<a href='properties.php?fileid=".$item['id']."'>Properties</a></span>";
		}
		echo "</div>";
		echo "<br>";
	}

echo "<a href='logout.php'>Log Out</a>";

	
}
require_once "footer.inc.php";
?>