<?php

function checkSecure() {
	if ( ! check_ajax_referer( 'epim-security-nonce', 'security' ) ) {
		wp_send_json_error( 'Invalid security token sent.' );
		wp_die();
	}
}


/**
 * ========================== Actions ==============================
 */


add_action( 'wp_ajax_get_all_categories', 'ajax_get_api_all_categories' );
add_action( 'wp_ajax_get_all_attributes', 'ajax_get_api_all_attributes' );
add_action( 'wp_ajax_get_all_products', 'ajax_get_api_all_products' );
add_action( 'wp_ajax_get_all_changed_products_since', 'ajax_get_api_all_changed_products_since' );
add_action( 'wp_ajax_get_product', 'ajax_get_api_product' );
add_action( 'wp_ajax_get_category', 'ajax_get_api_category' );
add_action( 'wp_ajax_get_picture', 'ajax_get_api_picture' );
add_action( 'wp_ajax_get_variation', 'ajax_get_api_variation' );
add_action( 'wp_ajax_create_category', 'ajax_create_category' );
add_action( 'wp_ajax_get_category_images', 'ajax_get_category_images' );
add_action( 'wp_ajax_get_picture_web_link', 'ajax_get_picture_web_link' );
add_action( 'wp_ajax_import_picture', 'ajax_import_picture' );
add_action( 'wp_ajax_sort_categories', 'ajax_sort_categories' );
add_action( 'wp_ajax_cat_image_link', 'ajax_cat_image_link' );
add_action( 'wp_ajax_product_image_link', 'ajax_product_image_link' );
add_action( 'wp_ajax_product_group_image_link', 'ajax_product_group_image_link' );
add_action( 'wp_ajax_create_product', 'ajax_create_product' );
add_action( 'wp_ajax_get_product_images', 'ajax_get_product_images' );
add_action( 'wp_ajax_product_ID_code', 'ajax_product_ID_from_code' );
add_action( 'wp_ajax_get_single_product_images', 'ajax_get_single_product_images' );
add_action( 'wp_ajax_import_single_product_images', 'ajax_import_single_product_images' );
add_action( 'wp_ajax_image_imported', 'ajax_image_imported' );


function ajax_image_imported() {
	checkSecure();
    if ( ! empty( $_POST['ID'] ) ) {
        if(imageImported($_POST['ID'])) {
            echo 'Image Imported';
        } else {
            echo 'Image not Imported';
        }
    }
    exit;
}

function ajax_import_single_product_images() {
	checkSecure();
    if ( ! empty( $_POST['productID'] ) ) {
        if ( ! empty( $_POST['variationID'] ) ) {
            $response = importSingleProductImages($_POST['productID'], $_POST['variationID']);
            echo $response;
        } else {
            echo 'error no variationID supplied';
        }
    } else {
        echo 'error no productID supplied';
    }
    exit;
}


function ajax_get_single_product_images() {
    if ( ! empty( $_POST['ID'] ) ) {
        $response = getSingleProductImages($_POST['ID']);
        header( "Content-Type: application/json charset=utf-8" );
        echo json_encode($response);
    } else {
        echo 'error no ID supplied';
    }
    exit;
}

function ajax_product_ID_from_code() {
	checkSecure();
    $response = 'Not Found';
    if ( ! empty( $_POST['CODE'] ) ) {
        $response = getAPIIDFromCode($_POST['CODE']);
        //error_log('Code = '.$_POST['CODE'].' | API = '.$response);
    }
    echo $response;
    exit;
}

function ajax_get_api_product() {
    if ( ! empty( $_POST['ID'] ) ) {
        $jsonResponse = get_api_product( $_POST['ID'] );
        $response     = $jsonResponse;
        header( "Content-Type: application/json" );
        echo json_encode( $response );
    } else {
        echo 'error no ID supplied';
    }
    exit;
}

function ajax_get_category_images() {
	if ( ! empty( $_POST['ID'] ) ) {
		header( "Content-Type: application/json" );
		echo getCategoryImages( $_POST['ID'] );
	}
	exit;
}

function ajax_get_product_images() {
	$response = getProductImages();
	//error_log(json_encode($response));
    header( "Content-Type: application/json charset=utf-8" );
	echo json_encode($response);
	exit;
}

function ajax_create_product() {
	if ( ! empty( $_POST['productID'] ) ) {
		if ( ! empty( $_POST['variationID'] ) ) {
			if ( ! empty( $_POST['productName'] ) ) {
				echo create_product( $_POST['productID'], $_POST['variationID'], $_POST['bulletText'], $_POST['productName'], $_POST['categoryIDs'], $_POST['pictureIDs'] );
				exit;
			} else {
				echo 'Product Creation Failed - no Product Name supplied';
				exit;
			}
		} else {
			echo 'Product Creation Failed - no Variation ID supplied';
			exit;
		}
	} else {
		echo 'Product Creation Failed - no Product ID';
		exit;
	}

}

