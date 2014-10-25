<?php
$input = "name.surname@email.com\nsecret";
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

$loader = require __DIR__ . '/vendor/autoload.php';

$process = new Process('php run.php task:list --profile behattest -b 2');
$process->setInput($input);
$process->run();

// eseguito deopo la fine del comando
if (!$process->isSuccessful()) {
    throw new \RuntimeException($process->getErrorOutput());
}

print $process->getOutput();
