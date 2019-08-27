<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section
 *
 */
?>

<!doctype html>

<html class="no-js" <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8">

    <!-- Force IE to use the latest rendering engine available -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta class="foundation-mq">

    <!-- If Site Icon isn't set in customizer -->
	<?php if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) { ?>
        <!-- Icons & Favicons -->
        <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
        <link href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-icon-touch.png"
              rel="apple-touch-icon"/>
	<?php } ?>

    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<div class="off-canvas-wrapper">

    <!-- Load off-canvas container. Feel free to remove if not using. -->
	<?php get_template_part( 'parts/content', 'offcanvas' ); ?>

    <div class="off-canvas-content" data-off-canvas-content>

        <header class="header" role="banner">

            <div class="top-row">
                <div class="grid-container">
                    <div class="grid-x">
                        <div class="cell auto"><a href="/"><img
                                        src="<?php echo get_field( 'logo', 'option' )['url']; ?>"></a> <span>Your
                            Local Plumbing and Heating Specialist</span>
                        </div>
                        <div class="cell shrink">
                            <div class="grid-x top-icons">
                                <div class="cell shrink grey-background white">
                                    <span>All prices Ex VAT</span>
                                </div>
                                <div class="cell shrink grey-background white">
                                    <a href="/find-branch/">
                                        <img src="<?php echo get_icon( 'target' ); ?>">
                                        <span>Find Branch</span>
                                    </a>
                                </div>
                                <div class="cell shrink grey-background white">
                                    <a href="/contact-us/">
                                        <img src="<?php echo get_icon( 'email' ); ?>">
                                        <span>Contact Us</span>
                                    </a>
                                </div>
                                <div class="cell shrink grey-background white">
                                    <a href="https://www.getplumbedin.co.uk/" target="_blank">
                                        <img src="<?php get_icon( 'plumbed in' ); ?>">
                                    </a>
                                </div>
                                <div class="cell shrink grey-background white">
									<?php $facebook = get_social_media( 'Facebook' ); ?>
                                    <a href="<?php echo $facebook['link']; ?>>" target="_blank">
                                        <img src="<?php $facebook['icon']; ?>">
                                    </a>
                                </div>
                                <div class="cell shrink grey-background white no-right-margin">
									<?php $twitter = get_social_media( 'Twitter' ); ?>
                                    <a href="<?php echo $twitter['link']; ?>>" target="_blank">
                                        <img src="<?php $twitter['icon']; ?>">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="second-row">
                    <div class="grid-x">
                        <div class="cell auto secondary-logo">
                            <img src="<?php echo get_secondary_logo( 'Always Available' ); ?>">
                        </div>
                        <div class="cell shrink telephone-number">
                            <a href="<?php echo get_field( 'telephone_link', 'option' ); ?>">
                                <img src="<?php echo get_icon( 'Call Now' ); ?>">
								<?php echo get_field( 'telephone_number', 'option' ); ?>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="nav-row">
                <!-- This navs will be applied to the topbar, above all content
					 To see additional nav styles, visit the /parts directory -->
				<?php get_template_part( 'parts/nav', 'offcanvas-topbar' ); ?>
            </div>

            <div class="bottom-row">
                <div class="grid-x">
                    <div class="cell auto">
                        <img src="<?php echo get_icon( 'checklist' ); ?>">
                        Get the products you're after
                    </div>
                    <div class="cell auto">
                        <img src="<?php echo get_icon( 'stopwatch' ); ?>">
                        Get the products you're after
                    </div>
                    <div class="cell auto">
                        <img src="<?php echo get_icon( 'gearhead' ); ?>">
                        Get the products you're after
                    </div>
                    <div class="cell auto show-for-medium">
                        <img src="<?php echo get_icon( 'pound' ); ?>">
                        Get the products you're after
                    </div>
                </div>
            </div>

        </header> <!-- end .header -->