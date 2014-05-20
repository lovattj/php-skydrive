<?php
date_default_timezone_set("Europe/London");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>php-skydrive demo</title>
    <style>
        html, body {
            margin: 0px, 5px, 0px, 5px;
            padding: 0;
            font-family: GeezaPro, Helvetica Neue, Helvetica, Arial, sans-serif

        }
        #headerBar {
            height: 40px;
            background: blue
        }
        #footerBar {
            height: 40px;
            background: red
        }

        #whiteText {
            color: #ffffff;
            font-family: GeezaPro, Helvetica Neue, Helvetica, Arial, sans-serif;
            font-size: 1.5em;
        }

        #tableFiles td{
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <div id="headerBar">
        <div id="whiteText">php-skydrive demo</div>
    </div>
    <br>
    <?= $content; ?>
    <div>
        <a href="/">Index Page</a>
    </div>
</body>
</html>
 