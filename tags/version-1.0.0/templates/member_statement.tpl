{* Smarty *}

{include file="header.tpl"}
</div>


<div class="Main">


<div class="invoice">
	<table noborder cellspacing="0" cellpadding="2">
		<tr class="print_header">
			<th align="left"><img src="{$logo}" width="200px"></th>
			<th align="right" colspan="5" font-size="+1">Account Statement</th>

		<tr>
			<th align="left"><br>{$member.member_first_name} {$member.member_last_name}</th>
		<th align="right" colspan="4">{$date|date_format:"%e %B, %Y"}</th>
		</tr>
		<tr>
			<td colspan="5">{$member.member_address1}<br>
					{if !empty($member.member_address2)}{$member.member_address2}<br>{/if}
					{if !empty($member.member_address3)}{$member.member_address3}<br>{/if}
					{$member.member_town}<br>
					{$member.member_county}<br>
					{$member.member_postcode}</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr valign="bottom">
			<th align="left">Date</th>
			<th align="left" style="padding-right:1em;">Reference</th>
			<th align="right" style="padding-right:1em;">Debit</th>
			<th align="right" style="padding-right:1em;">Credit</th>
			<th align="right" style="padding-right:1em;">Balance</th>
		</tr>

{section name=pr loop=$trans}

		<tr style="font-size: 0.8em; background-color: {cycle values="#DDDDDD,#FFFFFF"} ">
			<td style="font-weight:600; color: green;">{$trans[pr].date|date_format:"%e %B"}</td>
			<td align="left" style="padding-right:1em;">{$trans[pr].reference}</td>
			<td align="right" style="padding-right:1em;">&nbsp;{if !empty($trans[pr].debit)}{math|string_format:"%.2f" equation="0-d" d=$trans[pr].debit}{/if}</td>
			<td align="right" style="padding-right:1em;">&nbsp;{if !empty($trans[pr].credit)}{math|string_format:"%.2f" equation="0-c" c=$trans[pr].credit}{/if}</td>
			<td align="right" style="padding-right:1em;font-weight:600; color: green;">{math|string_format:"%.2f" equation="0-b" b=$trans[pr].balance}</td>
		</tr>
{/section}
{if $recent_payment != 0}
        <tr style="font-size: 0.8em; background-color: {cycle values="#DDDDDD,#FFFFFF"} ">
        <td colspan="2">Purchases made since last invoice</td>
        <td align="right" style="padding-right:1em;">{math|string_format:"%.2f" equation="0-d" d=$recent_payment}</td>
        <td align="right" style="padding-right:1em;">&nbsp;</td>
        <td align="right" style="padding-right:1em;">{math|string_format:"%.2f" equation="0-b" b=$balance}</td>
        </tr>
{/if}
        <tr><td colspan="5">&nbsp;</td></tr>
		<tr>
			<th colspan="4" align="right">{$member.member_first_name} {$member.member_last_name} Account Balance:</th>
			<td align="right">{math|string_format:"%.2f" equation="0-b" b=$member.member_account_balance}</td>
		</tr>
        {if $member.member_account_balance != $balance}
        {if $balance != 0.00}
        <tr>
        	<td colspan="4" align="right">Adjust the member's balance to {math|string_format:"%.2f" equation="0-b" b=$balance}
            <form action="account_statement.php" method="post" name="adjust_balance_form">
            <input name="member_id" type="hidden" value="{$member.member_id}" />
            <input name="acct_balance" type="hidden" value='{$balance|string_format:"%.2f"}' />
            <input name="adjust_balance" type="submit" value="Adjust Balance" />
            </form>
            </td>
			<td align="right">&nbsp;</td>
		</tr>
        {/if}
        {/if}

	
</table>
<br>

</center>
</div>


{include file="footer.tpl"}
