<?php

################################################################################
#                                                                              #
#		Filename:		payment_functions.php.inc			       #
#		Author:		Martin Settle				       #
#               Created:		9 April 2008				       #
#		Description:	functions related to paypal payment processing				       #
#		Calls:		nothing				       #
#		Called by:		payment.php, paypal_api.php					       #
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
	- 2008.04.09 file created
	-

*/

# Don't allow direct linking

if(!$Secure) die( 'Direct Access to this page is not allowed.' );

# create the fields for an unencrypted button
function create_unencrypted($vars) {
	// $form = '<input type="hidden" name="cmd" value="_xclick">';
	$form = '<input type="hidden" name="cmd" value="_cart">';
	$form .= '<input type="hidden" name="upload" value="1">';
	foreach($vars as $name => $value) {
		$form .= '<input type="hidden" name="' . $name . '" value="' . $value . '">' . "\n";
	}
	
	return $form;
}

# main function which first tries to create an encrypted form, but failing necessary info, creates unencrypted
function paypal_form($vars)
{
	# check to see if we need to use sandbox
	$sandbox = get_config_value('paypal_use_sandbox');

	#start the form - set target="_blank" to avoid PayPal error screen - "Sorry, an error occurred after you clicked the last link"
	$form = '<form target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="POST">' . "\n";
	if($sandbox) $form = '<form target="_blank" action="https://www.sandbox.paypal.com/webscr/cgi-bin" method="POST">' . "\n";
	
	# get the paypal business account info
	$vars['business'] = get_config_value('paypal_account');
	if($sandbox) $vars['business'] = get_config_value('paypal_sandbox_account');
	$vars['lc'] = get_config_value('paypal_language_code');
	$vars['currency_code'] = get_config_value('paypal_currency');
	

	# include encryption file
	if(file_exists('config_paypal_data.php.inc'))
	{
		$Secure = 1;
		include('config_paypal_data.php.inc');
				
		# check files exist
		if(file_exists($my_key_file) && file_exists($my_cert_file) && file_exists('thirdparty/paypal_cert.pem'))
		{
			#check there is a paypal cert_id stored in the database
			if($sandbox) {$cert_id = 'paypal_sandbox_cert_id';}
			else {$cert_id = 'paypal_cert_id';}
			$cert_id_full = get_config_value($cert_id);
			if($cert_id_full)
			{
				$form .= '<input type="hidden" name="cmd" value="_s-xclick">' . "\n";

				$vars['cert_id'] = $cert_id_full;
				# now build the encrypted data...
				
				$vars['cmd'] = '_xclick';
				
				global $BasePath;
				
				$paypal_cert = 'paypal_cert.pem';
				if($sandbox) $paypal_cert = 'sandbox_cert.pem';
				
				$encrypt_cmd = "$openssl smime -sign -signer $my_cert_file -inkey $my_key_file " .
								"-outform der -nodetach -binary | $openssl smime -encrypt " .
								"-des3 -binary -outform pem ./thirdparty/$paypal_cert";
				#print $encrypt_cmd . '<br><br>';
				
				$descriptors = array(
					0 => array('pipe','r'),
					1 => array('pipe','w'),
					2 => array("file","/tmp/errors.txt","a")
					);
					
				putenv("HOME=/tmp");
					
				$proc = proc_open($encrypt_cmd, $descriptors, $pipes);
				
				if(is_resource($proc))
				{
					foreach($vars as $key => $value)
					{
						#print ("$key=$value<br>");
						if($value != '') fwrite($pipes[0], "$key=$value\n");
						}
					fflush($pipes[0]);
					fclose($pipes[0]);
					
					$encrypted_data = "";
					while(!feof($pipes[1]))
					{
						$encrypted_data .= fgets($pipes[1]);
						}
					fclose($pipes[1]);
					$closed_value = proc_close($proc);
					
					$form .= '<input type="hidden" name="encrypted" value="' . $encrypted_data . '">' . "\n";
					$encrypted = TRUE;
					}
				
				
				}
		
			}
		}
	if(!$encrypted)
	{
		# couldn't make encrypted, build unencrypted	
		$form .= create_unencrypted($vars);
		}
	
	$form .= '<input type="submit" value="Pay by PayPal"></form>';	
	
	return $form;
	}


?>
