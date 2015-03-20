<?php
// logout.php

// Calls "destroy_tokens_in_store" to destroy the tokens in the token store, forcing a re-login next time.

require_once "functions.inc.php";

if (\SkyDriveSDK\skydrive_tokenstore::destroy_tokens_in_store()) {
	header("Location: index.php");
} else {
	echo "Error";
}
?>