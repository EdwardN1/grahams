<?php
/**
 * The template for displaying the footer.
 *
 * Comtains closing divs for header.php.
 *
 * For more info: https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>

<footer class="footer" role="contentinfo">

    <div class="explore-row">

        <div class="title-container">
            <div class="grid-container title">
                Explore Our Site
            </div>
        </div>
        <div class="grid-container">
            <div class="grid-x">
                <div class="logo-phone-contact cell shrink">
                    <div>
                        <img src="<?php echo get_field('logo_reversed', 'option')['url']; ?>">
                    </div>
                    <div class="telephone-number">
                        <a href="<?php echo get_field('telephone_link', 'option'); ?>"><?php echo get_field('telephone_number', 'option') ?></a>
                    </div>
                </div>
                <div class="menus cell auto">
                    <div class="grid-x">
                        <div class="cell auto">
                            <h3>Product Categories</h3>
                            <?php wp_nav_menu(array('theme_location' => "footer-product-categories",)); ?>
                        </div>
                        <div class="cell auto">
                            <h3>Website</h3>
                            <?php wp_nav_menu(array('theme_location' => "footer-website",)); ?>
                        </div>
                        <div class="cell auto">
                            <h3>Our Services</h3>
                            <?php wp_nav_menu($args = array('theme_location' => "footer-services",)); ?>
                        </div>
                        <div class="cell auto">
                            <h3>Other Graham Sites</h3>
                            <?php wp_nav_menu(array('theme_location' => "footer-other-sites",)); ?>
                        </div>
                    </div>
                </div>
                <div class="social-contact cell shrink">
                    <div class="follow-us">
                        Follow Us
                    </div>
                    <?php
                    $facebook = get_social_media('Facebook');
                    $twitter = get_social_media('Twitter');
                    ?>
                    <a href="<?php echo $facebook['link']; ?>" target="_blank" class="social-link"><img
                                src="<?php echo $facebook['icon-reversed']; ?>"></a>
                    &nbsp;
                    <a href="<?php echo $twitter['link']; ?>" target="_blank" class="social-link"><img
                                src="<?php echo $twitter['icon-reversed']; ?>"></a>
                </div>
            </div>
        </div>
    </div>

   <div class="copyright">
       <div class="grid-container">
           <div class="grid-x">
               <div class="footer-logo cell shrink">
                   <a href="/" style="padding-right: 1.5rem;"><img src="<?php echo get_field('logo','option')['url'];?>" style="width: 100px; height: auto;"></a>
               </div>
               <div class="notice cell auto">
                   <?php echo get_field('copyright_notice',option);?>
               </div>
               <div class="cell shrink footer-menu">
	               <?php wp_nav_menu(array('theme_location' => "footer",)); ?>
               </div>
           </div>
       </div>
   </div>

</footer> <!-- end .footer -->

</div>  <!-- end .off-canvas-content -->

</div> <!-- end .off-canvas-wrapper -->

<?php wp_footer(); ?>

<?php
$google_analytics = get_field('google_analytics', 'option');
if ($google_analytics) {
    echo $google_analytics;
}
?>

</body>

</html> <!-- end page -->