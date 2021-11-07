<?php

namespace smcodes\phpmvc\middlewares;

use smcodes\phpmvc\Application;
use smcodes\phpmvc\exception\ForbiddenException;

class AuthMiddleware extends baseMiddleware
{
    public array $actions = [];

    /**
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }


    public function execute()
    {
        if(Application::isGuest()){
            if(empty($this->actions) || in_array(Application::$app->controller->action , $this->actions)){
                throw new ForbiddenException();
            }
        }
    }
}