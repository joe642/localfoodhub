{* Smarty *}
	
{include file='header.tpl'}
	
<h1>Volunteering Activity</h1>

<h2>Current Month</h2>
<form action="volunteer.php" method="POST">
<input type="hidden" name="function" value="delete">
<table width="100%">
	<tr>
		<th colspan="4">Activity this month</th>
	</tr>
{if !empty($current_hours)}
	<tr>
		<th>Date</th><th>Activity</th><th>Hours</th><th>&nbsp;</th>
	</tr>
{else}
	<tr>
		<td colspan="3">No activity has been recorded for this month</td>
	</tr>
{/if}
{section name=c loop=$current_hours}
	<tr>
		<td>{$current_hours[c].volunteer_date|date_format:"%e %B"}</td>
		<td>{$current_hours[c].volunteer_task}</td>
		<td align="center">{$current_hours[c].volunteer_hours}</td>
		<td align="center"><input type="checkbox" name="volunteer_id[]" value="{$current_hours[c].volunteer_id}"></td>
	</tr>
{/section}
{if !empty($current_hours)}
	<tr>
		<td colspan="4" align="right"><input type="submit" value="Delete Checked"></td>
	</tr>
{/if}
</table>
</form>


<form name="addhours" action="volunteer.php" method="POST">
<input type="hidden" name="function" value="add">
<table align="center">
	<tr>
		<th colspan="2">Record Volunteering Time</th>
	</tr>
	<tr>
		<th align="left">Date:</th>
		<td>{html_select_date prefix=volunteer_date_ year_as_text=true field_order=DMY}</td>
	</tr>
	<tr>
		<th align="left">Task:</th>
		<td><input type="text" size="40" name="volunteer_task"></td>
	</tr>
	<tr>
		<th align="left">Hours given:</th>
		<td><select name="volunteer_hours">
			<option>0</option>
			<option>0.5</option>
			<option>1.0</option>
			<option>1.5</option>
			<option>2.0</option>
			<option>2.5</option>
			<option>3.0</option>
			<option>3.5</option>
			<option>4.0</option>
			</select></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" value="Record Hours"></td>
	</tr>
</table>
</form>
<strong>Note:</strong> Volunteer hours must be approved by the food club administrator before the discount is applied.  Discounts apply in the calendar month following the hours given.<br><br>

<h2>Last Month</h2>
<table width="100%">
	<tr>
		<th colspan="3">Activity last month</th>
	</tr>
{if !empty($past_hours)}
	<tr>
		<th>Date</th><th>Activity</th><th>Hours</th><th>Authorised</th>
	</tr>
{else}
	<tr>
		<td colspan="3">No activity was recorded for last month</td>
	</tr>
{/if}
{section name=c loop=$past_hours}
	<tr>
		<td>{$past_hours[c].volunteer_date|date_format:"%e %B"}</td>
		<td>{$past_hours[c].volunteer_task}</td>
		<td align="center">{$past_hours[c].volunteer_hours}</td>
		<td align="center"><input type="checkbox" {if $past_hours[c].authorised==1}checked{/if} disabled="true"></td>
	</tr>
{/section}
</table>


{include file='footer.tpl'}
