<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section
 *
 */

global $ie11;
$bodyClass = '';
if ( $ie11 ) {
	$bodyClass = 'ie11';
}
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

<body <?php body_class( $bodyClass ); ?>>

<div class="off-canvas-wrapper">

    <!-- Load off-canvas container. Feel free to remove if not using. -->
	<?php get_template_part( 'parts/content', 'offcanvas' ); ?>

    <div class="off-canvas-content" data-off-canvas-content>

        <header class="header" role="banner">

            <div class="top-row">
                <div class="grid-container">
                    <div class="grid-x">
                        <div class="cell auto">
                            <div class="grid-x">
                                <div class="cell shrink">
									<?php if ( $ie11 ): ?>
                                        <a href="/"><img src="<?php echo get_template_directory_uri() . '/assets/images/Graham-Logo.png' ?>"></a>
									<?php else: ?>
                                        <a href="/"><img src="<?php echo get_field( 'logo', 'option' )['url']; ?>"></a>
									<?php endif; ?>
                                </div>
                                <div class="cell shrink full-height-text green hide-early">
                                    YOUR LOCAL SPECIALIST
                                </div>
                                <div class="cell shrink hide-for-large">
                                    <a href="/store-locator/" class="grey button-link" style="margin-left: 2px;">
                                        <img src="<?php echo get_icon( 'target' ); ?>">
                                        <span>Find Branch</span>
                                    </a>
                                </div>
                                <div class="cell auto hide-for-large"></div>
                                <div class="cell shrink hide-for-large telephone-number">
                                    <a href="<?php echo get_field( 'telephone_link', 'option' ); ?>">
										<?php echo get_field( 'telephone_number', 'option' ); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="cell shrink">
                            <div class="grid-x top-icons">
                                <div class="cell shrink show-for-large">
									<?php $credit_account_form = get_field( 'credit_account_form', 'option' ); ?>
                                    <span><a href="<?php echo $credit_account_form['url']; ?>" target="_blank" class="grey button-link" style="line-height: 42px;">Account Application</a></span>
                                </div>
                                <div class="cell shrink show-for-large">
                                    <span><a href="https://billing.grahamplumbersmerchant.co.uk/" target="_blank" class="grey button-link" style="line-height: 42px;">E-Invoicing</a></span>
                                </div>
                                <div class="cell shrink show-for-large">
                                    <span class="button-link">All prices Ex VAT</span>
                                </div>
                                <div class="cell shrink show-for-large">
                                    <a href="/store-locator/" class="grey button-link">
                                        <img src="<?php echo get_icon( 'target' ); ?>">
                                        <span>Find Branch</span>
                                    </a>
                                </div>
                                <div class="cell shrink show-for-large">
                                    <a href="/contact-us/" class="grey button-link">
                                        <img src="<?php echo get_icon( 'email' ); ?>">
                                        <span>Contact Us</span>
                                    </a>
                                </div>
                                <div class="cell shrink show-for-large">
                                    <a href="https://www.getplumbedin.co.uk/" target="_blank" class="grey button-link">
                                        <img src="<?php echo get_icon( 'plumbed in' ); ?>">
                                    </a>
                                </div>
                                <div class="cell shrink white show-for-large">
									<?php $facebook = get_social_media( 'Facebook' ); ?>
                                    <a href="<?php echo $facebook['link']; ?>" target="_blank" class="white-background">
                                        <img src="<?php echo $facebook['icon']; ?>">
                                    </a>
                                </div>
                                <div class="cell shrink white no-right-margin show-for-large">
									<?php $twitter = get_social_media( 'Twitter' ); ?>
                                    <a href="<?php echo $twitter['link']; ?>" target="_blank" class="white-background">
                                        <img src="<?php echo $twitter['icon']; ?>">
                                    </a>
                                </div>
                                <div class="cell shrink white no-right-margin show-for-large" style="padding-left: 2px;">
									<?php $twitter = get_social_media( 'LinkedIn' ); ?>
                                    <a href="<?php echo $twitter['link']; ?>" target="_blank" class="white-background">
                                        <img src="<?php echo $twitter['icon']; ?>">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="second-row">
                <div class="grid-container">
                    <div class="grid-x">
                        <div class="cell auto secondary-logo">
                            <a href="/store-locator/"><img src="<?php echo get_secondary_logo( 'Always Available' ); ?>"></a>
                        </div>
                        <div class="cell auto search-form show-for-large">
							<?php get_search_form(); ?>
                        </div>
                        <div class="cell shrink telephone-number show-for-large">
                            <a href="<?php echo get_field( 'telephone_link', 'option' ); ?>">
                                <img src="<?php echo get_icon( 'Call Now' ); ?>">
								<?php echo get_field( 'telephone_number', 'option' ); ?>
                            </a>
                        </div>

                        <div class="cell auto hide-for-large"></div>
                        <div class="cell shrink hide-for-large">
                            <a data-toggle="off-canvas"><span class="menu-icon dark"></span> </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nav-row show-for-large">
                <!-- This navs will be applied to the topbar, above all content
					 To see additional nav styles, visit the /parts directory -->
				<?php get_template_part( 'parts/nav', 'offcanvas-topbar' ); ?>
            </div>

            <div class="bottom-row">
                <div class="grid-container">

					<?php if ( $ie11 ): ?>
                        <div class="logo-bar">
                            <div class="bar">
                                <div class="icon"><img src="<?php echo get_icon( 'checklist' ); ?>"></div>
                                <div class="description">Get the products you're after</div>
                            </div>
                            <div class="bar">
                                <div class="icon"><img src="<?php echo get_icon( 'stopwatch' ); ?>"></div>
                                <div class="description">Get sorted fast</div>
                            </div>
                            <div class="bar">
                                <div class="icon"><img src="<?php echo get_icon( 'gearhead' ); ?>"></div>
                                <div class="description">Talk to people in the know</div>
                            </div>
                            <div class="bar show-for-large">
                                <div class="icon"><img src="<?php echo get_icon( 'pound' ); ?>"></div>
                                <div class="description">Great value for service</div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
					<?php else: ?>
                        <div class="grid-x show-for-medium">
                            <div class="cell auto show-for-medium"></div>
                            <div class="cell shrink">
                                <div class="grid-x">
                                    <div class="cell large-shrink medium-shrink small-12">
                                        <div class="grid-x">
                                            <div class="cell shrink" style="padding-right: 1em;">
                                                <div class="grid-x">
                                                    <div class="cell shrink"><img src="<?php echo get_icon( 'checklist' ); ?>"></div>
                                                    <div class="cell auto">Get the products you're after</div>
                                                </div>
                                            </div>
                                            <div class="cell shrink" style="padding-right: 1em;">
                                                <div class="grid-x">
                                                    <div class="cell shrink"><img src="<?php echo get_icon( 'stopwatch' ); ?>"></div>
                                                    <div class="cell auto">Get sorted fast</div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="cell large-shrink medium-shrink small-12">
                                        <div class="grid-x">
                                            <div class="cell shrink" style="padding-right: 1em;">
                                                <div class="grid-x">
                                                    <div class="cell shrink"><img src="<?php echo get_icon( 'gearhead' ); ?>"></div>
                                                    <div class="cell auto">Talk to people in the know</div>
                                                </div>
                                            </div>
                                            <div class="cell shrink">
                                                <div class="grid-x">
                                                    <div class="cell shrink"><img src="<?php echo get_icon( 'pound' ); ?>" style="max-height:45px;"></div>
                                                    <div class="cell auto">Great value for service</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cell auto show-for-medium"></div>
                        </div>
                        <div class="grid-x hide-for-medium">
                            <div class="cell small-6 medium-6 large-6">
                                <div class="grid-x">
                                    <div class="cell shrink" style="padding-right: 1em;">
                                        <div class="grid-x">
                                            <div class="cell shrink"><img src="<?php echo get_icon( 'checklist' ); ?>"></div>
                                            <div class="cell auto icon-text">Get the products you're after</div>
                                        </div>
                                    </div>
                                    <div class="cell shrink" style="padding-right: 1em;">
                                        <div class="grid-x">
                                            <div class="cell shrink"><img src="<?php echo get_icon( 'gearhead' ); ?>"></div>
                                            <div class="cell auto icon-text">Talk to people in the know</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="cell small-6 medium-6 large-6">
                                <div class="grid-x">
                                    <div class="cell shrink" style="padding-right: 1em;">
                                        <div class="grid-x">
                                            <div class="cell shrink"><img src="<?php echo get_icon( 'stopwatch' ); ?>"></div>
                                            <div class="cell auto icon-text">Get sorted fast</div>
                                        </div>
                                    </div>
                                    <div class="cell shrink">
                                        <div class="grid-x">
                                            <div class="cell shrink"><img src="<?php echo get_icon( 'pound' ); ?>" style="max-height:45px;"></div>
                                            <div class="cell auto icon-text">Great value for service</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php endif; ?>

                </div>
            </div>

        </header> <!-- end .header -->