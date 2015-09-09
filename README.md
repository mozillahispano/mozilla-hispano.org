[![Stories in Ready](https://badge.waffle.io/mozillahispano/mozilla-hispano.org.png?label=ready&title=Ready)](https://waffle.io/mozillahispano/mozilla-hispano.org)

# Colabora con Mozilla Hispano

Si estás interesado en formar parte del área de Labs de Mozilla Hispano, echa un vistazo a [nuestro foro](https://foro.mozilla-hispano.org/c/labs) para presentantarte y poder guiarte en los primeros pasos. ¡Te esperamos!

## Cómo empezar a desarrollar

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

Comienza a contribuir en MH eligiendo un issue. Para ello, consulta los issues en Github o en nuestra vista de Kanban en [Waffle](https://waffle.io/mozillahispano/mozilla-hispano.org), en donde los issues se encuentran ordenados por prioridad.

Una vez que tengas tus mejoras implementadas, no olvides enviar un pull request para que sean integradas.

Si tienes cualquier duda puedes contactar con otros desarrolladores de MH en nuestro canal de [Gitter](http://gitter.im/mozillahispano).

Revisa la [documentación en el wiki](https://www.mozilla-hispano.org/documentacion/C%C3%B3mo_usar_el_repositorio_de_c%C3%B3digo#Github) sobre cómo crear un fork y enviar mejoras.

## Licencia

    Mozilla Hispano site files
    All code is under GPL3:
    http://www.gnu.org/licenses/gpl-3.0.html
    The images are under Creative Commons BY-SA 3.0:
    http://creativecommons.org/licenses/by-sa/3.0/deed.es
