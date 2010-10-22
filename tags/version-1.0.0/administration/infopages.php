<?php

################################################################################
#                                                                              #
#		Filename:	infopages.php				       #
#		Author:		Martin Settle				       #
#               Created:	10 Nov 2006				       #
#		Description:	information page management		       #
#		Calls:		config.php.inc				       #
#		Called by:	nothing					       #
#									       #
#   Copyright 2010 Trellis Ltd
#
#   Licensed under the Apache License, Version 2.0 (the "License");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an "AS IS" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
#
################################################################################

/*
MODIFICATION HISTORY
	- 2006.10.11 File created
	-

*/

function PrintChildren($PageID)
{
         global $PrintChild;
	 $ChildrenLookup = mysql_query("SELECT infopage_id, infopage_title FROM infopage WHERE infopage_parent='$PageID' ORDER BY infopage_priority");
	 print mysql_error();
         if(mysql_num_rows($ChildrenLookup) > 0)
         {
                 $PrintChild .= "<ul compact>\n";
                 while($Children = mysql_fetch_array($ChildrenLookup))
                 {
                       $PrintChild .= "<li><a href=\"infopages.php?infopage_id={$Children['infopage_id']}\">{$Children['infopage_title']}</a>\n";
                       PrintChildren($Children["infopage_id"]);
                       }
                 $PrintChild .= "</ul>\n";
                 }
	return $PrintChild;
         }

function PrintOrphan($PageID)
{
         global $PrintOrphan;
	 $ChildrenLookup = mysql_query("SELECT infopage_id, infopage_title FROM infopage WHERE infopage_parent='$PageID' ORDER BY infopage_priority");
         if(mysql_num_rows($ChildrenLookup) > 0)
         {
                 $PrintOrphan .= "<ul compact>\n";
                 while($Children = mysql_fetch_array($ChildrenLookup))
                 {
                       $PrintOrphan .= "<li><a href=\"infopages.php?infopage_id={$Children['infopage_id']}\">{$Children['infopage_title']}</a>\n";
                       PrintOrphans($Children["infopage_id"]);
                       }
                 $PrintOrphan .= "</ul>\n";
                 }
	return $PrintOrphan;
         }



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

include '../header.php';

if(!empty($_GET['infopage_id']))
{
        $Pagelookup = mysql_query("SELECT * FROM infopage
                                    WHERE infopage_id = '$_GET[infopage_id]'");
        $Page = mysql_fetch_array($Pagelookup);
        }
else
{
        $Page["infopage_parent"] = '';
        $Page["infopage_title"] = '';
        $Page["infopage_menu"] = 0;
        $Page["infopage_menuplacement"] = 'Bottom';
        $Page["infopage_priority"] = '';
        $Page["infopage_content"] = 'Place your page information here';
        $Page["infopage_main"] = 0;
        }

$MenuPagesLookup = mysql_query("SELECT infopage_id, infopage_title FROM infopage
				WHERE infopage_menu = 1
				ORDER BY infopage_title");
$ParentList[] = $MenuPage['None'];
while($MenuPage = mysql_fetch_array($MenuPagesLookup))
{
	$ParentList[$MenuPage['infopage_id']] = $MenuPage['infopage_title'];
	}
$smarty->assign('ParentList',$ParentList);
$smarty->assign('infopage_parent',$Page['infopage_parent']);
$smarty->assign('infopage_id',$Page['infopage_id']);
$smarty->assign('infopage_title',$Page['infopage_title']);
$smarty->assign('infopage_content',$Page['infopage_content']);
$smarty->assign('YesNo',array(1 => 'Yes',0 => 'No'));
$smarty->assign('infopage_menu',$Page["infopage_menu"]);
$smarty->assign('TopBottom',array('Top' => 'Top','Bottom' => 'Bottom'));
$smarty->assign('infopage_menuplacement',$Page['infopage_menuplacement']);
$smarty->assign('infopage_main',$Page['infopage_main']);

/* If the page being edited is a MenuPage, lookup and print the priority of the submenu */

if($Page["infopage_menu"] == 1)
{
        $MenuLookup = mysql_query("SELECT infopage_id, infopage_title, infopage_priority
                                    FROM infopage
                                     WHERE infopage_parent = {$_POST['infopage_id']}
                                      ORDER BY infopage_priority");
        $count = 0;
        while($Menu = mysql_fetch_array($MenuLookup))
        {
              $subpages[$count]['infopage_title'] = $Menu['infopage_title'];
              $subpages[$count]['infopage_priority'] = $Menu['infopage_priority'];
              $subpages[$count]['infopage_id'] = $Menu['infopage_id'];
              $count++;
              }
        }
$smarty->assign('subpages',$subpages);

$MainPageLookup = mysql_query("SELECT * FROM infopage WHERE infopage_main = 1");
$MainPage = mysql_fetch_array($MainPageLookup);

$smarty->assign('MainPageTitle',$MainPage['infopage_title']);
$smarty->assign('MainPageID', $MainPage['infopage_id']);

$smarty->assign('PrintChildren', PrintChildren($MainPage['infopage_id']));

$PrintOrphan = '';
$OrphanPageLookup = mysql_query("SELECT infopage_id, infopage_title FROM infopage WHERE infopage_parent = 0 AND infopage_main = 0");
while($orphans = mysql_fetch_array($OrphanPageLookup))
{
	$PrintOrphan .= "<a href=\"infopages.php?infopage_id={$orphans['infopage_id']}\">{$orphans['infopage_title']}</a>\n";
 	PrintOrphan($orphans['infopage_id']);
	$PrintOrphan .= "<br \>";

	}
$smarty->assign('PrintOrphans',$PrintOrphan);

# include SystemMessage from the processing system

$smarty->assign('SystemMessage', $SystemMessage);

include '../footer.php';

$smarty->display('infopage_admin.tpl');

?>
