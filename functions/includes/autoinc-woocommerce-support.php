<?php

function tc_add_woocommerce_support()
{
    add_theme_support('woocommerce');
}

add_action('after_setup_theme', 'tc_add_woocommerce_support');

//add_theme_support( 'wc-product-gallery-zoom' );
//add_theme_support( 'wc-product-gallery-lightbox' );
//add_theme_support( 'wc-product-gallery-slider' );

/*function mytheme_add_woocommerce_support() {
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 150,
		'single_image_width'    => 300,

		'product_grid'          => array(
			'default_rows'    => 3,
			'min_rows'        => 2,
			'max_rows'        => 8,
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 5,
		),
	) );
}

add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );*/

/*add_filter( 'woocommerce_enqueue_styles', '__return_false' );*/


/**
 *
 * Rearrange woocommerce_single_product_summary
 *
 * Hook: woocommerce_single_product_summary.
 *
 * @hooked woocommerce_template_single_title - 5
 * @hooked woocommerce_template_single_rating - 10
 * @hooked woocommerce_template_single_price - 10
 * @hooked woocommerce_template_single_excerpt - 20
 * @hooked woocommerce_template_single_add_to_cart - 30
 * @hooked woocommerce_template_single_meta - 40
 * @hooked woocommerce_template_single_sharing - 50
 * @hooked WC_Structured_Data::generate_product_data() - 60
 *
 * @hooked woocommerce_template_single_title - 5
 * @hooked woocommerce_template_single_meta - 10
 * @hooked woocommerce_template_single_rating - 20
 * @hooked woocommerce_template_single_price - 20
 * @hooked woocommerce_template_single_excerpt -30
 * @hooked woocommerce_template_single_add_to_cart - 40
 * @hooked woocommerce_template_single_sharing - 50
 * @hooked WC_Structured_Data::generate_product_data() - 60
 */

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
//remove_action('woocommerce_single_product_summary','WC_Structured_Data::generate_product_data()',60);

add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 20);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 20);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
//add_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt',40);
add_action('woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 50);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 55);
//add_action('woocommerce_single_product_summary','WC_Structured_Data::generate_product_data()',70);

/**
 * re-arrange: woocommerce_after_single_product_summary.
 *
 * @hooked woocommerce_output_product_data_tabs - 10
 * @hooked woocommerce_upsell_display - 15
 * @hooked woocommerce_output_related_products - 20
 *
 * @hooked woocommerce_upsell_display - 15
 * @hooked woocommerce_output_related_products - 20
 */

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);

/**
 * Reorder product data tabs
 */
add_filter('woocommerce_product_tabs', 'woo_reorder_tabs', 98);
function woo_reorder_tabs($tabs)
{

    $tabs['description']['priority'] = 10;            // Description second
    $tabs['reviews']['priority'] = 15;            // Reviews third

    return $tabs;
}

/**
 * Rename product data tabs
 */
add_filter('woocommerce_product_tabs', 'woo_rename_tabs', 98);
function woo_rename_tabs($tabs)
{

    $tabs['description']['title'] = __('Product Details');        // Rename the description tab

    return $tabs;

}


/**
 * Add a custom product data tab
 */
add_filter('woocommerce_product_tabs', 'woo_new_product_tab');
function woo_new_product_tab($tabs)
{

    // Adds the new tab

    $tabs['test_tab'] = array(
        'title' => __('Description', 'woocommerce'),
        'priority' => 5,
        'callback' => 'woo_new_product_tab_content'
    );

    return $tabs;

}

function woo_new_product_tab_content()
{

    // The new tab content

    /*echo '<h2>New Product Tab</h2>';
    echo '<p>Here\'s your new product tab.</p>';*/
    woocommerce_template_single_excerpt();
}

if(!function_exists('isInteger')) {
    function isInteger($value)
    {
        if (!$value) {
            return false;
        }
        if ($value == '') {
            return false;
        }
        if (!ctype_digit($value)) {
            return false;
        }
        return (int)$value;
    }
}

add_filter('woocommerce_get_price_html', 'ts_price_html', 100, 2);

