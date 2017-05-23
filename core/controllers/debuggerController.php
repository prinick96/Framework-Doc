<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class debuggerController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content = '# Debug

El debug es una herramienta de depuración para estar al tanto de la información que manejamos al desarrollar. Por defecto, viene desactivado pero podemos activarlo desde el fichero **./core/config.php**, incorpora [Kint](https://github.com/raveren/kint)
```php
# Activación del DEBUG, solo para desarrollo
define(\'DEBUG\', false); # Colocar como true para activarlo
```

Ofrece información acerca de:
* Archivo ejecutando actualmente.
* Versión de PHP
* Versión del Framework
* Controlador ejecutando actualmente.
* Variables de sesión existentes
* Variables **$_POST** enviadas
* Variables **$_GET** enviadas (Las URL amigables no se envían por GET)
* Variables **$_FILES** enviadas
* Querys cargadas en la página
* Host al que se intenta conectar con la base de datos
* Base de datos a la que se intenta conectar por defecto
* Estado del firewall
* Tiempo de ejecución de cada página
* Memoria RAM consumida por cada usuario en el servidor

Los últimos dos, no toman en cuenta el Debug.


Adicional a esto, cuando el modo debug está activo, todas las peticiones AJAX revelan en la consola de JavaScript (**F12**) el contenido recibido. Es ideal para detectar errores en PHP desde la consola de javascript cuando usamos AJAX.


Con el debug activo, probar iniciar sesión para ver la muestra del json recibido por Login.php

## d()

Es un alias de **var_dump()** que muestra muchísima más información y de una forma muy entendible para el programador. En vez de usar var_dump() para analizar internamente un elemento, podemos usar simplemente **d()**, esté encendido o apagado el Debug.

```php
d(array(
 \'Hola\' => \'Mundo\'
));

# Un alias es:
Kint::dump(...);
 ```

Hay un par de modificadores en tiempo real que se pueden utilizar:
 * ~d($var) esta llamada devuelve una salida en texto plano, útil para usarlo en algún return;
 * +d($var) no toma en cuenta niveles de profundidad para mostrar información (esto puede colgar el navegador si la cantidad de información es demasiado grande)
 * !d($var) mostrará una salida más amplia y rica.
 * -d($var) utilizará el ob_clean() en la salida anterior por lo que si estás dejando algo dentro de un código HTML, puedes observar la salida del debug por Kint.
 * Se puede combinar los modificadores, ~+d($var)

## ddd()

Es un alias de d() pero detiene la ejecución de todo el script, es equivalente a:
```php
d($mivariable);
exit;
```';
    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
