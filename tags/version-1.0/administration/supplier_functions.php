<?php

################################################################################
#                                                                              #
#		Filename:	supplier_functions.php				       #
#		Author:		Martin Settle				       #
#     Created:	15 January 2009				       #
#		Description:	functions for supplier administration 	       #
#		Calls:      nothing				       #
#		Called by:	suppliers.php					       #
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
	- 2009.01.15 file created
	-

*/

// this function returns an array containing the supplier's availability
function get_availability($supplier_id)
{
    // set default availabilty as off
    $available = 0;
    
    // check if the supplier has products  If not, set availability based on supplier defaults (active, recurring)
    $hasproducts = mysql_query("SELECT * FROM product WHERE product_supplier_id = {$supplier_id}");
    if(mysql_num_rows($hasproducts) == 0)
    {
        $default_lookup = mysql_query("SELECT (supplier_active + supplier_recurring) AS avail FROM supplier
                                                                WHERE supplier_id = {$supplier_id}");
        if(mysql_result($default_lookup,0,'avail') == 2) $available = 1;
        }
    
    // get an array of calendar dates
    $calendar_lookup = mysql_query("SELECT order_date FROM calendar WHERE order_date > NOW()");
    while($c = mysql_fetch_array($calendar_lookup))
    {
        $calendar[$c['order_date']] = $available;
        }
        
    // now check each date for availability and adjust the calendar array 
    $available_lookup = mysql_query("SELECT order_date FROM product_calendar, product
                                                                WHERE product_calendar.product_id = product.product_id
                                                                AND product_supplier_id = {$supplier_id}
                                                                AND order_date > NOW()
                                                                AND (quantity_available > 0 OR quantity_available IS NULL)
                                                                GROUP BY order_date");
    while ($a = mysql_fetch_array($available_lookup))
    {
        $calendar[$a['order_date']] = 1;
        }

    return $calendar;
    }


// This function changes the supplier availability, adjusting product records as appropriate
function change_supplier_availability($supplier_id,$order_date,$availability)
{
    switch($availability)
    {
        case '0':
            // not available, so reset all product quantities to zero
            mysql_query("UPDATE product_calendar SET quantity_available = 0
                                    WHERE order_date = '{$order_date}'
                                    AND product_id IN
                                    (SELECT product_id FROM product WHERE product_supplier_id = {$supplier_id})");
            break;
        case '1':
            // available.  Set product quantities equal to default if product is available
            $productlist = mysql_query("SELECT * FROM product 
                                                                WHERE product_supplier_id = {$supplier_id}
                                                                AND product_available = 1");

            while($p = mysql_fetch_array($productlist))
            {
                mysql_query("INSERT INTO product_calendar
                                        SET order_date = '{$order_date}',
                                        product_id = {$p['product_id']},
                                        quantity_available = '{$p['product_default_quantity_available']}',
                                        current_price = '{$p['product_cost']}'
                                        ON DUPLICATE KEY UPDATE quantity_available = '{$p['product_default_quantity_available']}'");

                }
        }
    }


// This one gets the available suppliers by date
function get_available_by_date($order_date)
{
	$count = 0;
	$suppliers_lookup = mysql_query("SELECT supplier_id, supplier_name FROM supplier WHERE supplier_active = 1 ORDER BY supplier_name");
	while($s = mysql_fetch_array($suppliers_lookup))
	{
		$available_lookup = mysql_query("SELECT product.product_id FROM product_calendar, product 
							WHERE product_calendar.product_id = product.product_id 
							AND product_supplier_id = {$s['supplier_id']}
							AND (quantity_available > 0 OR quantity_available IS NULL)
							AND order_date = '{$order_date}'
							LIMIT 1");
		if(mysql_num_rows($available_lookup) == 0) $suppliers[$count]['available'] = 0;
		else $suppliers[$count]['available'] = 1;
		$suppliers[$count]['id'] = $s['supplier_id'];
		$suppliers[$count]['name'] = $s['supplier_name'];
		// check if self-administered
		$producer_lookup = mysql_query("SELECT * FROM member WHERE supplier_id = {$s['supplier_id']}");
		if(mysql_num_rows($producer_lookup) > 0) $suppliers[$count]['producer'] = 1;
		$count++;
		}
	return $suppliers;
	}


