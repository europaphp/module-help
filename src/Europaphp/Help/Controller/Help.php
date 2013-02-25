<?php

namespace Europaphp\Help\Controller;
use Europa\Controller\ControllerAbstract;
use Europa\Filter\ClassNameFilter;
use Europa\Fs\Finder;
use Europa\Reflection\ClassReflector;
use LogicException;
use SplFileInfo;

class Help extends ControllerAbstract
{
    const ACTION = 'cli';

    /**
     * Shows the available commands or documentation for a specific command.
     * 
     * @param string $command The command to show the help for. If not specified, this help is shown.
     */
    public function cli($command = null)
    {
        $this->config = $this->service('modules')->get('europaphp/help')->getConfig();

        if ($command) {
            return $this->getCommand($command);
        }
        
        return $this->getAllCommands();
    }

    private function getCommand($command)
    {
        $class  = $this->getClassFromCommand($command);
        $class  = new ClassReflector($class);
        $params = $this->getCommandParams($command);
        $params = $this->sortCommandParams($params);

        return [
            'command'     => $command,
            'description' => $class->getMethod($this->config['action'])->getDocBlock()->getDescription(),
            'params'      => $params
        ];
    }

    private function getAllCommands()
    {
        $classes  = $this->getClassNames();
        $classes  = $this->sortClassNames($classes);
        $commands = $this->getCommands($classes);

        return [
            'commands' => $commands
        ];
    }

    private function getClassNames()
    {
        $classes = [];

        foreach ($this->service('modules') as $module) {
            foreach ($this->config['paths'] as $path) {
                $path = $module->getPath() . '/' . $path;

                $finder = new Finder;
                $finder->is('/\.php$/');
                $finder->in($path);

                foreach ($finder as $file) {
                    $class = $this->formatClassNameFromFile($path, $file);

                    if (is_subclass_of($class, 'Europa\Controller\ControllerInterface')) {
                        if (method_exists($class, self::ACTION)) {
                            $classes[$class] = $class;
                        }
                    }
                }
            }
        }

        foreach ($classes as $class => $command) {
            $command = str_replace($this->config['namespace'], '', $command);
            $command = str_replace(['\\', '_'], ' ', $command);
            $command = strtolower($command);
            $command = trim($command);
            $classes[$class] = $command;
        }

        return $classes;
    }

    private function sortClassNames(array $classes)
    {
        ksort($classes);
        return $classes;
    }

    private function getCommands(array $classes)
    {
        $commands = [];

        foreach ($classes as $class => $command) {
            $class  = new ClassReflector($class);
            $name   = $class->getName();
            $method = $class->getMethod(self::ACTION);

            $commands[$command] = $method->getDocBlock()->getDescription();
        }

        return $commands;
    }

    private function getCommandParams($command)
    {
        $class = $this->getClassFromCommand($command);
        $class = new ClassReflector($class);

        if ($class->hasMethod($this->config['action'])) {
            $method = $class->getMethod($this->config['action']);
        } else {
            throw new LogicException(sprintf('The command "%s" is not valid.', $command));
        }

        $block  = $method->getDocBlock();
        $params = [];

        if ($block->hasTag('param')) {
            foreach ($block->getTags('param') as $param) {
                $params[$param->getName()] = [
                    'type'        => $param->getType(),
                    'description' => $param->getDescription()
                ];
            }
        }

        return $params;
    }

    private function sortCommandParams(array $params)
    {
        ksort($params);
        return $params;
    }

    private function getClassFromCommand($command)
    {
        $filter = new ClassNameFilter;
        $class  = $filter->__invoke($command);
        $class  = __NAMESPACE__ . '\\' . $class;
        return $class;
    }

    private function formatClassNameFromFile($path, SplFileInfo $file)
    {
        $class = substr($file->getRealpath(), strlen($path));
        $class = str_replace(DIRECTORY_SEPARATOR, '\\', $class);
        $class = str_replace('.php', '', $class);
        $class = trim($class, '\\');
        return $class;
    }
}