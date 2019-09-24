<?php


function displayJSON( $apiCall ) {
	$allAttributes = new RecursiveIteratorIterator( new RecursiveArrayIterator( json_decode( $apiCall, true ) ), RecursiveIteratorIterator::SELF_FIRST );
	echo '<div style="padding-bottom: 10px; padding-top: 10px; border-bottom: 1px solid black">';
	echo '<p>';
	$indent = 1;
	foreach ( $allAttributes as $key => $val ) {
		if ( is_array( $val ) ) {
			echo "</p><p>$key:<br>";
		} else {
			echo "<span style='padding-left: 1em;'> $key => $val</span><br>";
		}

	}
	echo '</p>';
	echo '</div>';
}

function make_api_call( $url ) {
	$opts    = array(
		'http' => array(
			'method' => "GET",
			'header' => "Ocp-Apim-Subscription-Key: e57667e8bffa4384a6ce53f80521677a"
		)
	);
	$context = stream_context_create( $opts );
	$apiCall = file_get_contents( $url, false, $context );

	return $apiCall;
}

function get_api_updates_since( $datetime = '2002-10-02T10:00:00-00:00' ) {
	return make_api_call( 'https://epim.azure-api.net/Grahams/api/ProductsUpdatedSince?ChangedSinceUTC=' . $datetime );
}

function get_api_all_attributes() {
	return make_api_call( 'https://epim.azure-api.net/Grahams/api/Attributes' );
}

function get_api_all_categories() {
	return make_api_call( 'https://epim.azure-api.net/Grahams/api/Categories' );
}

function get_api_category( $id ) {
	return make_api_call( 'https://epim.azure-api.net/Grahams/api/Categories/' . $id );
}

function get_api_picture( $id ) {
	return make_api_call( 'https://epim.azure-api.net/Grahams/api/Pictures/' . $id );
}

function get_api_all_products() {
	$apiCall      = make_api_call( 'https://epim.azure-api.net/Grahams/api/Products/' );
	$allProducts  = json_decode( $apiCall );
	$TotalResults = $allProducts->TotalResults;

	return make_api_call( 'https://epim.azure-api.net/Grahams/api/Products/?limit=' . $TotalResults );
}

function get_api_products_in_category( $id ) {
	return make_api_call( 'https://epim.azure-api.net/Grahams/api/ProductsInCategory/' . $id );
}

function get_api_product( $id ) {
	return make_api_call( 'https://epim.azure-api.net/Grahams/api/Products/' . $id );
}

function get_api_variation( $id ) {
	return make_api_call( 'https://epim.azure-api.net/Grahams/api/Variations/' . $id );
}

function returnCategory( $id, $categories ) {
	$res = false;
	foreach ( $categories as $category ) {
		if ( $category->Id == $id ) {
			$res = clone $category;
			break;
		}
	}

	return $res;
}

function sortCategoriesByID_cmp( $a, $b ) {
	$res = 0;
	if ( $a->Id < $b->Id ) {
		$res = - 1;
	}
	if ( $a->Id > $b->Id ) {
		$res = 1;
	}

	return $res;
}

function getTermFromID( $id, $terms ) {
	$res = false;
	foreach ( $terms as $term ) {
		$apiID = get_field( 'api_id', $term );
		if ( $apiID == $id ) {
			$res = $term;
			break;
		}
	}

	return $res;
}

function create_categories() {
	$jsonCategories = get_api_all_categories();
	$api_categories = json_decode( $jsonCategories );
	usort( $api_categories, "sortCategoriesByID_cmp" );
	$terms = get_terms( [
		'taxonomy'   => 'grahamscat',
		'hide_empty' => false,
	] );
	foreach ( $api_categories as $category ) {
		$term = getTermFromID( $category->Id, $terms );
		if ( $term ) {
			wp_update_term( $term->term_id, 'grahamscat', array( 'name' => $category->Name ) );
			update_field( 'api_parents', $category->ParentId, 'grahamscat_' . $term->term_id );
			$pSuffix = '';
			$pField  = '';
			foreach ( $category->PictureIds as $picture_id ) {
				$pField  .= $pSuffix;
				$pSuffix = ',';
				$pField  .= $picture_id;
			}
			if ( $pField != '' ) {
				update_field( 'api_picture_ids', $pField, 'grahamscat_' . $term->term_id );
			}
		} else {
			$newTerm = wp_insert_term( $category->Name, 'grahamscat' );
			if ( is_wp_error( $newTerm ) ) {
				//error_log($newTerm->get_error_message().' Creating API_ID='.$category->Id.' Name='.$category->Name);
			} else {
				update_field( 'api_id', $category->Id, 'grahamscat_' . $newTerm["term_id"] );
				update_field( 'api_parents', $category->ParentId, 'grahamscat_' . $newTerm["term_id"] );
				$pSuffix = '';
				$pField  = '';
				foreach ( $category->PictureIds as $picture_id ) {
					$pField  .= $pSuffix;
					$pSuffix = ',';
					$pField  .= $picture_id;
				}
				if ( $pField != '' ) {
					update_field( 'api_picture_ids', $pField, 'grahamscat_' . $newTerm["term_id"] );
				}
			}
		}
	}
}

