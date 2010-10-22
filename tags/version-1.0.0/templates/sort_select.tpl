{* smarty *}

{include file='header.tpl'}

{literal}
<script language="JavaScript">

	function enablesupplier()
	{
		document.getElementById('suppliertext').style.color = "#000000";
		document.choose_select.supplier_id.disabled = false;
		document.choose_select.supplier_id.style.backgroundColor = "#FFFFFF";
		document.getElementById('customer').style.color = "#CCCCCC";
		document.getElementById('email_orders').style.color = "#CCCCCC";
	}
	
	function enablecustomer()
	{
		document.getElementById('suppliertext').style.color = "#CCCCCC";
                document.choose_select.supplier_id.disabled = true;
                document.choose_select.supplier_id.style.backgroundColor = "#CCCCCC";
                document.getElementById('customer').style.color = "#000000";
				document.getElementById('email_orders').style.color = "#000000";
	}
</script>
{/literal}

<p><strong>Please choose which sorting report you would like:</strong></p>

<form name="choose_select" action="sorting.php" method="POST">
<table noborder>
	<tr>
		<td><input type="radio" name="select_by" value="supplier" onClick="enablesupplier();" checked></td>
		<td><span id="suppliertext">By Supplier:</span><br>
			<select name="supplier_id"><option value=''>--Select One--</option>{html_options options=$suppliers}</select></td>
	</tr>

	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>

        <tr>
                <td valign="top"><input type="radio" name="select_by" value="customer" onClick="enablecustomer();"></td>
		<td><span id="customer" style="Color: #CCCCCC;">By Member<span></td>
	</tr>

        <tr>
                <td>&nbsp;</td>
                <td><span id="email_orders" style="Color: #CCCCCC;"> If you also want to email the orders to members tick here: 
                    <input name="email_orders" type="checkbox" value="1" /></span></td>
        </tr>
        <tr>
                <td colspan="2">&nbsp;</td>
        </tr>

	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Show Report"><br /><br />Click Show Report just once.<br />Sending emails can take a few moments so give the system time to do this please.</td>
	</tr>
</table>
</form>

{include file='footer.tpl'}

