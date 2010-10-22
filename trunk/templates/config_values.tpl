{* Smarty *}
	
{include file='header.tpl'}
	
{if !empty($warning)}	<p class='warning'>{$warning}</p>{/if}
	
	<h2>System Configuration</h2>
	<p class='warning'>Do not change these values unless you are absolutely certain that the values are incorrect!</p>
	
	<form name="config_form" action="config_values.php" method="POST">
	<table>
		<tr>
			<th colspan="2" class="banner">General Settings</th>
		</tr>
		<tr>
			<th align="left">Food Club Name:</th>
			<td><input type="text" name="foodclub_name" value="{$foodclub_name}" size="30"></td>
		</tr>
		<tr>
			<th align="left">Manager Name:</th>
			<td><input type="text" name="foodclub_manager" value="{$foodclub_manager}" size="30"></td>
		</tr>
		<tr>
			<th align="left">Manager Email:</th>
		  <td><input type="text" name="manager_email" value="{$manager_email}" size="30"> 
		    gets sent copies of orders</td>
		</tr>
 		<tr>
			<th align="left">Orders From Email:</th>
			<td><input type="text" name="orders_from_email" value="{$orders_from_email}" size="30"> 
			  order emails send from this address</td>
		</tr>
		<tr>
			<th align="left">Open Registration:</th>
			<td><select name="allow_autoregistration"><option value="0">No</option><option value="1"{if $allow_autoregistration==1} selected{/if}>Yes</option></select> 
			- recommended setting <strong>No</strong></td>
		</tr>
		<tr>
			<th align="left">Distribution Frequency:</th>
			<td>{html_options name=distribution_frequency options=$frequencies selected=$distribution_frequency}</td>
		</tr>
		<tr>
			<th align="left">Distribution Day:</th>
			<td>{html_options name="distribution_day" options=$weekdays selected=$distribution_day}</td>
		</tr>
		<tr>
			<th align="left">Order Close:</th>
			<td><input type="text" name="days_notice" value="{$days_notice}" size="1"> Days prior to distribution</td>
		</tr>
		<tr>
			<th align="left">Standard Markup:</th>
			<td><input type="text" name="markup" value="{$markup*100}" size="2">%</td>
		</tr>
		<tr>
			<th align="left">Volunteer Discount:</th>
			<td><input type="text" name="volunteer_discount" value="{$volunteer_discount*100}" size="2">
			% - not used at present</td>
		</tr>
		<tr>
			<th align="left">Volunteer Hours:</th>
			<td><input type="text" name="volunteer_discount_hours" value="{$volunteer_discount_hours}" size="2"> 
			(required per calendar month for discount) - not used</td>
		</tr>
		<tr>
			<th align="left">VAT Registered:</th>
			<td><select name="use_VAT"><option value="0">No</option><option value="1"{if $use_VAT==1} selected{/if}>Yes</option></select></td>
		</tr>
		<tr>
		  <th colspan="2" class="banner">&nbsp;</th>
	  </tr>
		<tr>
			<th colspan="2" class="banner">Producer Controls</th>
		</tr>
		<tr>
			<th align="left">Price Input</th>
			<td><select name="use_farm_gate_pricing"><option value="0">Before system mark-up (wholesale)</option><option value="1"{if $use_farm_gate_pricing == 1} selected{/if}>After system mark-up (farm gate price)</option></select> <br />
			  recommended setting &quot;After system mark-up (farm gate price)&quot;</td>
		</tr>

		<tr>
		  <th colspan="2" class="banner">&nbsp;</th>
	  </tr>
		<tr>
			<th colspan="2" class="banner">Payment Methods</th>
		</tr>
		<tr>
			<th align="left">Cheque:</th>
			<td><select name="accept_cheque_payments"><option value="0">No</option><option value="1"{if $accept_cheque_payments==1} selected{/if}>Yes</option></select></td>
		</tr>
		<tr>
			<th align="left">Cheque Payee:</th>
			<td><input type="text" name="foodclub_cheques_to" value="{$foodclub_cheques_to}" size="30"></td>
		</tr>
		<tr>
			<th align="left" valign="top">Cheque Post Address:</th>
			<td><textarea name="foodclub_post_address" rows="4" cols="40">{$foodclub_post_address}</textarea></td>
		</tr>
		<tr>
			<th align="left">Paypal:</th>
			<td><select name="accept_paypal"><option value="0">No</option><option value="1"{if $accept_paypal==1} selected{/if}>Yes</option></select></td>
		</tr>
		<tr>
			<th align="left">Paypal Account:</th>
			<td><input type="text" name="paypal_account" value="{$paypal_account}" size="30"> 
			  email address</td>
		</tr>
		<tr>
			<th align="left">Paypal Minimum Payment:</th>
			<td><input type="text" name="paypal_minimum_payment" value="{$paypal_minimum_payment}" size="4"></td>
		</tr>
		<tr>
		  <th align="left">Paypal Charge Percent</th>
		  <td><input type="text" name="paypal_charge" value="{$paypal_charge*100}" size="2">
		  % (added to bill to cover Paypal charges)</td>
	  </tr>
		<tr>
			<th align="left">Paypal Language Code:</th>
			<td><input type="text" name="paypal_language_code" value="{$paypal_language_code}" size="2"></td>
		</tr>
		<tr>
			<th align="left">Paypal Currency:</th>
			<td><input type="text" name="paypal_currency" value="{$paypal_currency}" size="2"></td>
		</tr>
		<tr>
			<th align="left">Paypal Cert ID:</th>
			<td><input type="text" name="paypal_cert_id" value="{$paypal_cert_id}" size="14"> 
			- not used at present</td>
		</tr>
		<tr>
			<th align="left">Paypal Use Sandbox:</th>
			<td><select name="paypal_use_sandbox"><option value="0">No</option><option value="1"{if $paypal_use_sandbox==1} selected{/if}>Yes</option></select></td>
		</tr>
		<tr>
			<th align="left">PP Sandbox Account:</th>
			<td><input type="text" name="paypal_sandbox_account" value="{$paypal_sandbox_account}" size="30"></td>
		</tr>
		<tr>
			<th align="left">PP Sandbox Cert ID:</th>
			<td><input type="text" name="paypal_sandbox_cert_id" value="{$paypal_sandbox_cert_id}" size="14"></td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input type="submit" name="save_changes" value="Save Changes"></td>
		</tr>
	</table>
	</form>

{include file='footer.tpl'}
