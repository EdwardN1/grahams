<?php
add_action( 'send_headers', 'tch_cors_security' );


function tch_cors_security() {

    header( "Access-Control-Allow-Origin: *" );

}