function ajax_cat_image_link() {
	linkCategoryImages();
	echo 'Category Images Linked';
	exit;
}

function ajax_product_image_link() {
    echo linkProductImages();
    //linkVariationImages();
    //echo 'Product Images Linked';
    exit;
}

function ajax_product_group_image_link() {
    if ( ! empty( $_POST['productID'] ) ) {
        echo linkProductGroupImages($_POST['productID']);
    }

    exit;
}

function ajax_sort_categories() {
	sort_categories();
	echo 'Categories Sorted';
	exit;
}

function ajax_import_picture() {
	if ( ! empty( $_POST['ID'] ) ) {
		if ( ! empty( $_POST['weblink'] ) ) {
			echo importPicture( $_POST['ID'], $_POST['weblink'] );
		}
	}
	exit;
}

function ajax_get_picture_web_link() {
	$response = '';
	if ( ! empty( $_POST['ID'] ) ) {
		$response = get_api_picture( $_POST['ID'] );
	}
	header( "Content-Type: application/json" );
	echo json_encode( $response );
	exit;
}

function ajax_get_api_all_categories() {
	$jsonResponse = get_api_all_categories();
	$response     = $jsonResponse;
	header( "Content-Type: application/json" );
	echo json_encode( $response );
	exit;
}

function ajax_get_api_all_attributes() {
	$jsonResponse = get_api_all_attributes();
	$response     = $jsonResponse;
	header( "Content-Type: application/json" );
	echo json_encode( $response );
	exit;
}

function ajax_get_api_all_products() {
	$jsonResponse = get_api_all_products();
	$response     = json_decode( $jsonResponse );
	//header( "Content-Type: application/json" );
	echo json_encode( $response->Results );
	exit;
}

function ajax_get_api_all_changed_products_since() {
    if ( ! empty( $_POST['timeCode'] ) ) {
        $jsonResponse = get_api_all_changed_products_since($_POST['timeCode'] );
        $response = json_decode($jsonResponse);
        //header( "Content-Type: application/json" );
        echo json_encode($response->Results);
    }
    exit;
}



function ajax_get_api_category() {
	if ( ! empty( $_POST['ID'] ) ) {

		$jsonResponse = get_api_category( $_POST['ID'] );
		$response     = $jsonResponse;
		header( "Content-Type: application/json" );
		echo json_encode( $response );
	} else {
		echo 'error no ID supplied';
	}
	exit;
}

function ajax_get_api_picture() {
	if ( ! empty( $_POST['ID'] ) ) {
		//error_log('Getting Picture: '.$_POST['ID']);
		$jsonResponse = get_api_picture( $_POST['ID'] );
		$response     = $jsonResponse;
		header( "Content-Type: application/json" );
		echo json_encode( $response );
	} else {
		//error_log('error no ID supplied in ajax_get_api_picture');
	}
	exit;
}

function ajax_get_api_variation() {
	if ( ! empty( $_POST['ID'] ) ) {
		$jsonResponse = get_api_variation( $_POST['ID'] );
		$response     = $jsonResponse;
		header( "Content-Type: application/json" );
		echo json_encode( $response );
	} else {
		echo 'error no ID supplied';
	}
	exit;
}

function ajax_create_category() {
	$response = 'Nothing Happened!!';
	if ( ! empty( $_POST['ID'] ) ) {
		if ( ! empty( $_POST['name'] ) ) {
			$response = create_category( $_POST['ID'], $_POST['name'], $_POST['ParentID'], $_POST['WebPath'], $_POST['picture_ids'] );
		}
	}
	echo $response;
	exit;
}





