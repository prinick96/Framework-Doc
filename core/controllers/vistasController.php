<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class vistasController extends Controllers {

  public function __construct() {
    parent::__construct();
    switch ($this->method) {
      case 'plates':
        $content = '
# PlatesPHP
Se explica de forma extendida el uso del motor de plantillas **PlatesPHP** dentro del framework como vista.

## Pasar variables a la vista
Podemos pasar variables a nuestra vista para su uso, éstas pueden contener **cualquier cosa**, pueden ser objetos, arreglos, matríces, cubos, cubos dentro de cubos, enteros, booleanos, strings, flotantes, etc.
Para ello sólo hemos de pasarlos dentro de un arreglo desde nuestro controlador, vamos a tomar de ejemplo la vista **home.phtml** y el controlador **homeController**, abrimos el controlador del home y vamos al a línea en la que se renderiza la vista.
```php
/*
 * render() recibe dos parámetros, el primero es la vista a renderizar, y el segundo es un arreglo con las variables
 * que deseamos pasar a nuestra vista, ya sabemos qué contenido pueden tener las mismas.
*/
echo $this->template->render(\'home/home\', array(
  \'mivariable\' => \'Mundo\'
));
```
Ahora desde **home/home.phtml** podemos simplemente escribir en algún punto de nuestro HTML lo siguiente:
```php
Hola <?= $mivariable ?>
```
Esto nos imprimirá en pantalla **Hola Mundo**. Se puede notar que el nombre del índice asociativo que coloquemos en el arreglo será el nombre de la variable que tenemos que usar en la vista.
Podemos pasar cuantas variables queramos:
```php
echo $this->template->render(\'home/home\', array(
  \'mivariable\' => \'Mundo\',
  \'un_arreglo\' => array(\'a\' => 1, \'b\' => 2),
  \'entero\' => 1,
  \'booleana\' => true,
  \'flotante\' => 1.5,
  # Para que esta instrucción no de error, obviamente debe existir Miclase en algún sitio
  \'objeto\' => new Miclase
));
```
Podríamos hacer cosas como estas:
```php
Hola <?=  $mivariable ?>
```
```php
En el arreglo <?= $un_arreglo[\'a\'] ?>
```
```php
Uno mas dos es <?= $entero + 2 ?>
```
Simplemente usamos el [operador ternario](http://php.net/manual/es/language.operators.comparison.php) sobre la variable
```php
¿Es verdad?: <?= $booleana ? \'Sí\' : \'No\' ?>
```
```php
Uno coma cinco por 10 es <?= $flotante * 10 ?>
```
```php
# Para que esta instrucción no de error obviamente test() debe ser un método de Miclase
En el objeto que estamos instanciando, el metodo test(): <?= $objeto->test() ?>
```
Si en algún momento queremos escapar HTML, podemos hacer algo cómo
```php
<?= $this->e(\'<b>no saldré en negrilla</b>\') ?>
```
## Estructuras selectivas (if)
Se manejan exactamente de la misma manera, solo cambia un poco la sintaxis ya que se utiliza la [sintaxis alternativa](http://php.net/manual/en/control-structures.alternative-syntax.php) de PHP para estructuras de control.
### If():
Podemos hacer uso del condicional IF y evaluar expresiones tal como lo hacemos en PHP, tomemos de ejemplo:
```php
<?php if(true): ?>
  Hola es verdadero
<?php else: ?>
  Hola es falso
<?php endif ?>
```
Para trabajar con else if, sería algo como:
```php
<?php if(1 == 2): ?>
  1 es igual a 2
<?php elseif(2 == 2): ?>
  2 es igual a 2
<?php else: ?>
  ¿Qué?
<?php endif ?>
```
Todo esto, evidentemente se puede utilizar con cualquier variable que pasemos a la vista al momento de renderizar.
## Estructuras repetitivas (for, foreach, while)
Todas mantienen exactamente el mismo comportamiento pero se utilizan con una sintaxis un poquito distinta pero aún así familiar y de fácil adaptación si nunca se ha usado.
### For():
El elemental for, se utiliza de la siguiente manera:
```php
<ul>
  <?php for($x = 0; $x <= 10; $x++): ?>
     <li><?= $x ?></li>
  <?php endfor ?>
</ul>
```
### While():
El esencial while, se puede utilizar así:
```php
<ul>
   <?php $x = 0 ?>
   <?php while ($x <= 10): ?>
      <li><?= $x++ ?></li>
   <?php endwhile ?>
</ul>
```
Si no queremos que se imprima mientas se incrementa el **$x++**, en vez de utilizar **<?= ?>** usamos **<?php ?>**
### Foreach():
Para utilizar el foreach, necesitamos iterar sobre un arreglo o sobre un objeto que se pueda iterar.
```php
<ul>
  <?php $arreglo = array(\'a\',\'b\',\'c\',\'d\') ?>
  <?php foreach($arreglo as $a): ?>
      <li><?= $a ?></li>
  <?php endforeach ?>
</ul>
```
## Tratar arreglos desde la vista
Primero que nada, pasemos unos cuantos arreglo a la vista:
```php
echo $this->template->render(\'home/home\', array(
  \'un_arreglo\' => array(\'a\' => 1, \'b\' => 2),
  \'otro_arreglo\' => array(\'uno\',\'dos\')
));
```
Para llamarlos desde la vista, lo debemos hacer exactamente igual que en PHP.
```php
<?= $un_arreglo[\'b\'] ?>
<?= $otro_arreglo[0] ?>
```
Para iterar sobre un arreglo:
```php
<?php foreach($un_arreglo as $a): ?>
  <p><?= $a ?></p>
<?php endforeach ?>
```
Veamos como iterar sobre los elementos que hemos traído de una base de datos:
```php
# Desde nuestro modelo tenemos
public function mimetodo() {
  return $this->db->select(\'*\',\'users\');
}
# Desde nuestro controlador hacemos ( obviamente debe existir el modelo Mimodelo, con el método mimetodo() dentro )
$e = new Mimodelo;
echo $this->template->render(\'home/home\', array(
  \'usuarios\' => $e->mimetodo()
));
# Desde nuestra vista en home/home, simplemente iteramos como un arreglo más (en este caso matriz)
<?php foreach($usuarios as $u): ?>
<ul>
  <li>Id: <?= $u[\'id\'] ?></li>
  <li>Email: <?= $u[\'email\'] ?></li>
  <li>Usuario: <?= $u[\'user\'] ?></li>
</ul>
<br />
<?php endforeach ?>
```
## Usar helpers y funciones desde la vista
Para utilizar un helper, este **debe ser previamente cargado desde el controlador que renderiza la vista**, para ver como cargar un helper, ir a la documentación acerca de los [Helpers](http://framework.ocrend.com/helpers)
```php
# Desde nuestro controlador
Helper::load(\'files\');
echo $this->template->render(\'home/home\');
# Desde la vista
La extensión es <?= Files::get_file_ext(\'hola.jpg\') ?>
```
Para utilizar una función, simplemente bastaría con hacer desde la vista:
```php
<img src="<?= Func::get_gravatar(\'princk093@gmail.com\',50) ?>" />
```
## Acceder a variables nativas globales y funciones nativas de PHP
Podemos en todo momento, acceder a **cualquier** función/variable nativa de PHP, ejemplos:
```php
<?= $_SESSION[\'misesion\'] ?>
<?= $_GET[\'variableget\'] ?>
<?= $_POST[\'variablepost\'] ?>
<?= $_FILES[\'archivo\'][\'name\'] ?>
<?= $_SERVER[\'REMOTE_ADDR\'] ?>
<?= strtolower(\'HOLA QUE HAY\') ?>
<?php if(is_numeric(\'1\')): ?>
  Sí
<?php endif ?>
```
## Incluir otras vistas desde nuestra vista
Si abrimos **home/home.phtml** podemos notar que hay ciertas instrucciones similares a esta:
```php
<?= $this->insert(\'overall/header\') ?>
```
Con ésto, básicamente estoy incluyendo al archivo que se encuentra en *./templates/plates/overall/header.phtml** y plasmando su contenido en nuestra vista. Si abrimos ese archivo se puede apreciar que contiene HTML con el <head> de nuestra aplicación y el inicio de la declaración de la etiqueta <html>, nosotros podemos estructurar en módulos los archivos de nuestro HTML para que si tenemos alguno que se tenga que reutilizar en todas las páginas, lo creemos una sola vez y lo incluimos directamente en todas las páginas que lo requieran. Por lo que si editamos este archivo, surgirá efecto la modificación en todas las vistas que lo incluyan.
Hagamos la prueba, creemos un archivo en  **./templates/plates/overall/hola.phtml** y coloquemos este contenido:
```markup
<p>Hola que pasa</p>
```
Ahora desde cualquier vista, vamos a incluirlo:
```php
<?= $this->insert(\'overall/hola\') ?>
```
De hecho podemos incluirlo todas las veces que queramos:
```php
<?= $this->insert(\'overall/hola\') ?>
<?= $this->insert(\'overall/hola\') ?>
<?= $this->insert(\'overall/hola\') ?>
<?= $this->insert(\'overall/hola\') ?>
```
Y veremos como se muestra el contenido desde nuestra vista.
Pero hasta allí no llega, **podemos tener plantillas dentro de las plantillas**, veamos un ejemplo muy sencillo y enseguida observaremos el potencial.
En  **./templates/plates/overall/hola.phtml** y coloquemos este contenido:
```php
Hola <?= $nombre ?>
```
Y desde cualquier vista en donde queramos incluir a **hola.phtml** hagamos:
```php
<?= $this->insert(\'overall/hola\', array(\'nombre\' => \'Ocrend\') ) ?>
```
Saldrá en pantalla **Hola Ocrend**.
## Crear una vista usando el generador
Simplemente con escribir el comando
```php
php gen.php v MiVista
```
Esto generará en **./templates/plates/mivista/mivista.phtml** el siguiente contenido:
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
El contenido de la vista generada, la plantilla puede ser modificada si se edita el fichero **./generator/plates/v.g** siendo esto extremadamente útil, para cuando ya tenemos un diseño web definido en nuestra aplicación, si queremos que el generador escriba por nosotros una vista con el diseño web que tenemos definido sólo editamos el molde en **v.g**, y en **va.g**, éste último se genera cuando hacemos contenido con la API REST.
## Obtener mayor información
El motor de plantillas que se utiliza es **Plates PHP**, se puede encontrar extensa documentación desde YouTube y desde su página oficial en [Plates](http://platesphp.com/)';
      break;

      case 'twig':
        $content ='
# Twig

Se explica de forma extendida el uso del motor de plantillas **Twig** dentro del framework como vista.

## Pasar variables a la vista
Podemos pasar variables a nuestra vista para su uso, éstas pueden contener **cualquier cosa**, pueden ser objetos, arreglos, matríces, cubos, cubos dentro de cubos, enteros, booleanos, strings, flotantes, etc.
Para ello sólo hemos de pasarlos dentro de un arreglo desde nuestro controlador, vamos a tomar de ejemplo la vista **home.twig** y el controlador **homeController**, abrimos el controlador del home y vamos al a línea en la que se renderiza la vista.
```php
/*
  * render() recibe dos parámetros, el primero es la vista a renderizar, y el segundo es un arreglo con las variables
  * que deseamos pasar a nuestra vista, ya sabemos qué contenido pueden tener las mismas.
*/
echo $this->template->render(\'home/home\', array(
	\'mivariable\' => \'Mundo\'
));
```
Ahora desde **home/home.twig** podemos simplemente escribir en algún punto de nuestro HTML lo siguiente:
```javascript
Hola {{ mivariable }}
```
Esto nos imprimirá en pantalla **Hola Mundo**. Se puede notar que el nombre del índice asociativo que coloquemos en el arreglo será el nombre de la variable que tenemos que usar en la vista.
Podemos pasar cuantas variables queramos:
```php
echo $this->template->render(\'home/home\', array(
	\'mivariable\' => \'Mundo\',
	\'un_arreglo\' => array(\'a\' => 1, \'b\' => 2),
	\'entero\' => 1,
	\'booleana\' => true,
	\'flotante\' => 1.5,
	# Para que esta instrucción no de error, obviamente debe existir Miclase en algún sitio
	\'objeto\' => new Miclase
));
```
Podríamos hacer cosas como estas:
```javascript
	Hola {{ mivariable }}
```
```javascript
En el arreglo {{ un_arreglo.a }} ó también {{ un_arreglo[\'a\'] }}
```
```javascript
	Uno mas dos es {{ entero + 2 }}
```
Simplemente usamos el [operador ternario](http://php.net/manual/es/language.operators.comparison.php) sobre la variable
```javascript
	¿Es verdad?: {{ booleana ? \'Sí\' : \'No\' }}
```
```javascript
  Uno coma cinco por 10 es {{ flotante * 10 }}
```
```javascript
//Para que esta instrucción no de error obviamente test() debe ser un método de Miclase
En el objeto que estamos instanciando, el metodo test(): {{ objeto.test() }}
```
Si en algún momento queremos escapar HTML, podemos hacer algo cómo
```php
	{{ \'<b>no saldré en negrilla</b>\'|e }}
```
## Definir elementos dentro de la plantilla
Para definir una variable
```javascript
{% set miVariable = 1 %}
```
Para definir una variable que contenga una sección de código entero
```javascript
{% set variable %}
	<div>
		<p>Esto es HTML</p>
	</div>
{% endset %}
```
Definir un arreglo secuencial
```javascript
{% set miArreglo = [\'Hola\',\'Mundo\'] %}
```
Definir un arreglo asociativo
```javascript
{% set miArreglo = {\'indice\':\'valor\'} %}
```

## Estructuras selectivas (if)
Se manejan exactamente de la misma manera, sólo cambia la sintaxis y es el único condicional que permite twig.

Podemos hacer uso del condicional IF y evaluar expresiones tal como lo hacemos en PHP, tomemos de ejemplo:
```javascript
{% if true %}
	Hola es verdadero
{% else %}
  Hola es falso
{% endif %}
```
Para trabajar con else if sería
```javascript
{% if (1 + 1) == 0 %}
	1 + 1 es igual a 0 en (Z2,+)
{% elseif (1 + 1) == 2 %}
	2 = 2
{% else %}
	¿Qué puede ser?
{% endif %}
```
Todo esto, evidentemente se puede utilizar con cualquier variable que pasemos a la vista al momento de renderizar.

## Operadores lógicos
Son sólamente tres:
```php
{{ A and B }} // TRUE si tanto A como B son TRUE
{{ A or B }} // TRUE cualquiera de A o B es TRUE
{{ not A }} // TUE si A es FALSE
```
En caso de querer hacer un XOR
```php
{{ (A or B) and not (A and B) }} // TRUE si A ó B es TRUE, pero no si ambos son TRUE o FALSE
```

## Comparadores lógicos
Los típicos comparadores lógicos, excepto los particulares de PHP como ===, <>, !==, ?? y <=>
```php
{{ A == B }} // A igual que B
{{ A != B }} // A distinto que B
{{ A < B }} // A menor que B
{{ A > B }} // A mayor que B
{{ A <= B }} // A menor o igual que B
{{ A >= B }} // A mayor o igual que B
```

## Estructuras repetitivas
En twig sólo existe una estructura repetitiva, el for, pero es bastante dinámico ya que lo podemos utilizar como un foreach, for típico o un while.
```javascript
// For desde el 0 al 10, repite 11 veces
{% for i in 0..10 %}
	{{ i }}<br />
{% endfor %}
```
```javascript
// Foreach, iterando sobre el arreglo llamado "elemento".
{% for e in elemento %}
	{{ e }}<br />
{% endfor %}
```
```javascript
// Foreach, iterando sobre el arreglo llamado "elemento" y mostrando sus índices.
{% for key, e in elemento %}
	La key es {{ key }} y el valor es {{ e }}
{% endfor %}
```
```javascript
// For con else
{% for e in elemento %}
	{{ e }}<br />
{% else %}
	<p>No hay nada en el arreglo elemento.</p>
{% endfor %}
```
```javascript
// Sólamente itera elemento, si elemento está definido. (isset)
{% for e in elemento if elemento is defined %}
	{{ e }}<br />
{% endif %}
```

## Tratar arreglos desde la vista
Primero que nada, pasemos unos cuantos arreglo a la vista:
```php
echo $this->template->render(\'home/home\', array(
	\'un_arreglo\' => array(\'a\' => 1, \'b\' => 2),
	\'otro_arreglo\' => array(\'uno\',\'dos\')
));
```
Para llamarlos desde la vista, lo debemos hacer exactamente igual que en PHP.
```javascript
{{ un_arreglo[\'b\'] }} ó {{ un_arreglo.b }}
{{ otro_arreglo[0] }}
```
Para iterar sobre un arreglo:
```javascript
{% for a in un_arreglo %}
	<p>{{ a }}</p>
{% endfor %}
```
Veamos como iterar sobre los elementos que hemos traído de una base de datos:
```php
# Desde nuestro modelo tenemos
public function mimetodo() {
	return $this->db->select(\'*\',\'users\');
}

# Desde nuestro controlador hacemos ( obviamente debe existir el modelo Mimodelo, con el método mimetodo() dentro )
$e = new Mimodelo;
echo $this->template->render(\'home/home\', array(
	\'usuarios\' => $e->mimetodo()
));

# Desde nuestra vista en home/home, simplemente iteramos como un arreglo más (en este caso matriz)
{% for u in usuarios %}
	 <ul>
		<li>Id: {{ u.id }}</li>
		<li>Email: {{ u.email }}</li>
		<li>Usuario: {{ u.user}} </li>
	</ul>
	<br />
{% endfor %}
```

## Usar helpers y funciones desde la vista
Para utilizar un helper, este **debe ser previamente cargado desde el controlador que renderiza la vista**, para ver como cargar un helper, ir a la documentación acerca de los [Helpers](http://framework.ocrend.com/helpers)
```php
# Desde nuestro controlador
Helper::load(\'files\',$this->template);
echo $this->template->render(\'home/home\');

# Desde la vista
La extensión es {{ get_file_ext(\'hola.jpg\') }}
```
Para utilizar una función creada en Func, simplemente bastaría con hacer desde la vista:
```php
	<img src="{{ get_gravatar(\'princk093@gmail.com\',50) }}" />
```

## Acceder a variables nativas globales de PHP y del framework
Podemos acceder a cualquier variable global definida en la clase Controllers con addGlobal:
```javascript
{{ session.mi_sesion }}
{{ get.mi_variable_get }}
{{ post.mi_variable_post }}
{{ route.getRoute(\'/miRuta\') }}
```

## Definir variables globales
Debemos ir a **./core/kernel/Controllers.php** y añadir debajo de:
```php
$this->template->addGlobal(\'route\', $this->route);
```
Colocar:
```php
$this->template->addGlobal(\'mivariable\', $mivariable);
```
Y ya podríamos acceder por defecto desde cualquier .twig como:
```javascript
{{ mivariable }}
```

## Incluir otras vistas desde nuestra vista
Si abrimos **home/home.twig** podemos notar que hay ciertas instrucciones similares a esta:
```javascript
{{ include \'overall/header\' }}
```
Con ésto, básicamente estoy incluyendo al archivo que se encuentra en *./templates/twig/overall/header.twig** y plasmando su contenido en nuestra vista. Si abrimos ese archivo se puede apreciar que contiene HTML con el <head> de nuestra aplicación y el inicio de la declaración de la etiqueta <html>, nosotros podemos estructurar en módulos los archivos de nuestro HTML para que si tenemos alguno que se tenga que reutilizar en todas las páginas, lo creemos una sola vez y lo incluimos directamente en todas las páginas que lo requieran. Por lo que si editamos este archivo, surgirá efecto la modificación en todas las vistas que lo incluyan.
Hagamos la prueba, creemos un archivo en  **./templates/twig/overall/hola.twig** y coloquemos este contenido:
```markup
  <p>Hola que pasa</p>
```
Ahora desde cualquier vista, vamos a incluirlo:
```javascript
{{ include \'overall/hola\' }}
```
De hecho podemos incluirlo todas las veces que queramos:
```javascript
{{ include \'overall/hola\' }}
{{ include \'overall/hola\' }}
{{ include \'overall/hola\' }}
{{ include \'overall/hola\' }}
{{ include \'overall/hola\' }}
```
Y veremos como se muestra el contenido desde nuestra vista.

## Crear una vista usando el generador
Simplemente con escribir el comando
```php
	php gen.php v MiVista
```
Esto generará en **./templates/twig/mivista/mivista.twig** el siguiente contenido:
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
El contenido de la vista generada, la plantilla puede ser modificada si se edita el fichero **./generator/twig/v.g** siendo esto extremadamente útil, para cuando ya tenemos un diseño web definido en nuestra aplicación, si queremos que el generador escriba por nosotros una vista con el diseño web que tenemos definido sólo editamos el molde en **v.g**, y en **va.g**, éste último se genera cuando hacemos contenido con la API REST.

## Obtener mayor información
El motor de plantillas que se utiliza es **Twig**, se puede encontrar extensa documentación desde [YouTube](https://www.youtube.com/playlist?list=PLDQZoQpLCoUDvafL7aERl1Mt4Xd_PPulK) orientada a ocrend framework, y desde su página oficial en [Twig Sensiolabs](http://twig.sensiolabs.org/)';
      break;
      
      default:
        $content = '
# Vistas

Las vistas son la parte no lógica de nuestra aplicación, aquella que contiene todo referente a diseño web, más que todo el HTML propio, previamente hemos visto el funcionamiento de los [Controladores](http://framework.ocrend.com/controladores) y sabemos que desde allí podemos renderizar una vista utilizando la propiedad **$this->template**
```php
	echo $this->template->render(\'carpeta/archivo\');
```

La extensión de los archivos va a depender mucho del **motor de plantilla** que estemos utilizando, a partir de la versión 1.2 el framework incorpora dos motores de plantillas para usar a nuestro antojo.

Para elegir cual utilizar, debemos ir a **core/config.php** y modificar el valor de la constante:
```php
define(\'USE_TWIG_TEMPLATE_ENGINE\', false);
```
Un valor de **true** significa que usaremos **TWIG** y un valor de **false** significa que usaremos **PlatesPHP**.

## Plates
Plates es un sistema de plantilla PHP nativo que es rápido, fácil de usar y fácil de extender. Está inspirado en el excelente motor de plantillas de Twig y se esfuerza por brindar funcionalidad de lenguaje de plantillas moderna a plantillas nativas de PHP.

Plates está diseñado para desarrolladores que prefieren utilizar plantillas PHP nativas sobre lenguajes de plantillas compilados, como Twig o Smarty.

### Algunas características
* Plantillas nativas de PHP, sin nueva sintaxis para aprender
* Plates es un sistema de plantilla, no un lenguaje de plantilla
* Plates fomenta el uso de funciones PHP existentes
* Aumentar la reutilización de código con diseños de plantillas y herencia
* Compartición de datos entre plantillas
* Preasignar datos a plantillas específicas
* Ayuda de escape integrada
* Fácil de ampliar utilizando funciones y extensiones
* El diseño desacoplado hace que las plantillas sean fáciles de probar

### Localización de templates
Todas las plantillas están ubicadas en **./templates/plates/** y son de extensión **.phtml**

## Twig
Twig es un motor de plantilla para el lenguaje de programación PHP. Su sintaxis origina de Jinja y las plantillas Django.

### Algunas características
* Twig compila las plantillas hasta el código PHP optimizado. La sobrecarga en comparación con el código PHP normal se redujo al mínimo.
* Twig tiene un modo de sandbox para evaluar el código de plantilla no confiable. Esto permite que Twig se utilice como un lenguaje de plantilla para aplicaciones en las que los usuarios puedan modificar el diseño de la plantilla.
* Twig es alimentado por un lexer y parser flexible. Esto permite al desarrollador definir sus propias etiquetas y filtros personalizados y crear su propia DSL.

### Localización de templates
Todas las plantillas están ubicadas en **./templates/twig/** y son de extensión **.twig**

';
      break;
    }
    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
