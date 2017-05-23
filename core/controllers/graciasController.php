<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class graciasController extends Controllers {

  public function __construct() {
    parent::__construct();
    echo $this->template->render('gracias/gracias');
  }

}

?>
