{* Smarty *}

{include file='header.tpl'}


{if $SystemMessage}
<p class="warning">{$SystemMessage}</p><hr><p>
{else}
<h2>Welcome to the Information Page administration system.</h2>
<p>This page allows you to add web pages that can be accessible through the INFO menu item on the guest and member sites.  To add a new page, complete the form below.  To edit a page currently in use by the system, select the title of the page from the map below.</p>
<hr>
<p>
{/if}
<form action="submitpage.php" method="POST">
<table width="100%">
	<tr>
		<th colspan="2" class='Banner'>Information Page Entry</th>
	</tr>
	<tr>
		<th align="left">Title:</th>
		<td><input type="text" name="infopage_title" value="{$infopage_title}" size="40"></td>
	</tr>
        <tr>
		<th align="left">Parent Page:</th>
		<td><select name="infopage_parent">{html_options options=$ParentList selected=$infopage_parent}</select></td>
	</tr>
        <tr>
		<th align="left">Menu Page:</th>
		<td>{html_radios name="infopage_menu" options=$YesNo selected=$infopage_menu separator='<br />'}</td>
	</tr>
        <tr>
		<th align="left">Menu Placement:</th>
		<td>{html_radios name="infopage_menuplacement" options=$TopBottom selected=$infopage_menuplacement separator='<br />'}</td>
	</tr>
        <tr>
		<th align="left" valign="top">Page Data:</th>

        	<td>{fckeditor BasePath="../thirdparty/FCKeditor/" InstanceName='infopage_content' Value="$infopage_content" Height="300px" ToolbarSet="MyTools"}
		</td>
	</tr>
        <tr>
		<th align="left">Main Page:</th>
		<td>{html_options name='infopage_main' options=$YesNo selected=$infopage_main}</td>
	</tr>

{if $infopage_menu == 1}
</table>
<table width=100%>
       	<tr>
		<td colspan=2>The following table lists the menu items on this page, in the priority that they currently appear.  To change the order, number all pages in the order you wish them to appear, beginning with number one.</td>
	</tr>
        <tr>
		<th colspan=2 Class='Banner'>Menu Items on this Page</th>
	</tr>
{section name=menus loop=$subpages}        
        <tr>
		<th align=left>{$subpages[menus].infopage_title}</th>
		<td><input type="text" name="MenuPriority[{$subpages[menus].infopage_id}]" size="3" value="{$subpages[menus].infopage_priority}"></td>
	</tr>
{/section}

{/if}

</table><p>
{if $infopage_id}
<input type=hidden name=infopage_id value='{$infopage_id}'>
{/if}

<center><input type=submit value='Submit'></center></form><p>

<table>
        <tr>
		<th Class='Banner'>Current Information Pages</th>
	</tr>
        <tr>
		<td>The following List shows all pages currently contained in the system.  To edit any page or sub-menu structure, click on the page title.<p>

<strong><a href='infopages.php?infopage_id={$MainPageID}'>{$MainPageTitle}</a>

{$PrintChildren}

{if !empty($PrintOrphans)}
<h3>Orphaned Pages</h3>
{$PrintOrphans}
{/if}
		</td>
	</tr>
	<tr>
		<th class="Banner">&nbsp;</th>
	</tr>
</table>
<p>
<form action=infopages.php action=POST>
<center><input type=submit value='Add a New Page'></center></form>
{include file='footer.tpl'}
