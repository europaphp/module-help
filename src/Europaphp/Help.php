<?php

namespace Europaphp;
use Europa\Module\ModuleAbstract;

class Help extends ModuleAbstract
{
    const VERSION = '0.1.0';

    protected $config = [
        'actions' => ['cli'],
        'paths'   => ['src']
    ];

    protected $dependencies = [
        'europaphp/main' => '0.1.0'
    ];
}