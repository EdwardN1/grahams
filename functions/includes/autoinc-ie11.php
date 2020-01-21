<?php
global $ie11;
if (isset($_SERVER['HTTP_USER_AGENT'])):
    if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~Trident/7.0(; Touch)?; rv:11.0~', $_SERVER['HTTP_USER_AGENT'])) {
        $ie11 = true;
    } else {
        $ie11 = false;
    }
else:
    $ie11 = false;
endif;