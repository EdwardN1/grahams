<?php


add_action('wp_ajax_get_all_categories', 'ajax_get_api_all_categories');
add_action('wp_ajax_get_all_attributes', 'ajax_get_api_all_attributes');
add_action('wp_ajax_get_all_products', 'ajax_get_api_all_products');
add_action('wp_ajax_get_product', 'ajax_get_api_product');
add_action('wp_ajax_get_category', 'ajax_get_api_category');
add_action('wp_ajax_get_picture', 'ajax_get_api_picture');
add_action('wp_ajax_get_variation', 'ajax_get_api_variation');


function ajax_get_api_all_categories() {
    $jsonResponse = get_api_all_categories();
    $response = $jsonResponse;
    header( "Content-Type: application/json" );
    echo json_encode($response);
    exit;
}

function ajax_get_api_all_attributes() {
    $jsonResponse = get_api_all_attributes();
    $response = $jsonResponse;
    header( "Content-Type: application/json" );
    echo json_encode($response);
    exit;
}

function ajax_get_api_all_products() {
    $jsonResponse = get_api_all_products();
    $response = $jsonResponse;
    header( "Content-Type: application/json" );
    echo json_encode($response);
    exit;
}

function ajax_get_api_product() {
    if(!empty($_POST['ID'])) {
        $jsonResponse = get_api_product($_POST['ID']);
        $response = $jsonResponse;
        header( "Content-Type: application/json" );
        echo json_encode($response);
    } else {
        echo 'error no ID supplied';
    }
    exit;
}

function ajax_get_api_category() {
    if(!empty($_POST['ID'])) {

        $jsonResponse = get_api_category($_POST['ID']);
        $response = $jsonResponse;
        header( "Content-Type: application/json" );
        echo json_encode($response);
    } else {
        echo 'error no ID supplied';
    }
    exit;
}

function ajax_get_api_picture() {
    if(!empty($_POST['ID'])) {
        error_log('Getting Picture: '.$_POST['ID']);
        $jsonResponse = get_api_picture($_POST['ID']);
        $response = $jsonResponse;
        header( "Content-Type: application/json" );
        echo json_encode($response);
    } else {
        error_log('error no ID supplied in ajax_get_api_picture');
    }
    exit;
}

function ajax_get_api_variation() {
    if(!empty($_POST['ID'])) {
        $jsonResponse = get_api_variation($_POST['ID']);
        $response = $jsonResponse;
        header( "Content-Type: application/json" );
        echo json_encode($response);
    } else {
        echo 'error no ID supplied';
    }
    exit;
}