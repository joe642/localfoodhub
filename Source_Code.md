# Introduction #

The v1.0 code uses the Smarty Template Engine for user interface display. This provides a degree of separation of code from display but to understand the code both the php script and the associated Smarty .tpl files need to be examined.

To find which php file performs what function just run the hub software standalone (eg. www.yoursite.hub) rather than in a iframe inside Joomla. You can then see the php pages linked from the hub menus. Search a php script for .tpl to find out which templates it calls. Some pages only call one template but some, for example producer.php call several depending on the action required.

PLEASE NOTE THAT STROUDCO IS OFFERING THE SOFTWARE FREE OF CHARGE BUT HAS NO RESOURCES TO PROVIDE TECHNICAL SUPPORT OR DEVELOPMENT OF THE SOFTWARE TO SUIT OTHER APPLICATIONS.  STROUDCO CAN PROVIDE USER TRAINING AND SUPPORT BUT WOULD NEED TO CHARGE FOR THIS SERVICE.


## Folders ##

root - holds the member pages

administration - holds the admin pages

ajax - ajax support for interface forms

facebox - popup support for product more info and images

images - logos etc

install - manual installation instructions

javascripts - scripts to support ajax and forms

templates - the Smarty templates

templates\_c - compiled Smarty templates

thirdparty - php packages: Smarty, FCKeditor, phpmailer, jquery


## Smarty User Interface ##

Uses Smart Template Engine for all user interface screens. Version 2.6.26 shipped in thirdparty/smarty.

For more information see: http://www.smarty.net/