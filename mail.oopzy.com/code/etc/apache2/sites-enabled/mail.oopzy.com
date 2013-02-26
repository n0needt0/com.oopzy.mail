<VirtualHost  *:80>
  ServerName mail.oopzy.com
  ServerAdmin webmaster@this.com
  AllowEncodedSlashes On
  DirectoryIndex index.php index.html
  DocumentRoot /var/www/mail.oopzy.com
  Alias /assets/  /var/www/mail.oopzy.com/app/assets
  <Directory "/var/www/mail.oopzy.com">
      Options All
      AllowOverride All
      Order allow,deny
      Allow from all
    RewriteEngine on
  </Directory>
</VirtualHost>