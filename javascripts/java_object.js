// This file holds three functions (two dynamic) for processing AJAX requests
// create the AJAX object

function createAjaxObject()
{
	var xmlHttp;
	// for IE6 and older
	try
	{
		xmlHttp = new XMLHttpRequest();
		}
	catch(e)
	{
		var XmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0",
									"MSXML2.XMLHTTP.5.0",
									"MSXML2.XMLHTTP.6.0",
									"MSXML2.XMLHTTP.3.0",
									"MSXML2.XMLHTTP",
									"Microsoft.XMLHTTP")
		// try until one works
		for (var i=0; i,XmlHttpVersions.length && !xmlHttp; i++)
		{
			try 
			{xmlHttp = new ActiveXObject(XmlHttpVersions[i]);
				}
			catch (e) {}
			}
		}
	// if it didn't work, post an error
	if(!xmlHttp)
		alert("Error creating the XMLHttpRequest object.");
	else
	 	return xmlHttp;
	}

// process the request
function processGet(handlerUrl, functionName)
{
	if(xmlHttp)
	{
		cache.push(handlerUrl);
		handlerCache.push(functionName);
		// try to connect to the server
		try
		{
			if((xmlHttp.readyState == 4 || xmlHttp.readyState == 0) && cache.length > 0)
			{
				var cacheEntry = cache.shift();
				var handlerFunction = handlerCache.shift();
				xmlHttp.open("GET", cacheEntry, true);
				xmlHttp.onreadystatechange = function () {handleRequestStateChange(handlerFunction); }
				xmlHttp.send(null);
				}
			}
		catch (e)
		{
			alert ("Can't connect to server: \n" + e.toString());
			}
		}
	}

// handle the response
function handleRequestStateChange(functionName)
{
	// when readyState is 4, we are ready to read the server response
	if (xmlHttp.readyState == 4)
	{
		//continue only if HTTP status is "OK"
		if (xmlHttp.status == 200)
		{
			try
			{
			//	displayForm();
				eval(functionName+"()");
				}
			catch(e)
			{
				// didn't work, so display error message
				// alert("Error reading the response: " + e.toString());
				}			
			}
		else
		{
			// display status message
			//alert ("Got response "+ xmlHttp.status + "\n\nXML was " + xmlHttp.responseText );
			}
		}
	}

