<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class generadorController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content =  '# Generador de código

Es un script escrito en PHP orientando su uso a la consola, está en el archivo llamado **./gen.php** y tiene como cometido
ser bastante útil al momento de escribir una y otra vez contenidos similares, además de automatizar enormemente la creación de
módulos completos en un sistema, por ejemplo CRUDS enteros, escribiéndonos todo el código y creando la tabla en la base de datos,
inclusive acoplándose al diseño web que tengamos en nuestra aplicación, todo por nosotros.

## Cómo utilizarlo
Para empezar, si jamás has tocado una consola y quieres ver como funciona esto, es simple, sólo abre tu CMD en el caso de que estés en Windows o tu terminal
en caso de que te encuentres en Linux / Mac y escribe en el lo siguiente:
```
cd /ruta/en/donde/está/instalado/el/framework/
```
Una vez hagas eso, estarás parado en el directorio donde está instalado el framework, si estamos en windows con xampp, sería algo así:
```
cd C:/xampp/htdocs/Ocrend-Framework/
```
Es así de simple, ahora para utilizar el generador, simplemente comprueba que al escribir lo siguiente:
```
php gen.php -ayuda
```
Veas un menú de ayuda con todos los posibles comandos que puedes manejar en el generador.

## Crear un controlador
Un uso simple, sería crear un controlador, para esto sólamente debemos escribir por consola:
```
php gen.php c Hola
```
Esto nos generaría el fichero **./core/controllers/holaController.php** el cual tendrá escrito código PHP con la estructura mínima
para que el framework la considere un controlador.


La plantilla que utiliza el framework para escribir este código está en **./generator/c.g** cuyo contenido es:
```php
<?php

# Seguridad
defined(\'INDEX_DIR\') OR exit(\'Ocrend software says .i.\');

//------------------------------------------------

class {{controller}} extends Controllers {

  public function __construct() {
    parent::__construct();
    echo $this->template->render(\'{{view}}/{{view}}\');
  }

}

?>
```
Donde **{{controller}}** es el nombre del controlador y **{{view}}** el nombre de la vista correspondiente, estas etiquetas las utiliza
el generador para saber qué y dónde debe escribir lo necesario.

## Crear una vista
Ahora quizá queremos crear una vista, para que ésta le corresponda al controlador holaController creado anteriormente.
```
php gen.php v Hola
```
Esto habrá creado un archivo nuevo en dos posibles rutas y con dos posibles contenidos, **esto depende del motor de templates que estamos usando**, si tenemos configurado nuestro
framework con **PlatesPHP** que es el motor por defecto, se generará **./templates/plates/hola/hola.phtml**, pero en cambio si tenemos configurado **Twig** el generador también lo va a detectar
y entonces generará **./templates/twig/hola/hola.twig**.


Si usamos plates, se generará con el siguiente contenido:
```markup
<?= $this->insert(\'overall/header\') ?>
  <body>
    <div class="container">
      <div class="presentacion center">
        <div class="row">
          <div class="col-xs-12">
            <h1>Página nueva</h1>
            Blank.
          </div>
        </div>
      </div>
    </div>
    <?= $this->insert(\'overall/footer\') ?>
  </body>
</html>
```
La plantilla se obtiene de **./generator/plates/v.g**


Si usamos twig, se generará con el siguiente contenido:
```markup
{% include \'overall/header\' %}
  <body>
    <div class="container">
      <div class="presentacion center">
        <div class="row">
          <div class="col-xs-12">
            <h1>Página nueva</h1>
            Blank.
          </div>
        </div>
      </div>
    </div>
    {% include \'overall/footer\' %}
  </body>
</html>
```
La plantilla se obtiene de **./generator/twig/v.g**


**NOTA:** Por defecto, esas plantillas generan un diseño acorde con el que trae el framework por defecto, la idea es, que al cambiar el diseño
de nuestra aplicación cambiemos también el diseño de las plantillas de las vistas, así el generador, clonará eso y el resultado será el mismo que
el de nuestra aplicación, es mejor hacer esto una vez, que tantas veces como vistas creemos en nuestra aplicación.


**NOTA 2:** Si estamos ocupando por ejemplo el motor de plantillas Twig, **no hace falta** tocar las plantillas para plates y viceversa, porque el generador
sólo tomará las plantillas del motor que estemos usando en el framework.

