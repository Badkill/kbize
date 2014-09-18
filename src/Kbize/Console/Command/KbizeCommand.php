<?php
namespace Kbize\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

use Kbize\KbizeKernel;
use Kbize\Console\MissingMandatoryParametersRequest;

abstract class KbizeCommand extends Command
{
    protected $kernel;

    public function __construct(KbizeKernel $kernel)
    {
        parent::__construct();
        $this->kernel = $kernel;
    }

    protected function configure()
    {
        $this
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_OPTIONAL,
                'set the environment for different configuration',
                'prod'
            )
            ->addOption(
                'project',
                'p',
                InputOption::VALUE_REQUIRED,
                'The ID of the project'
            )
            ->addOption(
                'board',
                'b',
                InputOption::VALUE_REQUIRED,
                'The ID of the board whose structure you want to get.'
            )
            ->addOption(
                'no-cache',
                'x',
                InputOption::VALUE_NONE,
                'Do not use cached data'
            )
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output) {
        $this->missingParameterRequest->enrichInputs($input, $output, $this->getHelper('question'));
    }
}
