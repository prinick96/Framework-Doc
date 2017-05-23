<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class funcionesController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content = '# Funciones

Las funciones en Ocrend Framework deberán ser creadas en la clase **Func**, ubicada en **./core/models/Func.php** cómo métodos estáticos.

Es decir, crear archivos como MiFuncion.php y por cada función que crees debas incluir el archivo que la contiene, es un poco tedioso, por lo cual conviene más crear tu nueva función dentro de la clase Func, como un método estático. Ésta función sólo se cargará cuándo la llames tú, pero con la ventaja de que no necesitas incluirla en una ruta principal o cada vez que la quieras usar.


Se pretende que **ninguna función llame a la base de datos**, que éstas solo sean para cumplir objetivos específicos sin necesidad de una conexión con la base de datos ya que para ésta tarea están los Modelos.


Si buscas tener a la mano algo que de forma global, que llame a la base de datos para realizar X tarea, lo ideal entonces es crearla cómo un método protected en **./core/kernel/Models.php**, y así ésta estará disponible por herencia en cualquiera de tus modelos.

## Creación de funciones

Simplemente hay que ir a **./core/models/Func.php** y añadir un nuevo método estático:
```php
/**
  * ¿Qué hace tu función? es importante documentar!
  *
  * @param tipo_de_dato $parametro: ¿Qué hace este parámetro? (claro, si es que recibe alguno)
  *
  * @return tipo_de_dato_retornado : ¿Qué es eso que retorna y por qué? (claro, si es que retorna algo)
  * En caso de que no retorne nada, coloca @return void
*/
   # Si no retorna nada, no debes colocar nada en tipo_de_dato_retornado
   final public static function mi_funcion(tipo_de_dato $parametro) : tipo_de_dato_retornado  {
       # ... código
   }
```

Ahora, desde cualquier punto de tu aplicación podrás llamarla como
```php
Func::mi_funcion(...);
```

## percent()

Calcula el porcentaje de una cantidad.


Recibe como **primer parámetro** el porcentaje a calcular.

Recibe como **segundo parámetro** la cantidad a la cual se le va a calcular el porcentaje.
```php
echo Func::percent(10,200); # Mostrará en pantalla 20, que es el 10% de 200
```

## convert()

Da unidades de peso a un integer según sea su tamaño asumido en bytes. Recibe como parámetro el peso en bytes.


Retorna un string con el formato 100 kb
```php
echo Func::convert(1024); # Mostrará en pantalla 1 kb
```

## redir()

Redirecciona a otra página, recibe como parámetro el sitio a donde se va a direccionar, si no se pasa ningún parámetro redireccionará a la página principal definida en **URL** dentro de **config.php**

```php
# Alerta: ¡Cuidado se coloca un echo antes de usar esta función!
Func::redir(URL . \'error/\');
```

O a otro sitio externo.
```php
Func::redir(\'http://www.ocrend.com\');
```

## all_full()
La función analiza que todos los datos de un arreglo estén totalmente llenos, como generalmente se recibe información de un formulario por **$_POST** o **$_GET**, éstos son arreglos, si en algún punto queremos que todos los datos recibidos de un formulario $_POST por ejemplo, estén llenos, llamamos a la función y esta nos dirá si están o no todos llenos.
```php
if(Func::all_full($_POST)){
 #... Todos los campos están llenos
} else {
 #... Al menos uno no está lleno
}
```

## emp()
Es un alias de Empty() y más completo, ya que por ejemplo en empty, al recibir un caracter en blanco este no cuenta como "vacío", sin embargo con emp() sí.

Devuelve **true** cuando realmente el contenido de una variable está vacío y **false** cuando no.
```php
Func::emp(\' \'); # Devuelve true
Empty(\' \'); # Devuelve false
```

## e()
Alias de Empty(), pero soporta más de un parámetro. Recibe **infinitos posibles parámetros**, cuando al menos uno está vacío devuelve **true**, y devolverá **false** cuando todos estén llenos. Internamente usa **Func::emp()** por lo que es igual de sensible para cada uno de los parámetros que pasen por el.
```php
Func::e(\'algo\',\'mas\',\'aqui\'); # Devuelve false
Func::e(\'algo\',\' \',\'aqui\'); # Devuelve true
Func::e(\'algo\',\'\',\'aqui\'); # Devuelve true
```

## get_gravatar()

Obtiene el gravatar de un determinado email.

Recibe como **primer parámetro** el email.

Recibe como **segundo parámetro** el tamaño final de la imagen en pixels, sólo un integer y será LxL.

```php
echo \'<img src="\', Func::get_gravatar(\'princk093@gmail.com\',32) , \'" />\';
```

### fecha()

Es un alias de **date() y funciona exactamente igual**, sólo que nos devuelve todas las fechas en español.


Recibe como **primer parámetro** el formato de la fecha [Ver Formatos](http://php.net/manual/es/function.date.php)

Recibe como **segundo parámetro** el tiempo para esa fecha, si se coloca 0 o ningún parámetro, por defecto será **time()**
```php
Func::fecha(\'l d/F/Y\');
```

';
     echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
