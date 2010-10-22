<?php

################################################################################
#                                                                              #
#		Filename:	config.php.inc				       #
#		Author:		Martin Settle				       #
#       	Created:	19 May 2006				       #
#		Description:	Sets up the system configuration variables     #
#		Calls:		core_functions.php			       #
#		Called by:	all pages				       #
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
	- 2006.05.19 file created
	-

*/
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

# Don't allow direct linking
if(!$Secure) die( 'Direct Access to this location is not allowed.' );

# Include core function library
include 'core_functions.php';

# Include private site information

include 'config_data.php';

# start Smarty template engine

define('SMARTY_DIR',"$BasePath/thirdparty/smarty/");

require_once("$BasePath/thirdparty/smarty/Smarty.class.php");
$smarty = new Smarty();
$smarty->template_dir = "$BasePath/templates";
$smarty->compile_dir = "$BasePath/templates_c";
$smarty->config_dir = "$BasePath/configs";
$smarty->cache_dir = "$BasePath/cache";

# Open database connection
if(!mysql_connect("$MyServer","$MyUser","$MyPassword"))
{
        ?>ERROR CONNECTING TO SERVER<?php
	exit();
}
if(!mysql_select_db("$MyDatabase"))
{
	?>ERROR CONNECTING TO DATABASE<?php
	exit();
}

# Get static config data
$CoopName = get_config_value('foodclub_name');

$smarty->assign('title',$CoopName);
$smarty->assign('pagetitle',$CoopName);

if(empty($_SESSION['order_date'])) {
	$_SESSION['markup'] = get_config_value('markup'); // override this in login script to set for groups discounts
	$days_notice = get_config_value('days_notice');
	$date_past = mktime(0,0,0,date('m'),date('d')+$days_notice,date('y'));
	
	$order_date_lookup = mysql_query("SELECT order_date FROM calendar WHERE order_date > FROM_UNIXTIME({$date_past}) LIMIT 1");
	$_SESSION['order_date'] = mysql_result($order_date_lookup,0,'order_date');
}

?>
