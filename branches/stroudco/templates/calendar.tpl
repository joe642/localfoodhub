{* Smarty *}
	
{include file='header.tpl'}
	
<h2>Distribution Calendar</h2>
Click on the date to configure available suppliers:<br>
{if !empty($order_dates)}<table>
<form name="delete_form" action="calendar.php" method="POST">
<input type="hidden" name="function" value="Delete_day">
<tr><td>&nbsp;</td></th><th>Delete</th></tr>{/if}

{section loop=$order_dates name=o}
<tr><td><a href="configure_dates.php?order_date={$order_dates[o]}">{$order_dates[o]|date_format:'%e %B, 
%Y'}</a>&nbsp;&nbsp;</td><td><input 
type="checkbox" 
name="calendar_id" value="{$calendar_ids[o]}" onChange="document.delete_form.submit();"></td></tr>
{/section}
{if !empty($order_dates)}</form>
</table>
{else}<p>No dates are currently configured</p>{/if}


<h3>Add a Day</h3>
<form action="calendar.php" method="POST">
{html_select_date prefix="New_" time=$order_dates[o] end_year="2020"}<br>
<input type="hidden" name="function" value="Add_day">
<input type="submit" value="Add">
</form>
<!-- safest to add one day at a time - adding multiple days is well complex and could easily go wrong - withdrawn until more testing and debug can be scheduled.
<h3>Add Multiple Days</h3>
<p>This section will autogenerate the calendar for distributions between start date and end date.  This will use the default distribution day and frequency stipulated in the system configuration, and will automatically register recurring supplier products.</p>
<form action="calendar.php" method="POST">
Start Date: {html_select_date prefix="start_" time=$order_dates[o] end_year=+1}<br>
End Date: {html_select_date prefix="end_" time=$order_dates[o] end_year=+1}<br>
<input type="hidden" name="function" value="Autogenerate">
<input type="submit" value="Add Range">
</form>
-->
<br>
<br>
{include file='footer.tpl'}