function sort_categories() {
	$terms = get_terms( [
		'taxonomy'   => 'grahamscat',
		'hide_empty' => false,
	] );
	foreach ( $terms as $term ) {
		$api_parents = get_field( 'api_parents', $term );
		if ( $api_parents != '' ) {
			$parent = getTermFromID( $api_parents, $terms );
			if ( $parent ) {
				wp_update_term( $term->term_id, 'grahamscat', array( 'parent' => $parent->term_id ) );
			}
		}
	}
}

function getAttributeNameFromID ($id,$attributes) {
	$res = 'Name Not Found';
	foreach ($attributes as $attribute) {
		if($attribute->Id==$id) {
			$res = $attribute->Name;
			break;
		}
	}
	return $res;
}

function getProductFromID( $productID, $variationID ) {
	$res  = false;
	$loop = new WP_Query( array( 'post_type' => 'grahams_product', 'posts_per_page' => - 1 ) );
	while ( $loop->have_posts() ) : $loop->the_post();
		$api_id = get_field( 'api_id' );
		if($api_id==$productID) {
			$variation_id = get_field( 'variation_id' );
			if($variation_id==$variationID) {
				$res = get_the_ID();
				break;
			}
		}
	endwhile;
	wp_reset_postdata();
	return $res;
}

function create_products() {
	$jsonProducts = get_api_all_products();
	$products = json_decode($jsonProducts);
	$jsonAttributes = get_api_all_attributes();
	$attributes     = json_decode( $jsonAttributes );
	foreach ($products->Results as $product) {
		$terms = get_terms( [
			'taxonomy'   => 'grahamscat',
			'hide_empty' => false,
		] );
		$catIds = array();
		foreach ($product->CategoryIds as $category_id) {
			$realCatID = getTermFromID($category_id,$terms);
			if($realCatID) {
				$catIds[] = $realCatID->term_id;
			}
		}
		if($product->VariationIds) {
			foreach ($product->VariationIds as $variation_id) {
				$jsonVariation = get_api_variation( $variation_id );
				$variation     = json_decode( $jsonVariation );
				$id = getProductFromID($product->Id,$variation->Id);
				$newPost = false;
				if ( $id ) {
					$thePost = array(
						'ID'           => $id,
						'post_title'   => $variation->Name,
						'post_content' => 'Imported',
						'post_status'  => 'publish',
					);
					if ( $thePost ) {
						$newPost = wp_update_post( $thePost );
						if ( $newPost ) {
							wp_set_object_terms( $newPost, $catIds, 'grahamscat' );
							update_field( 'api_id', $product->Id, $newPost );
							update_field( 'variation_id', $variation->Id, $newPost );
							update_field( 'description', $product->BulletText, $newPost );
							update_field( 'code', $variation->SKU, $newPost );
							update_field( 'product_group', $product->Name, $newPost );
							update_field( 'price', $variation->Price, $newPost );
							$attributeText = '';
							foreach ( $variation->AttributeValues as $attribute_value ) {
								$aName = getAttributeNameFromID($attribute_value->AttributeId,$attributes);
								$attributeText .= $aName.': '.$attribute_value->Value.'<br>';
							}
							update_field( 'specifications', $attributeText, $newPost );
						}
					}
				} else {
					$thePost = array(
						'post_title'   => $variation->Name,
						'post_type'    => 'grahams_product',
						'post_content' => 'Imported',
						'post_status'  => 'publish',
					);
					if ( $thePost ) {
						$newPost = wp_insert_post( $thePost );
						if ( $newPost ) {
							wp_set_object_terms( $newPost, $catIds, 'grahamscat' );
							update_field( 'api_id', $product->Id, $newPost );
							update_field( 'variation_id', $variation->Id, $newPost );
							update_field( 'description', $product->BulletText, $newPost );
							update_field( 'code', $variation->SKU, $newPost );
							update_field( 'product_group', $product->Name, $newPost );
							update_field( 'price', $variation->Price, $newPost );
							$attributeText = '';
							foreach ( $variation->AttributeValues as $attribute_value ) {
								$aName = getAttributeNameFromID($attribute_value->AttributeId,$attributes);
								$attributeText .= $aName.': '.$attribute_value->Value.'<br>';
							}
							update_field( 'specifications', $attributeText, $newPost );
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
