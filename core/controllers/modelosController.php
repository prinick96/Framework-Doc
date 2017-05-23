<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class modelosController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content = '# Modelos

## Lógica de la estructura
Los modelos son la parte que se encargará de manejar la lógica e interacción completa con la base de datos. Estos son llamados desde el Controlador para entregar la información a la vista (los templates Plates).


Los modelos se crean en **./core/models/**, para crear un modelo hay que tener en cuenta que **el nombre del archivo debe ser igual al nombre de la clase**, por ejemplo para un archivo llamado **Modelo.php**, el contenido del archivo debe ser:
```php
class Modelo extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  public function __destruct() {
    parent::__destruct();
  }

}
```
Vamos a analizar la estructura de un modelo, en primera parte
```php
class Modelo extends Models implements OCREND
```
El modelo se llama Modelo, y hereda sus características de **Models**, una clase del **Kernel**, además implementa la interface **OCREND**.


Para que las cualidades de Models funcionen correctamente, se debe cumplir la restricción de la interface, y esa es que nuestro modelo cuente con un constructor y con un destructor que invoquen al constructor/destructor padre respectivamente.
```php
public function __construct() {
  parent::__construct();
#...
public function __destruct() {
  parent::__construct();
#...
```

## La clase Models
La clase Models es una clase abstracta, es la padre de todos los Modelos y la que distribuye información que queramos compartir de forma global con todos los modelos de nuestra aplicación. Esta clase no puede ser instanciada.

## parent::__construct();
Recibe tres parámetros opcionales.

El **primer parámetro** es el nombre de la base de datos a la cual se quiere conectar desde el Modelo, por ejemplo:
```php
public function __construct() {
  parent::__construct(\'mibasededatos\');
#...
```
Si no se pasa este parámetro, por defecto se estará conectando a lo que esté definido en **DATABASE[\'name\']** en **config.php**

El **segundo parámetro** es el tipo de base de datos a la cual queremos conectar, la lista de parámetros soportados es:
  * mysql
  * sqlite
  * oracle
  * postgresql
  * cubrid
  * firebird
  * odbc

```php
public function __construct() {
  parent::__construct(\'mibasededatos.sqlite\',\'sqlite\');
#...
```
Si no se pasa este parámetro, por defecto se estará utilizando el motor **mysql** definido en **DATABASE[\'motor\']** en **config.php**



El **tercer parámetro** está definido para cuando queremos conectarnos a una nueva base de datos distinta a la que ya nos hayamos conectado previamente, es un valor boolean que define **true** cuando queremos que nuestro modelo cada vez que se llame instancie una nueva conexión y **false** cuando no, este parámetro es opcional y por defecto es **false**.

Tomemos de ejemplo que desde un **Controlador** hacemos:
```php
# Este modelo se conectar por defecto a MySQL
new MiModelo;
# En este modelo, nos conectamos a SQLite (o podría conectar también a MySQL pero a una base de datos distinta)
new MiModeloDos;
```

Si en MiModeloDos tenemos:
```php
parent::__construct(\'mibasededatos.db\',\'sqlite\');
```

**NO FUNCIONARÁ** la conexión con SQLite puesto que previamente se está conectando a MySQL en **MiModelo**, ya que este se instancia primero, pasaría lo mismo si hacemos desde el Controlador:
```php
# En este modelo, nos conectamos a SQLite (o podría conectar también a MySQL pero a una base de datos distinta)
new MiModeloDos;
# Este modelo se conectar por defecto a MySQL
new MiModelo;
```

En **MiModelo** no obtendríamos conexión alguna con MySQL puesto que ya hay una instancia abierta de conexión con SQLite generada por **MiModeloDos**.


Esto es así, para ahorrar recursos y evitar duplicar instancias a la base de datos de varios modelos cuando pretendemos conectarnos a la misma desde ellos. Para entonces poder crear la conexión a una nueva base de datos distinta, debemos pasar el tercer parámetro al constructor del modelo que se instancia después. Si asumimos que primero se instancia **MiModelo** y luego **MiModeloDos**, entonces en **MiModeloDos** debemos hacer:
```php
parent::__construct(\'mibasededatos.db\',\'sqlite\',true);
```

De esta forma sí se conectará a la base de datos nueva puesto que permitimos la creación de una instancia nueva. Hay que proceder exactamente igual si queremos conectarnos al mismo motor de base de datos, pero a una base de datos distinta. Cada vez que queramos conectarnos a una base de datos diferente a la vez desde un controlador, tenemos que asegurarnos de que desde el orden segundo en adelante, la llamada a modelos nuevos debe poseer el tercer parámetro como **true**.

## $this->db
Es la propiedad que se encarga de manejar todo lo referente a la base de datos, es la instancia de la clase Conexión.

## $this->db->select()
Selecciona elementos de una base de datos, recibe dos parámetros obligatorios y dos opcionales.
```php
$resultado = $this->db->select(\'*\',\'users\', "nombre=\'brayan\'" , \'LIMIT 10\' );
```
El ejemplo mostrado selecciona **TODOS** los elementos de la tabla **users** cuando el nombre sea brayan y solo traerá 10 máximos resultados.


El **primer parámetro** es qué elementos queremos seleccionar, * para todos, **id,user,password** separamos con coma si especificamos los que queremos traer.

El **segundo parámetro** indica de que tabla queremos extraer la información pasada por el primer parámetro.

El **tercer parámetro** indica la condición de búsqueda de la query a la base de datos (estos datos debemos filtrarlos manualmente antes de ingresarlos si es el caso en el que estamos pasando una variable que proviene de la acción de un usuario), este tercer parámetro si se omite, trae todos los usuarios existentes de la base de datos.

El **cuarto parámetro** limita los resultados obtenidos por el tercer parámetro, si colocamos LIMIT 1, solo trae un parámetro, si colocamos LIMIT 2, sólamente 2.
También si colocamos ORDER BY campo DESC LIMIT 1 , podemos traer los parámetros ordenados por "campo" de forma descendente limitados a 1.


Si queremos pasar el cuarto parámetro sin pasar por el tercero, sería algo como:
```php
$resultado = $this->db->select(\'campo1, campo2\',\'tabla\',\'1=1\',\'LIMIT 10\');
```
Allí estamos dando la condición de búsqueda, de cuando 1 sea igual a 1, eso es verdadero siempre por tanto traerá todos los resultados que existen en la tabla, limitamos a 10 esos resultados.


Si queremos todos los resultados sin importar una condición ni un límite, pues simplemente no pasamos los parámetros opcionales.
```php
$resultado = $this->db->select(\'campo1, campo2\',\'tabla\');
```

En conclusión, para que se entienda mejor, el método select() utiliza el método query() de Mysqli para traer la información y la procesa.


El método select() devuelve **false** si no consigue resultados, y devuelve un **arreglo asociativo/numérico** con cada uno de los resultados en la base de datos si es que encontró resultados.

**Ejemplo práctico**
```php
 $r = $this->db->select(\'nombre,email\',\'users\',"id=\'$this->id_user\'",\'LIMIT 1\');
```
Antes que nada, vemos **$this->id_user**, esta propiedad heredada de Models será explicada en detalle más adelante. Por ahora es suficiente saber que esa propiedad contiene el ID del con la sesión iniciada en el momento de usarse.

Por lo que la consulta de arriba se puede leer como, selecciona el **nombre,email** de la tabla **users** cuando el usuario tenga el **id=\'el que está conectado\'** y **limita el resultado a sólamente  1.**

Si consigue resultados, devuelve un arreglo, si no, devuelve false. Vamos a llevarlo a la lógica:
```php
if(false != $r) {
 #Nos ha devuelto una matriz, pero no te asuste. Más abajo le verás el sentido y la utilidad.
 echo \'El nombre es\', $r[0][\'nombre\'], \' y el email es \', $r[0][\'email\'];
} else {
 #Si entra aquí, es porque no se encontró el usuario, podemos hacer algo como redireccionar al home, mostrar un error etc.
}
```

Supongamos que ahora hacemos la siguiente consulta:
```php
 $r = $this->db->select(\'nombre,email\',\'users\',"id=\'$this->id_user\'",\'LIMIT 2\');
 if(false != $r) {
  #Primer resultado:
  echo \'El nombre es\', $r[0][\'nombre\'], \' y el email es \', $r[0][\'email\'],\'<br />\';
  #Segundo resultado (para que este resultado exista, debe haber al menos dos usuarios en la base de datos)
  echo \'El nombre es\', $r[1][\'nombre\'], \' y el email es \', $r[1][\'email\'];
 } else {
  #Si entra aquí, es porque no se encontró el usuario, podemos hacer algo como redireccionar al home, mostrar un error etc.
 }
```
Como se puede observar, tenemos un arreglo tipo matriz con la siguiente estructura:
```php
 Arreglo Principal {
  0 => Arreglo(\'nombre\' => \'el nombre 1\', \'email\' => \'el email 1\'),
  1 => Arreglo(\'nombre\' => \'el nombre 2\', \'email\' => \'el email 2\'),
  ....
  n => Arreglo(\'nombre\' => \'el nombre n\', \'email\' => \'el email n\')
 }
```
Cada posición del arreglo principal contiene un arreglo asociativo, con el nombre de cada campo de la base de datos y el respectivo valor que guarda. Si quitaramos el LIMIT de la consulta, pues tendríamos tantas posiciones en el arreglo principal como registros haya en la base de datos.

## $this->db->insert()
Este método se utiliza para insertar elementos a la base de datos, vamos a poner de ejemplo la tabla de usuarios y una inserción donde crearemos un nuevo usuario con su respectivo usuario, password y email.
```php
Helper::load(\'strings\');
$i = array(
  \'user\' => $_POST[\'user\'],
  \'pass\' => Strings::hash($_POST[\'pass\']),
  \'email\' => $_POST[\'email\']
 );
$this->db->insert(\'users\',$i);
```
El código anterior, inserta en la tabla **users**, en el campo **user** el valor **$_POST[\'user\']**, en el campo **pass** el valor **$_POST[\'pass\']** encriptado, en el campo **email** el valor **$_POST[\'email\']**.

**¿Y qué pasa con el filtrado?** ya no hay problema con ello, podemos poner directamente las variables que recibimos del formulario porque el método insert() filtra automáticamente cada variable, sabiendo quien necesita ser un entero, un flotante o un string.

Esta nueva manera de hacer las inserciones es más rápida y propensa a menos errores que cuando usamos el método query() y construimos manualmente la sentencia SQL de inserción, porque se solía hacer:
```sql
INSERT INTO users (user,pass,email) VALUES (\'$user\',\'$pass\',\'$email\');
```

Sabemos que en los primeros paréntesis, van los nombres de los campos en la tabla, y en los segundos paréntesis después de VALUES, van los valores en el mismo orden que van escritos los nombres de los campos. Esto cuando existe mucha información, al menos desde 10 campos empieza a cargarse bastante y verse muy poco legible, y resulta muy propenso cometer errores. La solución del framework es, crear un arreglo asociativo donde el index de cada valor sea el nombre del campo, y directamente se asocia con su valor.
```php
#campo en la bd => valor a entrar en ese campo
\'user\' => $_POST[\'user\'],
```

Si luego de una inserción llamamos a la propiedad **$this->db->lastInsertId();** esta nos devolverá la ID del elemento que acabamos de insertar.

## $this->db->update()

El método update actualiza campos en la base de datos, el funcionamiento es similar a el método **insert()** y trabaja con cuatro parámetros, uno de ellos opcionales.
```php
$u = array(
  \'user\' => $_POST[\'user\'],
  \'email\' => $_POST[\'email\']
 );
 $this->db->update(\'users\',$u,"id=\'$this->id_user\'",\'LIMIT 1\');
 ```
El intérprete que debemos tener al momento de construir el arreglo, es exactamente el mismo que insert(), los índices del arreglo son los nombres de los campos a modificar y tampoco debemos preocuparnos por filtrar manualmente los valores.

El **primer parámetro** es la tabla a actualizar.

El **segundo parámetro** es el arreglo con los campos => valores a actualizar

El **tercer parámetro** es la condición que indica quienes se van a actualizar, es obligatorio para evitar posibles accidentes de actualización de datos no deseada por algún descuido, si se desea poner una condición para que se actualice absolutamente todo sin restricción, se debería pasar \'1=1\', o alguna condición que sea siempre verdadera.

El **cuarto parámetro** es opcional, por defecto es un string vacío, generalmente es utilizado como un "LIMIT", pero se le puede dar usos extras para personalizar la query final.

## $this->db->delete()
Este método elimina un campo de una tabla en la base de datos, trabaja con tres parámetros y uno de ellos es opcional.
```php
$this->db->delete(\'users\',"id=\'$this->id_user\'");
```
Ese código elimina a **un solo** usuario, que cumpla con **id=\'$this->id_user\'** y esto es así porque el tercer parámetro, por defecto es un LIMIT 1, si se quiere borrar TODO lo que cumpla con esa condición, se debe pasar un string vacío al tercer parámetro.
```php
#borra todo lo que cumpla con id=\'$this->id_user\'
$this->db->delete(\'users\',"id=\'$this->id_user\'",\'\');
```

## Otros métodos
Realiza una query:
```php
$sql = $this->db->query("SENTENCIA SQL")
```
A partir de este punto, para seguir los ejemplos, vamos a asumir que $sql tiene exactamente lo que está escrito arriba.


Devuelve la cantidad de resultados obtenidos de un SELECT
```php
$this->db->rows($sql);
```
Filtra de forma inteligente, un valor, si es un entero devuelve un **integer**, si es un flotante devuelve un **float**, y si es un string devuelve un **string** sanado.
```php
$this->db->scape($variable_a_filtrar);
```
Devuelve una matriz con arreglos explícitamente asociativo que tienen toda la información traide desde la base de datos con una sentencia SELECT.
```php
$this->db->fetch_array($this->db->query("SELECT * FROM users;"));
```

Los métodos close() y Start() se utilizan en la clase Models, close() cierra la conexión y se llama en el destructor de la clase Models, por eso al crear un modelo se debe invocar el destructor de la clase padre (Models), igualmente para el constructor que utiliza Start() para iniciar la conexión.

## $this->id
Si la ruta en el controlador tiene definida el ID, esta propiedad nos otorga ese id.
```
http://url.com/controlador/metodo/5
```
Cómo el Modelo es llamado estrictamente desde el Controlador, podemos también acceder al ID pasado por url.

Si está definido nos devuelve el id (implica que sea un número y mayor o igual a 1 para estar definido), si no está definido devuelve 0. Ya que esta propiedad, este ID se utiliza con el propósito de manejar información por ID en una base de datos, estos ID en la base de datos deben ser de AUTOINCREMENT por lo cual jamás serán < 0.

## $this->id_user
Si la sesión de un usuario está iniciada, devuelve el ID del usuario que tiene la sesión iniciada, si no, tendrá como valor el número 0.

Para comprender esto, podemos revisar en la clase Models
```php
$this->id_user = $_SESSION[SESS_APP_ID] ?? 0;
```

**SESS_APP_ID** es una constante declarada en **config.php** que indica el nombre de la sesión que contendrá el ID de el usuario, cuando un usuario inicia sesión, en **Login.php** se define la variable de sesión **$_SESSION[SESS_APP_ID]** con el ID del usuario que ha iniciado sesión. Cuándo un usuario se registra, el sistema realiza un inicio de sesión automático y también se define en este punto. Cuándo cierra sesión, en el controlador **logout**, se elimina esta variable de sesión.

## Cómo llamar a un Modelo desde el Controlador
Suponemos que tenemos el Controlador, **ejemploController** y queremos llamar al modelo **Ejemplo** en **./core/models/Ejemplo.php**
```php
class ejemploController extends Controllers {

  public function __construct() {
    parent::__construct();

    $ej = new Ejemplo;
    $ej->Foo(); #Suponemos que existe un método llamado Foo en el modelo y lo queremos invocar.

    echo $this->template->render(\'error.twig\');
  }

}
```
Es todo lo que necesitamos hacer, no hay necesidad de utilizar **namespaces** ni **require()/include()**, el Framework detecta automáticamente en donde está el modelo y lo incluye por nosotros, sin importar la posición en donde nos encontremos, desde un controlador o desde controlador de api rest. Ya no habrá más problemas con las rutas para nuestro código.

## Crear un modelo con el Generador
Sí no queremos escribir código para el modelo, ni tomarnos la molestia de manualmente crear el archivo podemos optar por el generador de código.

**Necesita python para funcionar**

Vamos a la ruta en donde tenemos el framework:
```
cd /ruta/en/donde/esta/el/framework
```
Ejecutamos el comando
```
python gen.py m MiModelo
```

Y si queremos genera un modelo acorde con un controlador de API REST
```
python gen.py ma:get MiModelo
```
ó
```
python gen.py ma:post MiModelo
```

Siendo **get** o **post** el método de comunicación por ajax que deseamos utilizar, esto se explica más detalladamente en el uso de la api rest.

## Ejemplo COMPLETO de CRUD
[Ver en Gists](https://gist.github.com/prinick96/b8a294d722adbc36d5d98538a5a8b2a4)

O utilizando el generador de PHP:
```
python gen.py crud Modulo
```';
    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
