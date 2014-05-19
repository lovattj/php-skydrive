<?php
require_once __DIR__.'/../vendor/autoload.php';
// logout.php

// Calls "destroy_tokens_in_store" to destroy the tokens in the token store, forcing a re-login next time.



if (\OneDrive\TokenStore::destroy_tokens_in_store()) {
	header("Location: index.php");
} else {
	echo "Error";
}