## Crear un modelo
Para crear un modelo, también es muy sencillo, sólo tenemos que escribir por consola:
```
php gen.php m Hola
```
Y esto creará el fichero **./core/models/Hola.php** con el siguiente contenido:
```php
<?php

# Seguridad
defined(\'INDEX_DIR\') OR exit(\'Ocrend software says .i.\');

//------------------------------------------------

final class {{model}} extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  public function __destruct() {
    parent::__destruct();
  }

}
?>
```
Donde **{{model}}** es la etiqueta que usa el generador para saber donde escribir el nombre de la clase al generar el archivo.

## Combinaciones (MVC), (MC), (MV), (CV)
No debemos limitarnos en escribir sólamente un controlador, luego una vista, luego un modelo, podemos hacer todo de una vez y hacer bastantes combinaciones para logar lo mismo, por ejemplo,
veamos que para crear un MVC conjunto, sólamente debemos escribir:
```
php gen.php mvc Ejemplo
```
Esto nos creará un modelo, una vista y un controlador de una vez.


**NOTA:** No debemos memorizar los comandos, es sencillo, un controlador se representa con la "c", una vista con la "v" y un modelo con la "m", y **sin importar el orden**, cuando estén juntos, creará los tres, o sea que los siguientes comandos también serían válidos:
```
php gen.php mcv Ejemplo
php gen.php vmc Ejemplo
php gen.php vcm Ejemplo
php gen.php cvm Ejemplo
php gen.php cmv Ejemplo
```

Entonces, podemos crear bastantes combinaciones, por ejemplo, una **vista** + **controlador**:
```
php gen.php cv Bueno
```
Y solo deberíamos entrar a www.miframework.com/bueno/ y ya veríamos la vista funcionando conjunto al controlador.

Otras combinaciones serían, **vista** + **modelo**:
```
php gen.php mv Algo
```
**modelo** + **controlador**:
```
php gen.php mc Algo
```
Al igual que con el comando **mvc**, no importa el orden el que escribamos, sólo importa que estén los identificadores
de los elementos que queremos crear, o sea, "v", "c" ó "m".

## Combinaciones (API Rest)
Podemos adelantar importantes pasos si queremos diseñar en algún momento un formulario con AJAX que se comunique con nuestra aplicación, o simplemente generar modelos que se accedan desde la API REST.

Para ello, debemos tener en cuenta que, siempre que se quiera usar la API REST con el generador **sí o sí debe crearse un modelo**.
```
php gen.php ma:get Hola
```
El comando anterior nos creará un modelo en **./core/models/Hola.php** con una método llamado "foo(array $data)", éste retorna un arreglo que será convertido en un json.
```javascript
{
	\'success\' : 0, // Sirve para saber si hubo respuesta correcta
	\'message\' : "funcionando" // Un mensaje personalizado
}
```
Además, se habrá escrito al final del archivo **./api/http/get.php** lo siguiente:
```php
$app->get(\'/hola\',function($request, $response){

  $h = new Hola;
  $response->withJson($h->foo($_GET));

  return $response;
});
```
Donde vemos que ahora, en **Hola.php** en el parámetro **$data** de el método **foo()** estaremos recibiendo lo que sea que llegue a través de la variable global **$_GET**.
La API se ha escrito en **get.php** debido a que en el comando, hemos colocado **a:get**, si deseamos trabajar con otros verbos de http sería cuestión de jugar con:


Modifica **./api/http/post.php**
```
php gen.php ma:post Hola
```
Modifica **./api/http/put.php**
```
php gen.php ma:put Hola
```
Modifica **./api/http/delete.php**
```
php gen.php ma:delete Hola
```
Modifica **./api/http/map.php**
```
php gen.php ma:map Hola
```

### Formularios (MVC + AJAX + API REST)
Entre las combinaciones posibles, podemos además de crear sólamente un modelo con una petición REST, crear formularios que funcionen con AJAX y el generador creará todoel javascript correspondiente por nosotros, para esto simplemente debemos escrbir algo como:
```
php gen.php mvca:post Ejemplo
```
Ese comando nos creará un Modelo, Controlador, Escribirá en el archivo **post.php** de la API y además nos creará una vista con un formulario básico y un javascript asociado a ese formulario en **./views/app/js/ejemplo/ejemplo.js**.
Si ahora accedemos por URL a
```
http://nuestroframework.com/ejemplo/
```
Veremos un formulario sencillo con un input text y un botón que dice Enviar, si damos clic en el botón veremos que nos saldrá un alert en rojo que dice "Funcionando".

