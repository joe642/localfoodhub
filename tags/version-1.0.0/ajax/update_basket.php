<?php

################################################################################
#                                                                              #
#		Filename:		update_basket.php			       #
#		Author:		Martin Settle				       #
#               Created:		19 November 2008				       #
#		Description:	ajax handler for shopping basket			       #
#		Calls:		config.php				       #
#		Called by:	productlist.php				       #
#									       #
################################################################################

/*
MODIFICATION HISTORY
	- 2008.11.19 file created

*/

session_start();
	
//don't cache this page4
header('Expires: Wed 23 Dec 1980 00:30:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

header('Content-Type: text/xml');

$Secure = 1;
include '../config.php';

$_GET = quote_smart($_GET);
$product_id = $_GET['product_id'];
$quantity = trim($_GET['quantity']);

if ($quantity != "") {
//submission logic goes here
	if ($quantity >= 0) {
		$fetch_price = mysql_query("SELECT current_price FROM product_calendar WHERE product_id = $product_id and order_date = '{$_SESSION['order_date']}'");
		$current_price = mysql_result($fetch_price,0);			
		mysql_query("INSERT INTO temp_orders 
			(order_product_id, order_member_id, order_date, order_quantity_requested, order_current_price)
			VALUES ({$product_id},
					{$_SESSION['member_id']}, 
					'{$_SESSION['order_date']}', 
					{$quantity}, 
					{$current_price})
			ON DUPLICATE KEY UPDATE order_quantity_requested = VALUES(order_quantity_requested)");
	}
	if ($quantity == 0) {
		mysql_query("DELETE FROM temp_orders WHERE order_quantity_requested = 0");
	}
	
	// calculate how many of product ordered so far
	$qty_ordered = 0;
	$qty1_lookup = mysql_query("SELECT SUM(order_quantity_requested) AS qty1 FROM temp_orders WHERE order_product_id = $product_id and order_date = '{$_SESSION['order_date']}'");
	if(mysql_num_rows($qty1_lookup) > 0) {
		$qty_ordered = mysql_result($qty1_lookup,0,'qty1');
	}
	
	$qty2_lookup = mysql_query("SELECT SUM(order_quantity_requested) AS qty2 FROM orders WHERE order_product_id = $product_id and order_date = '{$_SESSION['order_date']}'");
	if(mysql_num_rows($qty2_lookup) > 0) {
		$qty_ordered += mysql_result($qty2_lookup,0,'qty2');
	}

	// and update the quantity_ordered in the product_calendar
	mysql_query("UPDATE product_calendar SET quantity_ordered = $qty_ordered WHERE product_id = $product_id and order_date = '{$_SESSION['order_date']}'");
}

$order_total_lookup = mysql_query("SELECT COUNT(temp_order_id) AS products, 
							SUM(ROUND(order_current_price * (1 + product_VAT_rate) * (1 + IFNULL(product_markup,$_SESSION[markup])),2) * order_quantity_requested)  AS total_cost
							FROM temp_orders, product 
							WHERE order_product_id = product_id
							AND order_quantity_requested > 0 
							AND order_member_id = $_SESSION[member_id] 
							AND order_date = '$_SESSION[order_date]'");
if(mysql_num_rows($order_total_lookup) == 0) {
	$total_cost = '0.00';
	$product_count = 0;
} else {
	$total_cost = mysql_result($order_total_lookup,0,'total_cost');
	$product_count = mysql_result($order_total_lookup,0,'products');
}

/*
$dom = new DOMDocument();
$basket = $dom->createElement('basket');
$dom->appendChild($basket);
$product = $dom->createElement('products');
$basket->appendChild($product);
$product_total = $dom->createTextNode($product_count);
$product->appendChild($product_total);
$cost = $dom->createElement('cost');
$basket->appendChild($cost);
$total = $dom->createTextNode($total_cost);
$cost->appendChild($total);
$xmlString = $dom->saveXML();
*/

$xmlString = "<?xml version=\"1.0\"?>
<basket>
<products>{$product_count}</products>
<cost>{$total_cost}</cost>
</basket>";

echo $xmlString;

?>
