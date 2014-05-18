<?php
// callback.php

// This is the page that Windows Live redirects to after a successful login.
// The page needs to call "\OneDrive\Auth::get_oauth_token" with the code returned in the querystring.
// Then this example page calls "\OneDrive\TokenSTokenStore::save_tokens_to_store" to save the tokens to a file (although you can handle them how you want).

require_once "functions.inc.php";
$response = \OneDrive\Auth::get_oauth_token($_GET['code']);
if (\OneDrive\TokenStore::save_tokens_to_store($response)) {
	header("Location: index.php");
} else {
	echo "error";
}
?>