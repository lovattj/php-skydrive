<?php
if (!file_exists('app-info.json')){
    exit('There is no `app-info.json` file with app credentials');
}

$credentials = json_decode(file_get_contents('app-info.json'),true);
$tokens      = json_decode(file_get_contents('app-tokens.json'),true);
$manager     = new \OneDrive\Manager($credentials,$tokens);
$manager->refreshToken();
 