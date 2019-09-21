<?php
// Create a stream
$opts = array(
	'http' => array(
		'method' => "GET",
		'header' => "Ocp-Apim-Subscription-Key: e57667e8bffa4384a6ce53f80521677a"
	)
);

$context = stream_context_create( $opts );

function displayJSON( $apiCall ) {
	$allAttributes = new RecursiveIteratorIterator( new RecursiveArrayIterator( json_decode( $apiCall, true ) ), RecursiveIteratorIterator::SELF_FIRST );
	echo '<div style="padding-bottom: 10px; padding-top: 10px; border-bottom: 1px solid black">';
	echo '<p>';
	$indent=1;
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

?>

<!--<h3>Attributes - Get all</h3>
<?php
/*$apiCall = file_get_contents( 'https://epim.azure-api.net/Grahams/api/Attributes/', false, $context );
displayJSON( $apiCall );
*/?>

<h3>Categories - Get all</h3>
<?php
/*$apiCall = file_get_contents( 'https://epim.azure-api.net/Grahams/api/Categories/', false, $context );
displayJSON( $apiCall );
*/?>

<h3>Category - Gas</h3>
--><?php
/*$apiCall = file_get_contents( 'https://epim.azure-api.net/Grahams/api/Categories/80', false, $context );
displayJSON( $apiCall );
*/?>

<h3>Products - Get all</h3>
<?php
$apiCall = file_get_contents( 'https://epim.azure-api.net/Grahams/api/Products/', false, $context );
$allProducts = json_decode($apiCall);
$limit = $allProducts->Limit;
$TotalResults = $allProducts->TotalResults;
$apiCall = file_get_contents( 'https://epim.azure-api.net/Grahams/api/Products/?limit='.$TotalResults, false, $context );
$allProducts = json_decode($apiCall);
?>

<?php
displayJSON( $apiCall );
?>

<h3>All Products Iterated</h3>
<?php
$allProductsResults=$allProducts->Results;
foreach ($allProductsResults as $productResult) {
	$apiCall = file_get_contents( 'https://epim.azure-api.net/Grahams/api/Products/'.$productResult->Id, false, $context );
	displayJSON( $apiCall );
	echo '<h4>Variations for '.$productResult->Id.'</h4>';
	$productVariations = $productResult->VariationIds;
	foreach ($productVariations as $productVariation) {
	    $apiCall = file_get_contents( 'https://epim.azure-api.net/Grahams/api/Variations/'.$productVariation, false, $context );
	    displayJSON($apiCall);
    }
}
?>
