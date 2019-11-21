<?php
add_action( 'send_headers', 'tch_strict_transport_security' );

/**

 * Enables the HTTP Strict Transport Security (HSTS) header.

 *

 * @since 1.0.0

 */

function tch_strict_transport_security() {



    header( 'Strict-Transport-Security: max-age=10886400' );



}