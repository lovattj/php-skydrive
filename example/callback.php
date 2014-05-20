<?php
// This is the page that Windows Live redirects to after a successful login.
// The page needs to call "\OneDrive\Auth::get_oauth_token" with the code returned in the querystring.
// Then this example page calls "\OneDrive\TokenSTokenStore::save_tokens_to_store" to save the tokens to a file (although you can handle them how you want).

require_once __DIR__.'/../vendor/autoload.php';
ob_clean();

if (!file_exists('app-info.json')){
    exit('There is no `app-info.json` file with app credentials');
}

$credentials = json_decode(file_get_contents('app-info.json'),true);
$manager = new \OneDrive\Manager($credentials);

$redirectUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/callback.php';

if (empty($_GET['code'])){
    exit('Query parameter `code` not defined');
}

$response = $manager->getAuth()->getOauthToken($_GET['code'],$redirectUrl);
var_dump($response);
file_put_contents('app-tokens.json',json_encode($response));
echo '<p>Authefication is success</p>';

$content = ob_get_contents();
ob_end_clean();
require_once __DIR__."/template/index.php";

