<?php

// index.php

// This is an example page that will display the contents of a given SkyDrive folder.
// If an access_token is not available, it'll direct the user to login with SkyDrive.

require_once "../functions.inc.php";
require_once "header.inc.php";

// Try and get a valid access_token from the token store.

$token = \OneDrive\TokenStore::acquire_token(); // Call this function to grab a current access_token, or false if none is available.

if (!$token) { // If no token, prompt to login. Call \OneDrive\Auth::build_oauth_url() to get the redirect URL.
	echo "<div>";
	echo "<img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
	echo "<span style='vertical-align: middle;'><a href='".\OneDrive\Auth::build_oauth_url()."'>Login with SkyDrive</a></span>";
	echo "</div>";
	
} else { // Otherwise, if we have a token, use it to create an object and start calling methods to build our page.
	
	$sd2 = new \OneDrive\Manager($token);
	$quotaresp = $sd2->get_quota();
	
	echo "Quota remaining: ".round((((int)$quotaresp['available']/1024)/1024))." Mbytes.</p>";
	echo "<p><b>Create folder here:<br>";
	echo "<form method='post' action='createfolder.php'><input type='hidden' name='currentfolderid' value='".@$_GET['folderid']."'><input type='text' name='foldername' placeholder='Folder Name'>&nbsp;<input type='submit' name='submit' value='submit'></form>";
	echo "</p>";
	
	// First, time to create a new OneDrive object.
	
	$sd = new \OneDrive\Manager($token);
	
	// Time to prepare and make the request to get the list of files.
	
	if (empty($_GET['folderid'])) {
	
		if (empty($_GET['offset'])) {
			$response = $sd->get_folder(null, 'name', 'ascending', 10);	// Gets the first 10 items of the root folder.
		} else {
			$response = $sd->get_folder(null, 'name', 'ascending', 10, $_GET['offset']);	// Gets the next 10 items of the root folder from the specified offset.
		}
		
		$properties = $sd->get_folder_properties(null);
		
	} else {
	
		if (empty($_GET['offset'])) {
			$response = $sd->get_folder($_GET['folderid'], 'name', 'ascending', 10); // Gets the first 10 items of the specified folder.
		} else {
			$response = $sd->get_folder($_GET['folderid'], 'name', 'ascending', 10, $_GET['offset']); // Gets the next 10 items of the specified folder from the specified offset.
		}
		
		$properties = $sd->get_folder_properties($_GET['folderid']);
	}
	
	// Now we've got our files and folder properties, time to display them.
	
	echo "<p><div id='bodyheader'><b>".$properties['name']."</b><br>";
	if (! empty($properties['parent_id'])) {
		echo "<a href='index.php?folderid=".$properties['parent_id']."'>Up to parent folder</a>";
	}
	echo "</div>";
	echo "<br>";

	foreach ($response['data'] as $item) {		// Loop through the items in the folder and generate the list of items.
		echo "<div>";
		if ($item['type'] == 'folder' || $item['type'] =='album') {
			echo "<img src='statics/folder-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
			echo "<span style='vertical-align: middle;'><a title='Open folder' href='index.php?folderid=".$item['id']."'>".$item['name']."</a></span>";
		} else {
			echo "<img src='statics/".$item['type']."-icon.png' width='32px' style='vertical-align: middle;'>&nbsp";
			echo "<span style='vertical-align: middle;'><a title='Download' href='download.php?fileid=".$item['id']."'>".$item['name']."</a><br>";
			echo "<a href='properties.php?fileid=".$item['id']."'>Properties</a></span>";
		}
		echo "</div>";
		echo "<br>";
	}

if (@$response['paging']['nextoffset'] != 0) {
	echo "<a href='index.php?folderid=".$_GET['folderid']."&offset=".$response['paging']['nextoffset']."'>See More</a>"; 
} else {
	echo "No more files in folder";
}
echo "<br>";
echo "<a href='logout.php'>Log Out</a>";
	
}
require_once "footer.inc.php";
?>