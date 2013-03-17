<?php

/**********************************************************
php-skydrive.
A PHP client library for Microsoft SkyDrive.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
**********************************************************/

// Define security credentials for your app.
// You can get these when you register your app on the Live Connect Developer Center.

define("client_id", "your_client_id");
define("client_secret", "your_client_secret");
define("callback_uri", "your_callback_url");
define("skydrive_base_url", "https://apis.live.net/v5.0/");

// Globals

$lastresponsecode = '';
global $lastresponsecode;

// *** Public Functions ***

// Builds a URL for the user to log in to SkyDrive and get the authorization code, which can then be
// passed onto get_oauth_token to get a valid oAuth token.

function build_oauth_url() {
	$response = "https://login.live.com/oauth20_authorize.srf?client_id=".client_id."&scope=wl.signin%20wl.skydrive_update%20wl.basic&response_type=code&redirect_uri=".urlencode(callback_uri);
	return $response;
}

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

function get_folder($access_token, $folderid, $sort_by='name', $sort_order='ascending', $limit='255') {
	if ($folderid === null) {
		$response = json_decode(curl_get(skydrive_base_url."me/skydrive/files?sort_by=".$sort_by."&sort_order=".$sort_order."&limit=".$limit."&access_token=".$access_token), true);
	} else {
		$response = json_decode(curl_get(skydrive_base_url.$folderid."/files?sort_by=".$sort_by."&sort_order=".$sort_order."&limit=".$limit."&access_token=".$access_token), true);
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
	$response = json_decode(curl_get(skydrive_base_url."me/skydrive/quota?access_token=".$access_token), true);
	return $response;
}

// Gets the properties of the folder.
// Pass in your oAuth token.
// Returns an array of folder properties.
// You can pass null as $folderid to get the properties of your root SkyDrive folder.

function get_folder_properties($access_token, $folderid) {
	$arraytoreturn = Array();
	if ($folderid === null) {
		$response = json_decode(curl_get(skydrive_base_url."/me/skydrive?access_token=".$access_token), true);
	} else {
		$response = json_decode(curl_get(skydrive_base_url.$folderid."?access_token=".$access_token), true);
	}
	@$arraytoreturn = Array('id' => $response['id'], 'name' => $response['name'], 'parent_id' => $response['parent_id'], 'size' => $response['size'], 'source' => $response['source'], 'created_time' => $response['created_time'], 'updated_time' => $response['updated_time'], 'link' => $response['link'], 'upload_location' => $response['upload_location'], 'is_embeddable' => $response['is_embeddable'], 'count' => $response['count']);
	return $arraytoreturn;
}

// Gets the properties of the file.
// Pass in your oAuth token.
// Returns an array of file properties.

function get_file_properties($access_token, $fileid) {
	$response = json_decode(curl_get(skydrive_base_url.$fileid."?access_token=".$access_token), true);
	$arraytoreturn = Array('id' => $response['id'], 'name' => $response['name'], 'parent_id' => $response['parent_id'], 'size' => $response['size'], 'source' => $response['source'], 'created_time' => $response['created_time'], 'updated_time' => $response['updated_time'], 'link' => $response['link'], 'upload_location' => $response['upload_location'], 'is_embeddable' => $response['is_embeddable']);
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
	$response = json_decode(curl_get(skydrive_base_url.$fileid."/shared_read_link?access_token=".$access_token), true);
	return $response['link'];
}

// Gets a shared edit (read-write) link to the item.

function get_shared_edit_link($access_token, $fileid) {
	$response = json_decode(curl_get(skydrive_base_url.$fileid."/shared_edit_link?access_token=".$access_token), true);
	return $response['link'];
}

// Deletes an object.

function delete_object($access_token, $fileid) {
	$response = json_decode(curl_delete(skydrive_base_url.$fileid."?access_token=".$access_token), true);
	if (@array_key_exists('error', $response)) {
		return false;
	} else {
		return true;
	}
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
	$GLOBALS['lastresponsecode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  } catch (Exception $e) {
  }
  return $output;
}

// Internally used function to make a DELETE request to SkyDrive.

function curl_delete($uri) {
  $output = "";
  try {
    $ch = curl_init($uri);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');    
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    $output = curl_exec($ch);
	$GLOBALS['lastresponsecode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);    
  } catch (Exception $e) {
  }
	return $output;
}



?>