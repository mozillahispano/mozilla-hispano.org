[![Stories in Ready](https://badge.waffle.io/mozillahispano/mozilla-hispano.org.png?label=ready&title=Ready)](https://waffle.io/mozillahispano/mozilla-hispano.org)

# Cómo empezar a desarrollar

La forma más fácil de comenzar es mediante la máquina virtual de Vagrant. Al descargarla, podrás comenzar a trabajar sin necesidad de instalar o configurar dependencias.

La máquina incluye actualmente Wordress 3.9.2, el theme de Wordpress de este repositorio y los [plugins](https://github.com/mozillahispano/wp-plugins) correspondientes, además de una base de datos de prueba.

### Pasos a seguir

1. Instala [Git](http://git-scm.com/downloads)
2. Instala [Vagrant](https://www.vagrantup.com/)
3. Instala [Virtual Box](https://www.virtualbox.org/wiki/Downloads)
4. Descarga la [máquina Vagrant de MH](https://box.mozilla-hispano.org/public.php?service=files&t=52e488276ef65eafbdeb8e75185af6d8)
5. Extrae el contenido y entra al directorio
6. Ejecuta ``vagrant up`` para arrancar la máquina

Una vez arrancada la máquina puedes entrar en ella utilizando el comando ``vagrant ssh``.

*Nota: Puedes modificar el archivo ``Vagrantfile`` para indicar la IP en la que se arrancará la máquina.*


## Envía mejoras

Una vez que tengas tus mejoras implementadas, no olvides dar pull-request para que las integremos en la web.

Revisa la [documentación en el wiki](https://www.mozilla-hispano.org/documentacion/C%C3%B3mo_usar_el_repositorio_de_c%C3%B3digo#Github) sobre cómo crear un fork y enviar mejoras.

## Licencia

    Mozilla Hispano site files
    All code is under GPL3:
    http://www.gnu.org/licenses/gpl-3.0.html
    The images are under Creative Commons BY-SA 3.0:
    http://creativecommons.org/licenses/by-sa/3.0/deed.es