//[{"Id":12,"Name":"Heating","ParentId":null,"PictureIds":[]},{"Id":80,"Name":"Water Treatment","ParentId":12,"PictureIds":[22609]},{"Id":81,"Name":"Filters","ParentId":80,"PictureIds":[22607]},{"Id":354,"Name":"Limescale Reduction","ParentId":80,"PictureIds":[14719]},{"Id":355,"Name":"Accessories","ParentId":80,"PictureIds":[13593]},{"Id":356,"Name":"Inhibitors","ParentId":80,"PictureIds":[13572]},{"Id":357,"Name":"Cleaner","ParentId":80,"PictureIds":[13579]},{"Id":358,"Name":"Leak Sealer","ParentId":80,"PictureIds":[13585]},{"Id":361,"Name":"Radiators & Fan Convectors","ParentId":12,"PictureIds":[13437]},{"Id":362,"Name":"Standard Radiators","ParentId":361,"PictureIds":[13418]},{"Id":363,"Name":"Heating Controls","ParentId":12,"PictureIds":[13441]},{"Id":364,"Name":"Thermostats & Programmers","ParentId":363,"PictureIds":[13445]},{"Id":365,"Name":"Boilers & Accessories","ParentId":12,"PictureIds":[13349]},{"Id":366,"Name":"Flues & Accessories","ParentId":365,"PictureIds":[22662]},{"Id":370,"Name":"Towel Warmers","ParentId":361,"PictureIds":[13437]},{"Id":371,"Name":"Radiator Valves & Accessories","ParentId":361,"PictureIds":[13422]},{"Id":372,"Name":"Fan convectors","ParentId":361,"PictureIds":[13439]},{"Id":373,"Name":"Smart Controls","ParentId":363,"PictureIds":[13194]},{"Id":374,"Name":"TRVs","ParentId":363,"PictureIds":[13425]},{"Id":376,"Name":"Gas Boilers","ParentId":365,"PictureIds":[19649]},{"Id":381,"Name":"Hotwater Cylinders","ParentId":12,"PictureIds":[13413]},{"Id":382,"Name":"Vented Cylinders","ParentId":381,"PictureIds":[13412]},{"Id":383,"Name":"Unvented Cylinders","ParentId":381,"PictureIds":[13413]},{"Id":385,"Name":"Water Heaters","ParentId":12,"PictureIds":[13591]},{"Id":387,"Name":"Instantaneous Hot Water","ParentId":385,"PictureIds":[13588]},{"Id":389,"Name":"Accessories","ParentId":385,"PictureIds":[13593]},{"Id":399,"Name":"Sealed Systems, Controls & Valves","ParentId":12,"PictureIds":[22697]},{"Id":400,"Name":"Expansion Vessels","ParentId":399,"PictureIds":[22697]},{"Id":402,"Name":"Heating Valves & Accessories","ParentId":399,"PictureIds":[22698]},{"Id":403,"Name":"Pumps","ParentId":12,"PictureIds":[13479]},{"Id":404,"Name":"Circulating Pumps","ParentId":403,"PictureIds":[13479]},{"Id":431,"Name":"Plumbing Supplies","ParentId":null,"PictureIds":[]},{"Id":432,"Name":"Pipe, Insulation & Fixings","ParentId":431,"PictureIds":[13622]},{"Id":434,"Name":"Copper Tube","ParentId":432,"PictureIds":[13622]},{"Id":435,"Name":"Pipe Insulation","ParentId":432,"PictureIds":[13624]},{"Id":437,"Name":"Metal Pipe Fittings","ParentId":431,"PictureIds":[22584]},{"Id":438,"Name":"End Feed Fittings","ParentId":437,"PictureIds":[22584]},{"Id":439,"Name":"Solder Ring Fittings","ParentId":437,"PictureIds":[22587]},{"Id":440,"Name":"Compression Fittings","ParentId":437,"PictureIds":[13700]},{"Id":441,"Name":"Push Fittings","ParentId":437,"PictureIds":[13710]},{"Id":442,"Name":"Press Fittings","ParentId":437,"PictureIds":[13715]},{"Id":445,"Name":"Plumbers Brassware","ParentId":431,"PictureIds":[13720]},{"Id":446,"Name":"Valves","ParentId":445,"PictureIds":[22717]},{"Id":447,"Name":"Tap Connectors","ParentId":445,"PictureIds":[13724]},{"Id":449,"Name":"Plastic Plumbing","ParentId":431,"PictureIds":[13814]},{"Id":450,"Name":"Pipe","ParentId":449,"PictureIds":[13811]},{"Id":451,"Name":"Fittings","ParentId":449,"PictureIds":[13815]},{"Id":452,"Name":"Soil & Waste","ParentId":449,"PictureIds":[13889]},{"Id":457,"Name":"Plumbing Sundries","ParentId":431,"PictureIds":[13783]},{"Id":458,"Name":"Plumbing Sundries","ParentId":457,"PictureIds":[13800]},{"Id":460,"Name":"Washers & Olives","ParentId":457,"PictureIds":[13790]},{"Id":461,"Name":"Pipe Clips & Collars","ParentId":457,"PictureIds":[13791]},{"Id":462,"Name":"Tape","ParentId":457,"PictureIds":[13808]},{"Id":463,"Name":"Bathrooms & Kitchens","ParentId":null,"PictureIds":[]},{"Id":464,"Name":"Bathrooms","ParentId":463,"PictureIds":[22743]},{"Id":465,"Name":"Bathroom Suites/Sanitaryware","ParentId":464,"PictureIds":[22742]},{"Id":469,"Name":"Macerators","ParentId":464,"PictureIds":[22744]},{"Id":470,"Name":"Cisterns & Spares","ParentId":464,"PictureIds":[22746]},{"Id":472,"Name":"Showering","ParentId":463,"PictureIds":[22757]},{"Id":473,"Name":"Showers","ParentId":472,"PictureIds":[22747]},{"Id":476,"Name":"Shower Trays","ParentId":472,"PictureIds":[22748]},{"Id":477,"Name":"Pumps","ParentId":472,"PictureIds":[22749]},{"Id":478,"Name":"Accessories","ParentId":472,"PictureIds":[22751]},{"Id":479,"Name":"Bathroom Taps & Fittings","ParentId":463,"PictureIds":[22758]},{"Id":480,"Name":"Taps","ParentId":479,"PictureIds":[22753]},{"Id":481,"Name":"Wastes","ParentId":479,"PictureIds":[22756]},{"Id":495,"Name":"Kitchens","ParentId":463,"PictureIds":[22762]},{"Id":496,"Name":"Kitchen Sinks","ParentId":495,"PictureIds":[22762]},{"Id":497,"Name":"Kitchen Taps","ParentId":495,"PictureIds":[22763]},{"Id":502,"Name":"Tools & Materials","ParentId":null,"PictureIds":[]},{"Id":503,"Name":"Accessories & Fixings","ParentId":502,"PictureIds":[22706]},{"Id":504,"Name":"Electrical Accessories","ParentId":503,"PictureIds":[22701]},{"Id":505,"Name":"Tape","ParentId":503,"PictureIds":[22702]},{"Id":506,"Name":"Accessories (skeleton gun, sealant remover)","ParentId":503,"PictureIds":[22704]},{"Id":507,"Name":"Screws & Fixings","ParentId":503,"PictureIds":[22700]},{"Id":508,"Name":"Tools","ParentId":502,"PictureIds":[22714]},{"Id":509,"Name":"Plumbing Tools","ParentId":508,"PictureIds":[22709]},{"Id":510,"Name":"Heating Tools","ParentId":508,"PictureIds":[22710]},{"Id":511,"Name":"General Tools","ParentId":508,"PictureIds":[22711]},{"Id":512,"Name":"Tiling Tools","ParentId":508,"PictureIds":[22713]},{"Id":514,"Name":"Heating Consumables","ParentId":502,"PictureIds":[22715]},{"Id":515,"Name":"Sealants","ParentId":514,"PictureIds":[22718]},{"Id":516,"Name":"Silicones","ParentId":514,"PictureIds":[22719]},{"Id":517,"Name":"Adhesives","ParentId":514,"PictureIds":[22720]},{"Id":518,"Name":"Heating Sundries","ParentId":502,"PictureIds":[22702]},{"Id":519,"Name":"Smoke pellets and testing","ParentId":518,"PictureIds":[22722]},{"Id":520,"Name":"Tape","ParentId":518,"PictureIds":[22724]},{"Id":522,"Name":"Warning Signage","ParentId":518,"PictureIds":[22725]},{"Id":523,"Name":"Washers","ParentId":518,"PictureIds":[22727]},{"Id":527,"Name":"Report Pads","ParentId":518,"PictureIds":[22730]},{"Id":528,"Name":"PPE, Cleaning & Maintenance","ParentId":502,"PictureIds":[22736]},{"Id":529,"Name":"PPE","ParentId":528,"PictureIds":[22738]},{"Id":530,"Name":"Cleaning & Maintenance","ParentId":528,"PictureIds":[22737]},{"Id":531,"Name":"Alarms & Detectors","ParentId":502,"PictureIds":[22739]},{"Id":532,"Name":"Co Alarms & Detectors","ParentId":531,"PictureIds":[22740]},{"Id":534,"Name":"Commercial Supplies","ParentId":null,"PictureIds":[]},{"Id":558,"Name":"Commercial Bathrooms","ParentId":534,"PictureIds":[14233]},{"Id":559,"Name":"Sanitaryware","ParentId":558,"PictureIds":[14233]},{"Id":563,"Name":"Taps","ParentId":558,"PictureIds":[14237]},{"Id":574,"Name":"Flushing Controls","ParentId":558,"PictureIds":[22731]},{"Id":612,"Name":"Baths & Panels","ParentId":464,"PictureIds":[]},{"Id":613,"Name":"Accessories","ParentId":479,"PictureIds":[27127]},{"Id":615,"Name":"Baths & Panels","ParentId":472,"PictureIds":[]}]
