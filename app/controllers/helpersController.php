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
 * Controlador helpers/
 *
 * @author Brayan Narváez <prinick@ocrend.com>
*/
  
class helpersController extends Controllers implements IControllers {

    public function __construct(IRouter $router) {
        parent::__construct($router);   
        switch($this->method) {
            case 'arrays':
                echo $this->template->render('helpers/methods/arrays');
            break;
            case 'emails':
                echo $this->template->render('helpers/methods/emails');
            break;
            case 'files':
                echo $this->template->render('helpers/methods/files');
            break;
            case 'strings':
                echo $this->template->render('helpers/methods/strings');
            break;
            case 'functions':
                echo $this->template->render('helpers/methods/functions');
            break;
            default:
                echo $this->template->render('helpers/helpers');
            break;
        }
    }

}