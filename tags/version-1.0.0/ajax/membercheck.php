<?php

################################################################################
#                                                                              #
#		Filename:	test.php				       #
#		Author:		Martin Settle				       #
#               Created:	17 October 2007				       #
#		Description:	conceptual test page			       #
#		Calls:		config.php				       #
#		Called by:	login.php				       #
#									       #
################################################################################


if(empty($CoopName))
{	
	session_start();
	
	$Secure = 1;
	include '../config.php';
	}

//don't cache this page4
header('Expires: Wed 23 Dec 1980 00:30:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

header('Content-Type: text/xml');

$_GET = quote_smart($_GET);

$membertest = mysql_query("SELECT count(member_id) FROM member WHERE member_email = {$_GET['email']}");
$membercount = mysql_result($membertest,0);
if($membercount == 1) $test = 'member';
else $test = 'new';
/*$dom = new DOMDocument();
$member = $dom->createElement('member');
$dom->appendChild($member);
$responseText = $dom->createTextNode($test);
$member->appendChild($responseText);
$xmlString = $dom->saveXML();
*/
$xmlString = "<?xml version=\"1.0\"?>
<member>{$test}</member>";

echo $xmlString;

//error_log("\nReturned {$xmlString}\n",3,'testing_log');

?>
