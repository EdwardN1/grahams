<?php
function site_scripts() {
  global $wp_styles; // Call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way
        
    // Adding scripts file in the footer
    wp_enqueue_script('js-cookie','https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js', array('jquery'),null,true);
    wp_enqueue_script( 'site-js', get_template_directory_uri() . '/assets/scripts/scripts.js', array( 'js-cookie' ), filemtime(get_template_directory() . '/assets/scripts/js'), true );
    wp_localize_script(
        'site-js',
        'mailing_ajax_object',
        [
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
            'security'  => wp_create_nonce( 'mailing-security-nonce' ),
        ]
    );
    // Register main stylesheet
    wp_enqueue_style( 'site-css', get_template_directory_uri() . '/assets/styles/style.css', array(), filemtime(get_template_directory() . '/assets/styles/style.css'), 'all' );

    // Comment reply script for threaded comments
    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
      wp_enqueue_script( 'comment-reply' );
    }
}
add_action('wp_enqueue_scripts', 'site_scripts', 999);

add_action('admin_enqueue_scripts', 'admin_enqueue');
function admin_enqueue($hook) {
    if ('toplevel_page_epim-admin-page' !== $hook) {
        return;
    }
	wp_enqueue_script('jquery-ui-datepicker');
	wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	wp_enqueue_style('jquery-ui');
	wp_enqueue_script('process_queue_script', get_template_directory_uri() . '/assets/scripts/processQueue.js');
    wp_enqueue_script('epim_admin_scripts', get_template_directory_uri() . '/assets/scripts/admin.js','process_queue_script', '1.0.1');
	/*$params = array(
		'ajaxurl' => admin_url('admin-ajax.php', $protocol),
		'ajax_nonce' => wp_create_nonce('epim-graham'),
	);
	wp_localize_script( 'my_blog_script', 'ajax_object', $params );*/
	wp_localize_script(
		'process_queue_script',
		'epim_ajax_object',
		[
			'ajax_url'  => admin_url( 'admin-ajax.php' ),
			'security'  => wp_create_nonce( 'epim-security-nonce' ),
		]
	);
}