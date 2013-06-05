<?php

namespace Europaphp\Help;
use Europa\Fs\Locator;
use Europa\Router\Router;
use Europa\Module\Bootstrapper\BootstrapperAbstract;

class Bootstrapper extends BootstrapperAbstract
{
    public function routes()
    {
        $router = new Router;
        $router->import($this->module->path() . '/configs/routes.json');
        $this->container('routers')->append($router);
    }

    public function views()
    {
        $locator = new Locator;
        $locator->addPath($this->module->path() . '/views');
        $this->container('viewLocators')->append($locator);
    }
}