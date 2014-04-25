php-skydrive
============

A PHP client library for Microsoft SkyDrive/OneDrive.
This is very much a work in progress!
See the Wiki for updates and documentation!

Update 25-Apr-2014:
- Added missing "$this->" before curl_get/curl_delete call in function.inc.php
- Renaming php-skydrive to php-onedrive
- Replace skydrive by onedrive in documentation

Update 19-Feb-2014:
- Yes! It works with OneDrive fine (new name for SkyDrive).
- Added support for refresh tokens.
- You can now build apps that don't require re-authentication every 60 minutes.
- Also implemented functions to help you build a token store, to help you store tokens if you want to.
- See the Wiki for more information - there are some major changes.

Update 1-Nov-2013:
- Converted into a Class
- First, edit `functions.inc.php` and include your Live Client ID, Secret Key and oAuth callback URL.
- Call `skydrive_auth::build_oauth_url();` to obtain an oAuth URL.
- Redirect your user to that URL, then call `skydrive_auth::get_oauth_token($_GET['code']);` on the callback to obtain an access token.
- Once you have an access token, create a new object - `$sd = new skydrive($access_token);`.
- Then call the specified method - `$response = $sd->get_folder();`
- Exceptions will be thrown when a non-200 HTTP status code is encountered.
- I'll update the Wiki with new class documentation. Thanks!

System Requirements:
- PHP 5 (I tested with 5.3.3)
- cURL extension for PHP

How to install:
- Clone project
- Edit "functions.inc.php" and include your Live Client ID, Secret Key and oAuth callback URL in relevant places.
- Start calling functions!
- A very basic test example is included.

How to get the example running:
- Deploy to your web server
- Make sure the file "example/tokens" is read+writable by your web user.
- Edit "functions.inc.php" and include your Live Client ID, Secret Key and oAuth callback URL in relevant places.
- Hit "example/index.php" and follow the prompts to login with SkyDrive!

Questions/Comments:
- E-Mail me at php-skydrive@jlls.info
