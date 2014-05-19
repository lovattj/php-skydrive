<?php
// This is an example page that will display the contents of a given SkyDrive folder.
// If an access_token is not available, it'll direct the user to login with SkyDrive.
require_once __DIR__.'/../vendor/autoload.php';
ob_clean();

if (!file_exists('app-info.json')){
    exit('There is no `app-info.json` file with app credentials');
}

$credentials = json_decode(file_get_contents('app-info.json'),true);
$oneDriveAuth = new \OneDrive\Auth($credentials);

$redirectUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/callback.php';

// Try and get a valid access_token from the token store.

$token = \OneDrive\TokenStore::acquire_token(); // Call this function to grab a current access_token, or false if none is available.

if (!$token) { // If no token, prompt to login. Call \OneDrive\Auth::build_oauth_url() to get the redirect URL.
	echo "<div>";
	echo "<img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>";
	echo "<span style='vertical-align: middle;'><a href='".$oneDriveAuth->build_oauth_url($redirectUrl)."'>Login with SkyDrive</a></span>";
	echo "</div>";
	
} else { // Otherwise, if we have a token, use it to create an object and start calling methods to build our page.
	
	$sd2 = new \OneDrive\Manager($token);
	$quotaresp = $sd2->get_quota();
?>
	<p>Quota remaining: <?= $quotaresp['available'];?> Bytes</p>
    <b>Create folder here:
	<form method='post' action='createfolder.php' style="display: inline-block">
        <input type='hidden' name='currentfolderid' value='<?= isset($_GET['folderid'])?$_GET['folderid']:''?>'>
        <input type='text' name='foldername' placeholder='Folder Name'>
        <input type='submit' name='submit' value='submit'>
    </form>
<?php
	// First, time to create a new OneDrive object.
	$sd = new \OneDrive\Manager($token);
	// Time to prepare and make the request to get the list of files.
	if (isset($_GET['folderid'])) {
        $response = $sd->get_folder($_GET['folderid'], 'name', 'ascending', 10, (isset($_GET['offset'])?$_GET['offset']:null)); // Gets the next 10 items of the specified folder from the specified offset.
        $properties = $sd->get_folder_properties($_GET['folderid']);
	} else {
        $response = $sd->get_folder(null, 'name', 'ascending', 10, (isset($_GET['offset'])?$_GET['offset']:null));	// Gets the next 10 items of the root folder from the specified offset.
        $properties = $sd->get_folder_properties(null);
	}
	
	// Now we've got our files and folder properties, time to display them.
    ?>
    <hr>
    <div id="bodyheader">
        <?php if (isset($properties['parent_id'])):?>
            <a href="index.php?folderid=<?=$properties['parent_id']?>">...</a>
            <span>\</span>
        <?php endif; ?>
        <b><?= $properties['name'] ?> </b>
	</div>
<?php
	echoFolderContent($response);

    if ($response['paging']['nextoffset'] != 0) {
        echo "<a href='index.php?folderid=".$_GET['folderid']."&offset=".$response['paging']['nextoffset']."'>See More</a>";
    } else {
        echo "No more files in folder";
    }
    echo "<br>";
    echo "<a href='logout.php'>Log Out</a>";
	
}

$content = ob_get_contents();
ob_end_clean();
require_once __DIR__."/template/index.php";


function echoFolderContent($response){
    echo '<table id="tableFiles">';
    foreach ($response['data'] as $item) {		// Loop through the items in the folder and generate the list of items.
        ?>
        <tr>
            <td><img src="statics/<?=(($item['type'] == 'folder' || $item['type'] =='album') ? 'folder' :$item['type']) ?>-icon.png" width="32px" style="vertical-align: middle;"></td>
            <td style="vertical-align: middle;"><a title="Open folder" href="index.php?folderid="<?=$item['id']?>"><?=$item['name']?></a></td>
            <td><?= $item['description']; ?></td>
            <td><?= $item['created_time']; ?></td>
            <td><?= $item['updated_time']; ?></td>
            <td><a href="<?= $item['link']; ?>">link</a></td>
        </tr>
        <?php
    }
    echo '</table>';
}