<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class controladoresController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content = '# Controladores

## Lanzador, moverse entre páginas
En Ocrend Framework, los controladores son la forma de crear nuevas "páginas" a las cuales se podrá acceder desde la URL.
```
  http://url.com/controlador/
```

Si hacemos eso, probablemente caemos en una página de **404** personalizada, sin embargo al colocar **home/** veremos la página principal, vamos a comprender esta llamada a los controladores. Se puede notar que la forma de moverse entre páginas es utilizando URL\'s Amigables, éstas vienen ya configuradas y aceptan por defecto tres parámetros, controller/method/id, los dos últimos los veremos luego.


Existe un lanzador que se encarga de accionar el controlador que se solicita por la URL a través de un enrutador.
```php
$Controller = $router->getController();

if(!is_readable(\'core/controllers/\' . $Controller . \'.php\')) {
  $Controller = \'errorController\';
}

require(\'core/controllers/\' . $Controller . \'.php\');
new $Controller;
```
En el código anterior, se obtiene el controlador actual desde el método getController() del enrutador, el cual detecta la existencia de palabras alfanuméricas en la URL con la estructura /palabra/ la cual tendrá el nombre del controlador a solicitar. Si este existe, se incluye y se instancia, si no existe instancia errorController, el controlador que se encarga de mostrar un error 404 personalizado.

Al entrar por defecto al sitio web, es decir www.url.com/, el enrutador automáticamente devuelve "homeController"

## La clase Controllers
La clase Controllers está ubicada en el kernel del framework, es decir **./core/kernel/Controllers.php**, al abrir el archivo veremos una documentación detallada acerca de cada elemento en él. Se puede apreciar a simple vista que es una clase abstracta, es decir, que no puede ser instanciada la clase Controllers, ésta existe solo para ser heredada por los controladores de nuestro sitio y distribuir de forma global funciones, propiedades y métodos entre todas las páginas (controladores) que tenga nuestra aplicación.

A continuación se explica en detalle cada propiedad de la clase Controllers

## $this->template
La propiedad template, es el encargado de dar acceso, configurar e invocar las vistas en todos los controladores  de la aplicación. Puede ser una instancia de el motor de templates **Plates PHP* ó de **TWIG**, esto dependerá de la configuración que tengamos en **config.php** en la constante **USE_TWIG_TEMPLATE_ENGINE**. Cuando esta constante sea **true**, esta propiedad será una instancia de Twig, cuando sea **false** será de Plates.
```php
  #\'templates/twig/\' es el directorio por defecto en el que se tendrán las vistas de todo el sistema
  if(USE_TWIG_TEMPLATE_ENGINE) {
      $this->template = new Twig_Environment(new Twig_Loader_Filesystem(\'./templates/twig/\'), array(
          # ruta donde se guardan los archivos compilados
          \'cache\' => \'./templates/twig/.cache/\',
           # false para caché estricto, cero actualizaciones, recomendado para páginas 100% estáticas
          \'auto_reload\' => true,
          # en true, las plantillas generadas tienen un método __toString() para mostrar los nodos generados
          \'debug\' => DEBUG
      ));
      #....... code
    }

    else {
      #\'templates/plates/\' es el directorio por defecto en el que se tendrán las vistas de todo el sistema
      $this->template = new League\Plates\Engine(\'templates/plates\',\'phtml\');
      #....... code
    }
```

## $this->route
La propiedad route, nos permite acceder a los métodos setRoute y getRoute para controlar rutas de URL Amigables de forma global o individuales para nuestros controladores, para ver una documentación detalla y el uso de esta propiedad se recomienda directamente la lectura sobre las [Rutas](http://framework.ocrend.com/rutas/)

## $this->isset_id
La propiedad isset_id es un valor booleano que nos permitirá tener control en todo momento sobre la variable **ID** que podamos utilizar para diversas situaciones, será true cuando esta variable sea numérica, mayor o igual a 1 y esté definida en la URL amigable. La utilidad de la propiedad es para cuando necesitemos por ejemplo, mostrar el perfil de un usuario, el proceso sería algo como **perfil/ver/id** donde **perfil** sería el nombre del controlador, **ver** sería el nombre de la acción/método, e **id** sería la variable que en este caso contendría el id asociado en la base de datos del usuario, nos es útil saber que si alguien está visitando esa página, esté definido el ID, que sea un número y lógicamente mayor o igual a 1.

```php
if($this->isset_id) {
 /*
   Llamar al método del modelo, ya que si se llama en este punto se tiene seguridad de que el ID
   está definido y está en los parámetros correctos como para que entre a la base de datos,
   se obtiene información y se manda a la vista
  */
} else {
  Func::redir();
  /*
   Si entra aquí, es porque algún usuario está intentando entrar a /perfil/ver/algo
   ó /perfil/ver/-1 ó /perfil/ver/ ó /perfil/ ciertamente este comportamiento no estaría permitido,
   por lo que redireccionamos al usuario a la página principal con la función redir()
  */
}
```

## $this->method
La propiedad Method, existe para controlar las sub páginas de un controlador, tomemos como ejemplo la gestión de usuarios en un panel administrativo.

Tendríamos que realizar un módulo para agregar un usuario, otro para editar un usuario, otro para eliminar un usuario y otro para listar todos los usuarios, ¿habría necesidad de crear un controlador para cada una? pues no, veamos el ejemplo concreto:
```php
switch ($this->method) {
      case \'add\':
        #Se invoca al método del modelo asociado que se encarga de agregar un usuario
        #Se invoca al método SOLO si asumimos que NO usaremos AJAX + API REST
        #Se invoca a la vista asociada a crear un usuario
      break;
      case \'edit\':
        #Se invoca al método del modelo asociado que se encarga de agregar un usuario
        if($this->isset_id) {
          /*
          Además, como aquí editamos a un usuario, debemos pasar el id del usuario a editar
          Por lo que necesitamos usar isset_id para asegurarnos de que manejamos una ID dentro de los parámetros correctos
          Todo esto asuminedo que NO usaremos AJAX + API REST
          */
          #Se invoca a la vista asociada a editar un usuario
        } else {
          Func::redir();
        }
      break;
      case \'del\':
        #Se invoca al método del modelo asociado que se encarga de eliminar un usuario
        #¿Tenemos que utilizar isset_id otra vez? No, cuando veamos los Modelos entendermos porque aquí no se necesita.
      break;
      default:
        #Se invoca al método asociado al modelo que se encarga de extraer todos los usuarios de la base de datos
        #Se invoca a la vista asociada a mostrar todos los usuarios
      break;
    }
