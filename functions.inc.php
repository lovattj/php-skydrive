<?php

/**********************************************************
php-skydrive.
A PHP client library for Microsoft SkyDrive.
**********************************************************/

// Define security credentials for your app.
// You can get these when you register your app on the Live Connect Developer Center.

define("client_id", "your_client_id");
define("client_secret", "your_client_secret");
define("callback_uri", "http://your_callback_url");

// *** Public Functions ***

// Obtains an oAuth token
// Pass in the authorization code parameter obtained from the inital callback.
// Returns the oAuth token and properties (you'll need to JSON-decode and get the token 'access_token' from the response).

function get_oauth_token($auth) {
  $output = "";
  try {
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://login.live.com/oauth20_token.srf");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/x-www-form-urlencoded',
		));
	curl_setopt($ch, CURLOPT_POST, TRUE);

	$data = "client_id=".client_id."&redirect_uri=".urlencode(callback_uri)."&client_secret=".urlencode(client_secret)."&code=".$auth."&grant_type=authorization_code";	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
  } catch (Exception $e) {
  }
  return $output;
}

// Gets the contents of a SkyDrive folder.
// Pass in your oAuth token and the ID of the folder you want to get.
// Or leave the second parameter blank for the root directory (/me/skydrive/files)
// Returns an array of the contents of the folder.

function get_folder($access_token, $folderid, $sort_by = 'name') {
	if ($folderid === null) {
		$response = json_decode(curl_get("https://apis.live.net/v5.0/me/skydrive/files?sort_by=".$sort_by."&access_token=".$access_token), true);
	} else {
		$response = json_decode(curl_get("https://apis.live.net/v5.0/".$folderid."/files?sort_by=".$sort_by."&access_token=".$access_token), true);
	}
	$arraytoreturn = Array();
	foreach ($response as $subarray) {
		foreach ($subarray as $item) {
			array_push($arraytoreturn, Array('name' => $item['name'], 'id' => $item['id'], 'type' => $item['type']));
		}
	}
	return $arraytoreturn;
}

// Gets the remaining quota of your SkyDrive account.
// Pass in your oAuth token.
// Returns an array containing your total quota and quota available.

function get_quota($access_token) {
	$response = json_decode(curl_get("https://apis.live.net/v5.0/me/skydrive/quota?access_token=".$access_token), true);
	return $response;
}

// Gets the properties of the file.
// Pass in your oAuth token.
// Returns an array of file properties.

function get_file_properties($access_token, $fileid) {
	$response = json_decode(curl_get("https://apis.live.net/v5.0/".$fileid."?access_token=".$access_token), true);
	$arraytoreturn = Array('id' => $response['id'], 'name' => $response['name'], 'parent_id' => $response['parent_id'], 'size' => $response['size'], 'source' => $response['source'], 'created_time' => $response['created_time'], 'updated_time' => $response['updated_time'], 'link' => $response['link'], 'upload_location' => $response['upload_location']);
	return $arraytoreturn;
}

// Gets a pre-signed (public) direct URL to the item
// Pass in your oAuth token and a file ID
// Returns a string containing the pre-signed URL.

function get_source_link($access_token, $fileid) {
	$response = get_file_properties($access_token, $fileid);
	return $response['source'];
}

// Gets a shared read link to the item.
// This is different to the 'link' returned from get_file_properties in that it's pre-signed.
// It's also a link to the file inside SkyDrive's interface rather than directly to the file data.

function get_shared_read_link($access_token, $fileid) {
	$response = json_decode(curl_get("https://apis.live.net/v5.0/".$fileid."/shared_read_link?access_token=".$access_token), true);
	return $response['link'];
}


// *** Private Functions ***

// Internally used function to make a GET request to SkyDrive.

function curl_get($uri) {
  $output = "";
  try {
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);

    $output = curl_exec($ch);
  } catch (Exception $e) {
  }
  return $output;
}


?>