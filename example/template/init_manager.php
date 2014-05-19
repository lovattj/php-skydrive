<?php
if (!file_exists('app-info.json')){
    exit('There is no `app-info.json` file with app credentials');
}

$credentials = json_decode(file_get_contents('app-info.json'),true);
$tokens      = json_decode(file_get_contents('app-tokens.json'),true);
$manager     = new \OneDrive\Manager($credentials,$tokens);
$manager->refreshToken();

if (!$tokens) {

    $redirectUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/callback.php';
?>
    <div>
        <img src="statics/key-icon.png" width="32px" style="vertical-align: middle;">
        <span style="vertical-align: middle;"><a href="<?= $manager->getAuth()->build_oauth_url($redirectUrl); ?>">Login with SkyDrive</a></span>
    </div>
<?php
    exit();
}


 