{* Smarty *}
	
{include file='header.tpl'}
	
<h2>Payment Methods</h2>
<p>{$partpaid}</p>
<p><strong>You need to pay &pound;{$payment_amount|string_format:"%.2f"} to complete your order.</strong></p>
<p>Once you have paid and <a href="send_msg.php">sent a message to us</a> with your payment details then your order will be accepted automatically. Remember to pay before the order deadline.</p>

<h3>{$title} accepts payments by the following methods:</h3>

{assign var=cols value=$methods|@count}
{assign var=tot value=100}
<table cellpadding="6">
<tr>
{section name=m loop=$methods}<td align="left"><strong>{$methods[m]}</strong></td>
{/section}</tr>
<tr>
{section name=m loop=$methods}
<td width="{$tot/$cols}%" valign="top">
{if $methods[m] == 'PayPal'}
{if !empty($paypal_notice)}{$paypal_notice}{else}Use PayPal to make your payment by Debit or Credit card or from your own PayPal account.<br/>Please note an extra PayPal charge of &pound;{$paypal_charge|string_format:"%.2f"} will be added to your bill to cover our costs.<br><br><center>{$paypal_button}</center>{/if}
{elseif $methods[m] == 'Cheque'}
<p>Post or deliver your cheque, made out to <strong>{$cheque_payee}</strong>, to:</p>
<p style="text-weight:strong; margin-left:2em;">{$foodclub_manager}<br>
{$foodclub_name}<br>
{$foodclub_postal_address}</p>
{elseif $methods[m] == 'Bank Transfer'}
<p>{$bank_transfer_msg}</p>
{/if}</td>
{/section}
</table>
<!--
<p>Please ensure your payment arrives on or before {$cutoff_date}</p>
-->
<p>Please send a message to the manager to inform them of the date and amount of your payment so that we may process your order promptly : <a href="send_msg.php">Send Message</a></p>
<p>When we've recieved your message your order will be automatically processed - you do not need to log in again to complete the order process.</p>

{include file='footer.tpl'}
