<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class sesionesController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content = '# Sesiones

El modelo de sesiones se encuentra en **core/models/Sessions.php**, a partir de la **versión 1.1.0** se integró una funcionalidad extra para controlar las sesiones de los usuarios. Básicamente, antes de esto el control de sesión de un usuario era el más básico posible, manteniendo una cookie en el navegador y una variable de sesión en el servidor para mantener a un usuario logeado.

Con la implementación del modelo de sesiones se mejora la funcionalidad de mantener a un usuario conectado, ya que además de manejar una cookie y una variable de sesión, se mantiene la sesión con pequeñas conexiones a la base de datos, sin embargo es opcional, por defecto está activado pero se puede desactivar para prescindir totalmente de el desde **core/config.php**
```php
define(\'DB_SESSION\', false); # Colocando como false el valor de la constante, prescindimos totalmente de este sistema
```

> Cuando este sistema está desactivado, podemos iniciar sesión desde varios navegadores a la vez con un mismo usuario, pero cuando está activo, sólamente podemos acceder desde un único sitio con un mismo usuario, por ejemplo si iniciamos sesión desde google chrome con un usuario, si este **no cierra sesión** o **no ha pasado el tiempo de caducidad de su sesión**, no podríamos iniciar sesión con ese usuario desde firefox por ejemplo.

## generate_session()

Se encarga de generar la sesión, tanto la cookie como el tiempo de caducidad en la base de datos, el cual está definido desde **core/config.php**
```php
define(\'SESSION_TIME\', 18000); # Tiempo de vida para las sesiones 5 horas = 18000 segundos.
```

* Recibe un único parámetro, es el ID del usuario al que se le quiere generar la sesión.
* No retorna nada, pero luego de ser usado tendremos en $_SESSION[SESS_APP_ID] el ID introducido y el tiempo de caducidad de dicha variable de sesión estará modificado en el campo **session** del usuario con ID en cuestión, de la tabla users en la base de datos.

## session_in_use()

Nos indica si un usuario tiene su sesión activa (ha iniciado y está dentro, o simplemente no ha caducado).

* Recibe un único parámetro, y es el ID del usuario al cual se quiere averiguar si tiene su sesión activa.
* Retorna **true** si el usuario en cuestión tiene su sesión activa, **false** si no la tiene.

## check_life()

Analiza la vida de una sesión, si en el proceso se encuentra con que ya ha caducado el tiempo de vida de ésta, la aniquila.

* Recibe un único parámetro, y es un boolean para forzar cualquier posible sesión existente así haya o no caducado, siempre y cuando ésta exista (que exista $_SESSION[SESS_APP_ID])
* No retorna nada, simplemente aniquila $_SESSION[SESS_APP_ID] en caso de que haya caducado la sesión en la base de datos  si ésta existe.';
    echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
