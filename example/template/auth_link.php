<?php
    $redirectUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/callback.php';
?>
<div>
    <img src='statics/key-icon.png' width='32px' style='vertical-align: middle;'>
    <span style='vertical-align: middle;'><a href='"<?=$manager->getAuth()->build_oauth_url($redirectUrl)?>"'>Login with SkyDrive</a></span>
</div>