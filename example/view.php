<?php
@session_start();
require_once "functions.inc.php";
if (!isset($_SESSION['access_token'])) {
	header("Location: index.php");
} else {
	if (!$_GET['fileid']) {
		echo "Error";
	} else {
		$sd = new skydrive($_SESSION['access_token']);
		$theuri = $sd->get_source_link($_GET['fileid']);
		header("Location: ".$theuri);
	}
}
?>