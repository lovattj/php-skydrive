php-skydrive
============

A PHP client library for Microsoft SkyDrive.
This is very much a work in progress!
See the Wiki for updates and documentation!

Update 1-Nov-2013:
- Converted into a Class
- First, edit functions.inc.php and include your Live Client ID, Secret Key and oAuth callback URL.
- To call skydrive_auth::build_oauth_url() to obtain an oAuth URL.
- Redirect your user to that URL, then call skydrive_auth::get_oauth_token on the callback to obtain an access token.
- Once you have an access token, create a new object - $sd = new skydrive($access_token).
- Then call the specified method - $response = $sd->get_folder();
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

Questions/Comments:
- E-Mail me at php-skydrive@jlls.info
