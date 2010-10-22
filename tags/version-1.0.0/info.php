<?php

/******************************************************************************/
/*                                                                            */
/*            File Name:        info.php                                      */
/*            Created By:       Martin Settle                                 */
/*            Creation Date:    December 17, 2001                             */
/*            Called By:        nothing		                              */
/*            Calls:            nothing                                       */
/*                                                                            */
/*            Change Log:                                                     */
/*                 Dec. 17, 2001 - File Created 			      */
/*		   09/11/06 - file modified for foodclub		      */
/*                                                                            */
/******************************************************************************/


if(empty($CoopName))
{
        session_start();
        header ("Cache-control: private");
        $Secure = 1;
        include 'config.php';
        }
															

include "header.php";
include "footer.php";

$javascripts = array('java_object.js','logintest.js');
$smarty->assign('javascripts',$javascripts);

/* The result of this page is controlled by a GET or POST variable entitled
Page.  If there is not Page specified, the system must print the default page */

// this allows a link to be created by pagename
if(!empty($_REQUEST['pagename']))
{
	$pagename = quote_smart($_REQUEST['pagename']);
	
	$pagelookup = mysql_query("SELECT infopage_id FROM infopage
							WHERE infopage_title = {$pagename} LIMIT 1");
	if(mysql_num_rows($pagelookup) == 1)
	{
		$Page = mysql_result($pagelookup,0,'infopage_id');
		}
	// if no page was found, this will default to the main page,if it exists
	}
else
{
	$Page = $_POST['infopage_id'];
	$Page .= $_GET['infopage_id'];
	}

if(empty($Page))
{
	if(!$pagelookup = mysql_query("SELECT infopage_id FROM infopage
                                       WHERE infopage_main = 1"))
	{
		$body_text = "<h1 class=\"InfoTitle\">No Information Page Available</h1>\nSorry, there is no information page available at this time.";
		$smarty->assign('body_text',$body_text);
		$smarty->display('index.tpl');
		exit();
	   }
       $Pageresult = mysql_fetch_array($pagelookup);
       $Page = $Pageresult["infopage_id"];
}

/* Now that we definitely have a page, look it up, and print it... */

$pageinfolookup = mysql_query("SELECT * FROM infopage
                                WHERE infopage_id = '$Page'");
$pageinfo = mysql_fetch_array($pageinfolookup);

$title = $pageinfo["infopage_title"];

$body_text = "<h1>{$pageinfo['infopage_title']}</h1>\n";

if(($pageinfo['infopage_menu'] == 1) && ($pageinfo['infopage_menuplacement'] == 'Top'))
{
    $body_text .= "<ul class='InfoMenu'>\n";

    $linklookup = mysql_query("SELECT infopage_id, infopage_title
                                FROM infopage
                                 WHERE infopage_parent = '$Page'
                                  ORDER BY infopage_priority");
    while($link = mysql_fetch_array($linklookup))
    {
        $body_text .= "<li class='InfoMenu'><a href='info.php?Page=$link[infopage_id]' class='InfoMenu'>$link[infopage_title]</a>\n";
    }

    $body_text .= "</ul>\n<p>\n<HR class='InfoMenu'>\n<p>\n";
}

$body_text .= $pageinfo['infopage_content'];

if(($pageinfo['infopage_menu'] == 1) && ($pageinfo['infopage_menuplacement'] == 'Bottom'))
{
    $body_text .= "<p>\n<HR class='InfoMenu'>\n<p>\n<ul class='InfoMenu'>\n";

    $linklookup = mysql_query("SELECT infopage_id, infopage_title
                                FROM infopage
                                 WHERE infopage_parent = '$Page'
                                  ORDER BY infopage_priority");
    while($link = mysql_fetch_array($linklookup))
    {
        $body_text .= "<li class='InfoMenu'><a href='info.php?infopage_id=$link[infopage_id]' class='InfoMenu'>$link[infopage_title]</a>\n";
    }

    $body_text .= "</ul>\n";
}

if($pageinfo["infopage_parent"] != 0)
{
        $ParentLookup = mysql_query("SELECT infopage_id, infopage_title
                                      FROM infopage
                                       WHERE infopage_id = '$pageinfo[infopage_parent]'");
        $Parent = mysql_fetch_array($ParentLookup);
        $body_text .= "<br><br><center><hr width=50%><br>
                Back to <a href='info.php?Page=$Parent[infopage_id]'>$Parent[infopage_title]</a>
                </center><br>";
        }

	$smarty->assign('body_text',$body_text);
	$smarty->display('index.tpl');

?>
