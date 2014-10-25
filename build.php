<?php
namespace Kbize;

use Symfony\Component\Console\Application;

$loader = require __DIR__.'/vendor/autoload.php';
$buildPath = 'build/';
$buildTarget = 'kbize.phar';
$output = $buildPath.$buildTarget;

// remove old
@unlink($output);
@unlink($output.'.gz');

// start phar creation
$phar = new \Phar($output);
$phar->startBuffering();

// the runner
$phar->addFile('run.php');

// add other paths if needed
/* $phar->buildFromDirectory(__DIR__, '/\/app\//'); */
$phar->buildFromDirectory(__DIR__, '/\/src\//');
$phar->buildFromDirectory(__DIR__, '/\/vendor\//');

// this will make the phar autoexecutable
$defaultStub = $phar->createDefaultStub('run.php');
$stub = "#!/usr/bin/env php \n" . $defaultStub;
$phar->setStub($stub);
$phar->compressFiles(\Phar::GZ);
$phar->stopBuffering();

// set execution rights
chmod($output, 0755);

echo "Finished!!\n";
