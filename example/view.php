<?php
@session_start();
require_once "../functions.inc.php";
if (!isset($_SESSION['access_token'])) {
	header("Location: index.php");
} else {
	if (!$_GET['fileid']) {
		echo "Error";
	} else {
		$theuri = get_source_link($_SESSION['access_token'], $_GET['fileid']);
		header("Location: ".$theuri);
	}
}
?>