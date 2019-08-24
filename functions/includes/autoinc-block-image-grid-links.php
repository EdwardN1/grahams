<?php
add_action( 'acf/init', 'acfgbc_BlockImageGridLinks' );
function acfgbc_BlockImageGridLinks() {
    if ( ! function_exists( 'acf_register_block' ) ) {
        return;
    }
    acf_register_block( array(
        'name'            => 'acfgbcBlockImageGridLinks',
        'title'           => __( 'Block – Image Grid Links' ),
        'description'     => __( 'Block – Image Grid Links' ),
        'render_callback' => 'acfgbc_BlockImageGridLinks_rc',
        'category'        => 'technickswpwordpresstheme',
        'icon'            => 'tagcloud',
        'mode'            => 'preview',
        'supports'        => array( 'align' => false, 'multiple' => true, ),
        'keywords'        => array( 'Row', 'Common' ),
    ) );
}
function acfgbc_BlockImageGridLinks_rc( $block, $content = '', $is_preview = false ) {
    if ($is_preview) {
        include_once get_template_directory().'/parts/blocks/editor/styles.php';
    }
    include get_template_directory(). '/parts/blocks/BlockImageGridLinks.php';
}