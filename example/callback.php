<?php
@session_start();
require_once "functions.inc.php";
$response = skydrive_auth::get_oauth_token($_GET['code']);
$_SESSION['access_token'] = $response['access_token'];
$_SESSION['access_token_expires'] = (time()+(int)$response['expires_in']);
header("Location: index.php");
?>