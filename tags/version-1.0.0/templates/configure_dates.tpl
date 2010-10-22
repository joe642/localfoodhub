{* Smarty *}
	
{include file='header.tpl'}

<h1>Active Suppliers on {$date|date_format:"%e %B"}</h1>

{section loop=$suppliers name=s}
<input type="checkbox" name="{$suppliers[s].id}" 
	onChange="change_availability({$suppliers[s].id},'{$date|date_format:"%Y-%m-%d"}');" 
	{if $suppliers[s].available == 1}checked {/if}{if $suppliers[s].producer}disabled {/if}> 
{$suppliers[s].name}<br>
{/section}
<br>
<a href="calendar.php">Return to Calendar Administration</a>


{include file='footer.tpl'}
