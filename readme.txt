# q2apro Premium Plugin: Best-Users-Pro

    "You want to motivate your community? You want to create a competition between your users every month? Well, you found the right plugin! By using the Best-Users-Pro plugin you push your users to be more active dramatically."

	
# Description

	Based on the q2a point system, user scores get saved per month/year and rewards can be granted to your winning users. Highly motivating. Provides a widget and two separate pages.


# Features

    - easy to setup and easy to use for all your users
	- displays a widget with best users of the current month
	- set the number of the users to be displayed in the widget
    - lightweight plugin that keeps your server fast
	  - all user scores in the widget are completely cached
      - all userscores are only updated on userpoint events
    - no cronjob necessary to store the user scores each month
	- provides a page for best users per month and per year
    - datepicker implemented for better usability
	- abbreviations of the month can be changed to your language
    - rewards can be enabled/disabled
	- set your own rewards in the plugin options
    - number of top users per month / per year can be set
    - option to exlude admin from the best user listing
    - permission to access month and year page
    - available languages: en, de


# Notes

	- Before installing the plugin, please think about your point system (>Admin >Points). The points should not change over time, as the plugin stores recent userpoints every month. If you change userpoints later on, the userscores shown for the recent month could be incorrect (or even negative).
	- When the plugin got installed, the widget will be empty. Wait for the first score event (question, answer, vote) and you will see the users listed in the widget.


# Installation

    - Buy the plugin and download the ZIP file provided q2apro-best-users-pro.zip
    - Make a full backup of your q2a database before installing the plugin.
    - Extract the folder q2apro-best-users-pro from the ZIP file.
    - Move the folder q2apro-best-users-pro to the qa-plugin folder of your Q2A installation.
    - Use your FTP-Client to upload the folder q2apro-best-users-pro into the qa-plugin folder of your server.
    - Navigate to your site, go to Admin -> Plugins and check if the plugin "Best Users PRO" is listed.
    - Change the plugin options to meet your needs. Hit "Save Changes".
    - Congratulations, your new plugin has been activated!

   
# Disclaimer

	This code is in use in many q2a forums and has been tested thoroughly. However, please make a full MySQL backup of your data before installing this plugin in production environments. There could be, for instance, another plugin that interferes with this plugin. We cannot accept any claim for compensation if data is lost.


# Copyright

	Copyright © q2apro.com - All rights reserved
	A redistribution of this code is not permitted.
