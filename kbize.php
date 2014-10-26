<?php
namespace Kbize;

/* use Dsilva\ConsoleUtilities\Application; */
use Symfony\Component\Console\Application;
use Kbize\Console\Command\TaskListCommand;
use Kbize\Console\Command\SelfUpdateCommand;
use Kbize\Console\Helper\AlternateTableHelper;
/* use KbizeCli\Console\Helper\TableWithRowTitleHelper; */

$loader = require __DIR__ . '/vendor/autoload.php';

$settings = settings();

$application = new Application('Kanbanize shell client', '@git-version@');

$helperSet = $application->getHelperSet();
$helperSet->set(new AlternateTableHelper());
/* $helperSet->set(new TableWithRowTitleHelper()); */
$application->setHelperSet($helperSet);


$application->add(new TaskListCommand(
    new KbizeKernelFactory($settings['profile_path']),
    $settings
));
$application->add(new SelfUpdateCommand());
$application->run();

function settings()
{
    $settings = [
        'env' => 'prod',
        'profile_path' => $_SERVER['HOME'] . DIRECTORY_SEPARATOR . '.kbize',
    ];

    foreach ($_SERVER as $env => $value) {
        if ('KBIZE_' == substr($env, 0, 6)) {
            $settings[strtolower(substr($env, 6))] = $value;
        }
    }

    return $settings;
}
