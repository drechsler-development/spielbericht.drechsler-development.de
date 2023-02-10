# Scoresheet-Generator (Volleyball)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Features

- Create/modify your team- and players
- Add/Modify location and time
- Add a date of the game
- Select your opponent team- and players
- Generate your scoresheet (report) as PDF

## Installation & loading

- Download the latest version and copy the files onto a webserver (either a shared hosting webserver or your local webserver like xampp or lampp)
- Point your DOCUMENT ROOT to the /public folder (example below shows the virtual host file for xampp on a localhost. This will later be available via browser http://localhost)

```apacheconf
<VirtualHost *:80>

  DocumentRoot "C:\xampp\htdocs\spielberichtsgenerator\public"
  ServerName localhost

  <Directory "C:\xampp\htdocs\spielberichtsgenerator\public">
  Require all granted
  AllowOverride All
  </Directory>

</VirtualHost>
```
- replace the background image (scoresheet.jpg) in the /public/img folder with your own image (keep the name! and extension!)
- it might be possible that you need to adjust the positions of the fields regarding your positions in the image (you need to play a bit and test that multiple times like I did :). 
- The positions can be changed in the createReportSheet.php file (line 150-163)
- adjust the gap (if needed) between the fields in the createReportSheet.php file (line 164)
- Create a MySQL database (or use existing one) and import (run) the SQL script (install.sql) to create the appropriate tables
- add the appropriate values in the _domaincontroller_default.php_ file and rename it to _domaincontroller.php_ only
- open the application in your browser and login with the default credentials (admin/admin)
- change the password for the admin user
- create at least two teams and their players
- create your first scoresheet
