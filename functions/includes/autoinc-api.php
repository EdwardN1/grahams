<?php

// Force certificate validation. You need a valid certificate in the site, self generated certificates are NOT VALID.
//add_filter( 'https_local_ssl_verify', '__return_true' );

// Setting a custom timeout value for cURL. Using a high value for priority to ensure the function runs after any other added to the same action hook.
/*add_action('http_api_curl', 'sar_custom_curl_timeout', 9999, 1);
function sar_custom_curl_timeout( $handle ){
    curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
    curl_setopt( $handle, CURLOPT_TIMEOUT, 30 ); // 30 seconds. Too much for production, only for testing.
}*/
// Setting custom timeout for the HTTP request
/*add_filter( 'http_request_timeout', 'sar_custom_http_request_timeout', 9999 );
function sar_custom_http_request_timeout( $timeout_value ) {
    return 30; // 30 seconds. Too much for production, only for testing.
}*/
// Setting custom timeout in HTTP request args
/*add_filter('http_request_args', 'sar_custom_http_request_args', 9999, 1);
function sar_custom_http_request_args( $r ){
    $r['timeout'] = 30; // 30 seconds. Too much for production, only for testing.
    return $r;
}*/

function displayJSON($apiCall)
{
    $allAttributes = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($apiCall, true)), RecursiveIteratorIterator::SELF_FIRST);
    echo '<div style="padding-bottom: 10px; padding-top: 10px; border-bottom: 1px solid black">';
    echo '<p>';
    $indent = 1;
    foreach ($allAttributes as $key => $val) {
        if (is_array($val)) {
            echo "</p><p>$key:<br>";
        } else {
            echo "<span style='padding-left: 1em;'> $key => $val</span><br>";
        }

    }
    echo '</p>';
    echo '</div>';
}

function make_api_call($url)
{
    $method = get_field('api_retrieval', 'options');
    if ($method == 'cUrl') {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        /*curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);*/

        /*$proxy = 'https://proxy.sgti.lbn.fr:4480';
        curl_setopt($ch, CURLOPT_PROXY, $proxy);*/

        $headers = array();
        $headers[] = "Ocp-Apim-Subscription-Key: e57667e8bffa4384a6ce53f80521677a";

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $apiCall = curl_exec($ch);

        curl_close($ch);

        return $apiCall;
    } else {
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Ocp-Apim-Subscription-Key: e57667e8bffa4384a6ce53f80521677a"
            )
        );
        $context = stream_context_create($opts);
        $apiCall = file_get_contents($url, false, $context);

        return $apiCall;
    }

}

function insert_attachment_from_url($url, $parent_post_id = null)
{
    if (!class_exists('WP_Http'))
        include_once(ABSPATH . WPINC . '/class-http.php');
    $http = new WP_Http();
    /*$resp = array();
    $args = array('method'=>'GET');
    $response = $http->handle_redirects($url,$args,$resp);
    if(!$response) {
        $response = $http->request($url);
    }*/
    $remoteHead =   wp_remote_head( $url);
    if(!is_wp_error($remoteHead)) {
        error_log(urldecode($remoteHead['headers']['location']));
    } else {
        error_log($remoteHead->get_error_message());
    }

    //$response = $http->request($url);
    $response = $http->request(urldecode($remoteHead['headers']['location']));
    if (!is_wp_error($response)):
        if ($response['response']['code'] != 200) {
            return false;
        }
        $upload = wp_upload_bits(basename($url), null, $response['body']);
        if (!empty($upload['error'])) {
            return false;
        }
        $file_path = $upload['file'];
        $file_name = basename($file_path);
        $file_type = wp_check_filetype($file_name, null);
        $attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));
        $wp_upload_dir = wp_upload_dir();
        $post_info = array(
            'guid' => $wp_upload_dir['url'] . '/' . $file_name,
            'post_mime_type' => $file_type['type'],
            'post_title' => $attachment_title,
            'post_content' => '',
            'post_status' => 'inherit',
        );
        // Create the attachment
        $attach_id = wp_insert_attachment($post_info, $file_path, $parent_post_id);
        // Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        // Assign metadata to attachment
        wp_update_attachment_metadata($attach_id, $attach_data);
        return $attach_id;
    else:
        error_log($response->get_error_message());
        return false;
    endif;

}

