<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class apirestController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content = '# API REST

La API REST que incluye Ocrend Framework configurada utiliza [Slimframework 3](http://www.slimframework.com/), el objetivo de esta es doble, permitir en todo momento crear una muy completa API REST para servir datos o permitir entrada de datos desde fuentes externas a nuestra aplicación, como podría ser widgets que creemos para otros sitios webs, aplicaciones móviles que necesiten obtener información de nuestra base de datos, o insertarla. El segundo objetivo es utilizarla como medio controlador para manejar de forma eficiente **todas** las peticiones AJAX que necesitemos en nuestra aplicación.

## Crear petición ajax a la API REST

En Ocrend Framework, todas las peticiones ajaxs **deben** ser realizadas mediante la api rest, de esta manera estaremos seguro de que las conexiones serán eficientes y seguras, ya que el Firewall interviene en el proceso. Analicemos un poco la estructura de nuestra api rest, vayamos a **./api/index.php** este fichero se encarga de cargar el núcleo e incluir los archivos de las peticiones HTTP para tenerlas separadas y en orden.
```php
define(\'INDEX_DIR\',true);
require(\'../core/api_core.php\');
....
if($_GET) {
  include(\'http/get.php\');
}
if($_POST) {
  include(\'http/post.php\');
}
include(\'http/delete.php\');
include(\'http/put.php\');
include(\'http/map.php\');
```

Lo primero que podemos notar, es que es llamado al archivo api_core.php que se encuentra en **./core/** este cumple la misma función que app_core.php, sólo que configurado para la API REST, no hay mayor cuestión que ver en ese archivo, puesto que allí también se llama a **config.php** y automáticamente se comparte toda la información también con la API REST.


Lo otro que podemos ver es que hay varios **include()**, los cuales incluyen archivos de la carpeta **./api/http/**, cada uno contiene peticiones las cuales hacen referencia con su nomnbre. Por ejemplo si entramos a **post.php**, veremos que allí se establecen unas peticiones para Login, Reigstro y Lostpass, más adelante explicaremos el contenido de este archivo y los demás.


Se explicará de la forma más sencilla posible, lo primero que hay que tener en cuenta, es que POST, GET, PUT y DELETE son otros métodos para solicitar/transferir información vía HTTP, por lo que en el fichero **post.php** tendremos el control de todas las peticiones POST, en **get.php** todas las peticiones GET, en  **put.php** todas las peticiones PUT y en **delete.php** todas las peticiones DELETE. El fichero **map.php** contiene peticiones híbridas, que pueden ser leídas a través del método GET o el método POST.


Hagamos una primera prueba, utilizando map en este caso. Vayamos a **./api/http/map.php** y añadimos:
```php
$app->map([\'GET\', \'POST\'], \'/hola\',function($request, $response){
  $response->withJson(array(\'hola\' => \'mundo\'));
});
```

Entonces ahora, vayamos a nuestra aplicación, desde el navegador y escribamos algo como http://url.com/api/hola

Enseguida entonces, veremos que tenemos un json:
```javascript
{"hola":"mundo"}
```
> **json** es simplemente un formato ligero para el intercambio de datos entre dos aplicaciones, que necesariamente no tienen que ser del mismo lenguaje.

Ahora, hay que ser claros en algo. Al acceder mediante el browser a http://url.com/api/hola estamos generando una petición GET, ahora, entonces esto debería estar en get.php no? **borremos lo que hemos escrito en map.php** y coloquemos esto en **./api/http/get.php**
```php
$app->get(\'/hola\',function($request, $response){
  $response->withJson(array(\'hola\' => \'mundo\'));
});
```
Probemos con ir a http://url.com/api/hola y veremos que esta vez no tenemos ninguna respuesta, sino que nos dice **Page Not Found**, esto sucede porque si ven en **index.php**, el archivo get.php sólamente es llamado cuando realizamos una petición GET real, esto se hace así para aumentar un poquito más la seguridad, y que simplemente con alguien entrar a esa ruta desde el navegador no pueda ver nada.

```php
# Si eliminamos
if($_GET) {
  include(\'http/get.php\');
}

# Y reemplazamos por
include(\'http/get.php\');
```

Vayamos a http://url.com/api/hola, y veremos que ya sale el resultado.
> Noten que http://url.com/api/hola no es lo mismo que http://url.com/api/hola/

Ahora, devolvamos el if que habíamos removido. Y hagamos un ejemplo práctico de todo esto, veremos que es extremadamente sencillo.

Ocupemos el generador de código
```shell
python gen.py mvca:get Ejemplo
```

Ahora vayamos a http://url.com/ejemplo veremos un formulario, al darle "Enviar", nos devolverá un mensaje que dice "Funcionando", estudiemos que pasó y cómo está funcionando esto.


Primero abrimos **./api/http/get.php** (Ya que hemos escrito a:get, si hubiésemos escrito a:post en la consola, se hubiese generado una petición POST entonces deberíamos abrir post.php)
```php
$app->get(\'/ejemplo\',function($request, $response) {

	$model = new Ejemplo;
	$response->withJson($model->Foo($_GET));

	return $response;
});
```

Pues allí, estamos llamando al modelo "Ejemplo", y estamos dando una respuesta en pantalla con **$response->withJson()**, este método de $response, nos devuelve un JSON, y recibe como parámetro un array, el cual convertirá en Json.

Así que si ahora abrimos el modelo **Ejemplo.php** veremos que dentro de el está el método Foo() el cual recibe un parámetro, que es un array, en este caso ese parámetro es la variable global $_GET.
```php
# Busquemos
final public function Foo(array $data) : array {
    # ...
    return array(\'success\' => 0, \'message\' => \'funcionando\');
  }

# Reemplacemos eso por esto
final public function Foo(array $data) : array {
    # ...
    return array(\'success\' => 0, \'message\' => $data[\'ejemplo\']);
  }
```
Luego de reemplazar, escribamos algo en el input que está en http://url.com/ejemplo y volvamos a darle a Enviar, veremos que obtenemos como respuesta lo que estamos escribiendo por el input en tiempo real.


Sigamos explicando, vayamos a la vista **ejemplo.phtml**
```markup
<form id="ejemplo_form" role="form"> <!-- ID A -->
      <div class="alert hide" id="ajax_ejemplo"></div> <!-- ID B -->
      <div class="form-group">
        <label class="cole">Ejemplo:</label>
        <input type="text" class="form-control form-input" name="ejemplo" placeholder="Escribe algo..." />
     </div>
     <div class="form-group">
       <button type="button" id="ejemplo" class="btn red  btn-block">Enviar</button> <!-- ID C -->
    </div>
</form>
```
He especificado en comentarios del código del formulario, los tres ID importantes para que esto funcione.

Vayamos a **./views/app/js/ejemplo.js** y veamos las siguientes líneas en ese código javascript hecho con jquery:
```javascript
// La primera línea es una reacción al evento CLICK sobre el botón con id="ejemplo", marcado como -- ID C --
$(\'#ejemplo\').click(function(){

// Cuando se hace clic en "ejemplo", se hacen unas pequeñas gestiones en el código que controlan las alertas en donde sale el  texto de la respuesta

// Entonces se hace la petición ajax
 $.ajax({
    type : "GET", // Se puede apreciar que usamos el método GET
    url : "api/ejemplo", // Vemos que, queremos enviar / recibir información de api/ejemplo
    // Vemos que la data que enviamos es básicamente todo lo que esté  en el formulario de id="ejemplo_form" -- ID A --
    data : $(\'#ejemplo_form\').serialize()
```
En **$.ajax** estamos usando el método GET y apuntando a api/ejemplo, por lo que estamos llegando a get.php y justo a:
```php
$app->get(\'/ejemplo\',function($request, $response)
```
Y como en realidad estamos pasando información del formulario por el método GET, y no simplemente estamos visitando la url api/ejemplo, pues el framework nos da acceso a la petición y se realiza lo explicado más arriba, que se llama al modelo y al método Foo() generado por el generador de código, el cual retorna un array() con un success => 0, y un message => \'el mensaje\'.

Si vemos un poco más en el javascript
```javascript
success : function(json) {
var obj = jQuery.parseJSON(json);
```
en "json", tenemos un string con formato json, y con jQuery.parseJSON() estamos convirtiendo ese json en un objeto de javascript el cual tiene como propiedades cada elemento del array retornado en Foo(), es decir obj.success y obj.message, todo lo demás es más de lo mismo con javascript.


> Tengamos en cuenta qué, cuando creamos formularios y utilizamos el método GET, si pasamos algún string que contenda caracteres como \'*\', se bloqueará el acceso por el Firewall, para que esto no suceda, debemos utilizar POST. De hecho, hagan una prueba y el log se generará dentro de ./api

## Pasar parámetros por la URL
Ya sabemos que por POST o GET, en los ficheros get.php y post.php debemos si o si generar una petición ajax para poder acceder a ellos, a menos que quitemos los correspondientes IF que lo impiden. Por ello, usemos **map.php**
```php
# Primero definimos la ruta, /hola/VARIABLE, las variables irán entre {}
$app->map([\'GET\', \'POST\'], \'/hola/{name}\',function($request, $response){

  # {name} lo recuperamos en $name con el método $request->getAttribute(), que debe recibir el nombre
  # de lo que tengamos entre las llaves, en este caso {name}
  $name = $request->getAttribute(\'name\');

  # Ahora damos una respuesta
  $response->withJson(array(\'hola\' => $name));
});
```

Ahora al acceder a http://url.com/hola/brayan Veremos el json correspondiente, ya que en la parte de la ruta en donde escribo \'brayan\', eso que está allí es el contenido de la variable.

Podemos crear más rutas e infinitas variables, y obviamente, estas no llegan como $_GET';

    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
