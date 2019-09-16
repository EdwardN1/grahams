<?php
// Create a stream
$opts = array(
'http' => array(
'method' => "GET",
'header' => "Ocp-Apim-Subscription-Key: e57667e8bffa4384a6ce53f80521677a"
)
);

$context = stream_context_create( $opts );

?>

<h3>Attributes - Get all</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Attributes/', false, $context );
?>

<h3>Categories - Get all</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Categories/', false, $context );
?>

<h3>Category - Gas</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Categories/14', false, $context );
?>

<h3>Products - Get all</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Products/', false, $context );
?>

<h3>Product - 1806018</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Products/1806018', false, $context );
?>

<h3>Product Image - 1806018 - 16149</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Pictures/16149', false, $context );
?>

<h3>Product Image - 1806018 - 19649</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Pictures/19649', false, $context );
?>


<h3>Product - 1806022</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Products/1806022', false, $context );
?>

<h3>Product Image - 1806022 - 13354</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Pictures/13354', false, $context );
?>

<h3>Product Image - 1806022 - 14422</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Pictures/14422', false, $context );
?>

<h3>Product - 1806293</h3>
<?php
// Open the file using the HTTP headers set above
echo file_get_contents( 'https://epim.azure-api.net/Grahams/api/Products/1806293', false, $context );
?>
