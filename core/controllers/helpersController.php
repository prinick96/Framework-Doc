<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class helpersController extends Controllers {

  public function __construct() {
    parent::__construct();
    switch($this->method) {
      case 'paypal':
        $content = '# PayPal
Helper con funciones auxiliares para generas pagos **integrados** utilizando la API SDK de PayPal.

## Conectar nuestra aplicación a PayPal

* Antes de poder utilizar este Helper, debemos registrar una APP en Paypal, para ello debemos ir a [PayPal Developer](https://developer.paypal.com/) e Iniciar Sesión con una **cuenta real y verificada de PayPal**.
* Lo siguiente es ir a [Applications](https://developer.paypal.com/developer/applications/) y en **REST API apps** debemos crear una nueva APP.

Luego en **SANDBOX API CREDENTIALS** debemos tomar los datos de **Client ID** y **Secret** para colocarlos respectivamente en **./core/config.php**
```php
# PayPal SDK
	define(\'PAYPAL_MODE\',\'sandbox\'); # sandbox ó live
	define(\'PAYPAL_CLIENT_ID\',\'AQUÍ EL Client ID\');
	define(\'PAYPAL_CLIENT_SECRET\',\'AQUÍ EL Secret\');
```

También podemos notar que **PAYPAL_MODE** que por defecto es **sandbox**, este estado es para realizar pruebas con dinero falso, es sólo para desarrollo, una vez montemos en producción debemos utilizar **live** en vez de **sandbox**, esto trae como consecuencia, uso de dinero real y **las credenciales de la api cambian** ya que no son las mismas que en modo **sandbox**, para obtener las nuevas credenciales, desde nuestra app en PayPal debemos dar clic en "Live"

![PayPal Helper](http://image.prntscr.com/image/4e37e2edb9724bd982e8724a16809897.png)

## Crear Pago con PayPal

Supongamos que tenemos una vista en la cual existe un botón de un carrito llamado "Pagar", al darle allí lo mandamos a un controlador llamado **pagarController**
```php
public function __construct() {
	# Dentro del controlador llamamos al modelo Pagar y al método HacerPago()
	$p = new Pagar;
	$p->HacerPago();
}
```

A continuación en el modelo **Pagar**
```php
final class Pagar extends Models implements OCREND {

	public function __construct() {
		parent::__construct();
	}

	public function HacerPago() {
		# De alguna forma obtenemos la lista de elementos del carrito
		# Entonces debemos generar un arreglo con la forma
		$items = array(
			array(), # Item 1
			array(), # Item 2
			array(), # Item 3 (Todos los items que estén en nuestro carrito)
			# Acá la estructura interna de cada item
			array(
			\'nombre\' => \'Pelota\',
			\'cantidad\' => 1, # Una pelota,
			\'precio\' => 200.4, # 200.4 euros, o dólares, o alguna moneda, la moneda se define después
			# En precio sólo va la magnitud del precio, no la moneda (un float)
			\'envio\' => 0, # Coste por el envío de este item
			\'tax\' => # Coste por el tax de este item
	   	)
		);

		# Cargamos el Helper
		Helper::load(\'paypal\');

		# Pasamos la configuración inicial
		$config = array(
			\'url\' => \'pagado/\', # Pasamos la URL en donde se debe verificar si el pago se hizo con éxito en PayPal,
			\'descripcion\' => \'Descripción del carrito de los items\'
		);

		/**
	  	* Generamos el pago
		  * El tercer parámetro indica si los costes de tax y shipping son individuales para cada producto, o totales
			* El cuarto parámetro es la MONEDA, USD, EUR, etc...
		*/
		$pago = Paypal::pay($config, $items, false, \'USD\');

		# Si todo estuvo bien (no hubo ningú problema de conexión con PayPal)
		# Entonces el ID del pago será muy distinto de NULL
		if(null !== $pago[\'id\'] && 1 === $pago[\'success\']) {
			# Si todo estuvo bien, pues
			$pago[\'id\']; # Tiene el ID del pago por PayPal
			$pago[\'hash\']; # Tiene un HASH seguro del ID del pago
			$pago[\'success\']; # 1
			$pago[\'message\']; # \'Conexión realizada\'

			/*
				EN ESTE PUNTO LO IDEAL SERÍA INSERTAR
				$pago[\'id\'] y $pago[\'hash\'] en una tabla en la base de datos.
				Para que quede como registro de una transacción, que más adelante se va a comprobar.
			*/

			# Redireccionamos al cliente para que haga un pago con paypal
			Func::redir($pago[\'url\']);

			# URL tiene la direccion a donde debemos redireccionar para que el cliente pague,así que si estamos
			# Desde la API REST, podemos pasarla por JSON a javascript para que redireccionemos con javascript
		  # Una vez el cliente pague, será redireccionado por PayPal a $config[\'url\'] que hemos definido
		} else {
			# De lo contrario, si hubo algún problema de conexión
			echo $pago[\'message\'];
			# Tendremos el problema que ocurrió en \'message\'
		}
	}

	public function __destruct() {
		parent::__destruct();
	}

}
```

## Cómo comprobar un pago realizado con PayPal

En nuestro modelo de ejemplo **Pagar** hemos establecido en
```php
$config = array(
	\'url\' => URL . \'pagado/\' # Pasamos la URL en donde se debe verificar si el pago se hizo con éxito en PayPal
);
```

Por lo que cuando nuestro cliente haga el pago o lo o cancele, paypal lo va a retornar hasta esa URL, la cual asumimos que llega a un controlador llamado **pagadoController** el cual debería tener algo como esto:
```php
public function __construct() {
	parent::__construct();

	# Volvemos llamar a Pago
	$p = new Pagar;
	# Llamamos al método AnalizarPago
	$respuesta = $p->AnalizarPago();
	echo $this->template->render(\'alguna/vista\', \'respuesta\' => $respuesta);

	# En donde $respuesta, será TRUE si se comprobó y FALSE si no el pago.
}
```

Lo cual nos lleva a ver el método **AnalizarPago()** en nuestro modelo **Pagar** de nuevo.
```php
public function AnalizarPago() : bool {

		# Cargamos el Helper
		Helper::load(\'paypal\');

		# Verificamos el pago
		$pago = Paypal::check_pay();

		# Si el cliente pagó con éxito en PayPal entrará aquí
		if($pago[\'success\']) {

			$pago[\'id\']; # ID del pago (el mismo que obtuvimos en Paypal::pay())
			$pago[\'hash\']; # Hash del ID del pago

			/*
				Para que todo esto tenga sentido, deberíamos de comprobar si el ID y el HASH
				del pago que estamos obteniendo aquí coincide con el de la base de datos en caso
				de que lo hayamos hecho en Paypal::pay() (meter esos campos en la DB)

				Para una explicación detallada visitar la lista de reproducción de YouTube
			*/

			return true; # Por tanto devolvemos true (se realizó bien el pago)
		}

	# Si no pagó con éxito o canceló el pago en PayPal, se devuelve false
	return false;
}
```';
      break;

      case 'bootstrap':
        $content = '# Bootstrap

Helper con funciones auxiliares para la vista, provee funciones estáticas que escriben [Bootstrap](http://getbootstrap.com) por nosotros.

## dropdown()

Crea un Dropdown de Boostrap, recibe cuatro parámetros y dos de ellos son opcionales.


El **primer parámetro** es el texto que se colocará como nombre del Dropdown.

El **segundo parámetro** es un arreglo asociativo con todos los enlaces que contendrá el Dropdown, donde cada índice asociativo es nombre del enlace y cada valor es la URL a la que direcciona.
```php
$arreglo_de_ejemplo = array(
 \'Enlace 1\' => \'#\',
 \'Enlace 2\' => \'users/\',
 \'Enlace 3\' => \'http://ocrend.com\'
);
```

El **tercer parámetro** es un valor booleano opcional, para establecer si se desea que el dropdown se despliegue hacia arriba, por defecto es **false** y se despliega hacia abajo, al establecer **true** se desplegará hacia arriba.

El **cuatro parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista (Plates)
<?= Bootstrap::dropdown(\'Mi Drop\', array(\'url 1\' => \'#\'), true, \'clase css extra\') ?>

# Desde la vista (Twig)
{{ dropdown(\'Mi Drop\', {\'url 1\' : \'#\'}, true, \'clase css extra\') }}
```

## button_dropdown()

Renderiza un botón con un dropdown


El **primer parámetro** es el texto que se colocará en el botón.

El **segundo parámetro** es un arreglo asociativo con todos los enlaces que contendrá el Dropdown, donde cada índice asociativo es nombre del enlace y cada valor es la URL a la que direcciona.
```php
$arreglo_de_ejemplo = array(
 \'Enlace 1\' => \'#\',
 \'Enlace 2\' => \'users/\',
 \'Enlace 3\' => \'http://ocrend.com\'
);
```
El **tercer parámetro** es un parámetro opcional, el tipo de botón de bootstrap a crear, por defecto es \'default\' y puede ser \'primary\', \'danger\', \'success\', \'info\', \'default\', \'warning\'

El **cuatro parámetro** es un valor booleano opcional, para establecer si se desea que el dropdown se despliegue hacia arriba, por defecto es **false** y se despliega hacia abajo, al establecer **true** se desplegará hacia arriba.

El **quinto parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista (Plates)
<?= Bootstrap::button_dropdown(\'Mi Botón\', array(\'url 1\' => \'#\'),\'primary\', true, \'clase css extra\') ?>

# Desde la vista (Twig)
{{ button_dropdown(\'Mi Botón\', {\'url 1\' : \'#\'},\'primary\', true, \'clase css extra\') }}

```

## button()

Crea un botón básico de bootstrap


El **primer parámetro** es el texto del botón.

El **segundo parámetro** es opcional, es el tipo de botón, por defecto es \'button\'

El **tercer parámetro**  es opcional, es el tipo de botón de bootstrap a crear, por defecto es \'default\' y puede ser \'primary\', \'danger\', \'success\', \'info\', \'default\', \'warning\'

El **cuatro parámetro** es opcional, es el valor que quisiéramos poner en una etiqueta **id=""**

El **quinto parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista (Plates)
<?= Bootstrap::button(\'Botón\',\'submit\',\'success\',\'el_boton\',\'clases adicionales\') ?>

# Desde la vista (Twig)
{{ button(\'Botón\',\'submit\',\'success\',\'el_boton\',\'clases adicionales\') }}
```

## basic_input()

Renderiza un input básico


El **primer parámetro** es el tipo de input a crear, por ejemplo \'text\', \'email\', \'tel\'

El **segundo parámetro** es el nombre del input que se colocará en la propiedad **name=""** y también en **id="id_"**

El **tercer parámetro** es un booleano opcional, por defecto es **false**, al establecer **true** se colocaría la propiedad **required=""**

El **cuatro parámetro** es opcional, por defecto es un string vacío, es lo que se colocaría en la propiedad **value=""**

El **quinto parámetro** es opciona, por defecto es un string vacío, es lo que se colocaría en la propiedad **placeholder=""**

El **sexto parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista (Plates)
<?= Bootstrap::basic_input(\'text\',\'ejemplo\',true,\'valor\',\'Escribe algo...\',\'form-input clases extras\') ?>

# Desde la vista (Twig)
{{ basic_input(\'text\',\'ejemplo\',true,\'valor\',\'Escribe algo...\',\'form-input clases extras\') }}
```

## basic_select()

Renderiza un select básico


El **primer parámetro** es el nombre del select que se colocará en la propiedad **name=""** y también en **id="id_"**

El **segundo parámetro** es un arreglo asociativo con todas las opciones que contendrá el select, donde cada índice asociativo es **value=""** y el valor de cada posición es el nombre entre las etiquetas **option**
```php
$arreglo_de_ejemplo = array(
 \'1\' => \'uno\',
 \'0\' => \'dos\',
 \'3\' => \'tres\'
);
```

El **tercer parámetro** es un booleano opcional, por defecto es **false**, al establecer **true** y se definirá el select  como **múltiple**

El **cuatro parámetro** es opcional, y su función es tener todos los valores que deben aparecer seleccionados por defecto en el select, esto tiene dos posibles soluciones, si nuestro select no es múltiple, basta con pasar un **string** que tenga el nombre del value="" seleccionado. En caso de ser un select que sí es múltiple, hay que pasar un **array** con todos los value="" seleccionados.

El **quinto parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista select normal (Plates)
<?= Bootstrap::basic_select(\'ejemplo\',array(
 \'1\' => \'uno\',
 \'0\' => \'dos\',
 \'3\' => \'tres\'), false, \'0\', \'clases extras\') ?>

# Desde la vista select múltiple (Plates)
<?= Bootstrap::basic_select(\'ejemplo_multi\',array(
 \'1\' => \'uno\',
 \'0\' => \'dos\',
 \'3\' => \'tres\'), true, [\'1\',\'3\'] , \'clases extras\') ?>

 # Desde la vista select normal (Twig)
 {{ basic_select(\'ejemplo\',{
  \'1\' : \'uno\',
  \'0\' : \'dos\',
  \'3\' : \'tres\'}, false, \'0\', \'clases extras\') }}

 # Desde la vista select múltiple (Twig)
 {{ basic_select(\'ejemplo_multi\',{
  \'1\' : \'uno\',
  \'0\' : \'dos\',
  \'3\' : \'tres\'}, true, [\'1\',\'3\'] , \'clases extras\') }}
```

## checkbox()

Renderiza un checkbox


El **primer parámetro** es el nombre del checkbox que se colocará en la propiedad **name=""** y también en **id="id_"**

El **segundo parámetro** Valor para el checkbox

El **tercer parámetro** es un booleano opcional, por defecto es **false**, al colocar en **true** se clicará el checkbox.

El **cuatro parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista (Plates)
<label class="cole"><?= Bootstrap::checkbox(\'ejemplo\',\'1\',true,\'clases extras\') ?> soy un check</label>

# Desde la vista (Twig)
<label class="cole">{{ checkbox(\'ejemplo\',\'1\',true,\'clases extras\') }} soy un check</label>
```

## radio()

Renderiza un Radio


El **primer parámetro** es el nombre del radio que se colocará en la propiedad **name=""** y también en **id="id_"**

El **segundo parámetro** Valor para el radio

El **tercer parámetro** es un booleano opcional, por defecto es **false**, al colocar en **true** se clicará el radio.

El **cuatro parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista (Plates)
<label class="cole"><?= Bootstrap::radio(\'ejemplo\',\'1\',true,\'clases extras\') ?> soy un radio</label>

# Desde la vista (Twig)
<label class="cole">{{ radio(\'ejemplo\',\'1\',true,\'clases extras\') }} soy un radio</label>
```

## textarea()

Renderiza un textarea


El **primer parámetro**  es el nombre del textarea que se colocará en la propiedad **name=""** y también en **id="id_"**

El **segundo parámetro** es opcional, por defecto es un string vacío
, define el texto que se coloca en **placeholder=""**

El **tercer parámetro** es opcional, por defecto es un string vacío, define el texto que se coloca de valor

El **cuatro parámetro** es un booleano opcional, por defecto es **false**, al establecer **true** se colocará la etiqueta **required=""**

El **quinto parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista (Plates)
<?= Bootstrap::texatea(\'ejemplo\',\'Escribe algo...\',\'\',false,\'form-input clases extras\') ?>

# Desde la vista (Twig)
{{ texatea(\'ejemplo\',\'Escribe algo...\',\'\',false,\'form-input clases extras\') }}
```

## alert()

Renderiza una alerta


El **primer parámetro** es el mensaje a mostrar en la alerta

El **segundo parámetro** es opcional, por defecto es \'danger\', es el estilo de la alerta, acepta \'info\', \'success\', \'danger\', \'warning\'

El **tercer parámetro** es un booleano opcional, por defecto es **false** si se coloca como true, se añade un botón de cerrar

El **cuatro parámetro** es opcional, por defecto es un string vacío, es el valor que se coloca en la propiedad **id=""**

El **quinto parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista (Plates)
<?= Bootstrap::alert(\'Alerta de <b>peligro</b>\',\'warning\',false,\'id_alerta\',\'clases extras\') ?>

# Desde la vista (Twig)
{{ alert(\'Alerta de <b>peligro</b>\',\'warning\',false,\'id_alerta\',\'clases extras\') }}
```

## paises()

Despliega un select de bootstrap con los países


El **primer parámetro** es el nombre del select que se colocará en la propiedad **name=""** y también en **id="id_"**

El **segundo parámetro** es un booleano opcional, por defecto es **false**, al establecer **true** y se definirá el select  como **múltiple**

El **tercer parámetro** es opcional, y su función es tener todos los valores que deben aparecer seleccionados por defecto en el select, esto tiene dos posibles soluciones, si nuestro select no es múltiple, basta con pasar un **string** que tenga el nombre del value="" seleccionado. En caso de ser un select que sí es múltiple, hay que pasar un **array** con todos los value="" seleccionados.

El **cuarto parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\', $this->template);

# Desde la vista select normal (Plates)
<?= Bootstrap::paises(\'ejemplo\', false, \'241\', \'clases extras\') ?>

# Desde la vista select múltiple (Plates)
<?= Bootstrap::paises(\'ejemplo_multi\', true, [\'241\',\'1\'] , \'clases extras\') ?>

# Desde la vista select normal (Twig)
{{ paises(\'ejemplo\', false, \'241\', \'clases extras\') }}

# Desde la vista select múltiple (Twig)
{{ paises(\'ejemplo_multi\', true, [\'241\',\'1\'] , \'clases extras\') }}
```

## pager()

Renderiza un paginador numérico de la forma **[<< anterior - 1 - 2 - 3 - *4* - 5 - 6 - 7 - siguiente >>]**


El **primer parámetro** Formato de URL de continuidad para el paginador, ejemplo /controlador/otras/rutas/

El **segundo parámetro** Número de páginas totales

El **tercer parámetro** Variable que contiene la página actual, tiene que provenir de $this->route probablemente

El **cuarto parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

El **quinto parámetro** Arreglo que contiene configuración del paginador, es opcional, por defecto es:
```php
array(
    \'anterior\' => \'Anterior\', # Texto del botón \'anterior\'
    \'siguiente\' => \'Siguiente\', # Texto del botón \'siguiente\'
    \'i\' => 4, # Cantidad de números máximos por la izquierda
    \'d\' => 4 # Cantidad de números máximos por la derecha
  );
```

### Ejemplo de uso de un paginador

Vamos a crear un paginador en la vista **home**, por tanto el controlador mencionado abajo será **./core/controllers/homeController.php** y la vista sería **./templates/twig/home/home.twig** ó **./templates/plates/home/home.phtml** en caso de usar PlatesPHP.

```php
# En el controlador

## Creamos una ruta en donde se colocarán los números del paginador
## Si no existe ninguna otra ruta, sabemos que para llegar a /pag debemos
## pasar por /home/metodo/id/1 <- es importante saber esto, si no queda claro
## ir al correspondiente capítulo de rutas
$this->route->setRoute(\'/pag\',\'int\');

## Tengamos en cuenta, que esto debe ser producto del row() de una probable query SELECT
## Para este simple ejemplo, sin manejar bases de datos, pondremos un valor fijo
$paginas_totales = 10;

## Creamos nuestra configuración
## Si queremos que se tengan los valores por defecto, no pasamos este parámetro
## Tengamos en cuenta, los valores del arreglo son totalmente modificables
$config = array(
  \'anterior\' => \'Anteriores\', # Texto del botón \'anterior\'
  \'siguiente\' => \'Siguientes\', # Texto del botón \'siguiente\'
  \'i\' => 4, # Cantidad de números máximos por la izquierda
  \'d\' => 4 # Cantidad de números máximos por la derecha
);

## Cargamos el helper de Bootstrap (si estamos usando PlatesPHP)
Helper::load(\'bootstrap\');

## Cargamos el helper Bootstrap para twig (si estamos usando Twig)
Helper::load(\'bootstrap\',$this->template);

## Pasamos todo a la vista
echo $this->template->render(\'home/home\', array(
  \'link\' => \'home/metodo/id/\',
  \'pag\' => $this->route->getRoute(\'/pag\'),
  \'paginas_totales\' => $paginas_totales,
  \'config\' => $config
));

# En la vista home.twig (si usamos Twig), en algún punto
<div class="container">
	{{ pager(link,paginas_totales,pag,\'\',config) }}
</div>

# En la vista home.phtml (si usamos PlatesPHP), en algún punto:
<div class="container">
  <?= Bootstrap::pager($link,$paginas_totales,$pag,\'\',$config) ?>
</div>

# Jugar con la URL, y pasar entre home/metodo/id/1 hasta home/metodo/id/10 por ejemplo
```

## table()

Renderiza una tabla con contenido


El **primer parámetro** un arreglo con todos los **th**, de la forma
```php
$ths = array(\'titulo 1\', \'titulo 2\', \'titulo 3\');
```

El **segundo parámetro** un arreglo con todos los valores que llenan la tabla, debe ser en forma de matriz, cada posición del arreglo principal corresponderá a un **tr** y cada posición de los arreglos internos un **td**:
```php
$contenido = array(
 array(1,2,3),
 array(4,5,6)
);
```

El **tercer parámetro** es un string opcional, que debe contener todas las clases extras que le queramos aplicar al elemento.

```php
# Desde el controlador (Plates)
Helper::load(\'bootstrap\');

# Desde el controlador (Twig)
Helper::load(\'bootstrap\',$this->template);

# Desde la vista (Plates)
<?= Bootstrap::table($ths,$contenido,\'table-bordered css extra\') ?>

# Desde la vista (Twig)
{{ table(ths,contenido,\'table-bordered css extra\') }}
```
';
      break;

      case 'emails':
        $content = '# Emails

Helper con funciones auxiliares para tratamiento de cadenas emails.

## send_mail()

Se encarga de enviar un correo electrónico utilizando la librería PHPMailer, recibe cinco parámetros y dos de ellos son opcionales.


El **primer parámetro** es un arreglo asociativo con la información de los destinatarios, la forma del mismo es un arreglo asociativo donde los índices asociativos son los emails y los valores corresponden a los nombres de cada uno.
```php
# En caso de que sea un solo destinatario, basta con solo colocar un elemento en el arreglo respetando la estructura.
$ejemplo_del_arreglo = array(
 \'usuario1@email.com\' => \'Usuario 1\',
 \'usuario2@email.com\' => \'Usuario 2\',
 \'usuario3@email.com\' => \'Usuario 3\',
);
```

El **segundo parámetro** es un string que acepta HTML para enviar el correo, es el contenido del correo a enviar.

El **tercer parámetro** es el asunto del correo a enviar.

El **cuarto parámetro** es un valor booleano, opcional, por defecto tiene como valor **true**, esto quiere decir que si está en **true** el correo se enviará utilizando una conexión SMTP a algún servidor SMTP, cuyos datos de acceso se configuran desde **config.php**, en caso de establecer este parámetro como **false**, se utilizará la función **mail()** que asume como servidor de correo el propio servidor en donde corre la aplicación, en general es más rápida la conexión así, pero en localhost no funciona sin previa configuración.

El **quinto parámetro** se utiliza para establecer adjuntos que se quieran enviar con el correo, es un parámetro opcional, si no se pasa nada por aquí, no se enviará ningún adjunto. Para enviar uno o más adjuntos, basta con pasar un arreglo que tenga en cada posición, la ruta en el servidor del archivo a enviar.
```php
$ejemplo_arreglo_adjuntos = array(
 \'ruta archivo 1\',
 \'ruta archivo 2\'
);
```

Para enviar un correo, bastaría con establecer algo como esto:
```php
Helper::load(\'emails\');
$dest[\'usuario1@gmail.com\'] = \'Usuario 1\';
$email = Emails::send_mail($dest,\'<b>Hola usuario 1</b>\',\'El Asunto\');
if(true === $email) {
  # Sí se envió el correo...
} else {
  # No se envió, entonces $email contiene un string con información del motivo por el cual no se envió
}
```

La función retorna **true** cuando el correo fue enviado correctamente, si no ha sido así, retornará un string con información acerca del error informado por PHPmailer.

## plantilla()

Ofrece una plantilla sencilla amigable que contiene compatibilidad con bootstrap, de uso frecuente para mandar correos electrónicos sin muchos detalles pero presentables. Recibe un único parámetro, y es un string que acepta HTML.

Se hace uso de esta función en **./core/models/Lostpass.php**, donde se envía un correo electrónico al usuario que solicita recuperar la contraseña.
```php
Helper::load(\'emails\');
$dest[$mail] = $user[0][\'nombre\'];
$email = Emails::send_mail($dest,Emails::plantilla($HTML),\'Recuperar contraseña perdida\');
```

Retorna un string HTML con la forma:
```markup
<html>
    <head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    </head>
    <body style="font-family: Verdana;">
      <section>
        CONTENIDO PASADO POR EL PARÁMETRO
      </section>
    </body>
</html>
```';
      break;

      case 'arrays':
        $content = '# Arrays

Helper con funciones auxiliares para tratamiento de arreglos.

## get_key_by_index()

Devuelve el índice numérico relacionado con el índice asociativo de un arreglo, por ejemplo.
```php
$arreglo = array(
 \'hola\' => \'mundo\',
 \'que\' => \'haces\',
 \'como\' => \'estás\'
);
```
Cómo hacemos para saber qué número secuencial está asociado al índice **\'que\'**?, vale con solo mirarlo sabemos que es la posición 1, ya que si hacemos **$arreglo[1]** obtendremos el string \'haces\', pero ¿qué pasa si no lo podemos ver? y necesitamos de una función que nos devuelva ese número para poder utilizarlo en algún algoritmo. Pues llamamos al helper arrays y accedemos a esta función que recibe dos parámetros.


El **primer parámetro** es el nombre del índice a averiguar.

El **segundo parámetro** es el arreglo.

```php
# Queremos obtener la posición del arreglo asociada al índice asociativo \'como\'
$n = Arrays::get_key_by_index(\'como\',$arreglo);
# Entonces
$n === 2

# Probamos con un índice inexistente
$m = Arrays::get_key_by_index(\'blabla\',$arreglo);
# Entonces
$n === -1
```

Esta función devolverá un número, que será la posición del arreglo asociada a ése indice asociativo, y devolverá -1 si no consigue el indice dentro del arreglo.

## unique_array()

Elimina todos los elementos repetidos existentes en un arreglo. Recibe un solo parámetro, el arreglo a modificar.
```php
# Original
$arreglo = array(
 \'1\',
  1,
 \'5\',
  6
);
# Modificamos
$arreglo_nuevo = Arrays::unique_array($arreglo);
# Resultado
$arreglo_nuevo = array(
  1,
 \'5\',
  6
);
```
[StackOverflow](http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip)

## is_assoc()

Devuelve **true** si un arreglo es enteramente asociativo y **false** si no lo es, solo recibe un parámetro y es el arreglo a evaluar.
```php
$arreglo = array(
 \'hola\' => \'mundo\',
 \'que\' => \'haces\',
 \'como\' => \'estás\'
);
$resultado = Arrays::is_assoc($arreglo);
# Entonces
$resultado === true

$arreglo = array(
 \'hola\' => \'mundo\',
 \'que\' => \'haces\',
 \'estás\'
);
$resultado = Arrays::is_assoc($arreglo);
# Entonces
$resultado === false
```

## is_numeric_array()

Devuelve **true** si un arreglo es secuencial, y **false** si no lo es, sólo recibe un parámetro y es el arreglo a evaluar.

```php
$arreglo = array(
 \'hola\' => \'mundo\',
 \'que\' => \'haces\',
 \'estás\'
);
$resultado = Arrays::is_numeric_array($arreglo);
# Entonces
$resultado === false

$arreglo = array(
 \'mundo\',
 \'haces\',
 \'estás\'
);
$resultado = Arrays::is_numeric_array($arreglo);
# Entonces
$resultado === true

```

## array_random_element()

Obtiene de forma aleatoria un elemento dentro de un arreglo, si tenemos:
```php
$arreglo = array(\'uno\',\'dos\',\'tres\',\'cuatro\',\'cinco\', \'seis\' => 6, 7, \'blabla\');
# Sacamos un elemento random
$e = Arrays::array_random_element($arreglo);
# Entonces
$e === (\'uno\' xor \'dos\' xor \'tres\' xor \'cuatro\' xor \'cinco\' xor 6 xor 7 xor \'blabla\')
# Es decir, un elemento aleatorio de entre todos los posibles, puede que sea distinto en cada ejecución, es random
```';
      break;

      case 'files':
        $content = '# Files

Helper con funciones auxiliares para tratamiento de archivos.

## read_file()

Lee un archivo y devuelve un string con el contenido del mismo, éste detecta los saltos de línea, pero si tratamos de imprimir con echo evidentemente no saldrán puesto que son caracteres de escape \'\n\', si luego plasmamos su contenido en otro archivo se colocarán correctamente los saltos de línea. Recibe un único parámetro, la ruta del archivo.
```php
# No necesariamente debe ser .txt, puede ser CUALQUIER formato de archivo
$contenido = Files::read_file(\'ruta/archivo.txt\');
```

## write_file()

Escribe sobre un archivo, sobrescribiendo todo su contenido si este existe y empezando a escribir desde cero, si el archivo no existe lo crea. Recibe un solo parámetro y es la ruta del archivo a escribir.
```php
# No necesariamente debe ser .txt, puede ser CUALQUIER formato de archivo
$bytes_escritos = Files::write_file(\'ruta/archivo.txt\');
```
Luego de escribir un archivo, podemos saber cuánta información en **bytes** es lo que ha sido escrito en ese archivo, ésta función devuelve un entero con dicha información siempre.

## get_file_ext()

Obtiene la extensión de un archivo, sin importar cual sea esta. Si el archivo no tiene extensión, devuelve un string vacío. Recibe un único parámetro, el nombre del archivo o la ruta del mismo incluyendo al archivo.
```php
$extension = Files::get_file_ext(\'ruta/archivo.pdf\');
# Entonces
$extension === \'pdf\'

# O simplemente pasando el nombre del archivo sin la ruta.
$extension = Files::get_file_ext(\'archivo.doc\');
# Entonces
$extension === \'doc\'
```

## file_size()

Devuelve el tamaño en Kb de un fichero, recibe como parámetro la ruta del fichero.
```php
$tamanio = Files::file_size(\'./mifichero.png\');
# Entonces
$tamanio === 103 # Suponiendo que pesa 103 Kb
```

## date_file()

Devuelve la fecha y hora exacta de creación de un archivo, recibe como parámetro la ruta del fichero.
```php
$tamanio = Files::date_file(\'./mifichero.png\');
# Entonces
$tamanio === \'01-01-1996 09:10:11\' # Suponiendo que en esa fecha fue creado
```

## is_image()

Devuelve **true** si un archivo es una imagen y **false** si no lo es, solo recibe un parámetro y es el nombre/ruta de la imagen, la función considera como imagen sólo a los formatos ** jpg - png - gif - jpeg**
```php
$resultado = Files::is_image(\'imagen.jpg\');
# Entonces
$resultado === true

# Si queremos podemos utilizarlo en un formulario
$resultado = Files::is_image($_FILES[\'archivo\'][\'name\']);
# Si es imagen lo que se está cargando por el formulario devuelve true, si no, false
$resultado === true xor false
```

## get_files_in_dir()

Obtiene todos los archivos existentes en un directorio en un arreglo secuencial. Recibe como único parámetro la ruta del directorio.
```php
$arreglo = Files::get_files_in_dir(\'views/\');
```
Si no hay archivos o el directorio no existe, devuelve un arreglo vacío.

## rm_dir()

Borra de forma recursiva un directorio completo incluyendo todos los archivos existentes en el sin piedad. Lanza un **\RuntimeException** si por alguna razón no se puede borrar el directorio o un archivo dentro del directorio.
```php
Files::rm_dir(\'directorio/a/borrar/\');
```

## create_dir()

Crea un directorio con permisos **0777** en la ruta indicada.


Recibe como **primer parámetro** la ruta a crear.

Recibe como **segundo parámetro** un integer con los permisos asociados, por defecto es **0777**


Devuelve un valor booleando, siendo **true** si se creó con éxito o **false** si ya existe el directorio o existió algún problema en la creación.

```php
if(Files::create_dir(\'./mi/directorio/\')) {
echo \'Directorio creado correctamente\';
} else {
echo \'Algo pasó\';
}
```

## images_in_dir()

Devuelve la cantidad de imágenes en un directorio (jpg,gif,png,jpeg)


Recibe como único parámetro el directorio a evaluar.
```php
Files::images_in_dir(\'views/app/images/\');
```

## move_from_dir()

Mueve todos los archivos de un directorio a otro.


Recibe como **primer parámetro** la ruta desde donde se moverán los archivos.

Recibe como **segundo parámetro** la ruta hacia donde se moverán los archivo.

Recibe como **tercer parámetro** un boolean que por defecto es **false**, y al declararse **true** sólamente pasará imágenes.

Recibe como **cuarto parámetro** un boolean que por defecto es **false**, y al declararse **true** borrará todos los archivos del directorio viejo.

```php
Files::move_from_dir(\'views/app/old\',\'views/app/new\',false,true);
```

## upload_file()

Carga un fichero desde una ruta temporal (con $_FILES)


Recibe como **primer parámetro** el nombre que se le quiere dar al archivo (Ej: $_FILES[\'nombre\'][\'name\'] ó imagen.jpg)

Recibe como **segundo parámetro** Directorio temporal ($_FILES[\'nombre\'][\'tmp_name\'])

Recibe como **tercer parámetro** Directorio a subir

Recibe como **cuarto parámetro** un boolean que por defecto es **false**, si se pasa **true** sobrescribe archivos con el mismo nombre, si no, copia el archivo de conflicto con un nombre aleatorio para no sobrescribir nada.

```php
Files::upload_file($_FILES[\'nombre\'][\'name\'],$_FILES[\'nombre\'][\'tmp_name\'],\'views/app/files\',true);
```';
      break;

      case 'strings':
        $content = '# Strings

Helper con funciones auxiliares para tratamiento de cadenas de texto.

## amigable_time()

Devuelve con el formato \'Hace X segundos/horas/días/semanas/meses/años\' el tiempo transcurrido en segundos desde un momento a otro, recibe dos parámetros y uno de ellos es opcional.


El **primer parámetro** es el tiempo en segundos desde donde queremos contar cuánto ha pasado.

El **segundo parámetro** es opcional, si se omite, el tiempo pasado por el primer parámetro se contará hasta el tiempo actual, si se pasa el segundo parámetro éste debe ser un tiempo en segundos y será hasta donde contaremos.


Ejemplos:
```php
# Imprime en pantalla \'Hace 1 hora\'
echo Strings::amigable_time(time() - (60*60));

# Imprime en pantalla \'Hace 2 horas\'
echo Strings::amigable_time(time() - (60*60), time() + (60*60));
```

## hash()

Genera un encriptado de **hash fuerte y variable**, es utilizado en el Login/Registro/Lostpass para manejar la seguridad de las contraseñas de los usuarios. Maneja un único parámetro, y es el string a encriptar.
```php
# Imprime algo similar a $2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G
echo Strings::hash(\'123456\');
```

A diferencia de los algoritmos de encriptación tradicionales de PHP estándar, este algoritmo jamás es estático, un mismo string puede generar infinitos hash, por lo que para saber si un string está contenido en un hash hay que utilizar otra función documentada en el punto de abajo.

## chash

Compara un hash dinámico con un string, si la llave en ellos coincide entonces son el mismo el string contenido en el hash es exactamente el mismo string con el que es comparado.

Devuelve **true** si es al compararlos son iguales, **false** si no lo son.
```php
# Comparamos un hash generado con Strings con un string sin hash
$resultado = Strings::chash(\'$2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G\',\'123456\');
# Entonces
$resultado === true

# Comparamos un hash generado con Strings con un string sin hash
$resultado = Strings::chash(\'$2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G\',\'1234567\');
# Entonces
$resultado === false
```

## date_difference()

Calcula la diferencia de tiempo entre dos fechas con el formato dd-mm-YYYY ó dd/mm/YYYY y devuelve la diferencia de tiempo en días.
```php
$dias = Strings::date_difference(\'01-06-2016\',\'02-06-2016\');
# Entonces
$dias === 1
```

## calculate_age()

Calcula la edad de una persona según la fecha de nacimiento (la cantidad de años transcurridas hasta el tiempo actual).
```php
$edad = Strings::calculate_age(\'28-10-1996\');
# Entonces
$edad === 19
```

## days_of_month()

Devuelve los días que tiene el mes actual, tiene en cuenta los años bisiestos con el mes de febrero.
```php
# Suponemos que estamos en Junio
$dias = Strings::days_of_month();
# Entonces
$dias === 30
```

## is_email()

Devuelve **true** si un string cumple con el formato de un email válido y **false** si no lo hace. Maneja un único parámetro, que es la cadena de texto con el email.
```php
$resultado = Strings::is_email(\'prinick@ocrend.com\');
# Entonces
$resultado === true

$resultado = Strings::is_email(\'prinick\');
# Entonces
$resultado === false
```

## remove_spaces()

Remueve todos los caracteres en blanco de un string, solo maneja un parámetro y es el string a transformar.
```php
$string = Strings::remove_spaces(\'Hola mundo\');
# Entonces
$string === \'Holamundo\'
```

## alphanumeric()

Analiza si una cadena de texto es alfanumérica, solo maneja un parámetro y es el string a analizar.
```php
$resultado = Strings::alphanumeric(\'Hol* que suc/de\');
# Entonces
$resultado === false
```

## only_letters()

Analiza que una cadena de texto solamente tenga letras, sólo maneja un parámetro y es el string a analizar.
```php
$resultado = Strings::only_letters(\'Hola mundo 123\');
# Entonces
$resultado === false
```

## letters_and_numbers()

Analiza que una cadena de texto tenga letras o números x2.
```php
$resultado = Strings::letters_and_numbers(\'Hol* que suc/de\');
# Entonces
$resultado === false
```

## url_amigable()

Transforma un string a un formato compatible con URL\'s amigables, sólo maneja un parámetro y es el string a transformar.
```php
$resultado = Strings::url_amigable()(\'Hola cómo estás y qué sucede\');
# Entonces
$resultado === \'hola-como-estas-y-que-sucede\'
```

De forma nativa, está disponible su uso en las vistas. Para llamarlo desde una vista solo debemos hacer:
```php
<html>
...
<?= $this->url_amigable(\'El texto transformar aquí\') ?>
</html>
```

## bbcode()

Transforma el formato BBCode en su equivalente HTML, maneja un único parámetro y es el texto a transformar.
```php
$html = Strings::bbcode(\'[b]hola[/b]\');
# Entonces
$html === \'<b>hola</b>\'
```

## begin_with()

Dice si un string comienza con un caracter especificado.
```php
$result = Strings::begin_with(\'A\',\'Algo\');
# Entonces
$result === true
```

## end_with()

Dice si un string termina con un caracter especificado.
```php
$result = Strings::end_with(\'o\',\'Algo\');
# Entonces
$result === true
```

## contain()

Ver si un string está contenido en otro.
```php
$result = Strings::contain(\'mundo\',\'Hola mundo\');
# Entonces
$result === true
```

## count_words()

Devuelve la cantidad de palabras en un string.
```php
$result = Strings::count_words(\'Hola mundo\');
# Entonces
$result === 2
```';
      break;

      default:
        $content =  '# Helpers
Los **Helpers** son bibliotecas de funciones clasificadas por categorías, éstas se cargan de una manera óptima y sólo las llamaremos cuando las necesitemos.

En la versión **1.0** se ha introducido unos pocos helpers, con el tiempo y apoyo de la comunidad se podrá agregar más Helpers, **para contribuir con un Helper o más funciones para uno ya existente**, puedes dejarlo en comentarios de un commit en github o enviarlos al correo <prinick@ocrend.com> y será colocado en las próximas versiones del Framework con sus respectivos créditos.

**Arrays**, **Bootstrap**, **Emails**, **Files**, **Strings** y **Paypal**, estos se ubican en **./core/kernel/helpers/**

## Cómo cargar un helper para usarlo

Para llamar a un Helper, sólo basta con cargarlo una única vez de la siguiente manera:
```php
# Cargamos por ejemplo, el helper Strings
Helper::load(\'strings\');
```
Y listo, ya podemos acceder a todas las funciones del helper strings, para acceder a una función solamente hay que hacer:
```php
# En cualquier punto del programa después de que se haya cargado previamente el helper
echo Strings::amigable_time( time() - (60*60*24*2) );
# Absolutamente todas las funciones en los helpers, son métodos estáticos
```
El helper de arriba, nos mostraría en pantalla **\'Hace 2 días\'**, esta función **amigable_time** será detallada más abajo en la sección de Strings, pero basta con saber que si le pasamos un tiempo en segundos, nos devolverá cuánto tiempo ha pasado hasta ahora desde ese tiempo con ese formato amigable.


Podemos cargar todos los helpers que necesitemos ocupar.
```php
Helper::load(\'strings\');
Helper::load(\'arrays\');
Helper::load(\'files\');
```
Se puede apreciar que, para cargar el Helper solo hay que escribir en minúsculas preferiblemente, el nombre del archivo del helper ubicado en **./core/kernel/helpers/** que posee el mismo nombre que la clase misma.


Los helpers se pueden usar en cualquier punto del programa con esa simple llamada **sólamente si estamos usando PlatesPHP como motor de plantillas**, en caso de que estemos usando **Twig** la llamada de arriba solo nos da disponibilidad de uso en el controlador.

## Usar un Helper en la vista cuando se usa TWIG

Para poder utilizar en la vista un helper, al momento de hacer su carga, debemos pasar como segundo parámetro al método load, la propiedad template del controlador actual.
```php
# Cargamos por ejemplo, el helper Strings
Helper::load(\'strings\',$this->template);
```

Haciendo esto, por ejemplo para llamar a un método del helper, debemos simplemente escribir.
```php
{{ hash(\'abc123\') }}
```
En la plantilla .twig y estaría listo.
**Esto sólamente aplica para los helpers Arrays, Bootstrap, Files y Strings** ya que si abrimos cada uno de estos helpers, podemos ver que heredan de **Twig_Extension** y al final en el método **getFunctions()** están las funciones que podemos usar desde twig y como llamarlas.

## Cómo añadir una función a un helper

Simplemente vamos al Helper, que están todos ubicados en **./core/kernel/helpers/** abrimos el archivo y creamos la función:
```php
/**
  * ¿Qué hace?
  *
  * @param tipo_de_dato $nombre_de_parametro: ¿para qué se pasa este parámetro?
  *
  * @return tipo_de_dato ¿qué es ese dato que retorna? (si no retorna nada basta poner void)
*/
#( si no devuelve nada, o más de un tipo de dato no colocar nada en \'tipo_dato_devuelto\' )
final public static function mi_funcion(tipo_de_dato $parametro) : tipo_dato_devuelto {
  #... todo el código
}
```

Ahora para utilizar la función, cuando cargamos el helper en donde creamos la función, solo hacemos:
```php
ElHelper::mi_funcion();
```
En caso de que usemos **PlatesPHP** y queramos usar la función en la vista el código anterior funciona, pero no lo hará si nuestro motor es **Twig**, para eso, en el punto de abjo se explica cómo crear un helper en forma de extensión para Twig, si ya nuestro helper tiene esas características, la creación de la función se hace de la misma manera, salvo por un paso extra que es, editar el métood **getFunctions()** sólamente **si estamos usando twig y queremos que esa función esté disponible en la vista**.
```php
public function getFunctions() : array {
	return array(
		#nombre_de_la_funcion_en_twig es el nombre que usaremos desde twig para llamar a la función.
		#mi_funcion es el nombre de la función que cree en el helper, que será ejecutada cuando se llame desde twig a \'nombre_de_la_funcion_en_twig\'
		new Twig_Function(\'nombre_de_la_funcion_en_twig\',array($this,\'mi_funcion\'))
	);
}
```

## Cómo crear mi propio helper

Es importante no salirse del contexto, los helpers son bibliotecas de funciones con una categoría, si creamos un helper llamado XML por ejemplo, eso nos da a entender que lo único que contendrá ese helper, es funciones encargadas de algún manejo relacionado con XML.

Para crear un helper, nos vamos **./core/kernel/helpers/** y colocamos el nombre del archivo igual al nombre del helper, **es importante que la primera letra esté en mayúsculas**.

Por ejemplo creamos **./core/kernel/helpers/MiHelper.php**, este helper **no será usado en twig y no pretendemos usarlo en una vista**
```php
# Seguridad
defined(\'INDEX_DIR\') OR exit(\'Ocrend software says .i.\');

//------------------------------------------------

final class MiHelper {

  #... todas las funciones aquí

}
```
Si en cambio creamos con éste código:
```php
# Seguridad
defined(\'INDEX_DIR\') OR exit(\'Ocrend software says .i.\');

//------------------------------------------------

final class MiHelper extends Twig_Extension {

  #... todas las funciones aquí

	public function getFunctions() : array {
		#... código aquí documentado arriba
	}

	public function getName() : string {
		return \'ocrend_framework_helper_mi_helper\';
	}

}
```
Con ese código, estamos preparando al Helper para que sea **una extensión de Twig** y podamos usar sus métodos en la plantilla de Twig, **esto es sólamente necesario si usamos twig y queremos que las funciones de nuestro helper estén disponible en las vistas.**
Para crear funciones, debemos ver el punto de arriba.


Ahora para utilizar nuestro helper, solo hay que cargarlo cuando lo necesitemos.
```php
Helper::load(\'mihelper\');
MiHelper::funcion_en_el();
```

';
      break;
    }
    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
