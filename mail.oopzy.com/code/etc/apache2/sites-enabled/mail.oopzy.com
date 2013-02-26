<VirtualHost  *:80>
  ServerName mail.oopzy.com
  
  RewriteLog "/var/log/rewrite.log"
  RewriteLogLevel 9
  
  ServerAdmin webmaster@this.com
  AllowEncodedSlashes On
  DirectoryIndex index.php index.html
  DocumentRoot /var/www/mail.oopzy.com
  Alias /assets/  /var/www/mail.oopzy.com/assets/
  <Directory "/var/www/mail.oopzy.com">
      Options All
      Options -Indexes
      AllowOverride All
      Order allow,deny
      Allow from all
    RewriteEngine on
  </Directory>
</VirtualHost>