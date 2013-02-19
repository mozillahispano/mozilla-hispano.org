mozilla-hispano.org
===================



Licencia
---------

Mozilla Hispano site files

All code is under GPL3:

http://www.gnu.org/licenses/gpl-3.0.html

The images are under Creative Commons BY-SA 3.0:

http://creativecommons.org/licenses/by-sa/3.0/deed.es




Instalación
-----------

###Clonar el repositorio e instalar Wordpress

`git clone https://github.com/mozillahispano/mozilla-hispano.org`

`wget http://wordpress.org/latest.zip`

`unzip wordpress-x.zip`

`cp wordpress/* mozilla-hispano.org/`



###Cómo montar un servidor virtual con Apache.

`# nano /etc/hosts`

añadir: 127.0.0.1 local.mozilla-hispano


####En Linux:

deshabilitamos el sitio default: `# a2dissite default`

`# cd /etc/apache2/sites-available/`

`# cp default mozilla-hispano`

`# nano mozilla-hispano`

        <VirtualHost *:80>
        
                ServerAdmin webmaster@localhost
        
                ServerName http://local.mozilla-hispano
    
                ServerAlias local.mozilla-hispano
     
                DocumentRoot /var/www/mozilla-hispano.org
      
                <Directory />
               
                        Options FollowSymLinks
              
                        AllowOverride None
     
                </Directory>
                
                <Directory /var/www/mozilla-hispano.org>
        
                ...
        
        </VirtualHost>

`# a2ensite mozilla-hispano`

`# service apache2 reload`



####En Mac:

`$ sudo nano /etc/apache2/httpd.conf`
descomentar la linea Include /private/etc/apache2/extra/httpd-vhosts.conf

`$ sudo nano /etc/apache2/extra/httpd-vhosts.conf`

        <VirtualHost *:80>

             ServerAdmin webmaster@localhost

              DocumentRoot "/Users/usuario/Sites/mozilla-hispano.org"
        
              ServerName local.mozilla-hispano
        
             ErrorLog "/private/var/log/apache2/mozilla-hispano-error_log"
        
             CustomLog "/private/var/log/apache2/mozilla-hispano-access_log" common

        </VirtualHost>

`$ sudo cp /etc/apache2/users/Guest.conf /etc/apache2/users/nombreusuario.conf`

`$ sudo apachectl restart`

