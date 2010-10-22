-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 18, 2010 at 01:09 PM
-- Server version: 5.1.48
-- PHP Version: 5.2.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

--
-- Table structure for table `balance_update`
--

DROP TABLE IF EXISTS `balance_update`;
CREATE TABLE `balance_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` varchar(128) DEFAULT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `old_balance` float(6,2) NOT NULL DEFAULT '0.00',
  `new_balance` float(6,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
CREATE TABLE `calendar` (
  `calendar_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_date` date NOT NULL,
  PRIMARY KEY (`calendar_id`),
  UNIQUE KEY `order_date` (`order_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `category_id` int(2) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(30) NOT NULL DEFAULT '',
  `category_description` text,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `category` (`category_id`, `category_name`, `category_description`) VALUES
(1, 'Alcoholic drinks', 'Beer, cider, wine etc.'),
(2, 'Non-alcoholic drinks', 'Juices, Coffee, Tea and Infusions, Cordials, etc.'),
(3, 'Fruit and Veg', 'Fresh, seasonal fruit and vegetables'),
(4, 'Jams, pickles and preserves', 'Cans jars and bottles of jams, pickles and preserves.'),
(5, 'Meat ', 'Fresh or preserved meat and poultry'),
(6, 'Milk, butter and other dairy', 'Dairy products (not cheese)'),
(7, 'Other produce', 'Other produce'),
(8, 'Fish', 'Fish and fish products'),
(9, 'Eggs', 'Eggs'),
(10, 'Cheese', 'Cheeses'),
(11, 'Cakes, biscuits and breads', 'Baking products - cakes, biscuits, bread etc.');

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `name` varchar(30) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `config` (`name`, `value`) VALUES
('distribution_frequency', '1'),
('foodclub_cheques_to', 'Food hub bank acct name'),
('accept_paypal', '0'),
('accept_cheque_payments', '1'),
('foodclub_manager', 'Managers Name'),
('use_distribution_sites', '0'),
('foodclub_name', 'Food Hub Name'),
('foodclub_post_address', 'Food Hub postal address\r\nTown\r\nPostcode'),
('paypal_sandbox_account', ''),
('paypal_use_sandbox', '0'),
('paypal_sandbox_cert_id', ''),
('paypal_currency', 'GBP'),
('paypal_language_code', 'UK'),
('paypal_cert_id', ''),
('paypal_minimum_payment', '5.00'),
('paypal_account', ''),
('days_notice', '2'),
('markup', '0.08'),
('volunteer_discount_hours', '4'),
('volunteer_discount', '0.05'),
('use_VAT', '1'),
('distribution_day', '6'),
('admin_password', '5f4dcc3b5aa765d61d8327deb882cf99'),
('allow_autoregistration', '0'),
('use_farm_gate_pricing', '1'),
('manager_email', 'manager@yourfoodhub-domain'),
('orders_from_email', 'orders@yourfoodhub-domain'),
('paypal_charge', '0.05');
-- --------------------------------------------------------

--
-- Table structure for table `credit`
--

DROP TABLE IF EXISTS `credit`;
CREATE TABLE `credit` (
  `credit_id` int(8) NOT NULL AUTO_INCREMENT,
  `credit_member_id` int(8) NOT NULL,
  `credit_date` date NOT NULL,
  `credit_amount` float(6,2) NOT NULL,
  `credit_reference` varchar(256) NOT NULL,
  PRIMARY KEY (`credit_id`),
  KEY `credit_member_id` (`credit_member_id`,`credit_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `distribution`
--

DROP TABLE IF EXISTS `distribution`;
CREATE TABLE `distribution` (
  `distribution_id` int(3) NOT NULL AUTO_INCREMENT,
  `distribution_name` varchar(30) NOT NULL DEFAULT '',
  `distribution_address1` varchar(40) DEFAULT NULL,
  `distribution_address2` varchar(40) DEFAULT NULL,
  `distribution_address3` varchar(40) DEFAULT NULL,
  `distribution_town` varchar(30) DEFAULT NULL,
  `distribution_county` varchar(30) DEFAULT NULL,
  `distribution_postcode` varchar(8) DEFAULT NULL,
  `distribution_contact` varchar(30) DEFAULT NULL,
  `distribution_phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`distribution_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `distribution` (`distribution_id`, `distribution_name`, `distribution_address1`, `distribution_address2`, `distribution_address3`, `distribution_town`, `distribution_county`, `distribution_postcode`, `distribution_contact`, `distribution_phone`) VALUES
(1, 'Default', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `frequency`
--

DROP TABLE IF EXISTS `frequency`;
CREATE TABLE `frequency` (
  `frequency_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `frequency_name` varchar(30) NOT NULL,
  `frequency_SQL_add` varchar(256) NOT NULL,
  PRIMARY KEY (`frequency_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `frequency` (`frequency_id`, `frequency_name`, `frequency_SQL_add`) VALUES
(1, 'Monthly', 'interval 1 month'),
(2, 'Fourweekly', 'interval 28 day'),
(3, 'Fortnightly', 'interval 2 weeks'),
(4, 'Weekly', 'interval 1 week');

-- --------------------------------------------------------

--
-- Table structure for table `infopage`
--

DROP TABLE IF EXISTS `infopage`;
CREATE TABLE `infopage` (
  `infopage_id` int(3) NOT NULL AUTO_INCREMENT,
  `infopage_parent` int(3) NOT NULL DEFAULT '0',
  `infopage_title` varchar(60) DEFAULT NULL,
  `infopage_content` longtext NOT NULL,
  `infopage_menu` tinyint(1) NOT NULL DEFAULT '0',
  `infopage_menuplacement` varchar(7) NOT NULL DEFAULT 'Bottom',
  `infopage_priority` tinyint(3) DEFAULT NULL,
  `infopage_main` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`infopage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice` (
  `invoice_id` int(8) NOT NULL AUTO_INCREMENT,
  `invoice_member_id` int(5) NOT NULL DEFAULT '0',
  `invoice_date` date NOT NULL DEFAULT '0000-00-00',
  `invoice_total` float(7,2) NOT NULL DEFAULT '0.00',
  `invoice_VAT` decimal(5,2) NOT NULL DEFAULT '0.00',
  `invoice_number` varchar(12) NOT NULL,
  PRIMARY KEY (`invoice_id`),
  KEY `member` (`invoice_member_id`),
  KEY `invoicedate` (`invoice_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `markup`
--

DROP TABLE IF EXISTS `markup`;
CREATE TABLE `markup` (
  `markup_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `markup_group` varchar(40) NOT NULL,
  `markup` decimal(2,2) NOT NULL,
  `markup_description` varchar(255) NOT NULL,
  PRIMARY KEY (`markup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `member_id` int(5) NOT NULL AUTO_INCREMENT,
  `membership_num` varchar(4) DEFAULT NULL COMMENT 'manually assigned membership number',
  `member_first_name` varchar(20) NOT NULL DEFAULT '',
  `member_last_name` varchar(20) NOT NULL DEFAULT '',
  `member_email` varchar(255) NOT NULL DEFAULT '',
  `member_password` varchar(32) NOT NULL DEFAULT '',
  `member_homephone` varchar(12) DEFAULT NULL,
  `member_workphone` varchar(20) DEFAULT NULL,
  `member_mobilephone` varchar(12) DEFAULT NULL,
  `member_address1` varchar(40) DEFAULT NULL,
  `member_address2` varchar(40) DEFAULT NULL,
  `member_address3` varchar(40) DEFAULT NULL,
  `member_town` varchar(30) DEFAULT NULL,
  `member_county` varchar(30) DEFAULT NULL,
  `member_postcode` varchar(8) DEFAULT NULL,
  `member_contact_method` varchar(10) DEFAULT NULL,
  `member_distribution_id` int(3) NOT NULL DEFAULT '0',
  `member_active` int(1) NOT NULL DEFAULT '1',
  `member_account_balance` float(6,2) DEFAULT '0.00',
  `markup_id` tinyint(4) DEFAULT NULL,
  `supplier_id` int(3) DEFAULT NULL,
  `verification_code` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`member_id`),
  KEY `email` (`member_email`),
  KEY `distribution` (`member_distribution_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `done` tinyint(4) NOT NULL,
  `from_id` int(5) NOT NULL DEFAULT '0',
  `to_id` int(5) NOT NULL,
  `msg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `msg_read` tinyint(4) NOT NULL DEFAULT '0',
  `msg_subject` varchar(255) DEFAULT NULL,
  `msg_body` text,
  PRIMARY KEY (`msg_id`),
  UNIQUE KEY `message_id` (`msg_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(20) NOT NULL AUTO_INCREMENT,
  `order_date` date NOT NULL DEFAULT '0000-00-00',
  `order_member_id` int(5) NOT NULL DEFAULT '0',
  `order_product_id` int(5) NOT NULL DEFAULT '0',
  `order_current_price` float(7,4) NOT NULL DEFAULT '0.0000',
  `order_paid_price` float(7,4) NOT NULL DEFAULT '0.0000',
  `order_quantity_requested` float(5,2) NOT NULL DEFAULT '0.00',
  `order_quantity_delivered` float(5,2) DEFAULT NULL,
  `order_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `orderdate` (`order_date`),
  KEY `member` (`order_member_id`),
  KEY `product` (`order_product_id`),
  KEY `order_time` (`order_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `product_id` int(5) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(50) NOT NULL,
  `product_description` varchar(255) DEFAULT NULL,
  `product_category_id` int(2) NOT NULL DEFAULT '0',
  `product_supplier_id` int(3) NOT NULL DEFAULT '0',
  `product_code` varchar(12) DEFAULT NULL,
  `product_cost` float(7,4) NOT NULL DEFAULT '0.0000',
  `product_VAT_rate` float(4,3) NOT NULL DEFAULT '0.000',
  `product_units` varchar(32) NOT NULL,
  `product_pkg_count` float(5,2) NOT NULL DEFAULT '1.00',
  `product_case_size` int(4) NOT NULL DEFAULT '0',
  `product_perishable` tinyint(1) NOT NULL DEFAULT '0',
  `product_allow_stock` float(4,1) NOT NULL DEFAULT '0.0',
  `product_current_stock` float(4,1) NOT NULL DEFAULT '0.0',
  `product_local` tinyint(1) NOT NULL DEFAULT '0',
  `product_organic` tinyint(1) NOT NULL DEFAULT '1',
  `product_fairtrade` tinyint(1) NOT NULL DEFAULT '0',
  `product_markup` float(4,3) DEFAULT NULL,
  `product_available` tinyint(1) NOT NULL DEFAULT '1',
  `product_default_quantity_available` int(6) DEFAULT NULL,
  `product_archived` tinyint(1) NOT NULL DEFAULT '0',
  `product_more_info` text,
  `product_pic` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`),
  KEY `category` (`product_category_id`),
  KEY `supplier` (`product_supplier_id`),
  KEY `local` (`product_local`),
  KEY `organic` (`product_organic`),
  KEY `fairtrade` (`product_fairtrade`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_calendar`
--

DROP TABLE IF EXISTS `product_calendar`;
CREATE TABLE `product_calendar` (
  `order_date` date NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_available` int(5) DEFAULT NULL,
  `quantity_ordered` int(5) NOT NULL DEFAULT '0',
  `purchase_quantity` float(5,2) DEFAULT NULL,
  `current_price` float(7,4) NOT NULL,
  `delivered_quantity` float(5,2) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_date`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recurring`
--

DROP TABLE IF EXISTS `recurring`;
CREATE TABLE `recurring` (
  `recurring_id` int(8) NOT NULL AUTO_INCREMENT,
  `recurring_member_id` int(5) NOT NULL DEFAULT '0',
  `recurring_product_id` int(5) NOT NULL DEFAULT '0',
  `recurring_quantity` int(3) NOT NULL DEFAULT '0',
  `recurring_frequency` tinyint(1) NOT NULL DEFAULT '0',
  `recurring_next_order` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`recurring_id`),
  KEY `member` (`recurring_member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `supplier_id` int(3) NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(60) NOT NULL DEFAULT '',
  `supplier_account` varchar(10) DEFAULT NULL,
  `supplier_contact_name` varchar(30) DEFAULT NULL,
  `supplier_phone` varchar(15) DEFAULT NULL,
  `supplier_fax` varchar(15) DEFAULT NULL,
  `supplier_email` varchar(255) DEFAULT NULL,
  `supplier_address1` varchar(40) DEFAULT NULL,
  `supplier_address2` varchar(40) DEFAULT NULL,
  `supplier_address3` varchar(40) DEFAULT NULL,
  `supplier_town` varchar(30) DEFAULT NULL,
  `supplier_county` varchar(30) DEFAULT NULL,
  `supplier_postcode` varchar(8) DEFAULT NULL,
  `supplier_order_method` varchar(10) DEFAULT NULL,
  `supplier_delivery_day` tinyint(1) DEFAULT NULL,
  `supplier_active` tinyint(1) NOT NULL DEFAULT '1',
  `supplier_recurring` tinyint(1) NOT NULL DEFAULT '1',
  `supplier_info` text,
  PRIMARY KEY (`supplier_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_accounts`
--

DROP TABLE IF EXISTS `supplier_accounts`;
CREATE TABLE `supplier_accounts` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(5) NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_reference` varchar(50) NOT NULL,
  `transaction_value` float(7,2) NOT NULL,
  `transaction_VAT` float(5,2) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `temp_orders`
--

DROP TABLE IF EXISTS `temp_orders`;
CREATE TABLE `temp_orders` (
  `temp_order_id` int(20) NOT NULL AUTO_INCREMENT,
  `order_date` date NOT NULL,
  `order_member_id` int(5) NOT NULL DEFAULT '0',
  `order_product_id` int(5) NOT NULL DEFAULT '0',
  `order_current_price` float(7,4) NOT NULL,
  `order_quantity_requested` float(5,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`temp_order_id`),
  UNIQUE KEY `product_order` (`order_date`,`order_member_id`,`order_product_id`),
  KEY `member` (`order_member_id`),
  KEY `product` (`order_product_id`),
  KEY `calendar_id` (`order_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

DROP TABLE IF EXISTS `volunteer`;
CREATE TABLE `volunteer` (
  `volunteer_id` int(5) NOT NULL AUTO_INCREMENT,
  `volunteer_member_id` int(5) NOT NULL DEFAULT '0',
  `volunteer_date` date NOT NULL DEFAULT '0000-00-00',
  `volunteer_hours` float(3,1) NOT NULL DEFAULT '0.0',
  `volunteer_task` varchar(50) DEFAULT NULL,
  `volunteer_hours_authorised` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`volunteer_id`),
  KEY `date` (`volunteer_date`),
  KEY `member` (`volunteer_member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
