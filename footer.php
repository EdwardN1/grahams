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
        <div class="grid-container">
            <div class="title">
                Explore Our Site
            </div>
            <div class="grid-x">
                <div class="logo-phone-contact cell auto">
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
                            <?php wp_nav_menu(array('theme_location' => "footer-product-categories", )); ?>
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
                <div class="social-contact cell auto">

                </div>
            </div>
        </div>
    </div>

    <div class="inner-footer grid-x grid-margin-x grid-padding-x">

        <div class="small-12 medium-12 large-12 cell">
            <nav role="navigation">
                <?php joints_footer_links(); ?>
            </nav>
        </div>

        <div class="small-12 medium-12 large-12 cell">
            <p class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.</p>
        </div>

    </div> <!-- end #inner-footer -->

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