function get_image_file($url)
{

    $method = get_field('api_retrieval', 'options');
    error_log('get_image_file method: ' . $method);
    if ($method == 'cUrl') {
        error_log('Getting Remote File using cUrl - ' . $url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $apiCall = curl_exec($ch);
        curl_close($ch);
        return $apiCall;
    } else {
        error_log('Getting Remote File using file_get_contents - ' . $url);
        return file_get_contents($url);
    }
}

function get_api_all_changed_products_since($datetime = '2002-10-02T10:00:00-00:00')
{
    $xdatetime = substr($datetime, 0, 10).'T10:00:00-00:00';
	$r = make_api_call('https://epim.azure-api.net/Grahams/api/ProductsUpdatedSince?ChangedSinceUTC=' . $xdatetime);
    //$r = make_api_call('https://epim.azure-api.net/Grahams/api/ProductsUpdatedSince?ChangedSinceUTC=' . '2020-01-06T10:00:00-00:00');
    return $r;
}

function get_api_all_attributes()
{
    return make_api_call('https://epim.azure-api.net/Grahams/api/Attributes');
}

function get_api_all_categories()
{
    return make_api_call('https://epim.azure-api.net/Grahams/api/Categories');
}

function get_api_category($id)
{
    return make_api_call('https://epim.azure-api.net/Grahams/api/Categories/' . $id);
}

function get_api_picture($id)
{
    return make_api_call('https://epim.azure-api.net/Grahams/api/Pictures/' . $id);
}

function get_api_all_products()
{
    $apiCall = make_api_call('https://epim.azure-api.net/Grahams/api/Products/');
    $allProducts = json_decode($apiCall);
    $TotalResults = $allProducts->TotalResults;

    return make_api_call('https://epim.azure-api.net/Grahams/api/Products/?limit=' . $TotalResults);
}

function get_api_products_in_category($id)
{
    return make_api_call('https://epim.azure-api.net/Grahams/api/ProductsInCategory/' . $id);
}

function get_api_product($id)
{
    return make_api_call('https://epim.azure-api.net/Grahams/api/Products/' . $id);
}

function get_api_variation($id)
{
    return make_api_call('https://epim.azure-api.net/Grahams/api/Variations/' . $id);
}

function returnCategory($id, $categories)
{
    $res = false;
    foreach ($categories as $category) {
        if ($category->Id == $id) {
            $res = clone $category;
            break;
        }
    }

    return $res;
}

function sortCategoriesByID_cmp($a, $b)
{
    $res = 0;
    if ($a->Id < $b->Id) {
        $res = -1;
    }
    if ($a->Id > $b->Id) {
        $res = 1;
    }

    return $res;
}

function getTermFromID($id, $terms)
{
    $res = false;
    foreach ($terms as $term) {
        $apiID = get_field('api_id', $term);
        if ($apiID == $id) {
            $res = $term;
            break;
        }
    }

    return $res;
}

function create_category($id, $name, $ParentID, $picture_webpath, $picture_ids)
{
    $response = 'Error';
    $terms = get_terms([
        'taxonomy' => 'grahamscat',
        'hide_empty' => false,
    ]);
    $term = getTermFromID($id, $terms);
    if ($term) {
        wp_update_term($term->term_id, 'grahamscat', array('name' => $name));
        update_field('api_parents', $ParentID, 'grahamscat_' . $term->term_id);
        update_field('api_link', $picture_webpath, 'grahamscat_' . $term->term_id);
        $pSuffix = '';
        $pField = '';
        if ($picture_ids) {
            foreach ($picture_ids as $picture_id) {
                $pField .= $pSuffix;
                $pSuffix = ',';
                $pField .= $picture_id;
            }
        }
        update_field('api_picture_ids', $pField, 'grahamscat_' . $term->term_id);
        $response = $name . ' Category Updated ';
    } else {
        $newTerm = wp_insert_term($name, 'grahamscat');
        if (is_wp_error($newTerm)) {
            $response = $newTerm->get_error_message() . ' Creating API_ID=' . $id . ' Name=' . $name;
        } else {
            update_field('api_id', $id, 'grahamscat_' . $newTerm["term_id"]);
            update_field('api_parents', $ParentID, 'grahamscat_' . $newTerm["term_id"]);
            $pSuffix = '';
            $pField = '';
            if ($picture_ids) {
                foreach ($picture_ids as $picture_id) {
                    $pField .= $pSuffix;
                    $pSuffix = ',';
                    $pField .= $picture_id;
                }
            }
            update_field('api_picture_ids', $pField, 'grahamscat_' . $newTerm["term_id"]);
            $response = $name . ' Category Created';
        }
    }
    return $response;
}

function get_category_pictures($id)
{
    $http_response_header = '';
    $terms = get_terms([
        'taxonomy' => 'grahamscat',
        'hide_empty' => false,
    ]);
    $term = getTermFromID($id, $terms);
    if ($term) {
        $response = get_field('api_picture_ids', $term);
    }
}

function create_categories()
{
    $jsonCategories = get_api_all_categories();
    $api_categories = json_decode($jsonCategories);
    usort($api_categories, "sortCategoriesByID_cmp");
    $terms = get_terms([
        'taxonomy' => 'grahamscat',
        'hide_empty' => false,
    ]);
    foreach ($api_categories as $category) {
        $term = getTermFromID($category->Id, $terms);
        if ($term) {
            wp_update_term($term->term_id, 'grahamscat', array('name' => $category->Name));
            update_field('api_parents', $category->ParentId, 'grahamscat_' . $term->term_id);
            $pSuffix = '';
            $pField = '';
            foreach ($category->PictureIds as $picture_id) {
                $pField .= $pSuffix;
                $pSuffix = ',';
                $pField .= $picture_id;
            }
            if ($pField != '') {
                if (strpos($pField, ',' !== false)) {

                } else {
                    $jsonPicture = get_api_picture($pField);
                    $picture = json_decode($jsonPicture);
                    update_field('api_link', $picture->WebPath, 'grahamscat_' . $term->term_id);
                    update_field('api_picture_ids', $pField, 'grahamscat_' . $term->term_id);
                }
            }
        } else {
            $newTerm = wp_insert_term($category->Name, 'grahamscat');
            if (is_wp_error($newTerm)) {
                //error_log($newTerm->get_error_message().' Creating API_ID='.$category->Id.' Name='.$category->Name);
            } else {
                update_field('api_id', $category->Id, 'grahamscat_' . $newTerm["term_id"]);
                update_field('api_parents', $category->ParentId, 'grahamscat_' . $newTerm["term_id"]);
                $pSuffix = '';
                $pField = '';
                foreach ($category->PictureIds as $picture_id) {
                    $pField .= $pSuffix;
                    $pSuffix = ',';
                    $pField .= $picture_id;
                }
                if ($pField != '') {
                    update_field('api_picture_ids', $pField, 'grahamscat_' . $newTerm["term_id"]);
                }
            }
        }
    }
}

function sort_categories()
{
    $terms = get_terms([
        'taxonomy' => 'grahamscat',
        'hide_empty' => false,
    ]);
    foreach ($terms as $term) {
        $api_parents = get_field('api_parents', $term);
        if ($api_parents != '') {
            $parent = getTermFromID($api_parents, $terms);
            if ($parent) {
                wp_update_term($term->term_id, 'grahamscat', array('parent' => $parent->term_id));
            }
        }
    }
}

function getAttributeNameFromID($id, $attributes)
{
    $res = 'Name Not Found';
    foreach ($attributes as $attribute) {
        if ($attribute->Id == $id) {
            $res = $attribute->Name;
            break;
        }
    }

    return $res;
}

function getAPIIDFromCode($code)
{
    $res = false;
    $loop = new WP_Query(array('post_type' => 'grahams_product', 'posts_per_page' => -1));
    while ($loop->have_posts()) : $loop->the_post();
        if ($code == get_field('code')) {
            $res = get_field('api_id');
            break;
        }
    endwhile;
    wp_reset_postdata();
    return $res;
}

function getProductFromID($productID, $variationID)
{
    $res = false;
    /* global $post;
     $args = array('post_type' => 'grahams_product', 'posts_per_page' => -1);

     $loop = get_posts($args);

     foreach ($loop as $post): setup_postdata($post);
         $api_id = get_field('api_id');
         if ($api_id == $productID) {
             $variation_id = get_field('variation_id');
             if ($variation_id == $variationID) {
                 $res = get_the_ID();
                 break;
             }
         }
     endforeach;*/

    $args = array('post_type' => 'grahams_product', 'posts_per_page' => -1);

    $loop = new WP_Query($args);
    if ($loop->have_posts()):
        while ($loop->have_posts()) : $loop->the_post();
            $api_id = get_field('api_id');
            if ($api_id == $productID) {
                $variation_id = get_field('variation_id');
                if ($variation_id == $variationID) {
                    $res = get_the_ID();
                    break;
                }
            }
        endwhile;
    endif;

    wp_reset_postdata();

    return $res;
}

function create_product($productID, $variationID, $productBulletText, $productName, $categoryIds, $pictureIds)
{
    $res = 'An Error has occurred for this product: ' . $productName;
    $jsonVariation = get_api_variation($variationID);
    $variation = json_decode($jsonVariation);
    if ($variation) {
        $id = getProductFromID($productID, $variation->Id);
        $newPost = false;
        $jsonAttributes = get_api_all_attributes();
        $attributes = json_decode($jsonAttributes);
        $terms = get_terms([
            'taxonomy' => 'grahamscat',
            'hide_empty' => false,
        ]);
        //$catEx = explode(',',$categoryIds);
        $catEx = $categoryIds;
        $catIds = array();
        foreach ($catEx as $category_id) {
            $realCatID = getTermFromID($category_id, $terms);
            if ($realCatID) {
                $catIds[] = $realCatID->term_id;
            }
        }
        //$pictureEx = explode(',',$pictureIds);
        $pictureEx = $pictureIds;
        if ($id) {
            $thePost = array(
                'ID' => $id,
                'post_title' => $variation->Name,
                'post_content' => 'Imported',
                'post_status' => 'publish',
            );
            if ($thePost) {
                $newPost = wp_update_post($thePost);
                if ($newPost) {
                    wp_set_object_terms($newPost, $catIds, 'grahamscat');
                    update_field('api_id', $productID, $newPost);
                    update_field('variation_id', $variation->Id, $newPost);
                    update_field('description', $productBulletText, $newPost);
                    update_field('code', $variation->SKU, $newPost);
                    update_field('product_group', $productName, $newPost);
                    update_field('price', $variation->Qty_Price_1, $newPost);
                    update_field('summary', $variation->Table_Heading, $newPost);
                    if ($pictureEx) {
                        foreach ($pictureEx as $pictureId) {
                            $jsonPicture = get_api_picture($pictureId);
                            $picture = json_decode($jsonPicture);
                            if ($picture) {
                                if (have_rows('product_images', $newPost)):
                                    $pictureUpdate = false;
                                    while (have_rows('product_images', $newPost)): the_row();
                                        $api_image_id = get_sub_field('api_image_id');
                                        if ($api_image_id == $picture->Id) {
                                            $pictureUpdate = true;
                                            update_sub_field('api_link', $picture->WebPath);
                                            break;
                                        }
                                    endwhile;
                                    if (!$pictureUpdate) {
                                        $row = array(
                                            'api_link' => $picture->WebPath,
                                            'api_image_id' => $picture->Id
                                        );

                                        add_row('product_images', $row, $newPost);
                                    };
                                else:
                                    $row = array(
                                        'api_link' => $picture->WebPath,
                                        'api_image_id' => $picture->Id
                                    );

                                    add_row('product_images', $row, $newPost);
                                endif;
                            }
                        }
                    }
                    foreach ($variation->PictureIds as $picture_id) {
                        $jsonPicture = get_api_picture($picture_id);
                        $picture = json_decode($jsonPicture);
                        if ($picture) {
                            if (have_rows('variation_images', $newPost)):
                                $pictureUpdate = false;
                                while (have_rows('variation_images', $newPost)): the_row();
                                    $api_image_id = get_sub_field('api_image_id');
                                    if ($api_image_id == $picture->Id) {
                                        $pictureUpdate = true;
                                        update_sub_field('api_link', $picture->WebPath);
                                        break;
                                    }
                                endwhile;
                                if (!$pictureUpdate) {
                                    $row = array(
                                        'api_link' => $picture->WebPath,
                                        'api_image_id' => $picture->Id
                                    );

                                    add_row('variation_images', $row, $newPost);
                                };
                            else:
                                $row = array(
                                    'api_link' => $picture->WebPath,
                                    'api_image_id' => $picture->Id
                                );

                                add_row('variation_images', $row, $newPost);
                            endif;
                        }
                    }
                    $attributeText = '';
                    foreach ($variation->AttributeValues as $attribute_value) {
                        $aName = getAttributeNameFromID($attribute_value->AttributeId, $attributes);
                        if ($aName != '0 ( )') {
                            $attributeText .= $aName . ': ' . $attribute_value->Value . '<br>';
                        }
                    }
                    update_field('specifications', $attributeText, $newPost);
                }
            }
            $res = $productName . ' (' . $variation->SKU . ') product updated';
        } else {
            $thePost = array(
                'post_title' => $variation->Name,
                'post_type' => 'grahams_product',
                'post_content' => 'Imported',
                'post_status' => 'publish',
            );
            if ($thePost) {
                $newPost = wp_insert_post($thePost);
                if ($newPost) {
                    wp_set_object_terms($newPost, $catIds, 'grahamscat');
                    update_field('api_id', $productID, $newPost);
                    update_field('variation_id', $variation->Id, $newPost);
                    update_field('description', $productBulletText, $newPost);
                    update_field('code', $variation->SKU, $newPost);
                    update_field('product_group', $productName, $newPost);
                    update_field('price', $variation->Qty_Price_1, $newPost);
                    update_field('summary', $variation->Table_Heading, $newPost);
                    if ($pictureEx) {
                        foreach ($pictureEx as $pictureId) {
                            $jsonPicture = get_api_picture($pictureId);
                            $picture = json_decode($jsonPicture);
                            if (have_rows('product_images', $newPost)):
                                $pictureUpdate = false;
                                while (have_rows('product_images', $newPost)): the_row();
                                    $api_image_id = get_sub_field('api_image_id');
                                    if ($api_image_id == $picture->Id) {
                                        $pictureUpdate = true;
                                        update_sub_field('api_link', $picture->WebPath);
                                        break;
                                    }
                                endwhile;
                                if (!$pictureUpdate) {
                                    $row = array(
                                        'api_link' => $picture->WebPath,
                                        'api_image_id' => $picture->Id
                                    );

                                    add_row('product_images', $row, $newPost);
                                };
                            else:
                                $row = array(
                                    'api_link' => $picture->WebPath,
                                    'api_image_id' => $picture->Id
                                );

                                add_row('product_images', $row, $newPost);
                            endif;
                        }
                    }
                    foreach ($variation->PictureIds as $picture_id) {
                        $jsonPicture = get_api_picture($picture_id);
                        $picture = json_decode($jsonPicture);
                        if (have_rows('variation_images', $newPost)):
                            $pictureUpdate = false;
                            while (have_rows('variation_images', $newPost)): the_row();
                                $api_image_id = get_sub_field('api_image_id');
                                if ($api_image_id == $picture->Id) {
                                    $pictureUpdate = true;
                                    update_sub_field('api_link', $picture->WebPath);
                                    break;
                                }
                            endwhile;
                            if (!$pictureUpdate) {
                                $row = array(
                                    'api_link' => $picture->WebPath,
                                    'api_image_id' => $picture->Id
                                );

                                add_row('variation_images', $row, $newPost);
                            };
                        else:
                            $row = array(
                                'api_link' => $picture->WebPath,
                                'api_image_id' => $picture->Id
                            );

                            add_row('variation_images', $row, $newPost);
                        endif;
                    }
                    $attributeText = '';
                    foreach ($variation->AttributeValues as $attribute_value) {
                        $aName = getAttributeNameFromID($attribute_value->AttributeId, $attributes);
                        if ($aName != '0 ( )') {
                            $attributeText .= $aName . ': ' . $attribute_value->Value . '<br>';
                        }
                    }
                    update_field('specifications', $attributeText, $newPost);
                }

            }
            $res = $productName . ' (' . $variation->SKU . ') Product Created';
        }
    }
    return $res;
}

function create_products()
{
    $jsonProducts = get_api_all_products();
    $products = json_decode($jsonProducts);
    $jsonAttributes = get_api_all_attributes();
    $attributes = json_decode($jsonAttributes);
    foreach ($products->Results as $product) {
        $terms = get_terms([
            'taxonomy' => 'grahamscat',
            'hide_empty' => false,
        ]);
        $catIds = array();
        foreach ($product->CategoryIds as $category_id) {
            $realCatID = getTermFromID($category_id, $terms);
            if ($realCatID) {
                $catIds[] = $realCatID->term_id;
            }
        }
        if ($product->VariationIds) {
            foreach ($product->VariationIds as $variation_id) {
                $jsonVariation = get_api_variation($variation_id);
                $variation = json_decode($jsonVariation);
                $id = getProductFromID($product->Id, $variation->Id);
                $newPost = false;
                if ($id) {
                    $thePost = array(
                        'ID' => $id,
                        'post_title' => $variation->Name,
                        'post_content' => 'Imported',
                        'post_status' => 'publish',
                    );
                    if ($thePost) {
                        $newPost = wp_update_post($thePost);
                        if ($newPost) {
                            wp_set_object_terms($newPost, $catIds, 'grahamscat');
                            update_field('api_id', $product->Id, $newPost);
                            update_field('variation_id', $variation->Id, $newPost);
                            update_field('description', $product->BulletText, $newPost);
                            update_field('code', $variation->SKU, $newPost);
                            update_field('product_group', $product->Name, $newPost);
                            update_field('price', $variation->Qty_Price_1, $newPost);
                            update_field('summary', $variation->Table_Heading, $newPost);
                            foreach ($product->PictureIds as $pictureId) {
                                $jsonPicture = get_api_picture($pictureId);
                                $picture = json_decode($jsonPicture);
                                if (have_rows('product_images', $newPost)):
                                    $pictureUpdate = false;
                                    while (have_rows('product_images', $newPost)): the_row();
                                        $api_image_id = get_sub_field('api_image_id');
                                        if ($api_image_id == $picture->Id) {
                                            $pictureUpdate = true;
                                            update_sub_field('api_link', $picture->WebPath);
                                            break;
                                        }
                                    endwhile;
                                    if (!$pictureUpdate) {
                                        $row = array(
                                            'api_link' => $picture->WebPath,
                                            'api_image_id' => $picture->Id
                                        );

                                        add_row('product_images', $row, $newPost);
                                    };
                                else:
                                    $row = array(
                                        'api_link' => $picture->WebPath,
                                        'api_image_id' => $picture->Id
                                    );

                                    add_row('product_images', $row, $newPost);
                                endif;
                            }
                            foreach ($variation->PictureIds as $picture_id) {
                                $jsonPicture = get_api_picture($picture_id);
                                $picture = json_decode($jsonPicture);
                                if (have_rows('variation_images', $newPost)):
                                    $pictureUpdate = false;
                                    while (have_rows('variation_images', $newPost)): the_row();
                                        $api_image_id = get_sub_field('api_image_id');
                                        if ($api_image_id == $picture->Id) {
                                            $pictureUpdate = true;
                                            update_sub_field('api_link', $picture->WebPath);
                                            break;
                                        }
                                    endwhile;
                                    if (!$pictureUpdate) {
                                        $row = array(
                                            'api_link' => $picture->WebPath,
                                            'api_image_id' => $picture->Id
                                        );

                                        add_row('variation_images', $row, $newPost);
                                    };
                                else:
                                    $row = array(
                                        'api_link' => $picture->WebPath,
                                        'api_image_id' => $picture->Id
                                    );

                                    add_row('variation_images', $row, $newPost);
                                endif;
                            }
                            $attributeText = '';
                            foreach ($variation->AttributeValues as $attribute_value) {
                                $aName = getAttributeNameFromID($attribute_value->AttributeId, $attributes);
                                if ($aName != '0 ( )') {
                                    $attributeText .= $aName . ': ' . $attribute_value->Value . '<br>';
                                }
                            }
                            update_field('specifications', $attributeText, $newPost);
                        }
                    }
                } else {
                    $thePost = array(
                        'post_title' => $variation->Name,
                        'post_type' => 'grahams_product',
                        'post_content' => 'Imported',
                        'post_status' => 'publish',
                    );
                    if ($thePost) {
                        $newPost = wp_insert_post($thePost);
                        if ($newPost) {
                            wp_set_object_terms($newPost, $catIds, 'grahamscat');
                            update_field('api_id', $product->Id, $newPost);
                            update_field('variation_id', $variation->Id, $newPost);
                            update_field('description', $product->BulletText, $newPost);
                            update_field('code', $variation->SKU, $newPost);
                            update_field('product_group', $product->Name, $newPost);
                            update_field('price', $variation->Qty_Price_1, $newPost);
                            update_field('summary', $variation->Table_Heading, $newPost);
                            foreach ($product->PictureIds as $pictureId) {
                                $jsonPicture = get_api_picture($pictureId);
                                $picture = json_decode($jsonPicture);
                                if (have_rows('product_images', $newPost)):
                                    $pictureUpdate = false;
                                    while (have_rows('product_images', $newPost)): the_row();
                                        $api_image_id = get_sub_field('api_image_id');
                                        if ($api_image_id == $picture->Id) {
                                            $pictureUpdate = true;
                                            update_sub_field('api_link', $picture->WebPath);
                                            break;
                                        }
                                    endwhile;
                                    if (!$pictureUpdate) {
                                        $row = array(
                                            'api_link' => $picture->WebPath,
                                            'api_image_id' => $picture->Id
                                        );

                                        add_row('product_images', $row, $newPost);
                                    };
                                else:
                                    $row = array(
                                        'api_link' => $picture->WebPath,
                                        'api_image_id' => $picture->Id
                                    );

                                    add_row('product_images', $row, $newPost);
                                endif;
                            }
                            foreach ($variation->PictureIds as $picture_id) {
                                $jsonPicture = get_api_picture($picture_id);
                                $picture = json_decode($jsonPicture);
                                if (have_rows('variation_images', $newPost)):
                                    $pictureUpdate = false;
                                    while (have_rows('variation_images', $newPost)): the_row();
                                        $api_image_id = get_sub_field('api_image_id');
                                        if ($api_image_id == $picture->Id) {
                                            $pictureUpdate = true;
                                            update_sub_field('api_link', $picture->WebPath);
                                            break;
                                        }
                                    endwhile;
                                    if (!$pictureUpdate) {
                                        $row = array(
                                            'api_link' => $picture->WebPath,
                                            'api_image_id' => $picture->Id
                                        );

                                        add_row('variation_images', $row, $newPost);
                                    };
                                else:
                                    $row = array(
                                        'api_link' => $picture->WebPath,
                                        'api_image_id' => $picture->Id
                                    );

                                    add_row('variation_images', $row, $newPost);
                                endif;
                            }
                            $attributeText = '';
                            foreach ($variation->AttributeValues as $attribute_value) {
                                $aName = getAttributeNameFromID($attribute_value->AttributeId, $attributes);
                                if ($aName != '0 ( )') {
                                    $attributeText .= $aName . ': ' . $attribute_value->Value . '<br>';
                                }
                            }
                            update_field('specifications', $attributeText, $newPost);
                        }

                    }
                }
            }
        } else {
            /**
             * No variations for product - so I am not creating anything
             */
        }


    }
}

function imageImported($id)
{
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'orderby' => 'post_date',
        'order' => 'desc',
        'posts_per_page' => '-1',
        'post_status' => 'inherit',
        'meta_key' => 'api_id',
        'meta_value' => $id
    );
    $loop = new WP_Query($args);

    return $loop->have_posts();
    /*$loop = get_posts($args);
    if (empty($loop)) {
        return false;
    } else {
        return true;
    }*/

}

