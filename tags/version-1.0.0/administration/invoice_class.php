<?php

class invoice
{
	var $number;
	var $member;
	var $order_date;
	var $orders;
	var $order_gross;
	var $order_VAT;
	var $order_discount;
	var $order_net;
	var $ending_balance;
	
	function __construct($member_id,$order_date)
	{
		$this->member = new member();
		$this->member->get_by_id($member_id);
		$this->order_date = $order_date;
		$date = str_replace('-','',$order_date);
		$this->number = $date . '.' . $member_id;

		// lookup orders
		$orders_lookup = mysql_query("SELECT order_id FROM orders 
						WHERE order_member_id = {$member_id}
						AND order_date = '{$order_date}'");
		while($o = mysql_fetch_array($orders_lookup))
		{
			$ord = new order($o['order_id']);
			$ord->calculate_total($this->member->markup);
			$this->orders[] = $ord;
			} 
			
		$this->calculate_totals();
		}

	function calculate_totals()
	{
		foreach($this->orders AS $ord)
		{
			$this->order_net += $ord->total;
			$this->order_VAT += $ord->VAT;
			$this->payment_received += $ord->total_request;
			$this->order_gross += $ord->total - $ord->VAT;
			$this->ending_balance = $this->member->balance + $this->payment_received - $this->order_net;
			}
		}

	function save()
	{
		mysql_query("INSERT INTO invoice
			SET invoice_member_id = {$this->member->member_id},
			invoice_date = '{$this->order_date}',
			invoice_total = {$this->order_net},
			invoice_VAT = {$this->VAT},
			invoice_number = {$this->number}
			ON DUPLICATE KEY UPDATE 
				(invoice_total = {$this->order_net}, invoice_VAT = {$this->order_VAT}");
		}

	}

class member
{
	var $member_id;
	var $firstname;
	var $lastname;
	var $address1;
	var $address2;
	var $address3;
	var $town;
	var $county;
	var $postcode;
	var $balance;
	var $markup;

	function get_by_id($member_id)
	{
		$member_lookup = mysql_query("SELECT * FROM member WHERE member_id = {$member_id}");
		$this->firstname = mysql_result($member_lookup,0,'member_first_name');
		$this->lastname = mysql_result($member_lookup,0,'member_last_name');
		$this->address1 = mysql_result($member_lookup,0,'member_address1');
                $this->address2 = mysql_result($member_lookup,0,'member_address2');
                $this->address3 = mysql_result($member_lookup,0,'member_address3');
		$this->town = mysql_result($member_lookup,0,'member_town');
		$this->county = mysql_result($member_lookup,0,'member_county');
		$this->postcode = mysql_result($member_lookup,0,'member_postcode');
		$this->balance = mysql_result($member_lookup,0,'member_account_balance');
		$markup_id = mysql_result($member_lookup,0,'markup_id');
		if(empty($markup_id)) $this->markup = get_config_value('markup');
		else
		{
			$markup_lookup = mysql_query("SELECT markup FROM markup WHERE markup_id = {$markup_id}");
			$this->markup = mysql_result($markup_lookup,0,'markup');
			}
		}

	}


class order
{
	var $order_id;
	var $member_id;
	var $product_id;
	var $product_name;
	var $product_cost;
	var $quantity_requested;
	var $quantity_delivered;
	var $unit_price;
        var $product_VAT_rate;
        var $VAT;
        var $total;
	var $total_request;
        
	function __construct ( $order_id )
	{
		if(empty($order_id))
		{
			// this would be used to create a new order as part of the invoice adjustment
			return true;
			}
		$order_lookup = mysql_query("SELECT * FROM orders, product
		                                         WHERE order_product_id = product_id 
		                                         AND order_id = {$order_id}");
		print mysql_error();
		$this->order_id = $order_id;
		$this->product_id = mysql_result($order_lookup,0,'order_product_id');
		$this->member_id = mysql_result($order_lookup,0,'order_member_id');
		$this->product_name = mysql_result($order_lookup,0,'product_name');
		$this->product_cost = mysql_result($order_lookup,0,'order_current_price');
		$this->quantity_requested = mysql_result($order_lookup,0,'order_quantity_requested');
		$this->quantity_delivered = mysql_result($order_lookup,0,'order_quantity_delivered');
		$this->product_VAT_rate = mysql_result($order_lookup,0,'product_VAT_rate');
		}
		
	function calculate_total($markup = NULL)
	{
		if(empty($markup))
		{
			$member_lookup = mysql_query("SELECT markup_id FROM member WHERE member_id = {$this->$member_id}");
			$markup_id = mysql_result($member_lookup,0,'markup_id');
			if(empty($markup_id)) $markup = get_config_value('markup');
			else
			{
				$markup_lookup = mysql_query("SELECT markup FROM markup WHERE markup_id = {$markup_id}");
				$markup = mysql_result($markup_lookup,0,'markup');
				}
			}
			
		$this->unit_price = round($this->product_cost * (1 + $markup) * (1 + $this->product_VAT_rate),2);
		$this->VAT = round($this->product_cost * (1 + $markup) * $this->quantity_delivered * $this->product_VAT_rate,2);
		$this->total = $this->unit_price * $this->quantity_delivered;
		$this->total_request = $this->unit_price * $this->quantity_requested;
		}

        function saveOrder()
        {
                
                
                }        
        
	}
