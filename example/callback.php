<?php
@session_start();
require_once "functions.inc.php";
$response = json_decode((get_oauth_token($_GET['code'])), true);
$_SESSION['access_token'] = $response['access_token'];
header("Location: index.php");
?>