function imageIDfromAPIID($id)
{
    $res = false;
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'orderby' => 'post_date',
        'order' => 'desc',
        'posts_per_page' => '-1',
        'post_status' => 'inherit',
        'meta_key' => 'api_id',
        'meta_value' => $id
    );
    $loop = new WP_Query($args);
    if ($loop->have_posts()) :
        while ($loop->have_posts()) : $loop->the_post();
            $res = get_the_ID();
            break;
        endwhile;
    endif;

    /*$loop = get_posts($args);

    foreach ($loop as $post): setup_postdata($post);
        $res = get_the_ID();
        break;
    endforeach;*/

    wp_reset_postdata();
    return $res;
}

function importSingleProductImages($productID, $variationID)
{
    $loop = new WP_Query(array('post_type' => 'grahams_product', 'posts_per_page' => -1));
    $res = 'Product Not Found';
    while ($loop->have_posts()) : $loop->the_post();
        $api_id = get_field('api_id');
        $variation_id = get_field('variation_id');
        if (($productID == $api_id) && ($variationID == $variation_id)) {
            $res = 'No Images Found For Product';
            if (have_rows('product_images')):
                while (have_rows('product_images')): the_row();
                    $api_link = get_sub_field('api_link');
                    $api_image_id = get_sub_field('api_image_id');
                    if (!imageImported($api_image_id)) {

                        /*$attach_id = insert_attachment_from_url($api_link, get_the_ID());
                        if ($attach_id) {
                            update_field('api_id', $api_image_id, $attach_id);
                            $res = 'Product Images Imported via WP_HTTP Object';
                        } else {
                            $res = 'Product Images Import WP_HTTP Object Error for - ' . $api_link;
                        }*/
                         $uploaddir = wp_upload_dir();
                         $filename = $api_image_id . '-' . uniqid() . '.jpg';
                         $uploadfile = $uploaddir['path'] . '/' . $filename;
                         $contents = get_image_file($api_link);
                         if ($contents) {
                             $savefile = fopen($uploadfile, 'w');
                             fwrite($savefile, $contents);
                             fclose($savefile);
                             $wp_filetype = wp_check_filetype(basename($filename), null);
                             $attachment = array(
                                 'post_mime_type' => $wp_filetype['type'],
                                 'post_title' => $filename,
                                 'post_content' => '',
                                 'post_status' => 'inherit'
                             );
                             $attach_id = wp_insert_attachment($attachment, $uploadfile);
                             $imagenew = get_post($attach_id);
                             $fullsizepath = get_attached_file($imagenew->ID);
                             $attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath);
                             wp_update_attachment_metadata($attach_id, $attach_data);
                             update_field('api_id', $api_image_id, $attach_id);
                         }
                         $res = 'Product Images Imported';
                    } else {
                        $res = 'Product Images Already Imported';
                    }
                endwhile;
            endif;
            if (have_rows('variation_images')):
                while (have_rows('variation_images')): the_row();
                    $api_link = get_sub_field('api_link');
                    $api_image_id = get_sub_field('api_image_id');
                    if (!imageImported($api_image_id)) {

                        /*$attach_id = insert_attachment_from_url($api_link, get_the_ID());
                        if ($attach_id) {
                            update_field('api_id', $api_image_id, $attach_id);
                            $res = 'Product Images Imported via WP_HTTP Object';
                        } else {
                            $res = 'Product Images Import WP_HTTP Object Error for - ' . $api_link;
                        }*/

                        $uploaddir = wp_upload_dir();
                        $filename = $api_image_id . '-' . uniqid() . '.jpg';
                        $uploadfile = $uploaddir['path'] . '/' . $filename;
                        $contents = get_image_file($api_link);
                        if ($contents) {
                            $savefile = fopen($uploadfile, 'w');
                            fwrite($savefile, $contents);
                            fclose($savefile);
                            $wp_filetype = wp_check_filetype(basename($filename), null);
                            $attachment = array(
                                'post_mime_type' => $wp_filetype['type'],
                                'post_title' => $filename,
                                'post_content' => '',
                                'post_status' => 'inherit'
                            );
                            $attach_id = wp_insert_attachment($attachment, $uploadfile);
                            $imagenew = get_post($attach_id);
                            $fullsizepath = get_attached_file($imagenew->ID);
                            $attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath);
                            wp_update_attachment_metadata($attach_id, $attach_data);
                            update_field('api_id', $api_image_id, $attach_id);
                        }
                        $res = 'Product Images Imported';
                    } else {
                        $res = 'Product Images Already Imported';
                    }
                endwhile;
            endif;
        }
    endwhile;
    wp_reset_postdata();
    return $res;
}

