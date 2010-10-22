{* Smarty *}
	
{include file='header.tpl'}

{if !empty($message)}<p>{$message}</p>{/if}

<form name="product_details" action="producer.php" method="post" class="Control_Panel">
<input type="hidden" name="product_id" value="{$product_id}">
<h2>Confirm Delete</h2>
<p>Deleting a product will remove its availability for all future distribution dates, and delete it from all product lists. Are you sure you want to 
delete this product?</p>
<input type="hidden" name="function" value="archiveproduct">
<input type="hidden" name="product_id" value="{$product_id}">
<input name="confirm_archive" type="submit" value="Delete">
{if empty($returnpage)}<a href="producer.php" class="Navlink">Return to Control Panel</a>{else}<a href="{$returnpage}" class="Navlink">Go back</a>{/if}
</form>

{include file='footer.tpl'}