function ts_price_html($price, $product)
{
    ob_start();
    $sPrice = explode('.', $price);
    ?>
    <span class="only">Only </span>
    <br><?php echo $sPrice[0]; ?><span class="pence">.<?php echo $sPrice[1]; ?></span>
    <br>
    <span class="normal">VAT excluded</span>
    <br>

    <?php
    $pID = $product->get_id();
    $epim_threshold1 = isInteger(get_post_meta($pID, 'epim_Qty_Break_1', true));
    $epim_threshold2 = isInteger(get_post_meta($pID, 'epim_Qty_Break_2', true));
    $epim_threshold3 = isInteger(get_post_meta($pID, 'epim_Qty_Break_3', true));
    $epim_price1 = get_post_meta($pID, 'epim_Qty_Price_1', true);
    $epim_price2 = get_post_meta($pID, 'epim_Qty_Price_2', true);
    $epim_price3 = get_post_meta($pID, 'epim_Qty_Price_3', true);

    if (($epim_threshold1) && ($epim_price1 > 0) && ($epim_price1 != '')) {
        ?>
        <br>
        <span class="normal">Buy <?php echo $epim_threshold1; ?> for £<?php echo number_format($epim_price1,2); ?> each.</span>
        <?php
    }

    if (($epim_threshold2) && ($epim_price2 > 0) && ($epim_price2 != '')) {
        ?>
        <br>
        <span class="normal">Buy <?php echo $epim_threshold2; ?> for £<?php echo number_format($epim_price2,2); ?> each.</span>
        <?php
    }

    if (($epim_threshold3) && ($epim_price3 > 0) && ($epim_price3 != '')) {
        ?>
        <br>
        <span class="normal">Buy <?php echo $epim_threshold3; ?> for £<?php echo number_format($epim_price3,2); ?> each.</span>
        <?php
    }

    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

/**
 * @param $translated_text
 * @param $text
 * @param $domain
 *
 * @return string|string[]
 *
 * Change Cart to Basket
 */

function gb_change_cart_string($translated_text, $text, $domain)
{

    $translated_text = str_replace("cart", "basket", $translated_text);

    $translated_text = str_replace("Cart", "Basket", $translated_text);

    return $translated_text;
}

add_filter('gettext', 'gb_change_cart_string', 100, 3);

/**
 * Hook: woocommerce_shop_loop_item_title.
 *
 * @hooked woocommerce_template_loop_product_title - 10
 */
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('woocommerce_shop_loop_item_title', 'ts_woocommerce_template_loop_product_title', 10);

function ts_woocommerce_template_loop_product_title()
{
    $title = get_the_title();
    if (str_word_count($title, 0) > 5) {
        $words = str_word_count($title, 2);
        $pos = array_keys($words);
        $title = substr($title, 0, $pos[5]) . '...';
    }

    echo '<h2 class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '">' . $title . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Hook: woocommerce_before_shop_loop
 *
 *  Add in categories and sub-categories to product listings.
 */

function ts_product_subcategories($args = array())
{

    $parentid = get_queried_object_id();
    $args = array(
        'parent' => $parentid
    );

    $terms = get_terms('product_cat', $args);

    if ($terms) {
        $numberCols = 4;
        echo '<ul class="products columns-' . $numberCols . '">';
        $colCount = 1;
        $colClass = '';
        foreach ($terms as $term) {
            if (($term->name != 'Uncategorized') && ($term->count > 0)):
                if ($colCount == 1) {
                    $colClass = ' first';
                } else {
                    $colClass = '';
                }
                if ($colCount == $numberCols) {
                    $colClass = ' last';
                    $colCount = 1;
                } else {
                    $colCount++;
                }
                echo '<li class="product-category product' . $colClass . '">';
                echo '<a href="' . esc_url(get_term_link($term)) . '" class="' . $term->slug . '">';
                woocommerce_subcategory_thumbnail($term);
                echo '<h2 class="woocommerce-loop-category__title">';
                echo $term->name;
                echo '</h2>';
                echo '</a>';
                echo '</li>';
            endif;
        }
        echo '</ul>';
    }

}


//add_action('woocommerce_before_main_content', 'ts_product_subcategories', 500);

/**
 * Add AJAX Shortcode when cart contents update
 */

add_filter('woocommerce_add_to_cart_fragments', 'woo_cart_but_count');

function woo_cart_but_count($fragments)
{


    $cart_count = WC()->cart->cart_contents_count;

    $frag = '<style id="basket-cart-qty">a.icon-link-your-cart:after {content: "' . $cart_count . '" !important;}</style>';
    $fragments['style#basket-cart-qty'] = $frag;

    return $fragments;
}

/**
 * Return a list of Product Categories
 */

function get_product_categories($id = '')
{
    if ($id == 'short-product-category-menu') {
        $res = '<ul id="' . $id . '" class="medium-horizontal menu dropdown" data-responsive-menu="accordion medium-dropdown" role="menubar">';
    } else {
        if($id=='off-canvas') {
            $res = '<ul class="vertical menu accordion-menu" data-accordion-menu data-submenu-toggle="true" role="menubar" style="width: 100%">';
        } else {
            $res = '<ul class="medium-horizontal menu dropdown" data-responsive-menu="accordion medium-dropdown" role="menubar">';
        }

    }

    $taxonomy = 'product_cat';
    $orderby = 'name';
    $show_count = 0;      // 1 for yes, 0 for no
    $pad_counts = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no
    $title = '';
    $empty = 0;

    $args = array(
        'taxonomy' => $taxonomy,
        'orderby' => $orderby,
        'show_count' => $show_count,
        'pad_counts' => $pad_counts,
        'hierarchical' => $hierarchical,
        'title_li' => $title,
        'hide_empty' => $empty
    );
    $all_categories = get_categories($args);
    $current_category = get_queried_object();
    $current_cat_id = $current_category->term_id;
    //error_log('current category id = '.$current_cat_id);
    foreach ($all_categories as $cat) {
        if (($cat->category_parent == 0) && ($cat->count > 0)) {
            $category_id = $cat->term_id;
            $top_active_class = '';
            if($current_cat_id==$category_id) {
                $top_active_class=' active';
            }

            $args2 = array(
                'taxonomy' => $taxonomy,
                'child_of' => 0,
                'parent' => $category_id,
                'orderby' => $orderby,
                'show_count' => $show_count,
                'pad_counts' => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li' => $title,
                'hide_empty' => $empty
            );
            $sub_cats = get_categories($args2);

            if ($sub_cats) {
                $text = $cat->name;
                if (str_word_count($text) > 2) {
                    $splitstring1 = substr($text, 0, floor(strlen($text) / 2));
                    $splitstring2 = substr($text, floor(strlen($text) / 2));

                    if (substr($splitstring1, 0, -1) != ' ' and substr($splitstring2, 0, 1) != ' ') {
                        $middle = strlen($splitstring1) + strpos($splitstring2, ' ') + 1;
                    } else {
                        $middle = strrpos(substr($text, 0, floor(strlen($text) / 2)), ' ') + 1;
                    }

                    $string1 = substr($text, 0, $middle);
                    $string2 = substr($text, $middle);
                    $linkDesc = $string1 . '<br>' . $string2;
                } else {
                    $linkDesc = str_replace(' ', '<br>', $text);
                }
                $res .= '<li class="menu-item'.$top_active_class.'"><a href="' . get_term_link($cat->slug, 'product_cat') . '">' . $linkDesc . '</a>';
                $top_active_class = '';

            }
            if ($sub_cats) {
                $res .= '<ul class="menu submenu is-dropdown-menu vertical">';
                foreach ($sub_cats as $sub_category) {
                    $sub_category_id = $sub_category->term_id;
                    if($sub_category_id==$current_cat_id) {
                        $top_active_class=' active';
                    }
                    $res .= '<li class="menu-item menu-item-type-taxonomy'.$top_active_class.'">' . '<a href="' . get_term_link($sub_category->slug, 'product_cat') . '">' . $sub_category->name . '</a></li>';
                    $top_active_class='';
                }
                $res .= '</ul>';
            }
            $res .= '</li>';
        }
    }

    $res .= '</ul>';

    return $res;
}