function importProductImages()
{
    $loop = new WP_Query(array('post_type' => 'grahams_product', 'posts_per_page' => -1));
    while ($loop->have_posts()) : $loop->the_post();
        if (have_rows('product_images')):
            while (have_rows('product_images')): the_row();
                $api_link = get_sub_field('api_link');
                $api_image_id = get_sub_field('api_image_id');
                if (!imageImported($api_image_id)) {

                    /*$attach_id = insert_attachment_from_url($api_link, get_the_ID());
                    if ($attach_id) {
                        update_field('api_id', $api_image_id, $attach_id);
                        $res = 'Product Images Imported via WP_HTTP Object';
                    } else {
                        $res = 'Product Images Import WP_HTTP Object Error for - ' . $api_link;
                    }*/

                    $uploaddir = wp_upload_dir();
                    $filename = $api_image_id . '-' . uniqid() . '.jpg';
                    $uploadfile = $uploaddir['path'] . '/' . $filename;
                    $contents = get_image_file($api_link);
                    if ($contents) {
                        $savefile = fopen($uploadfile, 'w');
                        fwrite($savefile, $contents);
                        fclose($savefile);
                        $wp_filetype = wp_check_filetype(basename($filename), null);
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => $filename,
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attach_id = wp_insert_attachment($attachment, $uploadfile);
                        $imagenew = get_post($attach_id);
                        $fullsizepath = get_attached_file($imagenew->ID);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        update_field('api_id', $api_image_id, $attach_id);
                    }
                }
            endwhile;
        endif;
    endwhile;
    wp_reset_postdata();
}

