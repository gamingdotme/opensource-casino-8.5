# opensource-casino
Open source slots casino script  (formely-Goldsvet)

This is a laravel casino app, you need to download game packs for it.

You can also join our discord at https://discord.gg/HscTu67FSu and access downloads for games.
Games work for both versions. (up to v9 so far).

Do not forget to download 
https://drive.google.com/file/d/1bbRD74BL-f2MOAG4LrBCKlwsYK6qteMj/view
And add it to your : storage/app/GeoIP2-City_20201006/  folder 
The setup assumes regular Laravel setup, with casino folder setup outside your www ( or change index )

THIS DOCUMENT SHOWS A SETUP SAMPLE ON A CPANEL SERVER, AND CAN BE REPLICATED ON OTHER SETUPS. 

Setup your server with Apache, mysql, php 7.1-4, composer, nodejs16 & PM2 
Force Domain SSL 
Generate SSL CRT KEY & BUNDLE COPY THE CONTENTS OF YOUR CRT/KEY/BUNDLE TO FILES IN FOLDER 
CASINO/PTWEBSOCKET/SSL/ Create a new email & password 

Create a new database Grant all access Import the SQL file located in folder CASINO/DATABASE/MIGRATIONS/betshopme_8.sql via PHPMYADMIN to the database extra DB file not required (experimentalarcadegames.sql) unless you are experimenting with arcade games Zip File Uploads Casino.zip and public_html.zip should be unzipped in the following manner public_html → this is your public directory casino → this goes outside your public folder for security so it becomes YOUR ROOT FOLDER /casino /public_html If you decide to move your casino folder INSIDE public_html You have to modify two things 1: open index.php inside public_html and replace all folder paths ( ./../casino portion to ./casino/ ) 2: MUST configure .htaccess to deny .env files or all dot files ( google dot files protection via htaccess) //**** extra tip since it contains demo user accounts Generate new password hash for existing users and run this in phpmyadmin (replace hash) https://bcrypt-generator.com/ If you need to has a new word. Example : (run this in phpmyadmin) UPDATE w_users SET `password` = '$2a$12$s1RpwEx/oTL3vYQGZjC33eBHECRJb7gkjmAk9Tmyefub7gQ4nh8XS';

This has makes all users have password : Test123 *********/// 
SSL SPECIFIC INSTRUCTIONS Delete self signed if any Generate or install the Lets Encrypt one if you have it Save text file via notepad or direct 
Certificate: (CRT) ==> crt.crt Private Key (KEY) --> key.key Certificate Authority Bundle: (CABUNDLE) ==> intermediate.pem 
Go in Folder casino/PTWebSocket/ssl and replace those 3 files --------------- FILE EDITS casino/.env EDIT LINES for domain, database and user/password, email and password EDIT casino/config/app.php (URL line 65 ) EDIT casino/public/* ALL SOCKET FILES CHANGE YOUR DOMAIN NAMEIF YOU NEED TO CHANGE PORTS YOU CAN DO SO HERE AS WELLGames downloads Find download packages at https://discord.gg/QfUJhzFsju Currently ~ 1000 games – 40 GB total. Go to /home/USERNAME/public_html/ Password is : password Download the core + 3 game packs Unpack (Tip : upload one zip, unpack with:: 7z x -ppassword file.zip ) ---
PM2 COMMANDS FROM INSIDE https://pm2.keymetrics.io/docs/usage/quick-start/ PTWEBSOCKET COMMANDS + pm2 start Arcade.js --watch pm2 start Server.js --watch pm2 start Slots.js --watch OR if you tested before and not expecting errors, all in one command : pm2 start Arcade.js --watch && pm2 start Server.js --watch && pm2 start Slots.js –watch SAMPLE USEFUL COMMANDS pm2 stop all pm2 delete all pm2 flush pm2 logs all commands on https://pm2.keymetrics.io/docs/usage/quick-start/ extra tool can be used called wscat (install via ssh)wscat -c "wss://domain:PORT/slots' <--- as an example to make sure you get connected msgOpen ports in Firewall 22154 22188 22197 (or whatever you set your Socket file ports to) Run site :: it should work now if everything was setup correctly. Visual Edits : To change Sliders Text, and footer notes / terms and conditions. Edit : \casino\resources\lang\en\app.php Lines 1255 ++ include text in frontpage Sliders are in root folder /woocasino/ for easy access and change (slider1,2,3,4,5 and for mobile mslider1,2,3,4,5)

Minor troubleshooting if your composer/artisan not ran correctly

php artisan cache:clear && php artisan view:clear && php artisan config:clear && php artisan event:clear && php artisan route:clear

URL TROUBLESHOOTING 404 ERROR
MAKE SURE YOUR HTACCESS WAS GENERATED, CORRECTLY AND DID HAVE

<IfModule mod_rewrite.c>
<IfModule mod_negotiation.c>
Options -MultiViews -Indexes
</IfModule>
RewriteEngine On
# Handle Authorization Header
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
# Redirect Trailing Slashes If Not A Folder...
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} (.+)/$
RewriteRule ^ %1 [L,R=301]
# Handle Front Controller...
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
</IfModule>
<IfModule mod_headers.c>
Header set Access-Control-Allow-Origin "*"
</IfModule>
