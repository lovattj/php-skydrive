php-skydrive
============

A PHP client library for Microsoft SkyDrive/OneDrive.
This is very much a work in progress!
See the Wiki for updates and documentation!

Update 19-May-2014 - Composer.
- By request, I've created a composer.json and published "lovattj/php-skydrive": "v1.0" on Packagist.
- I've also added an autoloader definition.
- I've not got much Composer or autoloading experience, so if it doesn't work please let me know!

Update 18-May-2014 - IMPORTANT CHANGE.
- `get_folder` now returns a multidimensional array.
- `$array['data']` is now the array of files.
- `$array['paging']` is an array of page tokens used for pagination.
- Previous behavior was that `$array` on it's own was the array of files only.
- Please update code accordingly and see the Wiki or example project for more information.

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

How to install manually:
- Clone project
- Edit "src/functions.inc.php" and include your Live Client ID, Secret Key and oAuth callback URL in relevant places.
- Require "src/functions.inc.php", create an object and start calling functions!

How to install via Composer:
- Require "lovattj/php-skydrive": "v1.0" in your composer.json
- Edit "vendor/lovattj/php-skydrive/src/functions.inc.php" and include your Live Client ID, Secret Key and oAuth callback URL in relevant places.
- Require "vendor/lovattj/php-skydrive/src/functions.inc.php", create an object and start calling functions!

How to get the example running:
- Deploy to your web server
- Make sure the file "example/tokens" is read+writable by your web user.
- Edit "src/functions.inc.php" and include your Live Client ID, Secret Key and oAuth callback URL in relevant places.
- Hit "example/index.php" and follow the prompts to login with SkyDrive!

Questions/Comments:
- E-Mail me at php-skydrive@jlls.info