function importVariationImages()
{
    $loop = new WP_Query(array('post_type' => 'grahams_product', 'posts_per_page' => -1));
    while ($loop->have_posts()) : $loop->the_post();
        if (have_rows('variation_images')):
            while (have_rows('variation_images')): the_row();
                $api_link = get_sub_field('api_link');
                $api_image_id = get_sub_field('api_image_id');
                if (!imageImported($api_image_id)) {

                    /*$attach_id = insert_attachment_from_url($api_link, get_the_ID());
                    if ($attach_id) {
                        update_field('api_id', $api_image_id, $attach_id);
                        $res = 'Product Images Imported via WP_HTTP Object';
                    } else {
                        $res = 'Product Images Import WP_HTTP Object Error for - ' . $api_link;
                    }*/

                    $uploaddir = wp_upload_dir();
                    $filename = $api_image_id . '-' . uniqid() . '.jpg';
                    $uploadfile = $uploaddir['path'] . '/' . $filename;
                    $contents = get_image_file($api_link);
                    if ($contents) {
                        $savefile = fopen($uploadfile, 'w');
                        fwrite($savefile, $contents);
                        fclose($savefile);
                        $wp_filetype = wp_check_filetype(basename($filename), null);
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => $filename,
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $attach_id = wp_insert_attachment($attachment, $uploadfile);
                        $imagenew = get_post($attach_id);
                        $fullsizepath = get_attached_file($imagenew->ID);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        update_field('api_id', $api_image_id, $attach_id);
                    }
                }
            endwhile;
        endif;
    endwhile;
    wp_reset_postdata();
}

