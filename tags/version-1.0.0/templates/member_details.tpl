{* Smarty *}
	
{include file='header.tpl'}
{if empty($message)}	
<h2>Member Details for {$member_details.member_first_name} {$member_details.member_last_name}</h2>
{else}
<h2>{$message}</h2>
{/if}
<form action="http://{$smarty.server.SERVER_NAME}{$smarty.server.PHP_SELF}" method="POST">
<input type="hidden" name="member_id" value="{$member_details.member_id}">

<table>

<tr>
	<th align="right">First Name</th>
	<td><input name="member_first_name" size="20" value="{$member_details.member_first_name}"></td>
</tr>
<tr>
	<th align="right">Last Name</th>
	<td><input name="member_last_name" size="20" value="{$member_details.member_last_name}"></td>
</tr>
<tr>
	<th align="right">Membership Number</th>
	<td>
	{if $edit_membership_num}
	<input name='membership_num' size="4" value="{$member_details.membership_num}">
	{else}
	<input name='membership_num' size="4" value="{$member_details.membership_num}" disabled>
	{/if}
	</td>
</tr>
<tr>
	<th align="right">E-mail (login)</th>
	<td><input name="member_email" size="60" value="{$member_details.member_email}"></td>
</tr>
<tr>
	<th align="right">Home Phone</th>
	<td><input name="member_homephone" size="14" value="{$member_details.member_homephone}"></td>
</tr>
<tr>
	<th align="right">Work Phone</th>
	<td><input name="member_workphone" size="14" value="{$member_details.member_workphone}"></td>
</tr>
<tr>
	<th align="right">Mobile Phone</th>
	<td><input name="member_mobilephone" size="14" value="{$member_details.member_mobilephone}"></td>
</tr>
<tr>
	<th align="right">Address Line 1</th>
	<td><input name="member_address1" size="40" value="{$member_details.member_address1}"></td>
</tr>
<tr>
	<th align="right">Address Line 2</th>
	<td><input name="member_address2" size="40" value="{$member_details.member_address2}"></td>
</tr>
<tr>
	<th align="right">Address Line 3</th>
	<td><input name="member_address3" size="40" value="{$member_details.member_address3}"></td>
</tr>
<tr>
	<th align="right">Town</th>
	<td><input name="member_town" size="30" value="{$member_details.member_town}"></td>
</tr>
<tr>
	<th align="right">County</th>
	<td><input name="member_county" size="20" value="{$member_details.member_county}"></td>
</tr>
<tr>
	<th align="right">Post Code</th>
	<td><input name="member_postcode" size="8" value="{$member_details.member_postcode}"></td>
</tr>
<tr>
	<th align="right">Distribution Centre</th>
	<td>{html_options name=member_distribution_id options=$distribution selected=$member_details.member_distribution_id}</td>
</tr>
{if empty($new_member)}
<tr>
	<th align="right">Account Balance</th>
	<td><input name='empty' size='8' value='{math|string_format:"%.2f" equation="0-b" b=$member_details.member_account_balance}' disabled></td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="Update" value="Update"></td>
</tr>
{else}
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="Register" value="Register Me Now"></td>
</tr>
{/if}
</table>

{include file='footer.tpl'}
