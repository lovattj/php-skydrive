<?php
namespace OneDrive;

class Manager
{
    const URL_BASE = "https://apis.live.net/v5.0/";

    /**
     * @var \OneDrive\Auth
     */
    protected $auth;

    /**
     * @var string
     */
    protected $tokens = '';

    public function __construct($credentials,$passed_access_token)
    {
        $this->auth = new Auth($credentials);
        $this->tokens = $passed_access_token;
    }

    /**
     * @return \OneDrive\Auth
     */
    public function getAuth()
    {
        return $this->auth;
    }

    //<editor-fold desc="filesystem">

    /**
     * Gets the contents of a SkyDrive folder.
     * @param $folderid
     * @param string $sort_by
     * @param string $sort_order
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws OneDriveException
     */
    public function get_folder($folderid, $sort_by = 'name', $sort_order = 'ascending', $limit = 255, $offset = 0)
    {
        $params = array(
            'sort_by' =>$sort_by,
            'sort_order' => $sort_order,
            'offset' => $offset,
            'limit' => $limit
        );

        $r2s = $this->generateUrl(($folderid ? $folderid : "me/skydrive")."/files",$params);
        $response = $this->curl_get($r2s);

        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }

        $arraytoreturn = array();
        $temparray = array();
        if (isset($response['paging']['next'])) {
            parse_str($response['paging']['next'], $parseout);
            $numerical = array_values($parseout);
        }
        if (isset($response['paging']['previous'])) {
            parse_str($response['paging']['previous'], $parseout1);
            $numerical1 = array_values($parseout1);
        }