function importCategoryImages()
{
    $res = '';
    $terms = get_terms(array(
        'taxonomy' => 'grahamscat',
        'hide_empty' => true,
    ));
    foreach ($terms as $term) {
        $api_image_id = get_field('api_picture_ids', $term);
        if ($api_image_id) {
            $api_link = get_field('api_link', $term);
            $res .= $term->name . ' has image id - ' . $api_image_id . ' and link = ' . $api_link . '</br>';
            if (!imageImported($api_image_id)) {

                /*$attach_id = insert_attachment_from_url($api_link, get_the_ID());
                if ($attach_id) {
                    update_field('api_id', $api_image_id, $attach_id);
                    $res = 'Product Images Imported via WP_HTTP Object';
                } else {
                    $res = 'Product Images Import WP_HTTP Object Error for - ' . $api_link;
                }*/

                $uploaddir = wp_upload_dir();
                $filename = $api_image_id . '-' . uniqid() . '.jpg';
                $uploadfile = $uploaddir['path'] . '/' . $filename;
                $contents = get_image_file($api_link);
                if ($contents) {
                    $savefile = fopen($uploadfile, 'w');
                    fwrite($savefile, $contents);
                    fclose($savefile);
                    $wp_filetype = wp_check_filetype(basename($filename), null);
                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => $filename,
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    $attach_id = wp_insert_attachment($attachment, $uploadfile);
                    $imagenew = get_post($attach_id);
                    $fullsizepath = get_attached_file($imagenew->ID);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    update_field('api_id', $api_image_id, $attach_id);
                }
            }
        }
    }

    return $res;
}

