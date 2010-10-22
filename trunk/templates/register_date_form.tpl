{* Smarty *}
	
{include file='header.tpl'}
	
<div class="Control_Panel">
<p class="Main_Head">Food Club Distribution on {$order_date|date_format:"%e %B, %Y"}</p>
<div class="Panel_Inset">
<p>You are not currently registered to sell products on this date.  You have the following three options:</p>
<form name=dothis action="producer.php?order_date={$order_date}" method="POST">
<input type="radio" name="function" value="register_date" onClick="document.dothis.submit();"> Register this date and automatically enable all active products<br>
<input type="radio" name="function" value="configure_calendar" onClick="document.dothis.submit();"> Register this date and manually configure products<br>
<input type="hidden" name="manual_config" value="TRUE">
<input type="radio" name="function" value="" onClick="document.dothis.submit();"> Do nothing.  You will not offer products for sale on this date.<br>
</div>

</div>

{include file='footer.tpl'}
