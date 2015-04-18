# Introduction #

PLEASE NOTE THAT STROUDCO IS OFFERING THE SOFTWARE FREE OF CHARGE BUT CURRENTLY HAS NO RESOURCES TO PROVIDE TECHNICAL SUPPORT OR DEVELOPMENT OF THE SOFTWARE TO SUIT OTHER APPLICATIONS. However, we are looking for funding to run a training session for people taking on the Stroudco system. Please let us know if you are interested in this.

BEFORE USING THE SOFTWARE, PLEASE READ THE CASE STUDY (AVAILABLE AS A DOWNLOAD HERE) WHICH OFFERS SOME TIPS ON SETTING UP A FOOD HUB.

The food hub software will run stand alone but is usually run from within Joomla or other similar CMS to that provides the information pages on your website. Stroudco also runs phpbb forum software.

These instructions assume you know how to set up MySQL database and are familar with phpmyadmin or other MySQL administration interface. This is usually available from your server hosting control panel.

This information is also available in the distribution in install/readme.txt

# Installing Food Hub Software #


1. Upload all the files and folders in the distribution into a sub folder in your web servers home folder. For example install into /hub folder.

2. Change server permissions to 777 (read/write/execute for all) on the folders named images and templates\_c

3. Create a mysql database and user with full privileges. Keep note of username, database name and password - also server (usually localhost).

4. Insert ./install/foodhub.sql into your new database.

5. Copy config\_data.tmpl to config\_data.php and insert your database values and installation folder. You can leave the logo file and modify it later.

6. Point your browser to administration in the install directory (eg www.yourdomain.org.uk/hub/administration )

7. Log in to administration - the default password is password

8. Change the administration password by selecting Admin Password from the menu in the top right corner.

9. Modify Configuration settings (top right menu)

10. Set up calendar dates - these are the distribution days (add some test ones to start with).

11. Add a member (use one of your own email addresses as a test member)

12. Add a producer linked to the member you set up above

13. Log out from administration

14. Log in at www.yourdomain.org.uk/hub using the member details from 10. and add some test products.

15. Go to the member area and try ordering products for the next distribution day set in 10. above.

16. Before the administrator sends order emails to members or suppliers modify administration/member\_order\_email.html and administration/supplier\_order\_email.html with your own food hub details.