function getPictureWebLink($id)
{
    $jsonPicture = get_api_picture($id);
    $picture = json_decode($jsonPicture);
    //error_log($picture->WebPath);
    return $picture->WebPath;
}

function importPicture($id, $webpath)
{
    $res = 'Picture Import Error';
    if (!imageImported($id)) {

       /* $attach_id = insert_attachment_from_url($webpath);
        if ($attach_id) {
            update_field('api_id', $id, $attach_id);
            $res = 'Product Images Imported via WP_HTTP Object';
        } else {
            $res = 'Product Images Import WP_HTTP Object Error for - ' . $webpath;
        }*/

        $uploaddir = wp_upload_dir();
        $filename = $id . '-' . uniqid() . '.jpg';
        $uploadfile = $uploaddir['path'] . '/' . $filename;
        $contents = get_image_file($webpath);
        if ($contents) {
            $savefile = fopen($uploadfile, 'w');
            fwrite($savefile, $contents);
            fclose($savefile);
            $wp_filetype = wp_check_filetype(basename($filename), null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => $filename,
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $uploadfile);
            $imagenew = get_post($attach_id);
            $fullsizepath = get_attached_file($imagenew->ID);
            $attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath);
            wp_update_attachment_metadata($attach_id, $attach_data);
            update_field('api_id', $id, $attach_id);
            $res = 'Image Imported Sucessfully';
        }
    } else {
        $res = 'Image Already Imported';
    }
    return $res;
}

function getCategoryFromId($id)
{
    $res = false;
    $terms = get_terms(array(
        'taxonomy' => 'grahamscat',
        'hide_empty' => true,
    ));
    foreach ($terms as $term) {
        $api_id = get_field('api_id', $term);
        if ($api_id == $id) {
            return $term;
        }
    }
    return $res;
}

function getCategoryImages($id)
{
    $term = getCategoryFromId($id);
    $res = array();
    if ($term) {
        $api_picture_ids = get_field('api_picture_ids', $term);
        $res = str_getcsv($api_picture_ids);
    }
    return json_encode($res);
}

function getSingleProductImages($id)
{
    $res = array();
    $loop = new WP_Query(array('post_type' => 'grahams_product', 'posts_per_page' => -1));
    while ($loop->have_posts()) : $loop->the_post();
        $api_id = get_field('api_id');
        if ($api_id == $id) {
            // error_log($id.' found getting images..');
            if (have_rows('product_images')):
                while (have_rows('product_images')): the_row();
                    $api_link = get_sub_field('api_link');
                    $api_image_id = get_sub_field('api_image_id');
                    $rec = array();
                    $rec['id'] = $api_image_id;
                    $rec['link'] = $api_link;
                    $res[] = $rec;
                endwhile;
            endif;
            if (have_rows('variation_images')):
                while (have_rows('variation_images')): the_row();
                    $api_link = get_sub_field('api_link');
                    $api_image_id = get_sub_field('api_image_id');
                    $rec = array();
                    $rec['id'] = $api_image_id;
                    $rec['link'] = $api_link;
                    $res[] = $rec;
                endwhile;
            endif;
        }
    endwhile;
    wp_reset_postdata();
    //error_log(print_r($res,TRUE));
    return $res;
}

function getProductImages()
{
    error_log('Begin: getProductImages');
    $res = array();

    $loop = new WP_Query(array('post_type' => 'grahams_product', 'posts_per_page' => -1));
    while ($loop->have_posts()) : $loop->the_post();
        if (have_rows('product_images')):
            while (have_rows('product_images')): the_row();
                $api_link = get_sub_field('api_link');
                $api_image_id = get_sub_field('api_image_id');
                if (!imageImported($api_image_id)) {
                    $rec = array();
                    $rec['id'] = $api_image_id;
                    $rec['link'] = $api_link;
                    if (!in_array($rec, $res)) {
                        $res[] = $rec;
                    }
                    //$res[] = $api_image_id;
                }
            endwhile;
        endif;
        if (have_rows('variation_images')):
            while (have_rows('variation_images')): the_row();
                $api_link = get_sub_field('api_link');
                $api_image_id = get_sub_field('api_image_id');
                if (!imageImported($api_image_id)) {
                    $rec = array();
                    $rec['id'] = $api_image_id;
                    $rec['link'] = $api_link;
                    if (!in_array($rec, $res)) {
                        $res[] = $rec;
                    }
                    //$res[] = $api_image_id;
                }
            endwhile;
        endif;
    endwhile;

    wp_reset_postdata();
    error_log('End: getProductImages');

    return $res;


}


function linkCategoryImages()
{
    $terms = get_terms(array(
        'taxonomy' => 'grahamscat',
        'hide_empty' => true,
    ));
    foreach ($terms as $term) {
        $api_id = get_field('api_picture_ids', $term);
        $attachmentID = imageIDfromAPIID($api_id);
        if ($attachmentID) {
            update_field('image', $attachmentID, $term);
        }
    }
}

