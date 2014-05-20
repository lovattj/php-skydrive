<?php
namespace OneDrive;

use OneDrive\Enum\StatusCodes;

/**
 * Class Manager
 * @package OneDrive
 * @see http://msdn.microsoft.com/en-us/library/dn659752.aspx
 */
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

    public function __construct($credentials,$passed_access_token=null)
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

    /**
     * Checking is token valid. If no - refresh it
     */
    public function refreshToken()
    {   //todo thing about global catch and refresh
        try {
            $this->getQuota();
        }catch(OneDriveException $exc){
            if ($exc->getCode() == StatusCodes::REQUEST_TOKEN_EXPIRED){
                $this->tokens = $this->auth->refreshOauthToken($this->tokens['refresh_token']);
            }
        }
    }

    //<editor-fold desc="filesystem">

    /**
     * Gets the contents of a SkyDrive folder.
     * @param $folderId
     * @param string $sort_by
     * @param string $sort_order
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws OneDriveException
     */
    public function getFolder($folderId, $sort_by = 'name', $sort_order = 'ascending', $limit = 255, $offset = 0)
    {
        $params = array(
            'sort_by' =>$sort_by,
            'sort_order' => $sort_order,
            'offset' => $offset,
            'limit' => $limit
        );

        $r2s = $this->generateUrl(($folderId ? $folderId : "me/skydrive")."/files",$params);
        $response = $this->curlGet($r2s);

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

    /**
     * Gets the remaining quota of your SkyDrive account.
     * @return array
     * @throws OneDriveException
     */
    public function getQuota()
    {
        $r2s = $this->generateUrl("me/skydrive/quota");
        $response = $this->curlGet($r2s);
        return $response;
    }

    // Gets the properties of the folder.
    // Returns an array of folder properties.
    // You can pass null as $folderid to get the properties of your root SkyDrive folder.

    public function getFolderProperties($folderid)
    {
        $r2s = $this->generateUrl(($folderid?$folderid:'/me/skydrive'));
        $response = $this->curlGet($r2s);
        return $response;
    }

    // Gets the properties of the file.
    // Returns an array of file properties.

    public function getFileProperties($fileId)
    {
        $r2s = $this->generateUrl($fileId);
        $response = $this->curlGet($r2s);
        return $response;
    }

    /**
     * Gets a shared read link to the item.
     * @param $fileId
     * @return mixed
     * @throws OneDriveException
     */
    public function getSharedReadLink($fileId)
    {
        $r2s = $this->generateUrl("$fileId/shared_edit_link");
        $response = $this->curlGet($r2s);
        return $response['link'];
    }


    /**
     * Gets a shared edit (read-write) link to the item.
     * @param $fileid
     * @return mixed
     * @throws OneDriveException
     */
    public function getSharedEditLink($fileid)
    {
        $r2s = $this->generateUrl("$fileid/shared_edit_link");
        $response = $this->curlGet($r2s);
        return $response['link'];
    }

    // Deletes an object.

    public function deleteObject($fileId)
    {
        $r2s = $this->generateUrl($fileId);
        $response = $this->curlDelete($r2s);
        return $response;
    }

    // Downloads a file from SkyDrive to the server.
    // Pass in a file ID.
    // Returns a multidimensional array:
    // ['properties'] contains the file metadata and ['data'] contains the raw file data.

    public function download($fileId)
    {
        $r2s = $this->generateUrl("$fileId/content");
        $content = file_get_contents($r2s);

        $props = $this->getFileProperties($fileId);
        return array('properties' => $props, 'data' => $content);
    }


    // Uploads a file from disk.
    // Pass the $folderid of the folder you want to send the file to, and the $filename path to the file.
    // Also use this function for modifying files, it will overwrite a currently existing file.

    public function putFile($folderId, $filename)
    {
        $r2s = $this->generateUrl("$folderId/files/$filename");
        $response = $this->curlPut($r2s, $filename);
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
    public function putFileFromUrl($sourceUrl, $folderId, $filename)
    {
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
        $response = $this->curlPut($r2s, $tempFilename);
        unlink($tempFilename);
        return $response;
    }


    // Creates a folder.
    // Pass $folderid as the containing folder (or 'null' to create the folder under the root).
    // Also pass $foldername as the name for the new folder and $description as the description.
    // Returns the new folder metadata or throws an exception.

    public function createFolder($folderid, $foldername, $description = "")
    {
        $r2s = self::URL_BASE . ($folderid? $folderid:"me/skydrive");

        $params = array(
            'name' => $foldername,
            'description' => $description
        );

        $response = $this->curlPost($r2s, $params);
        return $response;
    }

    //</editor-fold desc="filesystem">

    //<editor-fold desc="curl">

    protected function curlGet($uri)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if ($response === false){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }

        if ($tmp = json_decode($response,true)){
            $response = $tmp;
        }

        $this->checkResponse($response,curl_getinfo($ch, CURLINFO_HTTP_CODE));
        return $response;
    }

    /**
     * Internally used function to make a POST request to SkyDrive.
     */
    protected function curlPost($uri, $inputarray)
    {
        $trimmed = json_encode($inputarray);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->tokens['access_token'],
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $trimmed);
        $response = curl_exec($ch);
        if (!$response){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }

        $response = json_decode($response, true);
        $this->checkResponse($response,curl_getinfo($ch, CURLINFO_HTTP_CODE));
        return $response;
    }


    /**
     * Internally used function to make a PUT request to SkyDrive.
     * @param $uri
     * @param $fp
     * @throws \Exception
     * @return array|mixed
     */
    protected function curlPut($uri, $fp)
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
        $response = curl_exec($ch);
        if (!$response){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }

        $response = json_decode($response, true);
        $this->checkResponse($response,curl_getinfo($ch, CURLINFO_HTTP_CODE));
        return $response;
    }

    /**
     * Internally used function to make a DELETE request to SkyDrive.
     * @param $uri
     * @throws \Exception
     * @return array|mixed
     */
    protected function curlDelete($uri)
    {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        $response = curl_exec($ch);
        if (!$response){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }

        $response = json_decode($response, true);
        $this->checkResponse($response,curl_getinfo($ch, CURLINFO_HTTP_CODE));
        return $response;
    }
    //</editor-fold desc="curl">


    protected function generateUrl($path, array $params = array())
    {
        $params['access_token'] = $this->tokens['access_token'];

        return rtrim(self::URL_BASE,'/\\') . '/' . rtrim($path,'/\\'). '?' .http_build_query($params);
    }

    protected function checkResponse($response,$code)
    {
        if (is_array($response) && array_key_exists('error',$response)){
            $error = $response['error'];
            throw new OneDriveException("[{$error['code']}] {$error['message']}",$code);
        }
    }
}