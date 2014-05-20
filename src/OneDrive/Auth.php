<?php
namespace OneDrive;

class Auth
{
    const URL_AUTHORIZE = 'https://login.live.com/oauth20_authorize.srf';
    const URL_TOKEN     = 'https://login.live.com/oauth20_token.srf';

    protected $client_id;
    protected $client_secret;

    /**
     * @param array $credentials - [id,secret]
     * @throws OneDriveException
     */
    public function __construct($credentials)
    {
        if (!array_key_exists('id',$credentials) || !array_key_exists('secret',$credentials))
            throw new OneDriveException('Incorrect app credentials');

        $this->client_id = $credentials['id'];
        $this->client_secret = $credentials['secret'];
    }

    /**
     * Builds a URL for the user to log in to SkyDrive and get the authorization code, which can then be passed onto get_oauth_token to get a valid oAuth token.
     * @param $callback_uri
     * @param string|array $scopes - Can be array of \OneDrive\Enum\Scopes
     * @return string
     */
    public function buildOauthUrl($callback_uri, $scopes = 'wl.basic wl.signin wl.skydrive_update wl.offline_access')
    {
        if (is_array($scopes)){
            $scopes = implode(' ',$scopes);
        }

        $params = array(
            'client_id' =>$this->client_id,
            'scope' => $scopes,
            'response_type' => 'code',
            'redirect_uri' => $callback_uri
        );
        return self::URL_AUTHORIZE . '?' . http_build_query($params);
    }

    /**
     * Obtains an oAuth token
     * Pass in the authorization code parameter obtained from the inital callback.
     * Returns the oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).
     * @param $authCode
     * @param $callback_uri
     * @return array
     * @throws \Exception
     */
    public function getOauthToken($authCode,$callback_uri)
    {
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $callback_uri,
            'client_secret' => $this->client_secret,
            'code' => $authCode,
            'grant_type' => 'authorization_code'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL_TOKEN);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = curl_exec($ch);
        if (!$response){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }

        return json_decode($response, true);
    }

    /**
     * Attempts to refresh an oAuth token
     * Pass in the refresh token obtained from a previous oAuth request.
     * Returns the new oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).
     */
    public function refreshOauthToken($refresh,$callback_uri=null)
    {
        $params = array(
            'client_id' => $this->client_id,
//            'redirect_uri' => $callback_uri,
            'client_secret' => $this->client_secret,
            'refresh_token' => $refresh,
            'grant_type' => 'refresh_token'
        );
        if ($callback_uri){
            $params['redirect_uri'] = $callback_uri;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL_TOKEN);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = curl_exec($ch);
        if (!$response){
            throw new \Exception(curl_error($ch),curl_errno($ch));
        }

        return json_decode($response, true);
    }

} 