<?php

echo '<!--<link rel="stylesheet" href="https://use.typekit.net/mjd0yzh.css">--><!--imports the fonts into the editor-->';
$version=filemtime(get_template_directory().'/assets/styles/style.css');
echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri().'/assets/styles/style.css?v='.$version.'"/>';