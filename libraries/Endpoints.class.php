<?php

namespace Libraries\Endpoints;

use Libraries\BuriPHP\Router;

/**
 * Clase Endpoints que extiende de Router.
 * 
 * Esta clase se utiliza para definir y manejar los endpoints de la aplicación.
 * 
 * @package Libraries
 * @author Kiske
 * @since 2.0Alpha
 * @version 1.1
 * @license You can see LICENSE.txt
 * @copyright Copyright (C) CodeMonkey - Platform. All Rights Reserved.
 */
class Endpoints extends Router
{
    /**
     * Define los endpoints disponibles para los módulos de la aplicación.
     * 
     * Este método agrega los endpoints necesarios para cada módulo específico
     * utilizando el método `addForModule`.
     * 
     * @return void
     */
    public function endpoints()
    {
        // $this->addForModule('SetSettings');
        $this->addForModule('Authentication');
        $this->addForModule('Translate');
        $this->addForModule('User');
        $this->addForModule('Subscription');
        $this->addForModule('Pages');
    }
}
