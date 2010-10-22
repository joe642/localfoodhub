<?php
  /*********************************************************************/
  /*
       Writen By:     Martin Settle
       Last Modified: December 28, 2001
       Called By:     infopages.php
       Calls:         Nothing
       Description:   This processes edits of the system information
                      pages.

       Modification History:
                    December 28, 2001 - File created.
                    November 12, 2006 - File altered for foodclub
		    November 20, 2008 - altered to allow orphan pages
  */
  /*********************************************************************/

/* Get the includes out of the way */

if(empty($CoopName))
{	
	session_start();
	
	if(empty($_SESSION['member_id']))
	{
		header ("Location:../index.php");
		exit();
		}
	elseif(empty($_SESSION['admin']))
	{
		header ("Location:index.php");
		exit();
		}

	header ("Cache-control: private");

	$Secure = 1;
	include '../config.php';
	}
	
/* There should be a function here to check to make sure that that no more
than one page is identified as the MainPage */

if($_POST['infopage_main'] == 1)
{
	}

/* initialize a message variable */

$SystemMessage = '';

/* The process for inputting the data is dependent on whether or not the
page is being submitted for the first time.  This is shown by the presence
or absence of a PageID */

$_POST = quote_smart($_POST);

if(!empty($_POST['infopage_id']))
{
        $query = "UPDATE infopage SET infopage_parent = {$_POST['infopage_parent']}, 
			infopage_title = {$_POST['infopage_title']},
			infopage_menu = {$_POST['infopage_menu']}, 
			infopage_menuplacement = {$_POST['infopage_menuplacement']},
			infopage_content = {$_POST['infopage_content']},
			infopage_main = {$_POST['infopage_main']} 
			WHERE 
			infopage_id={$_POST['infopage_id']}";
        if(!mysql_query($query))
        {
                $SystemMessage .= "The system was unable to update the info page as requested.<p>
                	The database returned the following:<p><pre>";
                $SystemMessage .= mysql_error();
                $SystemMessage .= "</p></p><p>The query was<br><em><pre>$query</pre></em></p>";
                include "infopages.php";
                exit();
                }
        $SystemMessage .= "<strong>{$_POST['infopage_title']}</strong> was successfully changed in the database.<p>\n";
        }
else
{
	if($_POST['infopage_parent'] == "''") 
	{
		$_POST['infopage_parent'] = "'0'";
		$Priority = 1;
		}
	else
	{
		/* need to lookup the next priority */
	
		$PriorityLookup = mysql_query("SELECT Max(infopage_priority) AS Last
        				FROM infopage
                                       WHERE infopage_parent={$_POST['infopage_parent']}");
        	$Priority = mysql_result($PriorityLookup,0,'Last');
        	$Priority++;
		}

        $query = "INSERT INTO infopage 
		SET infopage_parent = {$_POST['infopage_parent']}, 
		infopage_title={$_POST['infopage_title']},
		infopage_menu={$_POST['infopage_menu']}, 
		infopage_menuplacement={$_POST['infopage_menuplacement']},
		infopage_content={$_POST['infopage_content']},
		infopage_main={$_POST['infopage_main']}, 
		infopage_priority=$Priority";
        if(!mysql_query("$query"))
        {
                $SystemMessage .= "The system was unable to add the <em>{$_POST['infopage_title']} page to the Database.<p>
                	The database returned the following:<p><pre>";
                $SystemMessage .= mysql_error();
		$SystemMessage .= '</pre>';
                include "infopages.php";
                exit();
                }
        $SystemMessage .= "<strong>{$_POST['infopage_title']}</strong> was successfully added to the database.<p>";
        include "infopages.php";
        exit();
        }

/* If still in this system, we may need to process priority changes.  These are held
in an array of $Priority["$PageID"] */

while(list($newPageID,$newPriority) = each($_POST['Priority']))
{
        $query = "UPDATE infopage SET infopage_priority='$newPriority' WHERE infopage_id = '$newPageID'";
        if(!mysql_query("$query"))
        {
        	$SystemMessage .= "The system was unable to change the priority of page #$newPageID.<br>";
                }
        }

/* Should be done now, so clear the PageID (so that the infopage will show a blank
form) and exit */

unset($_POST);
include "infopages.php";
exit();
?>
