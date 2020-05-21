<?php
/**
 * Block template file:
 *
 * Acfgbcblock Iframe Block Template.
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'acfgbcblock-iframe-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$classes = 'block-acfgbcblock-iframe';
if (!empty($block['className'])) {
    $classes .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $classes .= ' align' . $block['align'];
}
?>

<style type="text/css">
    <?php echo '#' . $id; ?>
    {
    /* Add styles that use ACF values here */
    }
</style>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($classes); ?>">
    <?php $attr = ''; ?>
    <?php if (have_rows('attributes')) : ?>
        <?php $attr = ' '; ?>
        <?php while (have_rows('attributes')) : the_row(); ?>
            <?php $name = get_sub_field('name'); ?>
            <?php $value = get_sub_field('value'); ?>
            <?php if ($value == ''): ?>
                <?php $attr .= $name . ' ' ?>
            <?php else : ?>
                <?php $attr .= $name . '="' . $value . '" ' ?>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php else : ?>
        <?php // no rows found ?>
    <?php endif; ?>
    <div class="grid-x">
        <div class="tch-iframe <?php the_field('css_class'); ?>">
            <iframe src="<?php the_field('src'); ?>"<?php echo $attr; ?>></iframe>
        </div>
    </div>


</div>
