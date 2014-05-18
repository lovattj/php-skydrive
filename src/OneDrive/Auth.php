<?php
namespace OneDrive;

class Auth
{
    protected $client_id;
    protected $client_secret;

    public function __construct($client_id,$client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    /**
     * Builds a URL for the user to log in to SkyDrive and get the authorization code, which can then be
     * passed onto get_oauth_token to get a valid oAuth token.
     */
    public function build_oauth_url($callback_uri)
    {
        $response = "https://login.live.com/oauth20_authorize.srf?client_id=".$this->client_id."&scope=wl.signin%20wl.offline_access%20wl.skydrive_update%20wl.basic&response_type=code&redirect_uri=" . urlencode($callback_uri);
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
        $client_secret_enc = urlencode($this->client_secret);
        $callback_uri_enc = urlencode($callback_uri);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://login.live.com/oauth20_token.srf");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
            ));
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $data = "client_id=".$this->client_id."&redirect_uri=" . $callback_uri_enc . "&client_secret=" . $client_secret_enc . "&code=$auth&grant_type=authorization_code";
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
        } catch (Exception $e) {
        }

        $out2 = json_decode($output, true);
        $arraytoreturn = array('access_token' => $out2['access_token'], 'refresh_token' => $out2['refresh_token'], 'expires_in' => $out2['expires_in']);
        return $arraytoreturn;
    }

    /**
     * Attempts to refresh an oAuth token
     * Pass in the refresh token obtained from a previous oAuth request.
     * Returns the new oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).
     */
    public function refresh_oauth_token($refresh,$callback_uri)
    {
        $arraytoreturn = array();
        $callback_uri_enc = urlencode($callback_uri);
        $client_secret_enc = urlencode($this->client_secret);
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


            $data = "client_id=".$this->client_id."&redirect_uri=$callback_uri_enc&client_secret=$client_secret_enc&refresh_token=$refresh&grant_type=refresh_token";
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
        } catch (Exception $e) {
        }

        $out2 = json_decode($output, true);
        $arraytoreturn = array('access_token' => $out2['access_token'], 'refresh_token' => $out2['refresh_token'], 'expires_in' => $out2['expires_in']);
        return $arraytoreturn;
    }

} 