```
Así, podemos movernos entre las sub páginas de la siguiente manera, asumiendo que el controlador es "usuarios":
```
 /usuarios/add #entraría en el case \'add\'
 /usuarios/edit/1  #entraría en el case \'edit\' para editar al usuario de id 1
 /usuarios/del/1  #entraría en el case \'del\' para eliminar al usuario de id 1
 /usuarios #al no estar definido \'method\', se va al default
```

## $this->session

Contiene una instancia del modelo [Sessions](http://framework.ocrend.com/sesiones/).

## Creando un Controlador
Sabiendo lo que nos ofrece la clase Controllers, crearemos nuestro primer controlador básico.

Los controladores de toda la aplicación están en **./core/controllers/** y el nombre de estos debe ser igual al nombre de la clase que contiene el archivo, ejemplo **micontroladorController.php**:
```php
class micontroladorController extends Controllers {

  public function __construct() {
    parent::__construct();
    echo $this->template->render(\'micontrolador/micontrolador\');
  }

}
```
**micontrolador**Controller, lo marcado en negrillas sería el nombre que deberíamos pasar por la URL para acceder a éste, por ejemplo al entrar en http://url.com/micontrolador/ accederíamos a nuestro primer controlador. Sin embargo, nos toparíamos seguramente con un error en pantalla arrojado por PlatesPHP, indicando que no consigue la vista que queremos compilar.

¡Entonces hay que crear una vista!, las vistas se crean en **./templates/twig/** o **./templates/plates/**, depende del motor de plantilla que usemos, así que crearemos la carpeta **micontrolador/** dentro de **./templates/twig/** o **./templates/plates/**, y dentro de **./templates/twig/micontrolador/** ó **./templates/plates/micontrolador/ debemos crear el archivo de la vista **micontrolador.twig** ó en caso de plates, **micontrolador.phtml**, ahora colocamos el texto que queramos en el, por ejemplo:
```php
 # Si es una plantilla de plates:
 Hola este es mi vista en el controlador y la fecha de hoy es <?= Func::fecha(\'l d-F-Y\') ?>
 # Si es una plantilla de twig
 Hola este es mi vista en el controlador y la fecha de hoy es {{ fecha(\'l d-F-Y\')}}
```

Y ya tendríamos listo nuestro primer controlador más básico.

## parent::__construct();
La llamada del constructor a la clase padre, es decir a __construct() es obligatoria, puesto que si no hacemos esto no tendríamos acceso a ningún método o propiedad escrito en la clase Controllers.

Este constructor (el de la clase padre **Controllers**) acepta dos parámetros opcionales **booleanos**.

El **primer parámetro** es útil si quisiéramos que nuestro nuevo controlador **micontrolador** fuese una página exclusiva para usuarios registrados y que estén logeados al momento de entrar en el controlador, bastaría con pasar el parámetro true al constructor de la clase padre, es decir
```php
parent::__construct(true);
```
Así automáticamente, cuando un usuario acceda a la página y no esté logeado, será redireccionado al home, este parámetro por defecto es **false**

El **segundo parámetro** es útil si quisiéramos que nuestro nuevo controlador **micontrolador** fuese una página exclusiva para usuarios **NO** logeados al momento de entrar en el controlador, bastaría con pasar el parámetro true al constructor de la clase padre, es decir
```php
# Colocamos false al primer parámetro, para que no se genere un error lógico
parent::__construct(false,true);
```
Un ejemplo claro de esto, es en el controlador **lostpassController**, dicho controlador sólo puede ser visto desde fuera de una cuenta, si iniciamos sesión e intentamos acceder al controlador, no podremos entrar, nos redireccionará al home. Sin embargo, si intentamos entrar al controlador sin una sesión iniciada, podemos ver perfectamente el controlador.

Recordar que por la lógica escrita en el controlador **lostpass**, una URL válida de acceso a este sería
```
http://url.com/lostpass/cambiar/1466721099
```
Si no se accede con esa estructura, nos redireccionará al index esté o no la sesión iniciada.

## Utilizando el generador de código PHP
El generador de código PHP del framework, es un sencillo script escrito en PHP para consola que se encarga de generar archivos base por nosotros. Vamos a crear un controlador con una vista utilizando el generador.

Vamos a nuestra consola sea Windows/Mac/Linux:
```
cd /directorio/en/donde/esta/nuestro/framework/
php gen.php cv Hola
```

Luego de esto ya estaría creado y perfectamente configurado un controlador y una vista (la cual se creará como .twig o .phtml dependiendo de como está configurado el framework) para ese controlador, así que si accedemos a http://url.com/hola/ veremos el controlador perfectamente creado, podemos revisar en **./core/controllers/** y veremos un archivo llamado holaController.php con el contenido programado, igual pasará con la vista que fue generada en **./templates/twig/hola/hola.twig** ó **./templates/plates/hola/hola.pthml**';
    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
