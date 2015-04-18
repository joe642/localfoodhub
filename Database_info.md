# Introduction #

The database schema can be found in install/foodhub.sql which is used during installation to initialise the database. This page has a summary of what the tables are used for and explanation of some of the important fields.

PLEASE NOTE THAT STROUDCO IS OFFERING THE SOFTWARE FREE OF CHARGE BUT CURRENTLY HAS NO RESOURCES TO PROVIDE TECHNICAL SUPPORT OR DEVELOPMENT OF THE SOFTWARE TO SUIT OTHER APPLICATIONS. However, we are looking for funding to run a training session for people taking on the Stroudco system. Please let us know if you are interested in this.


# Description of Database Tables #

## balance\_update ##

is a log used to track changes to customer balances - helpful for debug purposes.

Used by: core\_functions.php account\_statement.php post\_invoices.php

## calendar ##

order\_date field holds the dates of distribution days.

Entries can be added from administration Set Up Calendar - calendar.php

## category ##

holds list of product categories - default listed loaded from foodhub.sql

there's no admin interface for categories at present - use mysqladmin to modify

## config ##

name, value pairs for hub specific configuration options

See install/foodhub.sql for default values

note: volunteer discount info is not used in v1.0

## credit ##

records receipts to members account

used by core\_functions.php receive\_payment()

either from PayPal callback or admin Receipts - credits.php

## distribution ##

not currently used - intended to support multiple distribution sites

v1.0 supports only a single distribution site and config.use\_distribution\_sites must be 0

## frequency ##

stores options for distribution interval (weekly, monthly etc)

intended to support members repeat orders but redundant in v1.0 as unreliable and confusing

## infopage ##

intended to store site info pages - very basic CMS functionality

not used as assume Joomla or other CMS is used to provide info pages of the site

## invoice ##

records members invoices when posted - post\_invoice.php

## markup ##

not currently used, could support different markup for different supplier groups

currently only the single markup value in config.markup is used

## member ##

holds details of consumer members - members should be created by the administrator once they've agreed to the hub terms

member\_account\_balance field holds the members current balance

each member should have a unique membership\_num (stroudco uses 001, 002...)

supplier\_id is a link to supplier such that this member may access the supplier control panel, multiple members may access a single supplier control panel

## messages ##

holds hub internal messages, used for members to communicate with the hub manager

added when admin was swamped with email and sometimes missed important messages

## orders ##

holds members orders, copied here from temp\_orders when payment is made. This may be from member account balance, admin receipts or paypal callback

core\_functions.php receive\_payment() processes temp\_orders into orders this up to the payment value

order\_current\_price is the net price (minus VAT and markup) finally paid for the product - this can change in adjust invoice

order\_paid\_price is the net price paid when the product was ordered

during post\_invoice order\_paid\_price and order\_current\_price are used to compute the new customer account balance - what they actually pay on the distribution day may be different to what they paid when ordering and their account\_balance must be adjusted

order\_date is the distribution day the order belongs to

order\_time is used to allocate limited produce on a first buy first served basis

order\_quantity\_requested is the quantity of this product the customer ordered

order\_quantity\_delivered is the quantity the customer recieved on distribution day

## product ##

stores product details
v1.0 doesn't use some fields and defaults product\_pkg\_count = 1, product\_case\_size = 1, product\_perishable = 0

## product\_calendar ##

holds product availability and special pricing for different distribution dates
the table is populated when new distribution dates are added, or new products with current availability are added, or when producer make date a specific changes from their control panel

quantity\_available holds limits to product quantity, 0 means unlimited

quantity\_ordered counts how many are currently ordered (stored in shopping baskets)

current\_price is net price for this date, may differ from the default price held in the product table

## recurring ##

handles recurring member orders - this is not used in v1.0

## supplier ##

supplier (or producer) information - added by administration when suppliers have signed the hub terms documents

## supplier\_accounts ##

records value of deliveries - distribution day payments to suppliers

## temp\_orders ##

tracks the members shopping baskets

when payment is received temp\_orders are moved into orders up to the value of the payment, some goods may be left in temp orders if insufficient funds are available

## volunteer ##

tracks volunteering time
not used in v1.0