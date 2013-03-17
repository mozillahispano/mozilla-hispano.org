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

    $ git clone https://github.com/mozillahispano/mozilla-hispano.org`

    $ wget http://wordpress.org/latest.zip`

    $ unzip wordpress-x.zip`

    $ cp wordpress/* mozilla-hispano.org/`

###Montar un servidor virtual con Apache.

Editamos los host locales para trabajar mejor

    $ nano /etc/hosts

Añadimos

```127.0.0.1 local.mozilla-hispano```


####En Linux

Debemos tener instalado ``apache2`` y ``mysql-server``, en debian, ubuntu y similares:

    $ sudo aptitude install apache2 mysql-server

Deshabilitamos el sitio default:

    $ a2dissite default
    $ cd /etc/apache2/sites-available/
    $ cp default mozilla-hispano

Editamos el virtualhost

    $ nano mozilla-hispano

```
<VirtualHost *:80>
        
    ServerAdmin webmaster@localhost
    ServerName http://local.mozilla-hispano
    ServerAlias local.mozilla-hispano
    DocumentRoot /home/usuario/mozilla-hispano.org
      
    <Directory />
        Options FollowSymLinks
        AllowOverride None
    </Directory>
                
    <Directory /home/usuario/mozilla-hispano.org>
        Options -Indexes FollowSymLinks
	AllowOverride All
	Order allow,deny
	allow from all
    </VirtualHost>
```
(Sustituye ``/home/usuario/mozilla-hispano.org`` por la ruta donde hayas clonado el repositorio)

Lo activamos y reiniciamos Apache

    $ a2ensite mozilla-hispano
    $ service apache2 reload
    
La web debería estar accesible desde http://local.mozilla-hispano

####En Mac:

    $ sudo nano /etc/apache2/httpd.conf

Descomentar la linea ``Include /private/etc/apache2/extra/httpd-vhosts.conf``

    $ sudo nano /etc/apache2/extra/httpd-vhosts.conf`

```
    <VirtualHost *:80>
        ServerAdmin webmaster@localhost
        DocumentRoot "/Users/usuario/Sites/mozilla-hispano.org"
        ServerName local.mozilla-hispano
        ErrorLog "/private/var/log/apache2/mozilla-hispano-error_log"
        CustomLog "/private/var/log/apache2/mozilla-hispano-access_log" common
    </VirtualHost>
```

(Sustituye ``/Users/usuario/Sites/mozilla-hispano.org`` por la ruta donde hayas clonado el repositorio)

Copiamos la configuración y reiniciamos Apache

    $ sudo cp /etc/apache2/users/Guest.conf /etc/apache2/users/nombreusuario.conf`
    $ sudo apachectl restart`

La web debería estar accesible desde http://local.mozilla-hispano