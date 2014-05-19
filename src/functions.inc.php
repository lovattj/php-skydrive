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

define("client_id", "YOUR LIVE CLIENT ID");
define("client_secret", "YOUR LIVE CLIENT SECRET");
define("callback_uri", "YOUR CALLBACK URL");
define("skydrive_base_url", "https://apis.live.net/v5.0/");
define("token_store", "tokens"); // Edit path to your token store if required, see Wiki for more info.

class skydrive {

	public $access_token = '';

	public function __construct($passed_access_token) {
		$this->access_token = $passed_access_token;
	}
	
	
	// Gets the contents of a SkyDrive folder.
	// Pass in the ID of the folder you want to get.
	// Or leave the second parameter blank for the root directory (/me/skydrive/files)
	// Returns an array of the contents of the folder.

	public function get_folder($folderid, $sort_by='name', $sort_order='ascending', $limit='255', $offset='0') {
		if ($folderid === null) {
			$response = $this->curl_get(skydrive_base_url."me/skydrive/files?sort_by=".$sort_by."&sort_order=".$sort_order."&offset=".$offset."&limit=".$limit."&access_token=".$this->access_token);
		} else {
			$response = $this->curl_get(skydrive_base_url.$folderid."/files?sort_by=".$sort_by."&sort_order=".$sort_order."&offset=".$offset."&limit=".$limit."&access_token=".$this->access_token);
		}
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {		
			$arraytoreturn = Array();
			$temparray = Array();
			if (@$response['paging']['next']) {
				parse_str($response['paging']['next'], $parseout);
				$numerical = array_values($parseout);
			}
			if (@$response['paging']['previous']) {
				parse_str($response['paging']['previous'], $parseout1);
				$numerical1 = array_values($parseout1);
			}			
			foreach ($response as $subarray) {
				foreach ($subarray as $item) {
					if (@array_key_exists('id', $item)) {
						array_push($temparray, Array('name' => $item['name'], 'id' => $item['id'], 'type' => $item['type'], 'size' => $item['size'], 'source' => @$item['source']));
					}
				}
			}
			$arraytoreturn['data'] = $temparray;
			if (@$numerical[0]) {
				if (@$numerical1[0]) {
					$arraytoreturn['paging'] = Array('previousoffset' => $numerical1[0], 'nextoffset' => $numerical[0]);
				} else {
					$arraytoreturn['paging'] = Array('previousoffset' => 0, 'nextoffset' => $numerical[0]);		
				}			
			} else {
				$arraytoreturn['paging'] = Array('previousoffset' => 0, 'nextoffset' => 0);
			}
			return $arraytoreturn;
		}
	}

	// Gets the remaining quota of your SkyDrive account.
	// Returns an array containing your total quota and quota available in bytes.

	function get_quota() {
		$response = $this->curl_get(skydrive_base_url."me/skydrive/quota?access_token=".$this->access_token);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {			
			return $response;
		}
	}

	// Gets the properties of the folder.
	// Returns an array of folder properties.
	// You can pass null as $folderid to get the properties of your root SkyDrive folder.

	public function get_folder_properties($folderid) {
		$arraytoreturn = Array();
		if ($folderid === null) {
			$response = $this->curl_get(skydrive_base_url."/me/skydrive?access_token=".$this->access_token);
		} else {
			$response = $this->curl_get(skydrive_base_url.$folderid."?access_token=".$this->access_token);
		}
		
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {			
			@$arraytoreturn = Array('id' => $response['id'], 'name' => $response['name'], 'parent_id' => $response['parent_id'], 'size' => $response['size'], 'source' => $response['source'], 'created_time' => $response['created_time'], 'updated_time' => $response['updated_time'], 'link' => $response['link'], 'upload_location' => $response['upload_location'], 'is_embeddable' => $response['is_embeddable'], 'count' => $response['count']);
			return $arraytoreturn;
		}
	}

