<?php

$this->extend('europaphp/main/layout/cli');

if ($this->context('command')) {
  echo $this->context('description')
    . PHP_EOL
    . PHP_EOL;

  echo $this->helper('cli')->color('Usage', 'cyan')
    . PHP_EOL
    . $this->helper('cli')->color('-----', 'cyan')
    . PHP_EOL
    . PHP_EOL;

  echo '    '
    . $this->helper('cli')->color($_SERVER['PHP_SELF'], 'yellow')
    . ' '
    . $this->context('command');

  if ($this->context('params')) {
    echo ' [options]'
      . PHP_EOL
      . PHP_EOL;
  }

  if ($this->context('params')) {
    echo $this->helper('cli')->color('Options', 'cyan')
      . PHP_EOL
      . $this->helper('cli')->color('-------', 'cyan')
      . PHP_EOL
      . PHP_EOL;

    foreach ($this->context('params') as $index => $param) {
      echo $index + 1
        . '. '
        . $this->helper('cli')->color('`' . $param['name'] . '`', 'green')
        . ' '
        . $this->helper('cli')->color('`' . $param['type'] . '`', 'yellow')
        . ' '
        . $param['description']
        . PHP_EOL;
    }
  }
} else {
  echo 'This following information is generated from your controllers.'
    . PHP_EOL
    . PHP_EOL;

  echo $this->helper('cli')->color('Usage', 'cyan')
    . PHP_EOL
    . $this->helper('cli')->color('-----', 'cyan')
    . PHP_EOL
    . PHP_EOL;

  echo '    '
    . $this->helper('cli')->color($_SERVER['PHP_SELF'] . ' [command] [options]', 'yellow')
    . PHP_EOL
    . PHP_EOL;

  echo 'To see the documentation for a specific command, run:'
    . PHP_EOL
    . PHP_EOL
    . '    ' . $this->helper('cli')->color($_SERVER['PHP_SELF'] . ' --command [command]', 'yellow')
    . PHP_EOL
    . PHP_EOL;

  echo $this->helper('cli')->color('Available Commands', 'cyan')
    . PHP_EOL
    . $this->helper('cli')->color('------------------', 'cyan')
    . PHP_EOL
    . PHP_EOL;

  if ($commands = $this->context('commands')) {
    foreach ($commands as $command) {
      echo '* '
        . $this->helper('cli')->color('`' . $command['command'] . '`', 'green')
        . ' '
        . $command['description']
        . PHP_EOL;
    }
  } else {
    echo 'There are no commands.'
      . PHP_EOL;
  }

  echo PHP_EOL;
  echo $this->helper('cli')->color('1.', 'cyan')
    . ' To author your own command, simply create a controller and annotate it with a '
    . $this->helper('cli')->color('`@cli`', 'green')
    . ' tag.'
    . PHP_EOL;
  echo $this->helper('cli')->color('2.', 'cyan')
    . ' To document commands, just update the doc blocks of the action you want to document.';
}