Esto se debe a que nuestro formulario se está conectando con nuestro modelo **Ejemplo.php** a través de la API REST.

Probemos ahora, modificar en **Ejemplo.php** la línea 16 con el siguiente array:
```php
return array(\'success\' => 1, \'message\' => $data[\'ejemplo\']);
```
Y sin necesidad de recargar nuestra página, escribamos algo en el input text y a continuación hagamos clic de nuevo en "Enviar".

Lo que debe verse sería un alert verde, que tiene el texto que escribimos en nuestro input y encima se debe recargar la página en un período no mayor a un segundo. El alert verde y la recarga de la página se debe a que en el arreglo que retornamos, **"success"** tiene el valor **1**, y antes tenía un **0**, esto es útil cuando queremos por ejemplo decir por alerta en rojo algún error con el estilo de "Todos los campos deben estar llenos".


**Lo mejor es que podemos crear infinitos inputs en nuestra vista y el framework los detectará sin que tengamos que tocar el javascript**, podemos probar colocando por ejemplo en nuestra vista:
```
<div class="form-group">
	<label class="cole">Otro campo:</label>
	<input type="text" class="form-control form-input" name="otro" placeholder="Esto es otro campo..." />
</div>
```
Obviamente esto debe estar **dentro de la etiqueta form** que está en nuestra vista, y ahora para acceder al valor de ese input en nuestro modelo lo haríamos con **$data[\'otro\']**.


Y no debemos limitarnos a campos de texto, evidentemente pueden ser radios, checks, tel, textarea, etc... salvo campos "file", donde deberíamos vernos obligados a modificar el javascript para que este mande correctamente la información a nuestro modelo.

## Tablas en la base de datos
El generador se conecta completamente con el framework, por lo que también se conecta con la base de datos a la que esté conectada el framework. Es decir, lo que tengas en el fichero **config.php**, por lo que el generador puede tomarse la tarea
de crear tablas en la base de datos por tí.

Para crear una tabla debemos escribir el siguiente comando:
```
php gen.php b nombre_de_mi_tabla [id:int[11],campo2:varchar[100],campo3:text]
```
El comando anterior creará una tabla llamada **nombre_de_mi_tabla** y con los siguientes campos:
	* **id** de tipo entero (int) con longitud 11, como este campo se llama "id", el generador detecta automáticamente esto y le asigna la propiedad de **PRIMARY KEY** y **AUTO_INCREMENT**
	* **campo2** de tipo varchar con longitud 100
	* **campo3** de tipo text

Se entiende entonces, que la parte entre los corchetes, es para establecer los campos de la tabla a crear, y se separa cada campo con "," (comas), la sintaxis es:
```
nombre_de_campo:tipo[longitud]
```
Y **no debe haber** espacios vacíos entre los [], con los ":" (dos puntos), separamos el nombre del campo del tipo de dato.


## Combinaciones avanzadas
Con todo lo comentado anteriormente podemos ver que se puede hacer bastantes combinaciones, por ejemplo podemos crear un formulario con la api rest y además crear una tabla en la base de datos.
```
php gen.php mvca:post Formulario b formulario_tabla [id:int[11],nombre:varchar[100]]
```
Y allí podríamos jugar con crear vistas, modelos con api rest, sin api rest, controladores, etc.

## CRUDS
Sin duda esta es la parte más importante y útil del generador, porque nos ahorra un montón de tiempo a la hora de realizar cruds enteros en nuestra aplicación.

Todos los CRUDS sin excepción, se crean acompañados de una tabla en la base de datos, y el comando para realizarlos es:
```
php gen.php crud Modulo b mi_tabla [id:int[11],nombre:varchar[150],apellido:varchar[150],edad:tinyint[2]]
```
Al crear teclear ese comando, veremos que se creará además de la base de datos, un modelo, tres vistas, un controlador, dos ficheros javascript y se escribirá dos veces la api rest.

El generador en el modelo, escribirá todos los algoritmos básicos de CRUD, es decir, crear, borrar, editar y listar, ya lo hará **tomando en cuenta los campos de la tabla** de la base de datos que creamos, en concreto eso crearía esto **Modulo.php**:
```php
final class Modulo extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  # Control de errores
  final private function errores(array $data) {
    try {


    if(!Func::all_full($data)) {
      throw new Exception(\'Todos los campos son necesarios.\');
    }

      return false;
    } catch(Exception $e) {
      return array(\'success\' => 0, \'message\' => $e->getMessage());
    }
  }

  # Crear un elemento
  final public function crear(array $data) : array {
    $error = $this->errores($data);
    if(false !== $error) {
      return $error;
    }

    $i = array(
		\'nombre\' => $data[\'nombre\'],
		\'apellido\' => $data[\'apellido\'],
		\'edad\' => $data[\'edad\'],
	);
	$this->db->insert(\'mi_tabla\',$i);

  	return array(\'success\' => 1, \'message\' => \'<b>Creado</b> con éxito.\');
  }

  # Editar un elemento
  final public function editar(array $data) : array {

    $this->id = $this->db->scape($data[\'id\']);

    $error = $this->errores($data);
    if(false !== $error) {
      return $error;
    }

    $i = array(
		\'nombre\' => $data[\'nombre\'],
		\'apellido\' => $data[\'apellido\'],
		\'edad\' => $data[\'edad\'],
	);
	$this->db->update(\'mi_tabla\',$i,"id=\'$this->id\'",\'LIMIT 1\');

    return array(\'success\' => 1, \'message\' => \'<b>Editado</b> con éxito.\');
  }

  # Borrar un elemento
  final public function borrar() {
    $this->db->delete(\'mi_tabla\',"id=\'$this->id\'");
    Func::redir(URL . \'modulo/?success=true\');
  }

  # Leer uno o todos los elementos
  final public function leer(bool $multi = true) {
    if($multi) {
      return $this->db->select(\'*\',\'mi_tabla\');
    }

    return $this->db->select(\'*\',\'mi_tabla\',"id=\'$this->id\'",\'LIMIT 1\');
  }

  public function __destruct() {
    parent::__destruct();
  }

}
```
Se puede ver que ya se trabaja la lógica según los campos creados en la tabla, cuando tecleamos el comando y hay un manejador de errores básico que por defecto hace que todos los campos deban estar llenos en los formularios tanto de edición como de creación.

La plantilla de donde el CRUD toma este modelo está en **./generator/crud/m.g** donde **{{crear_php}}** contiene la lógica de la creación que la escribe el generador según las tablas, igual para **{{editar_php}}** pero con la edición, y en **{{errores_php}}** se escribe el control básico que exige todos los campos llenos.

Por otro lado, se habá creado un controlador que nos dotará de las siguientes características:
* Crear elemento **"miframework.com/modulo/crear"**
* Editar elemento **"miframework.com/modulo/editar/ID"**
* Crear borrar **"miframework.com/modulo/eliminar/ID"**
* Lista todos los elementos en una tabla con acciones de editar/eliminar **"miframework.com/modulo/"**

El contenido de nuestro controlador **moduloController.php** sería el siguiente
```php
class moduloController extends Controllers {

  public function __construct() {
    parent::__construct();

    $m = new Modulo;

    switch($this->method) {
      case \'crear\':
        echo $this->template->render(\'modulo/crear\');
      break;
      case \'editar\':
        if($this->isset_id and false !== ($item = $m->leer(false))) {
          echo $this->template->render(\'modulo/editar\', array(
            \'data\' => $item[0]
          ));
        } else {
          Func::redir(URL . \'modulo/\');
        }
      break;
      case \'eliminar\':
        $m->borrar();
      break;
      default:
        echo $this->template->render(\'modulo/modulo\',array(
          \'data\' => $m->leer()
        ));
      break;
    }
  }

}
```
La plantilla de donde el CRUD toma este controlador está en **./generator/crud/c.g**.


Luego, según el motor de templates que usemos se generarán tres vistas:
* **templates/plates/modulo/crear.phtml** ó **templates/twig/modulo/crear.twig**
* **templates/plates/modulo/editar.phtml** ó **templates/twig/modulo/editar.twig**
* **templates/plates/modulo/modulo.phtml** ó **templates/twig/modulo/modulo.twig**



Se sigue documentando...

## Ayuda sobre todos los comandos posibles
Sólamente se debe escribir el siguiente comando
```
php gen.php -ayuda
```
Y se mostrarán todos los comandos posibles.
';
    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
