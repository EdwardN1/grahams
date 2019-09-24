<?php
add_action( 'wp_dashboard_setup', 'my_custom_dashboard_widgets' );

function my_custom_dashboard_widgets() {
	global $wp_meta_boxes;

	wp_add_dashboard_widget( 'custom_help_widget', 'API Support', 'custom_dashboard_help' );
}

function custom_dashboard_help() {
	echo '<p>The button below will import and over write your product data from ePim:</p>';
	echo '<form action="'.admin_url('admin-post.php').'" method="post"> <input type="hidden" name="action" value="create_categories"><input type="submit" value="Create and Update Product Categories"></form>';
	echo '<form action="'.admin_url('admin-post.php').'" method="post"> <input type="hidden" name="action" value="sort_categories"><input type="submit" value="Sort Product Categories"></form>';
	echo '<form action="'.admin_url('admin-post.php').'" method="post"> <input type="hidden" name="action" value="create_products"><input type="submit" value="Create and Update Products"></form>';
}

function db_create_categories_handler() {
	create_categories();
	echo 'Categories Created and Updated <br><a href="/wp-admin/">Return to dashboard</a>';
}

function db_sort_categories_handler() {
	sort_categories();
	echo 'Categories Sorted <br><a href="/wp-admin/">Return to dashboard</a>';
}

function db_create_products_handler() {
	create_products();
	echo 'Products Created/Updated <br><a href="/wp-admin/">Return to dashboard</a>';
}

add_action( 'admin_post_create_categories', 'db_create_categories_handler' );
add_action( 'admin_post_sort_categories', 'db_sort_categories_handler' );
add_action( 'admin_post_create_products', 'db_create_products_handler' );
