<?php

namespace Europaphp\Help\Controller;
use Europa\Module;
use Europa\Reflection;
use Europa\Request;
use Europa\Router;

class Help
{
  private $config;

  private $modules;

  private $router;

  private $controllerResolver;

  public function __construct(
    Module\ManagerInterface $modules,
    callable $router,
    callable $controllerResolver,
    \ArrayIterator $routers
  ) {
    $this->config = $modules->get('europaphp/help')->config();
    $this->modules = $modules;
    $this->router = $router;
    $this->controllerResolver = $controllerResolver;
    $this->routers = $routers;
  }

  /**
   * Shows the available commands or documentation for a specific command.
   *
   * @cli
   *
   * @param string $command The command to show the help for. If not specified, this help is shown.
   */
  public function cli($command = null)
  {
    if ($command) {
      return $this->getCommand($command);
    }

    return [
      'commands' => $this->getAllCommands()
    ];
  }

  private function getCommand($command)
  {
    $request = new Request\Cli;
    $request->setCommand($command);

    if ($controller = call_user_func($this->router, $request)) {
      return $this->resolveCommand($command, $controller);
    }
  }

  private function getAllCommands()
  {
    $commands = [];

    foreach ($this->routers as $router) {
      if ($router instanceof Router\RouterInterface) {
        foreach ($router->routes() as $pattern => $controller) {
          if ($command = $this->resolveCommand($pattern, $controller)) {
            $commands[] = $command;
          }
        }
      }
    }

    return $commands;
  }

  private function resolveCommand($command, $controller)
  {
    $controller = new Reflection\CallableReflector($controller);
    $docblock = $controller->getReflector()->getDocBlock();

    if (!$docblock->hasTag($this->config['tag'])) {
      return;
    }

    $params = [];

    foreach ($docblock->getTags('param') as $tag) {
      $params[] = [
        'name' => $tag->getName(),
        'type' => $tag->getType(),
        'description' => $tag->getDescription()
      ];
    }

    return [
      'command' => $command,
      'description' => $docblock->getDescription(),
      'params' => $params
    ];
  }
}