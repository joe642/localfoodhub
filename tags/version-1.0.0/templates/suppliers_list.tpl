{* Smarty *}
	
{include file='header.tpl'}
	
{literal}
<script language="Javascript">
<!--
	function enable_new()
	{
		document.new_supplier.submit_button.disabled = false;
		}
-->
</script
{/literal}

<h1>Suppliers</h1>

{section name=s loop=$suppliers}

<p><a href="suppliers.php?supplier_id={$suppliers[s].id}" class="supplier_list">{$suppliers[s].name}</a></p>

{/section}

<form name="new_supplier" action="suppliers.php" method="post"><input type="text" name="supplier_name" length="30" onclick="enable_new();"><input type="hidden" name="supplier_id" value="new"><input type="submit" value="Add New Supplier" name="submit_button" disabled="true"></form>

{include file='footer.tpl'}
