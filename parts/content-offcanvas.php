<?php
/**
 * The template part for displaying offcanvas content
 *
 * For more info: http://jointswp.com/docs/off-canvas-menu/
 */
?>

<div class="off-canvas position-right" id="off-canvas" data-off-canvas>


    <div class="grid-x top-icons" style="padding-top: 5px;">

        <div class="cell auto white text-center">
            <?php $facebook = get_social_media('Facebook'); ?>
            <a href="<?php echo $facebook['link']; ?>" target="_blank" class="white-background">
                <img src="<?php echo $facebook['icon']; ?>">
            </a>
        </div>
        <div class="cell auto white no-right-margin text-center">
            <?php $twitter = get_social_media('Twitter'); ?>
            <a href="<?php echo $twitter['link']; ?>" target="_blank" class="white-background">
                <img src="<?php echo $twitter['icon']; ?>">
            </a>
        </div>
        <div class="cell auto white no-right-margin text-center" style="padding-left: 2px;">
            <?php $twitter = get_social_media('LinkedIn'); ?>
            <a href="<?php echo $twitter['link']; ?>" target="_blank" class="white-background">
                <img src="<?php echo $twitter['icon']; ?>">
            </a>
        </div>
    </div>

    <?php joints_off_canvas_nav(); ?>



    <?php if (is_active_sidebar('offcanvas')) : ?>

        <?php dynamic_sidebar('offcanvas'); ?>

    <?php endif; ?>

    <?php joints_top_nav(); ?>

    <div style="padding-left: 1rem; padding-bottom: 0.5rem;">
        <a href="/contact-us/" class="grey button-link">
            <span>Contact Us</span>
        </a>
    </div>

    <div style="padding-left: 0.5rem;">
        <a href="https://www.getplumbedin.co.uk/" target="_blank" class="grey button-link">
            <img src="<?php echo get_icon('plumbed in'); ?>">
        </a>
    </div>

</div>
