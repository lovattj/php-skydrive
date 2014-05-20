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
    public function getFolderFiles($folderId, $sort_by = 'name', $sort_order = 'ascending', $limit = 255, $offset = 0)
    {
        $params = array(
            'sort_by' =>$sort_by,
            'sort_order' => $sort_order,
            'offset' => $offset,
            'limit' => $limit
        );

        $r2s = $this->generateUrl(($folderId ? $folderId : "me/skydrive")."/files",$params);
        $response = $this->curlGet($r2s);

        return $response;
    }

    /**
     * Gets the properties of the folder.
     * @param null $folderId
     * @return mixed
     */
    public function getFolderProperties($folderId=null)
    {
        $r2s = $this->generateUrl($folderId?$folderId:'/me/skydrive');
        $response = $this->curlGet($r2s);
        return $response;
    }

    /**
     * Gets the properties of the file.
     * @param $fileId
     * @return mixed
     */
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
        $r2s = $this->generateUrl("$fileId/shared_read_link");
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

    /**
     * Downloads a file from SkyDrive to the server.
     * @param $fileId
     * @return array
     */
    public function downloadFile($fileId)
    {
        $r2s = $this->generateUrl("$fileId/content");
        $content = file_get_contents($r2s);

        $props = $this->getFileProperties($fileId);
        return array('properties' => $props, 'data' => $content);
    }

    /**
     * Uploads a file from disk.
     * @param $folderId
     * @param $filename
     * @return array|mixed
     */
    public function uploadFile($folderId, $filename)
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
    public function uploadFileFromUrl($sourceUrl, $folderId, $filename)
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

    /**
     * Creates a folder.
     * @param $folderid
     * @param $foldername
     * @param string $description
     * @return mixed
     */
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