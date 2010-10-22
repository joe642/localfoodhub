{* Smarty *}
	
{include file='header.tpl'}
	
<h2>{$supplier}</h2>

<table noborder>
	<tr>
		<td colspan="2">&nbsp;</td>
		<th>Delivered</th>
		<th>Sorted</th>
	</tr>
{section name=p loop=$products}
	<tr>
		<th colspan="4" align=left>{$products[p]}: {$description[p]}, {$units[p]}</th>
	</tr>{assign var="prod" value=$products[p]}
{section name=o loop=$sorting}
{if $sorting[o].product == $prod}
	<tr>
		<td style="width: 3em;">&nbsp;</td>
		<td>{$sorting[o].name}</td>
		<td align="right">{$sorting[o].quantity}</td>
		<td style="border: thin solid black; width:4em;">&nbsp;</td>
	</tr>
{/if}{/section}
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>{/section}
</table>


{include file='footer.tpl'}