	// Gets the properties of the file.
	// Returns an array of file properties.

	public function get_file_properties($fileid) {
		$response = $this->curl_get(skydrive_base_url.$fileid."?access_token=".$this->access_token);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			$arraytoreturn = Array('id' => $response['id'], 'type' => $response['type'], 'name' => $response['name'], 'parent_id' => $response['parent_id'], 'size' => $response['size'], 'source' => $response['source'], 'created_time' => $response['created_time'], 'updated_time' => $response['updated_time'], 'link' => $response['link'], 'upload_location' => $response['upload_location'], 'is_embeddable' => $response['is_embeddable']);
			return $arraytoreturn;
		}
	}

	// Gets a pre-signed (public) direct URL to the item
	// Pass in a file ID
	// Returns a string containing the pre-signed URL.

	public function get_source_link($fileid) {
		$response = $this->get_file_properties($fileid);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			return $response['source'];
		}
	}
	
	
	// Gets a shared read link to the item.
	// This is different to the 'link' returned from get_file_properties in that it's pre-signed.
	// It's also a link to the file inside SkyDrive's interface rather than directly to the file data.

	function get_shared_read_link($fileid) {
		$response = curl_get(skydrive_base_url.$fileid."/shared_read_link?access_token=".$this->access_token);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {	
			return $response['link'];
		}
	}

	// Gets a shared edit (read-write) link to the item.

	function get_shared_edit_link($fileid) {
		$response = curl_get(skydrive_base_url.$fileid."/shared_edit_link?access_token=".$this->access_token);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {	
			return $response['link'];
		}
	}

	// Deletes an object.

	function delete_object($fileid) {
		$response = curl_delete(skydrive_base_url.$fileid."?access_token=".$this->access_token);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			return true;
		}
	}
	
	// Downloads a file from SkyDrive to the server.
	// Pass in a file ID.
	// Returns a multidimensional array:
	// ['properties'] contains the file metadata and ['data'] contains the raw file data.
	
	public function download($fileid) {
		$props = $this->get_file_properties($fileid);
		$response = $this->curl_get(skydrive_base_url.$fileid."/content?access_token=".$this->access_token, "false", "HTTP/1.1 302 Found");
		$arraytoreturn = Array();
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			array_push($arraytoreturn, Array('properties' => $props, 'data' => $response));
			return $arraytoreturn;
		}		
	}

	
	// Uploads a file from disk.
	// Pass the $folderid of the folder you want to send the file to, and the $filename path to the file.
	// Also use this function for modifying files, it will overwrite a currently existing file.

	function put_file($folderid, $filename) {
		$r2s = skydrive_base_url.$folderid."/files/".basename($filename)."?access_token=".$this->access_token;
		$response = $this->curl_put($r2s, $filename);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			return $response;
		}
			
	}
	
	/**
	 * Upload file directly from remote URL
	 * 
	 * @param string $sourceUrl - URL of the file
	 * @param string $folderId - folder you want to send the file to
	 * @param string $filename - target filename after upload
	 */
	function put_file_from_url($sourceUrl, $folderId, $filename){
		$r2s = skydrive_base_url.$folderId."/files/".$filename."?access_token=".$this->access_token;
		
		$chunkSizeBytes = 1 * 1024 * 1024; //1MB
		
		//download file first to tempfile
		$tempFilename = tempnam("/tmp", "UPLOAD");
		$temp = fopen($tempFilename, "w");
		
		$handle = @fopen($sourceUrl, "rb");
		if($handle === FALSE){
			throw new Exception("Unable to download file from " . $sourceUrl);
		}
		
		while (!feof($handle)) {
			$chunk = fread($handle, $chunkSizeBytes);
			fwrite($temp, $chunk);
		}		
		
		fclose($handle);
		fclose($temp);
		
		//upload to OneDrive
		$response = $this->curl_put($r2s, $tempFilename);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			unlink($tempFilename);
			return $response;
		}
	}	
	
	
	// Creates a folder.
	// Pass $folderid as the containing folder (or 'null' to create the folder under the root).
	// Also pass $foldername as the name for the new folder and $description as the description.
	// Returns the new folder metadata or throws an exception.
	
	function create_folder($folderid, $foldername, $description="") {
		if ($folderid===null) {
			$r2s = skydrive_base_url."me/skydrive";
		} else {
			$r2s = skydrive_base_url.$folderid;
		}
		$arraytosend = array('name' => $foldername, 'description' => $description);	
		$response = $this->curl_post($r2s, $arraytosend, $this->access_token);
		if (@array_key_exists('error', $response)) {
				throw new Exception($response['error']." - ".$response['description']);
				exit;
			} else {		
				$arraytoreturn = Array();
				array_push($arraytoreturn, Array('name' => $response['name'], 'id' => $response['id']));					
				return $arraytoreturn;
			}
	}
	
	// *** PROTECTED FUNCTIONS ***
	
	// Internally used function to make a GET request to SkyDrive.
	// Functions can override the default JSON-decoding and return just the plain result.
	// They can also override the expected HTTP status code too.
	
	protected function curl_get($uri, $json_decode_output="true", $expected_status_code="HTTP/1.1 200 OK") {
		$output = "";
		$output = @file_get_contents($uri);
		if ($http_response_header[0] == $expected_status_code) {
			if ($json_decode_output == "true") {
				return json_decode($output, true);
			} else {
				return $output;
			}
		} else {
			return Array('error' => 'HTTP status code not expected - got ', 'description' => substr($http_response_header[0],9,3));
		}
	}

	// Internally used function to make a POST request to SkyDrive.

	protected function curl_post($uri, $inputarray, $access_token) {
		$trimmed = json_encode($inputarray);
		try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$access_token,
		));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $trimmed);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		} catch (Exception $e) {
		}
		if ($httpcode == "201") {
			return json_decode($output, true);
		} else {
			return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
		}
	}

	// Internally used function to make a PUT request to SkyDrive.

	protected function curl_put($uri, $fp) {
	  $output = "";
	  try {
	  	$pointer = fopen($fp, 'r+');
	  	$stat = fstat($pointer);
	  	$pointersize = $stat['size'];
		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_INFILE, $pointer);
		curl_setopt($ch, CURLOPT_INFILESIZE, (int)$pointersize);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
		
		//HTTP response code 100 workaround
		//see http://www.php.net/manual/en/function.curl-setopt.php#82418
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));			
		
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	  } catch (Exception $e) {
	  }
	  	if ($httpcode == "200" || $httpcode == "201") {
	  		return json_decode($output, true);
	  	} else {
	  		return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
	  	}
		
	}

	// Internally used function to make a DELETE request to SkyDrive.

	protected function curl_delete($uri) {
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
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	  } catch (Exception $e) {
	  }
	  	if ($httpcode == "200") {
	  		return json_decode($output, true);
	  	} else {
	  		return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
	  	}
	}
	

}

