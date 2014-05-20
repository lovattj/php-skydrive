<?php
// This is an example page that will display the contents of a given SkyDrive folder.
// If an access_token is not available, it'll direct the user to login with SkyDrive.
if (!file_exists(__DIR__.'/../vendor/')){
    exit('vеndor dir is not exists. use `composer update`');
}

require_once __DIR__.'/../vendor/autoload.php';

include __DIR__.'/template/init_manager.php';

ob_clean();

$quotaresp = $manager->getQuota();
?>
<p>Quota remaining: <?= $quotaresp['available'];?> Bytes</p>
<b>Create folder here:
<form method='post' action='createfolder.php' style="display: inline-block">
    <input type='hidden' name='currentfolderid' value='<?= isset($_GET['folderid'])?$_GET['folderid']:''?>'>
    <input type='text' name='foldername' placeholder='Folder Name'>
    <input type='submit' name='submit' value='submit'>
</form>
<?php

// Time to prepare and make the request to get the list of files.
$response = $manager->getFolderFiles(@$_GET['folderid'], 'name', 'ascending', 10, (isset($_GET['offset'])?$_GET['offset']:null)); // Gets the next 10 items of the specified folder from the specified offset.
$properties = $manager->getFolderProperties(@$_GET['folderid']);

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

if ($response['paging']) {
    echo '<div>';
    if (isset($response['paging']['previous'])){
        printf('<a href="index.php?folderid=%s&offset=%s">PreviousPage</a>',@$_GET['folderid'],$response['paging']['previous']['offset']);
    }
    if (isset($response['paging']['next'])){
        printf('<a href="index.php?folderid=%s&offset=%s">NextPage</a>',@$_GET['folderid'],$response['paging']['next']['offset']);
    }
    echo '</div>';
} else {
    echo "No more files in folder";
}
echo "<br>";
echo "<a href='logout.php'>Log Out</a>";
	


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
            <td><a href="/download.php?fileid=<?= $item['id'];?>">download</td>
        </tr>
        <?php
    }
    echo '</table>';
}