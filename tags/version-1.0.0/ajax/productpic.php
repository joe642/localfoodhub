<?php

################################################################################
#                                                                              #
#		Filename:	productpic.php				       #
#									       #
################################################################################


if(empty($CoopName))
{	
	session_start();
	
	$Secure = 1;
	include '../config.php';
	}

//don't cache this page
header('Expires: Wed 23 Dec 1980 00:30:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

header('Content-Type: text/html');

$_GET = quote_smart($_GET);

$lookup = mysql_query("SELECT product_name, product_more_info, supplier_info, supplier_name FROM product,supplier 
					WHERE product_id = {$_GET['product_id']} AND supplier.supplier_id = product.product_supplier_id");
$product_info =  mysql_result($lookup,0,'product_more_info');
$supplier_info = mysql_result($lookup,0,'supplier_info');
if(empty($supplier_info)) $supplier_info = 'Sorry, no further supplier information is available';
$product_name = mysql_result($lookup,0,'product_name');
$supplier_name = mysql_result($lookup,0,'supplier_name');

$response ='<html>
<head>
<title>{$product_name}</title>
</head>
<body>
<div id="product">';
$response .= "<h2>{$product_name}</h2><img src=\"images/normal/{$_GET['product_id']}.jpg\" alt=\"{$product_name} from {$supplier_name}\"";
$response .= '</div></body></html>';

echo $response;
?>
