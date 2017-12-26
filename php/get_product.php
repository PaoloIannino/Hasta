<?php

	// Include useful php routines
	@include 'utils.php';
	
	if(!($product= get_product(1))){
		write_log("ERROR\t" . "get_product.php: Getting product's info - ");
		exit();
	}
	
	// If a product is returned from the query in "get_product", print its features
	echo '	<div id="product_message_div" class="page_header_div">
				<h1 class="page_header">Product</h1>
			</div>
				<img id="product_image" src="' . $product['Image'] . '">
				<div id="description_div">
					<h3 class="h3_label">' . $product['Name'] . '</h3>
					<p>' . $product['Description'] . '</p> 
				</div>';
?>
