<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace app\controllers;

use app\models as Model;
use Ocrend\Kernel\Router\IRouter;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
  
/**
 * Controlador modelos/
 *
 * @author Brayan Narváez <prinick@ocrend.com>
*/
  
class modelosController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router);   
        switch($this->method) {
            case 'caracteristicas':
                switch($router->getId()) {
                    case 'database':
                        echo $this->template->render('modelos/caracteristicas/database');
                    break;
                    default:
                        global $config;
                        $this->functions->redir($config['site']['url'] . '/modelos');
                    break;
                }
            break;
            default:
		        echo $this->template->render('modelos/modelos');
            break;
        }
    }

}