function linkProductGroupImages($id)
{
    $res = '';
    $loop = new WP_Query(array('post_type' => 'grahams_product', 'posts_per_page' => -1));
    if ($loop->have_posts()):
        while ($loop->have_posts()) : $loop->the_post();
            $api_id = get_field('api_id');
            if ($api_id == $id) {
                $res .= 'Checking Product: ' . get_the_title() . '</br>';
                $postID = get_the_ID();
                if (have_rows('variation_images', $postID)):
                    $res .= '<br><span style="color: green;">Found Variation Images to Check</span></br>';
                    while (have_rows('variation_images', $postID)): the_row();
                        $api_id = get_sub_field('api_image_id');
                        $res .= 'Looking for Image: ' . $api_id . '</br>';
                        $attachmentID = imageIDfromAPIID($api_id);
                        $res .= 'AttachmentID for ' . $api_id . ' = ' . $attachmentID . '</br>';
                        if ($attachmentID) {
                            update_sub_field('image', $attachmentID);
                        } else {
                            $res .= 'Image ID ' . $api_id . ' not found in media library</br>';
                        }
                    endwhile;
                endif;
                if (have_rows('product_images', $postID)):
                    $res .= '<br><span style="color: green;">Found Variation Images to Check</span></br>';
                    while (have_rows('product_images', $postID)): the_row();
                        $api_id = get_sub_field('api_image_id');
                        $res .= 'Looking for Image: ' . $api_id . '</br>';
                        $attachmentID = imageIDfromAPIID($api_id);
                        $res .= 'AttachmentID for ' . $api_id . ' = ' . $attachmentID . '</br>';
                        if ($attachmentID) {
                            update_sub_field('image', $attachmentID);
                        } else {
                            $res .= 'Image ID ' . $api_id . ' not found in media library</br>';
                        }
                    endwhile;
                endif;
                $res .= '<hr>';
            }
        endwhile;
    endif;
    wp_reset_postdata();

    return $res;
}

function linkProductImages()
{
    error_log('Begin: linkProductImages');
    $res = '';
    $args = array('post_type' => 'grahams_product', 'posts_per_page' => -1);
    $loop = new WP_Query($args);
    if ($loop->have_posts()):
        while ($loop->have_posts()) : $loop->the_post();
            $res .= 'Checking Product: ' . get_the_title() . '</br>';
            $postID = get_the_ID();
            if (have_rows('variation_images', $postID)):
                $res .= '<br><span style="color: green;">Found Variation Images to Check</span></br>';
                while (have_rows('variation_images', $postID)): the_row();
                    $api_id = get_sub_field('api_image_id');
                    $res .= 'Looking for Image: ' . $api_id . '</br>';
                    $attachmentID = imageIDfromAPIID($api_id);
                    $res .= 'AttachmentID for ' . $api_id . ' = ' . $attachmentID . '</br>';
                    if ($attachmentID) {
                        update_sub_field('image', $attachmentID);
                    } else {
                        $res .= 'Image ID ' . $api_id . ' not found in media library</br>';
                    }
                endwhile;
            endif;
            if (have_rows('product_images', $postID)):
                $res .= '<br><span style="color: green;">Found Variation Images to Check</span></br>';
                while (have_rows('product_images', $postID)): the_row();
                    $api_id = get_sub_field('api_image_id');
                    $res .= 'Looking for Image: ' . $api_id . '</br>';
                    $attachmentID = imageIDfromAPIID($api_id);
                    $res .= 'AttachmentID for ' . $api_id . ' = ' . $attachmentID . '</br>';
                    if ($attachmentID) {
                        update_sub_field('image', $attachmentID);
                    } else {
                        $res .= 'Image ID ' . $api_id . ' not found in media library</br>';
                    }
                endwhile;
            endif;
            $res .= '<hr>';
        endwhile;
    endif;
    wp_reset_postdata();

    error_log('End: linkProductImages');

    return $res;
}

function linkVariationImages()
{
    $res = '';
    $loop = new WP_Query(array('post_type' => 'grahams_product', 'posts_per_page' => -1));
    if ($loop->have_posts()):
        while ($loop->have_posts()) : $loop->the_post();
            $res .= 'Checking Product: ' . get_the_title() . '</br>';
            $postID = get_the_ID();
            if (have_rows('variation_images', $postID)):
                $res .= '<br><span style="color: green;">Found Variation Images to Check</span></br>';
                while (have_rows('variation_images', $postID)): the_row();
                    $api_id = get_sub_field('api_image_id');
                    $res .= 'Looking for Image: ' . $api_id . '</br>';
                    $attachmentID = imageIDfromAPIID($api_id);
                    $res .= 'AttachmentID for ' . $api_id . ' = ' . $attachmentID . '</br>';
                    if ($attachmentID) {
                        update_sub_field('image', $attachmentID);
                    } else {
                        $res .= 'Image ID ' . $api_id . ' not found in media library</br>';
                    }
                endwhile;
            endif;
            $res .= '<hr>';
        endwhile;
    endif;
    wp_reset_postdata();
    return $res;
}

function deleteAllProducts() {
    /*global $wpdb;
    $result = $wpdb->query(
        $wpdb->prepare("
            DELETE posts,pt,pm
            FROM wp_posts posts
            LEFT JOIN wp_term_relationships pt ON pt.object_id = posts.ID
            LEFT JOIN wp_postmeta pm ON pm.post_id = posts.ID
            WHERE posts.post_type = %s
            ",
            'grahams_product'
        )
    );
    return $result!==false;*/
    $args = array( 'posts_per_page' => -1, 'post_type' => 'grahams_product', );
    $allposts= get_posts( $args );
    foreach ( $allposts as $post ) :
        wp_delete_post( $post->ID, true );
    endforeach;
}

function deleteProductImages() {
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'orderby' => 'post_date',
        'order' => 'desc',
        'posts_per_page' => '-1',
        'post_status' => 'inherit'
    );
    $loop = get_posts($args);
    foreach ($loop as $attachment):
        wp_delete_attachment($attachment,true);
    endforeach;
}
