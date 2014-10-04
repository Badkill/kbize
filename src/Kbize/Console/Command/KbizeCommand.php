<?php
namespace Kbize\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Kbize\KbizeKernel;
use Kbize\Exception\ForbiddenException;
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $needAuth = !$this->kernel->isAuthenticated();
        for ($i = 0; $i < 5; $i++) {
            try {
                if ($needAuth) {
                    $this->authenticate($input, $output);
                }
                $this->doExecute($input, $output);
                break;
            } catch (ForbiddenException $e) {
                $needAuth = true;
            }
        }
    }

    protected function authenticate(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('Insert your login email: ');
        $question->setValidator(function ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException("`$email` is not a valid email address");
            }
            return $email;
        });
        $question->setMaxAttempts(3);
        $username = $helper->ask($input, $output, $question);

        $question = new Question('Insert your password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $question);

        $this->kernel->authenticate($username, $password);
    }
}