class skydrive_auth {

	// build_oauth_url()
	
	// Builds a URL for the user to log in to SkyDrive and get the authorization code, which can then be
	// passed onto get_oauth_token to get a valid oAuth token.

	public static function build_oauth_url() {
		$response = "https://login.live.com/oauth20_authorize.srf?client_id=".client_id."&scope=wl.signin%20wl.offline_access%20wl.skydrive_update%20wl.basic&response_type=code&redirect_uri=".urlencode(callback_uri);
		return $response;
	}


	// get_oauth_token()

	// Obtains an oAuth token
	// Pass in the authorization code parameter obtained from the inital callback.
	// Returns the oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).

	public static function get_oauth_token($auth) {
		$arraytoreturn = array();
		$output = "";
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://login.live.com/oauth20_token.srf");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/x-www-form-urlencoded',
				));
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);		

			$data = "client_id=".client_id."&redirect_uri=".urlencode(callback_uri)."&client_secret=".urlencode(client_secret)."&code=".$auth."&grant_type=authorization_code";	
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$output = curl_exec($ch);
		} catch (Exception $e) {
		}
	
		$out2 = json_decode($output, true);
		$arraytoreturn = Array('access_token' => $out2['access_token'], 'refresh_token' => $out2['refresh_token'], 'expires_in' => $out2['expires_in']);
		return $arraytoreturn;
	}
	
	
	// refresh_oauth_token()
	
	// Attempts to refresh an oAuth token
	// Pass in the refresh token obtained from a previous oAuth request.
	// Returns the new oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).
		
	public static function refresh_oauth_token($refresh) {
		$arraytoreturn = array();
		$output = "";
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://login.live.com/oauth20_token.srf");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/x-www-form-urlencoded',
				));
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);		

			$data = "client_id=".client_id."&redirect_uri=".urlencode(callback_uri)."&client_secret=".urlencode(client_secret)."&refresh_token=".$refresh."&grant_type=refresh_token";	
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$output = curl_exec($ch);
		} catch (Exception $e) {
		}
	
		$out2 = json_decode($output, true);
		$arraytoreturn = Array('access_token' => $out2['access_token'], 'refresh_token' => $out2['refresh_token'], 'expires_in' => $out2['expires_in']);
		return $arraytoreturn;
	}
	
}

