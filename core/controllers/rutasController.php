<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class rutasController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content = '# Rutas (URL\'s Amigables)

Las rutas, son la solución para las URL\'s Amigables que ofrece Ocrend Framework, es importante saber que éstas no se manejan en el sistema como variables **$_GET**, para evitar que posibles caracteres allí activen alertas innecesarias del **Firewall**, sin embargo aquí se presenta una solución para garantizar seguridad en estas rutas.


Existen tres rutas básicas por defecto, predefinidas por el mismo framework, sin embargo éstas no son las únicas que podemos manejar, más adelante se explica en detalle como añadir más rutas a nuestro sistema, pero antes veamos cómo funcionan y cómo usar las rutas que trae el framework por defecto.


La arquitectura de las rutas por defecto es **controlador/metodo/id** la utilidad de estas tres rutas ha sido descrita en el capítulo de [Controladores](https://github.com/prinick96/Ocrend-Framework/wiki/Controladores) y se ha explicado como acceder a ellas desde un Controlador.

![Funcionamiento del Framework](http://i.imgur.com/terPeXC.jpg?1)

El diagrama muestra cómo la información entra a través de index.php, que es en lanzador y quien carga todo el núcleo del framework, esta información solicitada por url, a través de URL amigables o el método tradicional de variables **$_GET**, es filtrada por el **Firewall**, luego pasa por el **Router** quien verifica siempre al inicio, las tres rutas básicas, y luego la información al final llega al **Controlador**.

## getController()
Cuándo la información llega al Controlador, el Router tiene siempre un Controlador. Qué en este caso sería la primera ruta básica:
```
http://url.com/controlador/
```
En caso de que no exista la primera ruta básica, el Router toma por defecto al controlador homeController.


La cadena del controlador sólo acepta caracteres alfanuméricos, por lo que si este se topa con caracteres como \'-,+/ñáéíóú, en fin cualquier caracter no alfanumérico, será ignorado y tomará por defecto a homeController
getController() nos devuelve siempre al controlador que esté siendo utilizado.

## getMethod()
Éste método nos da información acerca de la segunda ruta básica, para que esta pueda existir debe existir también la ruta del controlador, es decir
```
http://url.com/controlador/metodo/
```
Ya que si intentamos hacer algo como:
```
http://url.com/metodo/
```
Estaríamos intentando llamar a un **controlador** llamado **metodoController**.


La cadena del método **no tiene restricción alguna**, en getId() se explica este motivo.
getMethod() siempre nos devuelve el metodo que esté actualmente solicitándose, si no existe, este devolverá **null**

## getId()
Éste método nos da información acerca de la tercera y ruta básica, para que esta pueda existir debe existir también la ruta controlador y la ruta método.
```
http://url.com/controlador/metodo/id/
```
Algo **muy importante** es que a pesar de ser un id, **no necesariamente debe ser numérico**, el Router no tiene ninguna sentencia que bloquee el acceso cuando éste no sea un número, o contenga caracteres especiales como el asterisco, o palabras clave que tiende a bloquear el framework, igual que el **método**, ya que se ha decidido dejar a libre uso estas dos últimas rutas al programador.

## Colocar restricciones de acceso al método o al id

Para dar restricciones de acceso, debemos hacerlo ya directamente desde el controlador. Para esto debemos conocer los [Helpers](http://framework.ocrend.com/helpers). Aunque se presentará de forma breve su uso en este capítulo para introducirlos y poder aplicarlos.


Básicamente un **helper** es una librería auxiliar de funciones clasificadas por categoría, sin más, podemos empezar a usarlos.

Para llamar a un helper, debemos hacer:
```php
# La carga
Helper::load(\'nombre_del_helper\');
# Y para usarlo
NombreDelHelper::funcion_en_el_helper();
```

Entendiendo esto, usaremos el helper [Strings](https://github.com/prinick96/Ocrend-Framework/wiki/Helpers#strings):

Limitando a caracteres alfanuméricos el método o la id:
```php
Helper::load(\'strings\');

#Para hacer la restricción al método se utiliza $this->route->getMethod()
#Para hacer la restricción al id se utiliza $this->route->getId()
#Para este ejemplo, solo utilizaré $this->route->getMethod() para cada uno

# Restricción alfanumérica
if(null != $this->route->getMethod() and Strings::alphanumeric($this->route->getMethod())) {
  # Hay acceso
  $metodo = $this->route->getMethod();
} else {
  # No hay acceso, redireccionamos al home y detenemos la ejecución
  Func::redir();
  exit;
}

# Restricción de sólo letras
if(null != $this->route->getMethod() and Strings::only_letters($this->route->getMethod())) {
  # Hay acceso
  $metodo = $this->route->getMethod();
} else {
  # No hay acceso, redireccionamos al home y detenemos la ejecución
  Func::redir();
  exit;
}

# Restricción de solo números (sí, el método también lo podemos agarrar como un número si queremos)
if(is_numeric($this->route->getMethod())) {
  # Hay acceso
  $metodo = $this->route->getMethod();
  # Ya que es un número, si nos da la gana, podemos colocarlo como id
  $id = $this->route->getMethod();
  /*
   * Pero recomiendo que, si se va a utilizar como, añadir también a la condición \'and $this->route->getMethod() >= 1\'
   * Y recomiendo aún más que si necesitamos manejar un ID, lleguemos a la ruta del ID,
    si no ocuparemos un método, podemos poner cualquier cosa en el como por ejemplo controlador/ver/id y captamos directamente el ID con $this->route->getId()
  */
} else {
  # No hay acceso, redireccionamos al home y detenemos la ejecución
  Func::redir();
  exit;
}

# ... resto del código, puedo usar $metodo/$id aquí
```

## Crear nuestras propias rutas

Ya que manejamos bien las rutas básicas, quizá se nos queden un poco cortas, de antemano digo que pueden optar por la solución **híbrida** y pasar más información a través de la URL como variables típicas **$_GET**, aprovechando la protección del Firewall para que ahora sea éste quien se encargue de filtrar la información.
```
http://url.com/controlador/?hola=esto&tambien=se&puede=hacer
http://url.com/controlador/metodo/?hola=esto&tambien=se&puede=hacer
http://url.com/controlador/metodo/id/?hola=esto&tambien=se&puede=hacer
```

Pero si queremos mantener la elegancia con las rutas de URL\'s amigables, pues tenemos dos sencillos métodos que nos ayudarán con la tarea de crear y filtrar automáticamente nuestras rutas, los métodos **setRoute** y **getRoute**

![Intervención de setRoute y getRoute](http://i.imgur.com/Kp1ayEZ.jpg?1)

Así se ve modificada la lógica al utilizar setRoute y getRoute.

## $this->route->setRoute()
Este método nos permite crear nuevas rutas y asignar las restricciones directamente al crearlas, de una manera sencilla, existe solamente cinco reglas por defecto, más abajo se explica cómo crear nuestras propias reglas.


setRoute() recibe dos parámetros, uno de ellos es por opcional.


El **primer parámetro** es el nombre de la ruta, puede ser el que nos venga en gana, ya que este solamente se ve reflejado para su uso adentro del código, la única regla es que debe tener una barra invertida delante, con la forma **/miruta /comoquiera /lo_que_sea**

Hay que tener en cuenta que ésto entra en un arreglo asociativo, por lo que se debe respetar las reglas para los nombres de los index en los arreglos asociativos.
```php
parent::__construct();
# Se debe añadir justo debajo de la llamada al constructor padre, dentro de __construct() en nuestro controlador.
$this->route->setRoute(\'/nueva\');
$this->route->setRoute(\'/otra_mas\');
$this->route->setRoute(\'/podemos_hacer_las_que_nos_venga_en_gana\');
```


El **segundo parámetro** es la regla que estará asociada a esta ruta, por defecto si no colocamos nada, la regla será **alphanumeric**, es decir, esto nos va a filtrar para que lo que sea que entre en nuestra ruta **/nueva**, pueda tener solamente caracteres alfanuméricos.

**Reglas:**
* **alphanumeric**: Filtra para que el contenido sólo pueda contener caracteres alfanumérico.
* **letters**: Filtra para que el contenido sólo pueda contener letras.
* **int**: Filtra para que el contenido sólo pueda contener números enteros.
* **float**: Filtra paa que el contenido sólo pueda contener números de coma flotante y enteros.
* **none**: No añade ningún tipo de filtro.

La forma de aplicar la regla sería:
```php
$this->route->setRoute(\'/nueva\',\'int\');
```
De tal forma que al momento de alguien acceder a cualquiera de estas posibles rutas
```
http://url.com/controlador/metodo/id/174.5
http://url.com/controlador/metodo/id/7878x18
http://url.com/controlador/metodo/id/xa7r87
http://url.com/controlador/metodo/id/abcd
http://url.com/controlador/metodo/id/xa*r
http://url.com/controlador/metodo/id/aa-gl
```
Todas serán rechazadas, así que en vez de obtener el valor que está entrando allí obtendremos **null**, cuando usemos getRoute() para obtener el valor, pero eso lo vemos más adelante.


La única ruta correcta para esa regla sería algo similar a:
```
http://url.com/controlador/metodo/id/15
```
Se entiende que es, sólo números enteros, por lo que cuando recuperemos su valor obtendremos 15.

**Se da a entender, que para llegar a nuestra ruta nueva, primero hay que pasar por el controlador, luego por el método y luego por la id, es decir que estén definidas en la URL**, esta es otra razón por la cual no se ha dado restricciones a el método ni al id en las rutas, por si necesitamos que nuestra ruta personalizada empiece justo después del controlador, podamos verificar las restricciones propias y obtener sus valores.

O sea que si hacemos
```
http://url.com/15 estaríamos intentando entrar al controlador "15"
http://url.com/controlador/15 estaríamos recibiendo 15 en el método
http://url.com/controlador/metodo/15 estaríamos recibiendo 15 en la id
```

También **es muy importante**, tener presente que las rutas se acceden en el orden en que son creadas. Es decir:
```php
$this->route->setRoute(\'/nueva\',\'int\');
$this->route->setRoute(\'/otra_mas\',\'letters\');
```
Para que algún valor pueda entrar en **/otra_mas**, debe existir uno entrando en **/nueva**.
```
http://url.com/controlador/metodo/id/15/hola
```
En **/nueva** tendríamos **15** y en **/otra_mas** tendríamos el valor **hola**.


Si sucede algo como esto:
```
http://url.com/controlador/metodo/id/15
```
En **/nueva** tendríamos **15** y en **/otra_mas** tendríamos el valor **null**.


Creo que queda extremadamente claro donde va cada cosa.

## $this->route->getRoute()
Éste método nos permite obtener los valores de las rutas que ya hemos creado con setRoute(), sabiendo utilizar setRoute(), solamente maneja un único parámetro, el de la ruta a rescatar, ahora supongamos qué:

```php
$this->setRoute(\'/miruta\',\'int\');
```
Para acceder al valor de lo que sea que entre en **/miruta** solo debemos rescatarlo de la siguiente manera:
```php
$miruta = $this->getRoute(\'/miruta\');
```
Con eso tendríamos en la variable $miruta, lo que sea que esté entrando en **/miruta** por la URL.
```php
# Si la URL está http://url.com/controlador/metodo/id/20
$miruta === (int) 20 # el (int) quiere decir que ya lo tendríamos correctamente filtrado como un entero para trabajar con el
# Si la URL está http://url.com/controlador/metodo/id/12.4
$miruta === null # Porque la hemos definido con la regla \'int\', que solo permite enteros.
# Si la URL está http://url.com/controlador/metodo/id/abc
$miruta === null # Porque la hemos definido con la regla \'int\', que solo permite enteros.
```
Con esto se entiende que, cuando en la URL entre algún valor que no cumpla con la regla que hemos definido, obtendremos **null**, de esta manera podemos nosotros tener el control de qué hacer cuándo no esté entrando un parámetro que respete la regla.

## Cómo definir reglas de restricción personalizadas

¿Qué sucede si las 5 reglas básicas se nos quedan cortas? pues vamos a aprender a meterle mano al **Kernel** por primera vez, es muy sencillo crear una nueva regla.

**Vamos a crear una regla que filtre un dato entero, y que éste sólo pueda ser 1 o 0.**

Abrir **./core/kernel/Router.php** y buscar el método **setRoute**, la línea:
```php
if(!in_array($type,[\'letters\',\'int\',\'float\',\'none\'])) {
```
Modificar por:
```php
if(!in_array($type,[\'letters\',\'int\',\'float\',\'none\',\'nombre_de_mi_regla\'])) {
```

Ahora vamos a añadir su funcionalidad, en el mismo archivo buscamos el método **getRoute** y buscaremos la estructura **switch**, justo arriba del default, añadiremos un nuevo **case**:
```php
case \'nombre_de_mi_regla\':
   return (is_numeric($this->url[$index]) and ($this->url[$index] == 0 or $this->url[$index] == 1)) ? intval($this->url[$index]) : null; # Podemos cambiar \'null\' por lo que deseemos que devuelva cuando no se cumpla la regla.
break;
```
O también podemos hacerlo así, que es más óptimo y elegante:
```php
case \'nombre_de_mi_regla\':
   # Básicamente estamos diciendo que si el contenido de la ruta está en el arreglo, es 0 o 1, por tanto lo devolvemos.
   return in_array($this->url[$index],[0,1]) ? intval($this->url[$index]) : null;
break;
```

Guardamos **Router.php** y para utilizar nuestra regla simplemente debemos crear la ruta así:
```php
$this->setRoute(\'/miruta\',\'nombre_de_mi_regla\');
```

## Definir rutas globales

Ya que las rutas creadas en un Controlador, sólo funcionan en ese controlador, ¿qué pasa si tenemos la necesidad de que una ruta sea **global para absolutamente todos los controladores de nuestra aplicación** y que sólo tengamos que hacer esta configuración **una vez**?

Es sencillo, modificando el **Kernel**, esta vez nos vamos a **./core/kernel/Controllers.php**:
```php
# Buscar
protected $route;
# Añadir debajo
protected $mi_nueva_ruta;
```
Ahora creamos la ruta:
```php
# Buscamos dentro del constructor
$this->route = $router;
# Añadimos debajo
$this->route->setRoute(\'/mi_nueva_ruta\',\'mi_regla\');
$this->mi_nueva_ruta = $this->route->getRoute(\'/mi_nueva_ruta\');
```

Y listo, ahora desde cualquier controlador podemos acceder al valor de esa ruta, si somos creativos podemos sacarle mucho provecho a la estructura de programación que nos ofrece Ocrend Framework.
```php
class micontroladorController extends Controllers {

  public function __construct() {
    parent::__construct();

    # Puedo llamar a
    $this->mi_nueva_ruta
    # Y obtendré su valor, que sabemos dependerá de la regla elegida.

    echo $this->template->render(\'mivista/mivista\');
  }

}
```';
    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
