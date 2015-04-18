# Summary #

The food hub system differs from a conventional on-line shop in a number of fundamental ways. A normal e-shop allows orders to be placed and then places those orders with (possibly multiple) suppliers for direct delivery. The food hub holds on to orders until the ordering phase is completed and then places consolidated orders with suppliers for delivery to the hub distribution point on the next distribution day.

This is much better for small independent suppliers as they do not have to handle, or have the overheads associated with, multiple small orders. Suppliers can also specify limited availability and special pricing for different distribution days.

Reduced supplier overheads means reduced prices for the consumer whilst maintaining profit margins for the suppliers. The hub is funded by taking a small mark-up margin on produce sold. In the case of Stroudco this is 8%.

A vibrant food hub can help stimulate a thriving local food economy and encourage more supplies to sell locally providing more choice for consumers.

PLEASE NOTE THAT STROUDCO IS OFFERING THE SOFTWARE FREE OF CHARGE BUT CURRENTLY HAS NO RESOURCES TO PROVIDE TECHNICAL SUPPORT OR DEVELOPMENT OF THE SOFTWARE TO SUIT OTHER APPLICATIONS.  However, we are looking for funding to run a training session for people taking on the Stroudco system.  Please let us know if you are interested in this.

BEFORE USING THE SOFTWARE, PLEASE READ THE CASE STUDY (AVAILABLE AS A DOWNLOAD HERE) WHICH OFFERS SOME TIPS ON SETTING UP A FOOD HUB.  PLEASE ALSO DOWNLOAD, COMPLETE AND RETURN THE REGISTRATION FORM SO THAT WE CAN KEEP IN TOUCH WITH PEOPLE USING THE SYSTEM AND SHARE BEST PRACTICE.

Thanks and good luck!


# Fundamental Concepts #


## Distribution Day ##

Distribution days are defined by the hub manager. These are the days when the hub has a physical presence, for example Stroudco operates from a local primary school on Saturdays.

Orders are delivered to the hub during the morning, sorted by the hub manager and volunteers, and collected by consumers. Suppliers are paid immediately, on the day, to minimise the cash flow issues they often experience with wholesalers or other distribution channels.


## Order Cycle ##

Consumers can place orders up to the cut off date, which is typically 2 full days before the next distribution day. This gives time for the suppliers to prepare their orders for delivery to the hub distribution point. For Stroudco this is midnight on the Wednesday preceding the next distribution day.

Once one order period has closed then consumers accessing the site may place orders for the following distribution day (eg. 2 weeks time) even though the current one has not taken place.

After the order phase has closed (for Stroudco this is done on Thursday morning) the hub manager accesses the administration section and processes purchase orders (emailed to suppliers) and customer reminders (emailed to consumers).

On the distribution day suppliers delivery orders, these are checked and received by the hub manager and actual goods received are allocated to customers. Goods are sorted by customer ready for pick-up later in the day.


## Types of User ##

There are three types of user. Consumer members who purchase produce, suppliers (or producers) who provide produce for purchase and the hub manager (which may be one person or a shared role).

### Members ###

Consumer members have agreed to the hub membership terms, see www.stroudco.org.uk for example of these, and been added to the system by the hub manager.

Members may log in, order and pay for produce. They will pick up their orders from the distribution centre at the agreed date and time.

### Suppliers or Producers ###

The term supplier or producer is used interchangeably - these are the people or businesses that supply goods for the site.

There are supplier terms and conditions that must be agreed to and then the hub manager can create a member account (if one doesn't exist already) and a supplier account. The member account is linked to the supplier account so that member can access the supplier control panel when logged in.

When logged in to the hub suppliers may switch between their control panel where they specify goods for sale and their shopping basket where they can orders other goods if they wish to.

### Hub Manager or Administrator ###

Is the person or team that keeps the whole thing ticking over. They log in to the administrator section and can access the administrator pages to manage members and suppliers, configure the hub, create distribution days etc.

The hub manager adds member payments (Receipts) to the system when paid by cheque or bank transfer and this triggers automatic conversion of goods in shopping basket (temp\_orders table) into orders (orders) table.

They access the system the morning after an ordering period closes and process purchase orders which are emailed to suppliers. Members are also emailed reminders to collect their order from the distribution centre.

On the distribution day the hub manager receives supplier deliveries and logs them on the system. Sorts these into member orders for pick-up (typically with the help of volunteers), pays suppliers, modifies member invoices (if needed) and finally posts the invoices when all is complete.