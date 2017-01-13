# foscam-ip-camera-monitor
Web App for viewing Foscam ip camera ftp images, works with other cams that upload ftp images.

****Requires LAMP to be installed.

To begin reading FTP images simply fill out the config file located in:
CCTV/config.php

To setup config.php:
• Enter Database Constants
• Enter $directory name and path from root server
• Assign $group_ids to directory names ($directory) starting from integer 1
• Enter Admin Web Application User info

Once config is setup then simply run the php script /CCTV/login.php. 

To alleviate mass FTP Image buildup add cron jobs to run periodically on the server. 
This helps when you have motion detection triggered ftp images that build up over time and clog the server when you hit refresh on the web app.

Example:

/usr/bin/php /home/admin/web/example-domain.com/public_html/CCTV/cron.php > /dev/null 2>&1 

Any suggestions or bugs report to emre@ebdesigns.us
