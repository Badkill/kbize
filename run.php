#!/usr/bin/env php
<?php
namespace Kbize;

use Symfony\Component\Console\Application;
use Kbize\Console\Command\TaskListCommand;
use Kbize\Console\Helper\AlternateTableHelper;
/* use KbizeCli\Console\Helper\TableWithRowTitleHelper; */

/* // the autoloader */
$loader = require __DIR__ . '/vendor/autoload.php';

$application = new Application();

$helperSet = $application->getHelperSet();
$helperSet->set(new AlternateTableHelper());
/* $helperSet->set(new TableWithRowTitleHelper()); */
$application->setHelperSet($helperSet);

$application->add(new TaskListCommand(
    new KbizeKernelFactory()
));
$application->run();
