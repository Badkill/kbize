#!/usr/bin/env php
<?php
namespace Kbize;

use Kbize\Console\Command\TaskListCommand;
use Symfony\Component\Console\Application;

/* // the autoloader */
$loader = require __DIR__ . '/vendor/autoload.php';

$application = new Application();
$application->add(new TaskListCommand(new \Kbize\Fake\KbizeKernel()));
$application->run();