class skydrive_tokenstore {

	// acquire_token()
	
	// Will attempt to grab an access_token from the current token store.
	// If there isn't one then return false to indicate user needs sending through oAuth procedure.
	// If there is one but it's expired attempt to refresh it, save the new tokens and return an access_token.
	// If there is one and it's valid then return an access_token.
	
	
	public static function acquire_token() {
		
		$response = skydrive_tokenstore::get_tokens_from_store();
		if (empty($response['access_token'])) {	// No token at all, needs to go through login flow. Return false to indicate this.
			return false;
			exit;
		} else {
			if (time() > (int)$response['access_token_expires']) { // Token needs refreshing. Refresh it and then return the new one.
				$refreshed = skydrive_auth::refresh_oauth_token($response['refresh_token']);
				if (skydrive_tokenstore::save_tokens_to_store($refreshed)) {
					$newtokens = skydrive_tokenstore::get_tokens_from_store();
					return $newtokens['access_token'];
				}
				exit;
			} else {
				return $response['access_token']; // Token currently valid. Return it.
				exit;
			}
		}
	}
	
	// get_tokens_from_store()
	// save_tokens_to_store()
	// destroy_tokens_in_store()
	
	// These functions provide a gateway to your token store.
	// In it's basic form, the tokens are written simply to a file called "tokens" in the current working directory, JSON-encoded.
	// You can edit the location of the token store by editing the DEFINE entry on line 28.
	
	// If you want to implement your own token store, you can edit these functions and implement your own code, e.g. if you want to store them in a database.
	// You MUST save and retrieve tokens in such a way that calls to get_tokens_from_store() will return an associative array
	// which contains the access token as 'access_token', the refresh token as 'refresh_token' and the expiry (as a UNIX timestamp) as 'access_token_expires'
	
	// For more information, see the Wiki on GitHub.
	
	public static function get_tokens_from_store() {
		$response = json_decode(file_get_contents(token_store), TRUE);
		return $response;
	}
	
	public static function save_tokens_to_store($tokens) {
		$tokentosave = Array();
		$tokentosave = Array('access_token' => $tokens['access_token'], 'refresh_token' => $tokens['refresh_token'], 'access_token_expires' => (time()+(int)$tokens['expires_in']));
		if (file_put_contents(token_store, json_encode($tokentosave))) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function destroy_tokens_in_store() {
		if (file_put_contents(token_store, "loggedout")) {
			return true;
		} else {
			return false;
		}
		
	}
}

?>
