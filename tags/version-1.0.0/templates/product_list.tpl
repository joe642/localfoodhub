{* Smarty *}
{assign var=suppress_headers value=1}
<html>
<head>
<script src="thirdparty/javascripts/jquery.js" type="text/javascript"></script>
<link href="facebox/facebox.css" media="screen" rel="stylesheet" type="text/css">
<script src="facebox/facebox.js" type="text/javascript"></script>
<script src="javascripts/java_object.js" type="text/javascript"></script>
<script src="javascripts/updateorder.js" type="text/javascript"></script>
<SCRIPT SRC="javascripts/logintest.js"></SCRIPT>
<script type="text/javascript" >
{literal}
$(document).ready(function() {
    $('a[rel*=facebox]').facebox();
  });

 function getInfo(product_id)
 {
 
 	}
{/literal}
</script>

{include file='header.tpl'}

<div id="basket_frame_container">
	<div id="basket_frame">
		<h3><a href="my_order.php">Shopping Basket:</a></h3>
		<h3>Products: <span id="product_count">{$product_count}</span></h3>
		<h3>Total Cost: &pound;<span id="total_cost">{$total_cost|string_format:"%.2f"}</span></h3>
        <h3>Account Balance: &pound;<span id="acct_balance">{math equation="0-b" b=$opening_balance|string_format:"%.2f"}</span></h3>
		<h3><form action="payment.php" method="POST" name="payment">
		<input type="hidden" id="payment_amount" name="payment_amount" value="{math equation="t+b" t=$total_cost b=$opening_balance}">
		<input type="hidden" id="balance" name="balance" value="{$opening_balance}">
		<a href="javascript: void(0);" onClick="document.payment.submit();">Pay Now</a></form></h3>
		&nbsp;
	</div>
</div>

<div id="key_frame">
<table>
	
	<tr>
		<td><img src="images/organic.jpg" alt="organic" align="left"> = Certified organic production methods</td>
        <form name="choose_filter" action="productlist.php" method="POST">

		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Search&nbsp;products:<input type="text" name="search_term" size="30"><input type="hidden" name="filter_type" value="search"><input type="submit" value="Search"></td>
</form>
	<tr>
	

</table>
	
</div>

<div id="productlist_frame">

<h1>{$heading}</h1>

<form name="order_form" action="process_order.php" method="POST">
<center>{$VAT_warning}</center>
<table noborder cellspacing="2" cellpadding="2" width="95%">
<tr valign="bottom">
	<th align="left">Product</th>
	<th colspan="2" align="left" valign="bottom">Description</th>
	<th align="left" valign="bottom">Unit</th>
	<th align="left" valign="bottom">Producer</th>
	<th align="center" valign="bottom">Avail</th>
	<th align="right" valign="bottom">Price</th>
	{if empty($view_only)} <th align="right" valign="bottom">Order</th> {/if}
</tr>
{section name=pr loop=$products}
<tr style="font-size: 0.8em; background-color: {cycle values="#F8F8F8,#F0F0F0"} ">
	<td valign="top" style="font-weight:600; color: green;">{$products[pr].product_name}</td>
	<td valign="top">{if ($products[pr].thumbnail <> "")}<a href="ajax/productpic.php?product_id={$products[pr].product_id}" rel="facebox"><img src="{$products[pr].thumbnail}" align="right" border="0"/></a>{/if}{$products[pr].product_description}{if ($products[pr].more_info == 1)} <a href="ajax/productinfo.php?product_id={$products[pr].product_id}" rel="facebox">more</a>{/if}</td>
	<td valign="top">{if $products[pr].product_organic eq 1}<img src="images/organic.jpg" alt="Organic">{else}&nbsp;{/if}</td>
	<td valign="top">{$products[pr].product_units|lower}</td>
	<td valign="top">{$products[pr].supplier_name}</td>
	<td valign="top" align="right">{if $products[pr].quantity_remaining > 1000}&nbsp;
{else}{$products[pr].quantity_remaining}{/if}<input type="hidden" name="quantity_remaining[{$products[pr].product_id}]" value="{$products[pr].quantity_remaining}"></td>
	<td valign="top" align="right">{$products[pr].price}</td>
	{if empty($view_only)}<td valign="top"><input type="text" name="quantity[{$products[pr].product_id}]" size="3" value="{if $products[pr].order_quantity_requested > 0}{$products[pr].order_quantity_requested|string_format:"%.0f"}{/if}" onBlur="update_basket({$products[pr].product_id})"></td>{/if}
</tr>
{/section}

</table>
<table noborder width="100%">
<tr>
<td align="center">
{if empty($view_only)}
<input type="submit" name="process" value="Update my Order">
{else}
<a href="productlist.php">Back to search for products</a>
{/if}
</td>
</tr>
</table>

</form>
{if empty($view_only)}
<br/>
<strong>Please note:</strong> Order may be altered by returning to this page, or by selecting "Shopping Basket " from the main menu.
{/if}
</div>
{include file='footer.tpl'}
