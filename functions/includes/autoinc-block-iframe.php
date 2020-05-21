<?php
add_action('acf/init', 'acfgbc_Block_iFrame');
function acfgbc_Block_iFrame()
{
    if (!function_exists('acf_register_block')) {
        return;
    }
    acf_register_block(array(
        'name' => 'acfgbcBlock_iFrame',
        'title' => __('Block – iFrame'),
        'description' => __('Block – iFrame'),
        'render_callback' => 'acfgbc_Block_iFrame_rc',
        'category' => 'technickswpwordpresstheme',
        'icon' => 'tagcloud',
        'mode' => 'preview',
        'supports' => array('align' => false, 'multiple' => true,),
        'keywords' => array('Row', 'Common'),
    ));
}

function acfgbc_Block_iFrame_rc($block, $content = '', $is_preview = false)
{
    if ($is_preview) {
        include_once get_template_directory() . '/parts/blocks/editor/styles.php';
    }
    include get_template_directory() . '/parts/blocks/BlockiFrame.php';
}