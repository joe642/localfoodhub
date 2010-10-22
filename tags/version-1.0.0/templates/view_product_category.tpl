{* smarty *}

{include file='header.tpl'}

{literal}
<SCRIPT LANGUAGE="JavaScript" SRC="javascripts/java_object.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="javascripts/logintest.js"></SCRIPT>
<script language="JavaScript">

	function enablecategory()
	{
		document.getElementById('searchtext').style.color = "#CCCCCC";
		document.choose_filter.search_term.disabled = true;
		document.choose_filter.search_term.style.backgroundColor = "#CCCCCC";
		document.getElementById('categorytext').style.color = "#000000";
		document.choose_filter.category.disabled = false;
		document.choose_filter.category.style.backgroundColor = "#FFFFFF";
		document.getElementById('show_all').style.color = "#CCCCCC";
	}
	
	function enablesearch()
	{
		document.getElementById('searchtext').style.color = "#000000";
                document.choose_filter.search_term.disabled = false;
		document.choose_filter.search_term.value = '';
                document.choose_filter.search_term.style.backgroundColor = "#FFFFFF";
		document.getElementById('categorytext').style.color = "#CCCCCC";
                document.choose_filter.category.disabled = true;
                document.choose_filter.category.style.backgroundColor = "#CCCCCC";
                document.getElementById('show_all').style.color = "#CCCCCC";
	}

	function enableall()
	{
		document.getElementById('searchtext').style.color = "#CCCCCC";
                document.choose_filter.search_term.disabled = true;
                document.choose_filter.search_term.style.backgroundColor = "#CCCCCC";
		document.getElementById('categorytext').style.color = "#CCCCCC";
                document.choose_filter.category.style.backgroundColor = "#CCCCCC";
                document.choose_filter.category.disabled = true;
                document.getElementById('show_all').style.color = "#000000";
	}
</script>
{/literal}

<p><strong>Please choose how you would like to browse the products:</strong></p>

<table noborder>
<form name="choose_filter" action="productlist.php" method="POST">
        <tr>
        
		<td>Show all products grouped by category </td>
		<td><input type="hidden" name="filter_type" value="show_all"><input type="submit" value="Show All Products"></td>
	</tr>

	<tr>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
</form>
<form name="choose_filter" action="productlist.php" method="POST">
	<tr>
		<td><span id="categorytext">View products by category:</span>
			<select name="category"><option value=''>--Select One--</option>{html_options options=$categories}</select></td>
		<td valign="bottom"><input type="hidden" name="filter_type" value="category"><input type="submit" value="View Category"></td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
</form>

<form name="choose_filter" action="productlist.php" method="POST">

	<tr>
		<td>Search for a product (looks in product name and description)<br>
			<input type="text" name="search_term" size="60"></td>
					<td valign="bottom"><input type="hidden" name="filter_type" value="search"><input type="submit" value="Search"></td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
</form>

</table>
<p>&nbsp;</p>
<p>{include file='footer.tpl'}
  
</p>
