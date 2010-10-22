{* Smarty *}
	
{include file='header.tpl'}
	
<br>

<div>

<strong>Please select the supplier:</strong>
<br/><br/>

<form action="supplier_orders.php" method="POST">
Display: 

<select name="supplier_id">
<option value="ALL">All Orders</option>
{html_options options=$suppliers}
</select>&nbsp;&nbsp;
<input type="submit" value="Display Purchase Orders">
<br /><br /><br />
If you also want to email orders to suppliers tick here: 
<input name="email_orders" type="checkbox" value="1" />
<br/><br/>
Click Display Purchase Orders just once.<br  />
Sending emails can take a few moments, please give the system time to do it's stuff.
</form>
</div>
{include file='footer.tpl'}
