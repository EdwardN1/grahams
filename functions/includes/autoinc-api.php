<?php


function displayJSON( $apiCall ) {
    $allAttributes = new RecursiveIteratorIterator( new RecursiveArrayIterator( json_decode( $apiCall, true ) ), RecursiveIteratorIterator::SELF_FIRST );
    echo '<div style="padding-bottom: 10px; padding-top: 10px; border-bottom: 1px solid black">';
    echo '<p>';
    $indent=1;
    foreach ( $allAttributes as $key => $val ) {
        if ( is_array( $val ) ) {
            echo "</p><p>$key:<br>";
        } else {
            echo "<span style='padding-left: 1em;'> $key => $val</span><br>";
        }

    }
    echo '</p>';
    echo '</div>';
}

function make_api_call($url) {
    $opts = array(
        'http' => array(
            'method' => "GET",
            'header' => "Ocp-Apim-Subscription-Key: e57667e8bffa4384a6ce53f80521677a"
        )
    );
    $context = stream_context_create( $opts );
    $apiCall = file_get_contents( $url, false, $context );
    return $apiCall;
}

function get_api_updates_since($datetime='2002-10-02T10:00:00-00:00') {
    return make_api_call('https://epim.azure-api.net/Grahams/api/ProductsUpdatedSince?ChangedSinceUTC='.$datetime);
}

function get_api_all_attributes() {
    return make_api_call('https://epim.azure-api.net/Grahams/api/Attributes');
}

function get_api_all_categories() {
    return  make_api_call('https://epim.azure-api.net/Grahams/api/Categories');
}

function get_api_category($id) {
    return make_api_call('https://epim.azure-api.net/Grahams/api/Categories/'.$id);
}

function get_api_picture($id) {
    return make_api_call('https://epim.azure-api.net/Grahams/api/Pictures/'.$id);
}

function get_api_all_products() {
    $apiCall = make_api_call( 'https://epim.azure-api.net/Grahams/api/Products/');
    $allProducts = json_decode($apiCall);
    $TotalResults = $allProducts->TotalResults;
    return make_api_call( 'https://epim.azure-api.net/Grahams/api/Products/?limit='.$TotalResults);
}

function get_api_products_in_category($id) {
    return make_api_call('https://epim.azure-api.net/Grahams/api/ProductsInCategory/'.$id);
}

function get_api_product($id) {
    return make_api_call('https://epim.azure-api.net/Grahams/api/Products/'.$id);
}

function get_api_variation($id) {
    return make_api_call('https://epim.azure-api.net/Grahams/api/Variations/'.$id);
}

function create_categories_object() {

}
