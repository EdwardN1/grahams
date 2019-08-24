<?php
add_action( 'acf/init', 'acfgbc_Block_CTA_Grid' );
function acfgbc_Block_CTA_Grid() {
if ( ! function_exists( 'acf_register_block' ) ) {
return;
}
acf_register_block( array(
'name'            => 'acfgbcBlock_CTA_Grid',
'title'           => __( 'Block – CTA Grid' ),
'description'     => __( 'Block – CTA Grid' ),
'render_callback' => 'acfgbc_Block_CTA_Grid_rc',
'category'        => 'technickswpwordpresstheme',
'icon'            => 'tagcloud',
'mode'            => 'preview',
'supports'        => array( 'align' => false, 'multiple' => true, ),
'keywords'        => array( 'Row', 'Common' ),
) );
}
function acfgbc_Block_CTA_Grid_rc( $block, $content = '', $is_preview = false ) {
if ($is_preview) {
include_once get_template_directory().'/parts/blocks/editor/styles.php';
}
include get_template_directory(). '/parts/blocks/BlockCTAGrid.php';
}