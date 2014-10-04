#!/usr/bin/env php
<?php
namespace Kbize;

use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Kbize\Console\Command\TaskListCommand;
use Kbize\Console\Helper\AlternateTableHelper;
use Kbize\Http\GuzzleClient;
use GuzzleHttp\Client;
use Kbize\Sdk\HttpKbizeSdk;
use Kbize\StateUser;
use Kbize\Config\FilesystemConfigRepository;
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
        ),
        new StateUser(
            new FilesystemConfigRepository(
                '/tmp/user.yml', //FIXME:!
                new Parser(),
                new Dumper()
            )
        )
    )
));
$application->run();
