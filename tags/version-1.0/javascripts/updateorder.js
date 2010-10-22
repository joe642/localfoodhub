// create the AJAX object

var xmlHttp = createAjaxObject();
var cache = new Array();
var handlerCache = new Array();

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}
function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function ltrim(stringToTrim) {
	return stringToTrim.replace(/^\s+/,"");
}
function rtrim(stringToTrim) {
	return stringToTrim.replace(/\s+$/,"");
}


function update_basket(product_id)
{
	var quantity_request = document.order_form.elements['quantity[' + product_id + ']'].value;
	var quantity_remaining = document.order_form.elements['quantity_remaining[' + product_id + ']'].value;

	if (trim(quantity_request) == "") {
		quantity_request = "0";
	}
		//alert("request " + quantity_request + " remaining " + quantity_remaining);
	if (parseInt(quantity_request) <= parseInt(quantity_remaining)) {
		request = "ajax/update_basket.php?product_id=" + product_id + "&quantity=" + quantity_request;
		processGet(request, 'showBasket');
	} else {
		alert("ORDER QUANTITY NOT AVAILABLE\nYou can't order more than " + quantity_remaining + " at this time.");
		document.order_form.elements['quantity[' + product_id + ']'].value = quantity_remaining;
		request = "ajax/update_basket.php?product_id=" + product_id + "&quantity=" + quantity_remaining;
		processGet(request, 'showBasket');
	}
}


function showBasket()
{
	var basket, total_cost, products;
	// get the response as an xml objet
	var xmlResponse = xmlHttp.responseXML;
	// catch IE and opera errors
	if(!xmlResponse || !xmlResponse.documentElement)
		throw("1. Invalid XML structure: \n" + xmlHttp.responseText);
	// catch Firefox mozilla errors
	var rootNodeName = xmlResponse.documentElement.nodeName;
	if (rootNodeName == "parsererror")
		throw("2. invalid XML structure:\n" + xmlHttp.responseText);
	// get the root node element
	xmlRoot = xmlResponse.documentElement;
	// check that we got the right document
	if (rootNodeName != "basket"  || !xmlRoot.firstChild)
		throw ("3. invalid XML structure: \n" + xmlHttp.responseText);
	
	// Got the document, now update the basket
	basket = xmlResponse.documentElement;
	total_cost = basket.getElementsByTagName("cost");
	products = basket.getElementsByTagName("products");
	var balance = document.payment.balance.value * 1;
	
	document.getElementById("product_count").innerHTML = products[0].firstChild.data;
	document.getElementById("total_cost").innerHTML = total_cost[0].firstChild.data;
	var payment_amount = total_cost[0].firstChild.data * 1;
	document.payment.payment_amount.value =  payment_amount + balance;

}