        foreach ($response as $subarray) {
            foreach ($subarray as $item) {
                if (array_key_exists('id', $item)) {
                    array_push($temparray, $item);
                }
            }
        }
        $arraytoreturn['data'] = $temparray;
        if (isset($numerical)) {
            if ($numerical1[0]) {
                $arraytoreturn['paging'] = array('previousoffset' => $numerical1[0], 'nextoffset' => $numerical[0]);
            } else {
                $arraytoreturn['paging'] = array('previousoffset' => 0, 'nextoffset' => $numerical[0]);
            }
        } else {
            $arraytoreturn['paging'] = array('previousoffset' => 0, 'nextoffset' => 0);
        }
        return $arraytoreturn;
    }

    // Gets the remaining quota of your SkyDrive account.
    // Returns an array containing your total quota and quota available in bytes.

    public function get_quota()
    {
        $r2s = $this->generateUrl("me/skydrive/quota");
        $response = $this->curl_get($r2s);
        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }
        return $response;
    }

    // Gets the properties of the folder.
    // Returns an array of folder properties.
    // You can pass null as $folderid to get the properties of your root SkyDrive folder.

    public function get_folder_properties($folderid)
    {
        $r2s = $this->generateUrl(($folderid?$folderid:'/me/skydrive'));
        $response = $this->curl_get($r2s);

        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }
        return $response;
    }

    // Gets the properties of the file.
    // Returns an array of file properties.

    public function get_file_properties($fileId)
    {
        $r2s = $this->generateUrl($fileId);
        $response = $this->curl_get($r2s);
        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }
        return $response;
    }

    /**
     * Gets a shared read link to the item.
     * @param $fileId
     * @return mixed
     * @throws OneDriveException
     */
    public function get_shared_read_link($fileId)
    {
        $r2s = $this->generateUrl("$fileId/shared_edit_link");
        $response = $this->curl_get($r2s);

        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);

        }
        return $response['link'];
    }


    /**
     * Gets a shared edit (read-write) link to the item.
     * @param $fileid
     * @return mixed
     * @throws OneDriveException
     */
    public function get_shared_edit_link($fileid)
    {
        $r2s = $this->generateUrl("$fileid/shared_edit_link");
        $response = $this->curl_get($r2s);
        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }
        return $response['link'];
    }

    // Deletes an object.

    public function delete_object($fileId)
    {
        $r2s = $this->generateUrl($fileId);
        $response = $this->curl_delete($r2s);
        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }
        return true;
    }

    // Downloads a file from SkyDrive to the server.
    // Pass in a file ID.
    // Returns a multidimensional array:
    // ['properties'] contains the file metadata and ['data'] contains the raw file data.

    public function download($fileid)
    {
        $r2s = $this->generateUrl("$fileid/content");
        $response = $this->curl_get($r2s, "false", "HTTP/1.1 302 Found");

        $props = $this->get_file_properties($fileid);
        $arraytoreturn = array();
        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }
        array_push($arraytoreturn, array('properties' => $props, 'data' => $response));
        return $arraytoreturn;

    }


    // Uploads a file from disk.
    // Pass the $folderid of the folder you want to send the file to, and the $filename path to the file.
    // Also use this function for modifying files, it will overwrite a currently existing file.

    public function put_file($folderId, $filename)
    {
        $r2s = $this->generateUrl("$folderId/files/$filename");
        $response = $this->curl_put($r2s, $filename);

        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }
        return $response;
    }

    /**
     * Upload file directly from remote URL
     *
     * @param string $sourceUrl - URL of the file
     * @param string $folderId - folder you want to send the file to
     * @param string $filename - target filename after upload
     * @throws OneDriveException
     * @return array|mixed
     */
    public function put_file_from_url($sourceUrl, $folderId, $filename)
    {
//        $r2s = self::URL_BASE . $folderId . "/files/" . $filename . "?access_token=" . $this->tokens['access_token'];

        $r2s = $this->generateUrl("$folderId/files/$filename");

        $chunkSizeBytes = 1 * 1024 * 1024; //1MB

        //download file first to tempfile
        $tempFilename = tempnam("/tmp", "UPLOAD");
        $temp = fopen($tempFilename, "w");

        $handle = @fopen($sourceUrl, "rb");
        if ($handle === FALSE) {
            throw new OneDriveException("Unable to download file from " . $sourceUrl);
        }

        while (!feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            fwrite($temp, $chunk);
        }

        fclose($handle);
        fclose($temp);

        //upload to OneDrive
        $response = $this->curl_put($r2s, $tempFilename);
        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);

        }
        unlink($tempFilename);
        return $response;
    }


    // Creates a folder.
    // Pass $folderid as the containing folder (or 'null' to create the folder under the root).
    // Also pass $foldername as the name for the new folder and $description as the description.
    // Returns the new folder metadata or throws an exception.

    public function create_folder($folderid, $foldername, $description = "")
    {
        $r2s = self::URL_BASE . ($folderid? $folderid:"me/skydrive");

        $params = array(
            'name' => $foldername,
            'description' => $description
        );

        $response = $this->curl_post($r2s, $params, $this->tokens['access_token']);
        if (array_key_exists('error', $response)) {
            throw new OneDriveException($response['error'] . " - " . $response['description']);
        }
        return $response;
    }

    //</editor-fold desc="filesystem">

    //<editor-fold desc="curl">

    protected function curl_get($uri, $json_decode_output = "true", $expected_status_code = "HTTP/1.1 200 OK")
    {
        $output = file_get_contents($uri);
        if ($http_response_header[0] == $expected_status_code) {
            if ($json_decode_output == "true") {
                return json_decode($output, true);
            } else {
                return $output;
            }
        } else {
            return array(
                'error' => 'HTTP status code not expected - got ',
                'description' => substr($http_response_header[0], 9, 3)
            );
        }
    }

    /**
     * Internally used function to make a POST request to SkyDrive.
     */
    protected function curl_post($uri, $inputarray, $access_token)
    {
        $trimmed = json_encode($inputarray);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
        ));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $trimmed);
        $output = curl_exec($ch);
        if (!$output){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode == 201) {
            return json_decode($output, true);
        } else {
            return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
        }
    }


    /**
     * Internally used function to make a PUT request to SkyDrive.
     * @param $uri
     * @param $fp
     * @throws \Exception
     * @return array|mixed
     */
    protected function curl_put($uri, $fp)
    {
        $output = "";
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
        if (!$output){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode == "200" || $httpcode == "201") {
            return json_decode($output, true);
        } else {
            return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
        }

    }

    /**
     * Internally used function to make a DELETE request to SkyDrive.
     * @param $uri
     * @throws \Exception
     * @return array|mixed
     */
    protected function curl_delete($uri)
    {
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
        if (!$output){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpcode == 200) {
            return json_decode($output, true);
        } else {
            return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
        }
    }
    //</editor-fold desc="curl">


    protected function generateUrl($path, array $params = array())
    {
        $params['access_token'] = $this->tokens['access_token'];

        return http_build_url(self::URL_BASE,array(
            "path"  => $path,
            "query" => http_build_query($params)
        ));
    }
}