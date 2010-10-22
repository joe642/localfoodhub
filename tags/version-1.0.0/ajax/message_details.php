<?php

################################################################################
#                                                                              #
#		Filename:	message_details.php				       #
#		Author:		Dave Cockcroft			       #
#       Created:	23 Nov 2009				       #
#		Description:	displays message			       #
#		Calls:		config.php				       #
#		Called by:	messages.php, admin_message.tpl				       #
#									       #
################################################################################

if(empty($CoopName)) {	
	session_start();
	
	$Secure = 1;
	include '../config.php';
}

//don't cache this page
header('Expires: Wed 23 Dec 1980 00:30:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

header('Content-Type: text/html; charset=ISO-8859-1');

$_GET = quote_smart($_GET);
								
$lookup = mysql_query("SELECT msg_id, UNIX_TIMESTAMP(msg_date) as msg_sent, msg_subject, msg_body, member_first_name, member_last_name, member_email FROM messages, member
								WHERE msg_id = {$_GET['message_id']} AND messages.from_id = member.member_id");

if(mysql_num_rows($lookup) == 0) {
echo "<html>
<head>
<title>Display Message</title>
</head>
<body>
<div id='message_info'>
<h2>Sorry no message to display</h2>
</div>
</body>
</html>";

} else {

$msg = mysql_fetch_array($lookup);

echo "
<head>
<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'/>
<title>Display Message</title>
</head>
<body>
<div id='message_info'>
<h2>Subject: " . $msg['msg_subject'] . "</h2>
<h3>From: " . $msg['member_first_name'] . " " . $msg['member_last_name'] . "</h3>
<h3>Date: " . date("d-M-y", $msg['msg_sent']) . "&nbsp;" . date("H:i", $msg['msg_sent']) . "</h3>
<p>" . nl2br($msg['msg_body']) . "</p>
</div>
</body></html>";
}

?>
