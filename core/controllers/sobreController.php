<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class sobreController extends Controllers {

  public function __construct() {
    parent::__construct();
    # Contenido
    $content = '# Introducción
## ¿Qué es Ocrend Framework?

Es un framework sencillo escrito en **PHP 7** que utiliza la arquitectura **MVC** como base de su aplicación en el desarrollo web, adicionalmente pretende acelerar el proceso de desarrollo con unas cuantas herramientas. La curva de aprendizaje es bastante baja, el concepto del framework es ofrecer una arquitectura de sencillo manejo, inclusive para aquellos que jamás han programado utilizando MVC.
## ¿Por qué utilizarlo?

* No requiere manejo de una shell (aunque existe la posibilidad con un pequeño programa escrito en php por consola)
* Es pequeño y de muy fácil aprendizaje
* Es eficiente y seguro
* Fomenta la creación de código limpio, comentado, bien estructurado y eficiente
* Se configura en 2 minutos y se puede empezar a desarrollar con el
* No estás interesado en librerías gigantes como PEAR
* No estás interesado en aprender un framework gigante como Symfony, Laravel o ZendFramework
* No necesitas gestionar rutas usando namespaces o requires/includes, el framework lo hace por tí
* Incluye Slim framework 3 en sus dependencias, para manejo de API REST correctamente configurado
* Soporte de múltiples bases de datos con distintos motores usando PDO **simultáneamente**
  * MySQL 5.1+
  * Oracle
  * PostgreSQL
  * MS SQL
  * SQLite
  * CUBRID
  * Interbase/Firebid
  * ODBC

## Requisitos

Para colocar el framework en producción se requiere un VPS, Dedicado o Hosting que cumpla las siguientes características:

* PHP 7
* APACHE 2';

    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
