// this just sends the change availability function to the ajax handler

function change_availability(supplierID,orderDate)
{
	xmlHttp = createAjaxObject();
	xmlHttp.open("GET","../ajax/supplier_availability.php?supplier_id=" + supplierID + "&order_date=" + orderDate, false);
	xmlHttp.send(null);
	}
