<?php
/**
 * Template Name: Protected Content
 */

get_header();
?>
    <div class="content">

        <div class="inner-content grid-x">

            <main class="main small-12 large-12 medium-12 cell" role="main">
                <?php
                $contentenabled = false;

                if (shortcode_exists('wpam')) {
                    ?>
                    <div class="login-section grid-container">
                        <?php
                        echo do_shortcode('[wpam recapture="true"]');
                        $account = new Account;
                        if ($account->sessionLogin()) $contentenabled = true;
                        ?>
                    </div>
                    <?php
                } else {
                    $contentenabled = true;
                }

                if ($contentenabled):
                    ?>


                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <?php get_template_part('parts/loop', 'page'); ?>

                <?php endwhile; endif; ?>


                <?php

                endif;

                ?>
            </main> <!-- end #main -->

        </div> <!-- end #inner-content -->

    </div> <!-- end #content -->
<?php

get_footer();

?>