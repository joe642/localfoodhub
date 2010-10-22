// create the AJAX object

var xmlHttp = createAjaxObject();
var cache = new Array();
var handlerCache = new Array();

function checkMember()
{
	var emailAddress = document.LoginRegister.Email.value;
	document.LoginRegister.Email.disabled = true;
	emailAddress = encodeURIComponent(emailAddress);
	request = "ajax/membercheck.php?email=" + emailAddress;
	processGet(request, 'displayForm');
	}


function displayForm()
{
	// get the response as an xml objet
	var xmlResponse = xmlHttp.responseXML;
	// catch IE and opera errors
	if(!xmlResponse || !xmlResponse.documentElement)
		throw("1. Invalid XML structure: \n" + xmlHttp.responseText);
	// catch Firefox mozilla errors
	var rootNodeName = xmlResponse.documentElement.nodeName;
	if (rootNodeName == "parsererror")
		throw("2. invalid XML structure:\n" + xmlHttp.responseText);
	// get the root node element
	xmlRoot = xmlResponse.documentElement;
	// check that we got the right document
	if (rootNodeName != "member"  || !xmlRoot.firstChild)
		throw ("3. invalid XML structure: \n" + xmlHttp.responseText);
	
	// Got the document, now see if it is member or new
	if (xmlRoot.firstChild.data == "member")
	{
		passwordForm = document.getElementById("password_spacer");
		passwordForm.innerHTML = '<br>Password: <input type="password" name="Password" size="12" onChange="submit();"><a href="javascript: void(0);" onClick="document.LoginRegister.submit();" style="text-decoration: none;"><strong>Go</strong></a>';
		document.LoginRegister.Password.focus();
		document.LoginRegister.tested.value=1;
		document.LoginRegister.Email.disabled = false;
		document.LoginRegister.action = 'login.php';
		}
	else
	{
		document.LoginRegister.action = 'join.php';
		document.LoginRegister.tested.value=1;
		document.LoginRegister.submit();
		}
	}

function check()
{
	if (document.LoginRegister.tested.value == 0)
	{	
		checkMember();
		return false;
		}
	else
	{
		return true;
		}
	}	

