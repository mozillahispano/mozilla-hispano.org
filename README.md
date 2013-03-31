# Entorno de desarrollo mozilla-hispano.org

## Licencia

    Mozilla Hispano site files
    All code is under GPL3:
    http://www.gnu.org/licenses/gpl-3.0.html
    The images are under Creative Commons BY-SA 3.0:
    http://creativecommons.org/licenses/by-sa/3.0/deed.es

# Requisitos

## Sistema

Para montar todo el entorno de desarrollo por cuenta propia, se necesita un
sistema operativo Linux o Mac OSX. Si dispones de Windows, deberías directamente
utilizar la máquina virtual con [Vagrant](#utilizando-vagrant).

> **NOTA:**
>
> Es **nuestra recomendación** que si vas a colaborar con Mozilla Hispano, **uses
> la máquina virtual de [Vagrant](#utilizando-vagrant)**, que nos asegura un
> entorno prolijo y consistente para todos los desarrolladores. Además, usando
> Vagrant no hay que configurar casi nada, ya que viene todo listo para dar manos
> a la obra en el codigo.

## Paquetes requeridos:

 * apache2
 * mysql-server
 * mysql-client
 * php5
 * php-mysql
 * phpmyadmin
 * vsftpd

# Utilizando Vagrant

* Instalar [Vagrant](http://www.vagrantup.com/)
* Agregar la box de vagrant al sistema:

```
$ vagrant add mhvm <path-al-mhvm.box>
```

* Inicializar la VM en el directorio con el repo

```
$ cd <path-al-codigo>
$ vagrant init
```

* Levantar la VM:

```
$ vagrant up
```

* Agregar la siguiente linea a /etc/hosts:

```
192.168.70.3 local.mozilla-hispano
```

* Listo, podemos navegar a [http://local.mozilla-hispano](http://local.mozilla-hispano)
  y ver el sitio funcionando.

* Si se necesita importar cosas con el WP Importer, darle permisos totales de
  escritura a wp-content:

```
chmod -R 777 wp-content/
```

# Instalación desde Cero

## Servicios Básicos

Primero que nada, hay que instalar y configurar Apache, PHP y MySQL.

### Montar un servidor virtual con Apache.

Editamos los host locales para trabajar mejor

    $ nano /etc/hosts

Añadimos

    127.0.0.1 local.mozilla-hispano

Primero debemos instalar ``apache2`` y ``mysql-server``:

**Linux**:

    $ sudo aptitude install apache2 mysql-server

**OSX**:

> Apache ya viene instalado, MySQL hay que instalarlo con los instaladores provistos
> por Oracle.

Una vez instalado MySQL hay que configurarlo con un usuario root. Los datos a
usar son los siguientes:

* Usuario: root
* Contraseña: toor

Luego deshabilitamos el sitio default:

**Linux**:

    $ a2dissite default
    $ cd /etc/apache2/sites-available/
    $ cp default mozilla-hispano

> Editamos el virtualhost...

    $ nano mozilla-hispano

> Poniendo el siguiente contenido

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
    </Directory>
</VirtualHost>
```
>**Nota:**
> _Sustituye `/home/usuario/mozilla-hispano.org` por la ruta donde hayas
> clonado el repositorio)_

**OSX**:

> Descomentar la linea `Include /private/etc/apache2/extra/httpd-vhosts.conf`

    $ sudo nano /etc/apache2/httpd.conf

> Editamos el virtualhost...

    $ sudo nano /etc/apache2/extra/httpd-vhosts.conf`

> Poniendo el siguiente contenido

```
    <VirtualHost *:80>
        ServerAdmin webmaster@localhost
        DocumentRoot "/Users/usuario/Sites/mozilla-hispano.org"
        ServerName local.mozilla-hispano
        ErrorLog "/private/var/log/apache2/mozilla-hispano-error_log"
        CustomLog "/private/var/log/apache2/mozilla-hispano-access_log" common
    </VirtualHost>
```
>**Nota:**
> _Sustituye `/Users/usuario/Sites/mozilla-hispano.org` por la ruta donde hayas
> clonado el repositorio)_

Activamos el nuevo sitio y reiniciamos Apache:

**Linux:**

    $ a2ensite mozilla-hispano
    $ service apache2 reload

**OSX**:

    $ sudo apachectl restart

Ahora hay que configurar el servidor de ftp para que funcionen ciertos plugins
de Wordpress.

**Linux:**
Seguir los pasos indicados [aqui](https://help.ubuntu.com/10.04/serverguide/ftp-server.html).
Configurarlo

**OSX**:
> Ir a "Compartir" en las preferencias del sistema. Tildar "permitir acceso FTP".

Listo, el apache debería estar accesible desde http://local.mozilla-hispano.
Ahora hay que proceder a instalar y configurar la instancia de Wordpress.

## Componentes de Mozilla-Hispano

### Clonar el repositorio e instalar Wordpress

    $ git clone https://github.com/mozillahispano/mozilla-hispano.org

### Instalar PHPMyAdmin

Por lo general instalarlo es muy simple, y requiere minima configuración.

**Linux**:

> Al instalar el paquete con `apt-get` ya se configura automáticamente. Se accede
> al mismo desde [http://local.mozilla-hispano/phpmyadmin](http://local.mozilla-hispano/phpmyadmin)

**OSX**:

> Seguir los pasos listados en la [web de phpMyAdmin](http://phpmyadmin.net)

### Instalar Wordpress

Primero hay que crar una base de datos y usuario para que utilice Wordpress, y
darle permisos totales sobre la BD que utilizará:

* Base de Datos: mh
* Usuario: mh
* Contraseña: mh

Esto se puede hacer facilmente ingresando como root desde phpmyadmin. Usar el
usuario y contraseña configurados como root cuando se instaló MySQL.

Descargar y descomprimir el codigo fuente de Wordpress

    $ wget http://wordpress.org/latest.zip
    $ unzip latest.zip
    $ mv wordpress/* .
    $ rm -rf wordpress latest.zip

Revisa [la documentación](http://codex.wordpress.org/es:Istalando_Wordpress#La_famosa_.C2.ABInstalaci.C3.B3n_de_5_minutos.C2.BB)
de Wordpress para finalizar la instalación. Una vez terminado, proceder a
configurar el usuario y password de FTP, requerido por ciertos plugins, poniendo
lo siguiente en wp-config.php, después de la linea que menciona `NONCE_SALT`:

    /* Store FTP Details */
    define("FTP_HOST", "127.0.0.1");
    define("FTP_USER", "<usuario>");
    define("FTP_PASS", "<password>");

Reemplazar `<usuario>` y `<password>` por los valores correspondientes al usuario
y password configurados para el FTP. Por lo general suele ser los datos de la
cuenta del usuario del sistema.

Instalar uno a uno los siguientes plugins:

* All in One SEO Pack
* Digg Digg
* FancyBox for WordPress
* FeedWordPress
* Fetch Feed shortcode pageable
* Google Social Analytics Extension
* Mozilla Persona
* Quick Cache
* Stealth Publish
* WP-Syntax
* WP Orbit Slider
* Yet Another Related Posts Plugin
* Wordpress Importer

> Los siguientes plugins deben ser instalados pero deshabilitados hasta que se
> indique lo contrario:
> * WP-Phpbb Last Topics
> * Wp2BB

Si alguno de los plugins falla, puede ser por falte de permisos totales de
escritura en wp-content. Agregarlos de la siguient manera.

```
chmod -R 777 wp-content/
```

Hay algunos plugins que han sido modificados para el sitio de mozilla hispano,
revisar el siguiente apartado que detalla cada caso.

Una vez listos lis plugins, importar las noticias base usando el Wordpress
Importer.

#### Cambios a plugins

> Pendiente

### Instalar phpBB

> Pendiente

Luego de instalar phpBB, proceder a habilitar los siguientes plugins en
Wordpress:
 * WP-Phpbb Last Topics
 * Wp2BB

### Instalar MediaWiki

> Pendiente

# Envía mejoras

Una vez que tengas tus mejoras implementadas, no olvides dar pull-request para que las integremos en la web.

Revisa la [documentación en el wiki](https://www.mozilla-hispano.org/documentacion/C%C3%B3mo_usar_el_repositorio_de_c%C3%B3digo#Github) sobre cómo crear un fork y enviar mejoras.