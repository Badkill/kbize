#!/usr/bin/env php
<?php
namespace Kbize;

use Kbize\Console\Command\TaskListCommand;
use Symfony\Component\Console\Application;
use Kbize\Console\Helper\AlternateTableHelper;
use Kbize\Sdk\HttpKbizeSdk;
use Kbize\Http\GuzzleClient;
use GuzzleHttp\Client;
/* use KbizeCli\Console\Helper\TableWithRowTitleHelper; */

/* // the autoloader */
$loader = require __DIR__ . '/vendor/autoload.php';

$application = new Application();

$helperSet = $application->getHelperSet();
$helperSet->set(new AlternateTableHelper());
/* $helperSet->set(new TableWithRowTitleHelper()); */
$application->setHelperSet($helperSet);

$application->add(new TaskListCommand(
    new \Kbize\RealKbizeKernel(
        new HttpKbizeSdk(
            new GuzzleClient(
                new Client([
                    'base_url' => 'http://localhost:8000',
                ])
            )
        )
    )
));
$application->run();

function kernel()
{
}
