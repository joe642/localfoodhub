<?php

################################################################################
#                                                                              #
#		Filename:	productinfo.php				       #
#		Author:		Martin Settle				       #
#               Created:	27 October 2007				       #
#		Description:	returns more info field			       #
#		Calls:		config.php				       #
#		Called by:	productlist.php				       #
#									       #
################################################################################


if(empty($CoopName))
{	
	session_start();
	
	$Secure = 1;
	include '../config.php';
	}

//don't cache this page4
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
<title>Product Info</title>
<script language="JavaScript">
function setProduct()
{
	document.getElementById("product").style.display = "";
	document.getElementById("supplier").style.display = "none";
	}

function setSupplier()
{
	document.getElementById("product").style.display = "none";
	document.getElementById("supplier").style.display = "";
	}
</script>
</head>
<body>
<div>
<table width="100%">
<tr>
<td width="50%" align="center"><a href="javascript: setProduct();">Product</a></td>
<td width="50%" align="center"><a href="javascript: setSupplier();">Supplier</a></td>
</tr>
</table>
</div>
<div><br>
<div id="product">';
$response .= "<h2>{$product_name}</h2>{$product_info}";
$response .= '</div>
<div id="supplier" style="display: none;">';
$response .= "<h2>{$supplier_name}</h2>{$supplier_info}";
$response .= '</div></div></body></html>';

echo $response;
?>
