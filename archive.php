<?php
/**
 * Displays archive pages if a speicifc template is not set.
 *
 * For more info: https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_header(); ?>

    <div class="grid-container archive">


        <header>
            <h1 class="page-title"><?php the_archive_title(); ?></h1>
            <?php //the_archive_description('<div class="taxonomy-description">', '</div>');?>
        </header>

        <?php
        $thisTerm = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));

        if($thisTerm) {
            if ($thisTerm->taxonomy == 'grahamscat') {
                ?>
                <div class="grid-container text-center breadcrumbs">
                    <?php
                    $divider = '';
                    if ($thisTerm->parent > 0) {
                        $parentTerm = get_term_by("id", $thisTerm->parent, "grahamscat");
                        echo '<a href="/product-category/' . $parentTerm->slug . '" class="green">' . '«  Back to  ' . $parentTerm->name . '</a>';
                        $divider = ' | ';
                    }
                    foreach (get_terms('grahamscat', array('hide_empty' => false, 'parent' => $thisTerm->term_id)) as $child_term) {
                        echo $divider . '<a href="/product-category/' . $child_term->slug . '" class="green">' . $child_term->name . '</a>';
                        $divider = ' | ';
                    }

                    ?>
                </div>
                <div class="grid-container">
                    <?php echo term_description();?>
                </div>
                <?php
                $gridTerms = get_terms('grahamscat', array('hide_empty' => false, 'parent' => $thisTerm->term_id));
                if ($gridTerms) {
                    ?>
                    <div class="grid-x grid-margin-x grid-padding-x archive-grid small-up-1 medium-up-4 large-up-5">
                        <?php
                        foreach ($gridTerms as $gridTerm) {
                            $catURL = '/product-category/' . $gridTerm->slug;
                            $catIMG = get_field('image', $gridTerm);
                            $catName = $gridTerm->name;
                            ?>
                            <div class="cell panel product-carousel no-border">
                                <div class="carousel-slide">
                                    <a href="<?php echo $catURL; ?>" class="carousel-link-container">
                                        <div class="slide-image">
                                            <img src="<?php echo $catIMG['url']; ?>">
                                        </div>
                                        <div class="slide-title text-center">
                                            <?php echo $catName; ?>
                                        </div>

                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <hr>
                    <?php
                }
            }
        }

        /* foreach( get_terms( 'grahamscat', array( 'hide_empty' => false, 'parent' => 0 ) ) as $parent_term ) {
             // display top level term name
             echo $parent_term->name . '<br>';

             foreach( get_terms( 'grahamscat', array( 'hide_empty' => false, 'parent' => $parent_term->term_id ) ) as $child_term ) {
                 // display name of all childs of the parent term
                 echo '-'.$child_term->name . '<br>';
             }

         }*/
        ?>

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <!-- To see additional archive styles, visit the /parts directory -->
            <?php get_template_part('parts/loop', 'archive-grid'); ?>

        <?php endwhile; ?>

            <?php joints_page_navi(); ?>

        <?php else : ?>

            <?php get_template_part('parts/content', 'missing'); ?>

        <?php endif; ?>

    </div> <!-- end #content -->

<?php get_footer(); ?>