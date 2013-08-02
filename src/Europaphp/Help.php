<?php

namespace Europaphp;
use Europa\Module\ModuleAbstract;

/**
 * Displays information about the available CLI controllers found in routers
 * that implement Europa\Router\RouterInterface.
 */
class Help extends ModuleAbstract
{
  const VERSION = '0.1.0';

  protected $config = [
    'tag' => 'cli'
  ];

  protected $dependencies = [
    'europaphp/main' => '0.1.0'
  ];

  protected $routes = [
    [
      'when' => 'CLI (help|\?)?',
      'call' => 'Europaphp\Help\Controller\Help->cli'
    ]
  ];
}
