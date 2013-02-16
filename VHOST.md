Cómo montar un servidor virtual con Apache.

# nano /etc/hosts

añadir: 127.0.0.1 local.mozilla-hispano



En Linux:

deshabilitamos el sitio default: # a2dissite default
# cd /etc/apache2/sites-availeble/
# cp default mozilla-hispano
# nano mozilla-hispano









En Mac:

$ sudo nano /etc/apache2/httpd.conf
descomentar la linea Include /private/etc/apache2/extra/httpd-vhosts.conf

$ cd /etc/apache2/extra/httpd-vhosts.conf
$ sudo nano /etc/apache2/extra/httpd-vhosts.conf

<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot "/Users/usuario/Sites/mozilla-hispano.org"
    ServerName local.mozilla-hispano
    ErrorLog "/private/var/log/apache2/mozilla-hispano-error_log"
    CustomLog "/private/var/log/apache2/mozilla-hispano-access_log" common
</VirtualHost>

$ sudo cp /etc/apache2/users/Guest.conf /etc/apache2/users/nombreusuario.conf


