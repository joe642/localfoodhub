{* Smarty *}
	
{include file='header.tpl'}
	
	<p class='warning'>{$message}</p>
	<form action="change_password.php" method="POST">
	<table noborder>
	<tr>
		<th align="right">Current Password</th>
		<td><input type="password" name="current_password" value="{$current_password}" size="10"></td>
	</tr>
	<tr>
		<th align="right">New Password</th>
		<td><input type="password" name="new_password_1" size="10"></td>
	</tr>
	<tr>
		<th align="right">Repeat New Password</th>
		<td><input type="password" name="new_password_2" size="10"></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="Change" value="Change Password"></td>
	</tr>
	</table>
	</form>

{include file='footer.tpl'}
