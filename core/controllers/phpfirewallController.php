<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class phpfirewallController extends Controllers {

  public function __construct() {
    parent::__construct();
    $content =  '# Firewall

El firewall es una protección de entrada de datos para ataques comunes en aplicaciones web, tales como ataques xss, dos o union sql.


Por defecto viene activado, el framework tiene una carga adicional de aproximadamente **110Kb reales de memoria RAM** consumida por cada usuario que acceda al sitio web.

Para desactivarlo solo tenemos que ir a **./core/config.php**
```php
# Activación del Firewall
define(\'FIREWALL\', false); # Y establecemos en false.
```

Por ejemplo, para activar una acción del framework simplemente escribir en la URL.
```
http://url.com/?variable=*insert
```
Este comportamiento también surge efecto en las conexiones GET que se realizan con la API REST, si en algún punto de nuestra aplicación tenemos una conexión a través de ajax mediante el método GET, cualquier información será analizada por el firewall y si detecta algún posible caracter peligroso detendrá la ejecución y enviará un mensaje.


Además del resultado desplegado en el navegador, en el root del sistema se crea un archivo llamado **__logs__.log** con un historial de registros para estos ataques / posibles ataques obteniendo información acerca del ente que los provocó.


Adicional a esto, ciertas configuraciones en el firewall permiten mandar un correo electrónico de alerta a un email especificado en las configuraciones cada vez que se detecte un ataque y se añada una nueva linea al historial de logs.

## Configuración

```php
const FCONF = array(
    \'WEBMASTER_EMAIL\' => \'test@ocrend.com\', # Email a donde se envían las alertas en caso de aprobar PUSH_MAIL
    \'PUSH_MAIL\' => false, # Si se coloca como TRUE, cada alerta mandará un email a test@ocrend.com
    \'LOG_FILE\' => \'__logs__\', # Nombre del archivo que se genera para los logs
    \'PROTECTION_UNSET_GLOBALS\' => true,
    \'PROTECTION_RANGE_IP_DENY\' => false,
    \'PROTECTION_RANGE_IP_SPAM\' => false,
    \'PROTECTION_URL\' => true,
    \'PROTECTION_REQUEST_SERVER\' => true,
    \'PROTECTION_BOTS\' => true,
    \'PROTECTION_REQUEST_METHOD\' => true,
		# el primer true corresponde al uso de esta protección para la API REST
    # el segundo true corresponde al uso de esta protección para forma de acceso común
    \'PROTECTION_DOS\' => IS_API ? true : true,
    \'PROTECTION_UNION_SQL\' => true,
    \'PROTECTION_CLICK_ATTACK\' => true,
    \'PROTECTION_XSS_ATTACK\' => true,
    \'PROTECTION_COOKIES\' => true,
    \'PROTECTION_COOKIES_LOGS\' => true,
    \'PROTECTION_POST\' => true,
    \'PROTECTION_POST_LOGS\' => true,
    \'PROTECTION_GET\' => true,
    \'PROTECTION_GET_LOGS\' => true,
    \'PROTECTION_SERVER_OVH\' => true,
    \'PROTECTION_SERVER_KIMSUFI\' => true,
    \'PROTECTION_SERVER_DEDIBOX\' => true,
    \'PROTECTION_SERVER_DIGICUBE\' => true,
    \'PROTECTION_SERVER_OVH_BY_IP\' => true,
    \'PROTECTION_SERVER_KIMSUFI_BY_IP\' => true,
    \'PROTECTION_SERVER_DEDIBOX_BY_IP\' => true,
    \'PROTECTION_SERVER_DIGICUBE_BY_IP\' => true,
    \'PROTECTION_ROUTER_STRICT\' => false
   );
```
**El Firewall por defecto viene con PROTECTION_RANGE_IP_DENY & PROTECTION_RANGE_IP_SPAM desactivados:** Al activar alguno, si se detecta un ataque y se configura el envío de un email, se utiliza la función mail() de PHP, por lo que si esto sucede desde un servidor que no tenga configurada la salida de emails no se enviará nunca.

Adicionalmente, abajo de este arreglo se encuentra otro de **mucha importancia**, es la lista de **IP\'s restringidas**
```php
const IPLIST = array(
    # Si los dos primeros dígitos de tu IP, coinciden con alguno de estos, elimínalo.
    \'SERVER_OVH_BY_IP\' => [\'87.98\',\'91.121\',\'94.23\',\'213.186\',\'213.251\'],
    \'DEDIBOX_BY_IP\' => \'88.191\',
    \'DIGICUBE_BY_IP\' => \'95.130\',
    # Si el primer dígito de tu IP, coinciden con alguno de estos, elimínalo.
    \'RANGE_IP_DENY\' => [\'0\', \'1\', \'2\', \'5\', \'10\', \'14\', \'23\', \'27\', \'31\', \'36\', \'37\', \'39\', \'42\', \'46\',
    \'49\', \'50\', \'100\', \'101\', \'102\', \'103\', \'104\', \'105\', \'106\', \'107\', \'114\', \'172\', \'176\', \'177\', \'179\',
    \'181\', \'185\', \'192\', \'223\', \'224\'],
    \'RANGE_IP_SPAM\' => [\'24\', \'186\', \'189\', \'190\', \'200\', \'201\', \'202\', \'209\', \'212\', \'213\', \'217\', \'222\']
  );
```
Al momento de subirlo a nuestro servidor, si vemos algún mensaje similar a
```
Protection SPAM IPs active, this IP range is not allowed.
```
O algo como
```
Protection died IPs active, this IP range is not allowed.
```
Simplemente debemos fijarnos en la IP de nuestro servidor, para saber eso generalmente podemos verlo desde un CPanel o como otra opción, haciendo ping:
```
ping -a miservidor.com
```
Entonces nos tenemos que fijar en el prefijo del IP, si ésta entra de la lista de IP\'s protegidas, sólo hay que eliminarla.


Ejemplo: **24.124.16.1**, el **24** se encuentra en
```php
\'RANGE_IP_SPAM\' => [\'24\', \'186\', \'189\', \'190\', \'200\', \'201\', \'202\', \'209\', \'212\', \'213\', \'217\', \'222\']
```
Solo debemos quitarlo:
```php
\'RANGE_IP_SPAM\' => [\'186\', \'189\', \'190\', \'200\', \'201\', \'202\', \'209\', \'212\', \'213\', \'217\', \'222\']
```

En caso de que aún no se reconozca la IP o no sepamos por alguna razón cual es, con colocar como **false**
```php
 \'PROTECTION_RANGE_IP_DENY\' => false,
 \'PROTECTION_RANGE_IP_SPAM\' => false,
```

## Protección GET, POST, COOKIE
El firewall ofrece cierta protección para variables de tipo GET, POST, COOKIE, cuidando su contenido. Entre todas las protecciones que ofrece, se encuentra la de sanar variables que contiene cualquiera de estos elementos en su contenido:
```php
const CT_RULES = [\'applet\', \'base\', \'bgsound\', \'blink\', \'embed\', \'expression\',
  \'frame\', \'javascript\', \'layer\', \'link\', \'meta\', \'object\', \'onabort\', \'onactivate\',
  \'onafterprint\', \'onafterupdate\', \'onbeforeactivate\', \'onbeforecopy\', \'onbeforecut\',
  \'onbeforedeactivate\', \'onbeforeeditfocus\', \'onbeforepaste\', \'onbeforeprint\',
  \'onbeforeunload\', \'onbeforeupdate\', \'onblur\', \'onbounce\', \'oncellchange\',
  \'onchange\', \'onclick\', \'oncontextmenu\', \'oncontrolselect\',
  \'oncopy\', \'oncut\', \'ondataavailable\', \'ondatasetchanged\', \'ondatasetcomplete\',
  \'ondblclick\', \'ondeactivate\', \'ondrag\', \'ondragend\', \'ondragenter\', \'ondragleave\',
  \'ondragover\', \'ondragstart\', \'ondrop\', \'onerror\', \'onerrorupdate\',
  \'onfilterchange\', \'onfinish\', \'onfocus\', \'onfocusin\', \'onfocusout\',
  \'onhelp\', \'onkeydown\', \'onkeypress\', \'onkeyup\', \'onlayoutcomplete\',
  \'onload\', \'onlosecapture\', \'onmousedown\', \'onmouseenter\', \'onmouseleave\',
  \'onmousemove\', \'onmouseout\', \'onmouseover\', \'onmouseup\',
  \'onmousewheel\', \'onmove\', \'onmoveend\', \'onmovestart\',
  \'onpaste\', \'onpropertychange\', \'onreadystatechange\', \'onreset\',
  \'onresize\', \'onresizeend\', \'onresizestart\', \'onrowenter\', \'onrowexit\',
  \'onrowsdelete\', \'onrowsinserted\', \'onscroll\', \'onselect\', \'onselectionchange\',
  \'onselectstart\', \'onstart\', \'onstop\', \'onsubmit\', \'onunload\',
  \'script\', \'style\', \'title\', \'vbscript\', \'xml\'];
```

Por ejemplo, vamos a suponer que estamos haciendo un blog, y para la creación de una nueva entrada nuestro editor de texto en el blog utiliza **wysiwyg** o simplemente acepta texto en HTML, si entre todo lo que se escriba se encuentran atributos por ejemplo style="unestilo", el firewall lo va a sar, y verificará que "style" no sea potencialmente peligroso. Hará un log que informará de que se realizó una protección POST, GET o COOKIE según sea el caso, si no queremos que se muestren estos logs porque serán acciones frecuentes de nuestro sistema, simplemente debemos colocar en false:
```php
    # Si el log se genera por cookies
    \'PROTECTION_COOKIES_LOGS\' => false,
    # Si el log se genera por post
    \'PROTECTION_POST_LOGS\' => false,
    # Si el log se genera por get
    \'PROTECTION_GET_LOGS\' => false,
```

## Protección estricta para el Router
Por defecto, se dejó claro que el router no filtra ningún tipo de elemento que se pase por las URL amigables ya que estas no son tratadas como variables GET.

Escribir en la URL y accionar:
```
http://url.com/home/?v=<script src="algo.js"></script>
```
Se puede ver que con el ejemplo anterior, hemos activado al Firewall, sin embargo si hacemos
```
http://url.com/home/<script src="algo.js"></script>
```
No veremos ninguna alerta, esto sucede porque por defecto no se filtra ningún elemento a través de URL Amigables a menos que tratemos a esa segunda ruta como $this->method o realicemos un filtro manualmente de $this->route->getMethod() en el controlador. Aunque con cualquiera de los dos, no vamos a obtener una alerta del Firewall.


**Si estamos seguros de que no queremos que ese tipo de datos entre en nuestra aplicación a través de la URL amigable, podemos hacer que el firewall filtre por nosotros las URL amigables para que las revise con la misma intensidad que a las variables de tipo GET**

Es decir, vamos a perder la capacidad de pasar algo como:
```
http://url.com/home/hola*
```
Sin embargo, rutas como estas:
```
http://url.com/home/esto-es-una-url-amigale
```
Seguirán funcionando sin problema, y ya vuelve a recaer en nuestro uso de la ruta correspondiente para tratar esos "-" si es que lo estamos pasando por el método o por el id o por una ruta creada por nosotros.


Para activar la protección estricta para el router, dentro del firewall debemos colocar en **true**:
```php
\'PROTECTION_ROUTER_STRICT\' => true
```

De esa manera, al realizar
```
http://url.com/home/<script src="algo.js"></script>
```
Obtendremos una alerta del firewall como si se tratase de una variable GET.';
     echo $this->template->render('content/content',array(
      'content' => $content
    ));
  }

}

?>
