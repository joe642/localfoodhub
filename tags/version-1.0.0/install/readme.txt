Installing Food Hub Software

Note: the food hub software will run stand alone but is usually run from within Joomla or other similar CMS to that provides the information pages on your website. Stroudco also runs phpbb forum software.

These instructions assume you know how to set up MySQL database and are familar with phpmyadmin or other MySQL administration interface. This is usually available from your server hosting control panel.

1. Upload all the files and folders in the distribution into a sub folder in your web servers home folder. For example install into /hub folder.

2. Change server permissions to 777 (read/write/execute for all) on the folders named images and templates_c

2. Create a mysql database and user with full privileges. Keep note of username, database name and password - also server (usually localhost).

3. Insert ./install/foodhub.sql into your new database.

4. Copy config_data.tmpl to config_data.php and insert your database values and installation folder. You can leave the logo file and modify it later.

5. Point your browser to administration in the install directory (eg www.yourdomain.org.uk/hub/administration )

6. Log in to administration - the default password is password  

7. Change the administration password by selecting Admin Password from the menu in the top right corner.

8. Modify Configuration settings (top right menu)

9. Set up calendar dates - these are the distribution days (add some test ones to start with).

10. Add a member (use one of your own email addresses as a test member)

11. Add a producer linked to the member you set up above

12. Log out from administration

13. Log in at www.yourdomain.org.uk/hub using the member details from 10. and add some test products.

14. Go to the member area and try ordering products for the next distribution day set in 9. above.

15. Before the administrator sends order emails to members or suppliers modify administration/member_order_email.html and administration/supplier_order_email.html with your own food hub details.

- good luck -
