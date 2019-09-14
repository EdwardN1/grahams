<?php
add_action( 'acf/init', 'acfgbc_BlockProductCarousel' );
function acfgbc_BlockProductCarousel() {
	if ( ! function_exists( 'acf_register_block' ) ) {
		return;
	}
	acf_register_block( array(
		'name'            => 'acfgbcBlockProductCarousel',
		'title'           => __( 'Block Product Carousel' ),
		'description'     => __( 'Block Product Carousel' ),
		'render_callback' => 'acfgbc_BlockProductCarousel_rc',
		'category'        => 'technickswpwordpresstheme',
		'icon'            => 'tagcloud',
		'mode'            => 'preview',
		'supports'        => array( 'align' => false, 'multiple' => true, ),
		'keywords'        => array( 'Row', 'Common' ),
	) );
}
function acfgbc_BlockProductCarousel_rc( $block, $content = '', $is_preview = false ) {
	if ($is_preview) {
		include_once get_template_directory().'/parts/blocks/editor/styles.php';
	}
	include get_template_directory(). '/parts/blocks/BlockProductCarousel.php';
}
