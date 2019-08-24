<?php
/*// *******************  ACF Google Maps function ****************** //*/


function ritz_acf_google_map_api($api)
{

	$api['key'] = '';

	$google_maps_api_key = get_field('google_maps_api_key','option');

	if($google_maps_api_key) {
	    $api = $google_maps_api_key;
    }

	return $api;

}

add_filter('acf/fields/google_map/api', 'ritz_acf_google_map_api');