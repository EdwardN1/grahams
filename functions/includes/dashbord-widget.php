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
    echo '<form action="'.admin_url('admin-post.php').'" method="post"> <input type="hidden" name="action" value="import_product_images"><input type="submit" value="Import Product Images"></form>';
    echo '<form action="'.admin_url('admin-post.php').'" method="post"> <input type="hidden" name="action" value="import_variation_images"><input type="submit" value="Import Variation Images"></form>';
    echo '<form action="'.admin_url('admin-post.php').'" method="post"> <input type="hidden" name="action" value="import_category_images"><input type="submit" value="Import Category Images"></form>';
	echo '<form action="'.admin_url('admin-post.php').'" method="post"> <input type="hidden" name="action" value="link_images"><input type="submit" value="Link Images to Products"></form>';
    echo '<form action="'.admin_url('admin-post.php').'" method="post"> <input type="hidden" name="action" value="link_category_images"><input type="submit" value="Link Images to Categories"></form>';
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

function db_import_product_images_handler() {
    importProductImages();
    echo 'Product Images Created/Updated <br><a href="/wp-admin/">Return to dashboard</a>';
}

function db_import_variation_images_handler() {
    importVariationImages();
    echo 'Variation Images Created/Updated <br><a href="/wp-admin/">Return to dashboard</a>';
}

function db_import_category_images_handler() {
    echo importCategoryImages();
    echo 'Category Images Created/Updated <br><a href="/wp-admin/">Return to dashboard</a>';
}

function db_link_images_handler() {
	echo linkProductImages();
	echo 'Images Linked<br><a href="/wp-admin/">Return to dashboard</a>';
}

function db_link_category_images_handler() {
    echo linkCategoryImages();
    echo 'Images Linked<br><a href="/wp-admin/">Return to dashboard</a>';
}

add_action( 'admin_post_create_categories', 'db_create_categories_handler' );
add_action( 'admin_post_sort_categories', 'db_sort_categories_handler' );
add_action( 'admin_post_create_products', 'db_create_products_handler' );
add_action( 'admin_post_import_product_images', 'db_import_product_images_handler' );
add_action( 'admin_post_import_variation_images', 'db_import_variation_images_handler' );
add_action( 'admin_post_import_category_images', 'db_import_category_images_handler' );
add_action( 'admin_post_link_images', 'db_link_images_handler' );
add_action( 'admin_post_link_category_images', 'db_link_category_images_handler' );
