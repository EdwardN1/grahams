<?php
/**
 * Created by PhpStorm.
 * User: Edward Nickerson
 * Date: 24/07/2019
 * Time: 07:51
 */

add_filter( 'block_categories', function( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'technickswpwordpresstheme',
				'title' => __( 'Technicks WP WordPress Theme Blocks', 'technickswpwordpressthemeblocks' ),
			),
		)
	);
}, 10, 2 );
// Update CSS within in Admin
function admin_style() {
	$version=filemtime(get_template_directory().'/assets/styles/style.css');
	wp_enqueue_style('technickswpwordpresstheme-admin-styles', get_template_directory_uri().'/assets/styles/admin.css?v='.$version);
}
add_action('admin_enqueue_scripts', 'admin_style');