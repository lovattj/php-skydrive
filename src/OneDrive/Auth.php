<?php
namespace OneDrive;

class Auth
{
    const URL_AUTHORIZE = 'https://login.live.com/oauth20_authorize.srf';

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
     * Builds a URL for the user to log in to SkyDrive and get the authorization code, which can then be
     * passed onto get_oauth_token to get a valid oAuth token.
     */
    public function build_oauth_url($callback_uri)
    {
        $params = array(
            'client_id' =>$this->client_id,
            'scope' => 'wl.signin wl.offline_access wl.skydrive_update wl.basic',
            'response_type' => 'code',
            'redirect_uri' => $callback_uri
        );
        $response =self::URL_AUTHORIZE . '?' . http_build_query($params);
        return $response;
    }

    /**
     * Obtains an oAuth token
     * Pass in the authorization code parameter obtained from the inital callback.
     * Returns the oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).
     * @param $auth
     * @param $callback_uri
     * @internal param $client_id
     * @internal param $client_secret
     * @return array
     */
    public function get_oauth_token($auth,$callback_uri)
    {
        $output = "";
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::URL_AUTHORIZE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
            ));
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $params = array(
                'client_id' => $this->client_id,
                'redirect_uri' => $callback_uri,
                'client_secret' => $this->client_secret,
                'code' => $auth,
                'grant_type' => 'authorization_code'
            );

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            $output = curl_exec($ch);
        } catch (\Exception $e) {
            //todo user notice
        }

        return json_decode($output, true);
    }

    /**
     * Attempts to refresh an oAuth token
     * Pass in the refresh token obtained from a previous oAuth request.
     * Returns the new oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).
     */
    public function refresh_oauth_token($refresh,$callback_uri)
    {
        $output = "";
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::URL_AUTHORIZE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
            ));
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $params = array(
                'client_id' => $this->client_id,
                'redirect_uri' => $callback_uri,
                'client_secret' => $this->client_secret,
                'refresh_token' => $refresh,
                'grant_type' => 'refresh_token'
            );

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            $output = curl_exec($ch);
        } catch (\Exception $e) {
            //todo user notice
        }

        $out2 = json_decode($output, true);
        $arraytoreturn = array(
            'access_token' => $out2['access_token'],
            'refresh_token' => $out2['refresh_token'],
            'expires_in' => $out2['expires_in']
        );
        return $arraytoreturn;
    }

} 