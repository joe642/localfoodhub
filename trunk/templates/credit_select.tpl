{* smarty *}

{include file='header.tpl'}

{literal}
<script language="JavaScript">

	function enablesupplier()
	{
		document.getElementById('suppliertext').style.color = "#000000";
		document.choose_select.member_id.style.backgroundColor = "#FFFFFF";
		document.choose_select.member_id.disabled = false
		document.choose_select.credit_amount.disabled = false;
		document.choose_select.credit_amount.style.backgroundColor = "#FFFFFF";
		document.choose_select.credit_reference.disabled = false;
		document.choose_select.credit_reference.style.backgroundColor = "#FFFFFF";
		document.getElementById('multiple').style.color = "#CCCCCC";
		document.choose_select.submit_button.value = "Post Credit";
	}
	
	function enablecustomer()
	{
		document.getElementById('suppliertext').style.color = "#CCCCCC";
                document.choose_select.member_id.disabled = true;
                document.choose_select.member_id.style.backgroundColor = "#CCCCCC";
		document.choose_select.credit_amount.disabled = true;
		document.choose_select.credit_amount.style.backgroundColor = "#CCCCCC";
		document.choose_select.credit_reference.disabled = true;
		document.choose_select.credit_reference.style.backgroundColor = "#CCCCCC";
		document.getElementById('multiple').style.color = "#000000";
		document.choose_select.submit_button.value = "Show Form";
	}
</script>
{/literal}

<p class="warning">{$message}</p>

<p><strong>Please choose how you wish to post credits:</strong></p>

<form name="choose_select" action="credits.php" method="POST">
<table noborder>
	<tr>
		<td valign="top"><input type="radio" name="method" value="single" onClick="enablesupplier();" checked></td>
		<td><span id="suppliertext">To a single member:</span><br>
			<select name="member_id"><option value=''>--Select One--</option>{html_options options=$members}</select><br>
			<table noborder>
			<tr>
				<th align="right">Membership Number:</th>
				<td><input type="text" name="credit_member_num" size="6"></td>
			</tr>
			<tr>
				<th align="right">Amount:</th>
				<td><input type="text" name="credit_amount" size="8"></td>
			</tr>
			<tr>
                                <th align="right">Reference:</th>
                                <td><input type="text" name="credit_reference" size="60"></td>
			</tr>
			</table></td>
	</tr>

	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>

        <tr>
                <td valign="top"><input type="radio" name="method" value="multiple" onClick="enablecustomer();"></td>
		<td><span id="multiple" style="Color: #CCCCCC;">To multiple members (shows all members with a balance):<span></td>
	</tr>

        <tr>
                <td colspan="2">&nbsp;</td>
        </tr>

	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit_button" value="Post Credit"></td>
	</tr>
</table>
</form>

{include file='footer.tpl'}

