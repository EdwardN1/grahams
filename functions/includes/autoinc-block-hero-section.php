<?php
/**
 * Created by PhpStorm.
 * User: Edward Nickerson
 * Date: 24/07/2019
 * Time: 07:55
 */

add_action( 'acf/init', 'acfgbc_BlockHeroSection' );
function acfgbc_BlockHeroSection() {
	if ( ! function_exists( 'acf_register_block' ) ) {
		return;
	}
	acf_register_block( array(
		'name'            => 'acfgbcBlockHeroSection',
		'title'           => __( 'Block – Hero Section' ),
		'description'     => __( 'Block – Hero Section' ),
		'render_callback' => 'acfgbc_BlockHeroSection_rc',
		'category'        => 'technickswpwordpresstheme',
		'icon'            => 'tagcloud',
		'mode'            => 'edit',
		'supports'        => array( 'align' => false, 'multiple' => true, ),
		'keywords'        => array( 'Row', 'Common' ),
	) );
}
function acfgbc_BlockHeroSection_rc( $block, $content = '', $is_preview = false ) {
	if ($is_preview) {
		include_once get_template_directory().'/parts/blocks/editor/styles.php';
	}
	include get_template_directory(). '/parts/blocks/BlockHeroSection.php';
}