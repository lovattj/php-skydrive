<?php
namespace OneDrive;

class TokenStore
{
    const TOKEN_STORE_FILE = "app-tokens.json";

    /**
     * Will attempt to grab an access_token from the current token store.
     * If there isn't one then return false to indicate user needs sending through oAuth procedure.
     * If there is one but it's expired attempt to refresh it, save the new tokens and return an access_token.
     * If there is one and it's valid then return an access_token.
     * @return bool
     */
    public static function acquire_token()
    {

        $response = self::get_tokens_from_store();
        if (empty($response['access_token'])) { // No token at all, needs to go through login flow. Return false to indicate this.
            return false;
        } else {
            if (time() > (int)$response['access_token_expires']) { // Token needs refreshing. Refresh it and then return the new one.
                $refreshed = Auth::refresh_oauth_token($response['refresh_token']);
                if (TokenStore::save_tokens_to_store($refreshed)) {
                    $newtokens = \OneDrive\TokenStore::get_tokens_from_store();
                    return $newtokens['access_token'];
                }
            } else {
                return $response['access_token']; // Token currently valid. Return it.
            }
        }
    }


    // These functions provide a gateway to your token store.
    // In it's basic form, the tokens are written simply to a file called "tokens" in the current working directory, JSON-encoded.
    // You can edit the location of the token store by editing the DEFINE entry on line 28.

    // If you want to implement your own token store, you can edit these functions and implement your own code, e.g. if you want to store them in a database.
    // You MUST save and retrieve tokens in such a way that calls to get_tokens_from_store() will return an associative array
    // which contains the access token as 'access_token', the refresh token as 'refresh_token' and the expiry (as a UNIX timestamp) as 'access_token_expires'

    // For more information, see the Wiki on GitHub.

    public static function get_tokens_from_store()
    {
        if (!file_exists(self::TOKEN_STORE_FILE))
            return false;

        $response = json_decode(file_get_contents(self::TOKEN_STORE_FILE), true);
        return $response;
    }

    public static function save_tokens_to_store(array $tokens)
    {
        if (!array_key_exists('access_token',$tokens))  return false;

        $tokens['access_token_expires'] = (time() + (int)$tokens['expires_in']);

        if (file_put_contents(self::TOKEN_STORE_FILE, json_encode($tokens))) {
            return true;
        } else {
            return false;
        }
    }

    public static function destroy_tokens_in_store()
    {
        if (!file_exists(self::TOKEN_STORE_FILE))
            return false;

        if (file_put_contents(self::TOKEN_STORE_FILE, "loggedout")) {
            return true;
        } else {
            return false;
        }

    }
}