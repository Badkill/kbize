<?php
namespace Kbize\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Kbize\KbizeKernel;
use Kbize\KbizeKernelFactory;
use Kbize\Exception\ForbiddenException;
use Kbize\Exception\MissingSettingsException;
use Kbize\Console\MissingMandatoryParametersRequest;

abstract class KbizeCommand extends Command
{
    protected $kernel;
    protected $kernelFactory;

    public function __construct(KbizeKernelFactory $kernelFactory)
    {
        parent::__construct();
        $this->kernelFactory = $kernelFactory;
    }

    protected function configure()
    {
        $this
            /* ->addOption( */
            /*     'env', */
            /*     'e', */
            /*     InputOption::VALUE_OPTIONAL, */
            /*     'set the environment for different configuration', */
            /*     'prod' */
            /* ) */
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
            ->addOption(
                'profile',
                'e',
                InputOption::VALUE_REQUIRED,
                'You can use different configuration for different profile',
                'default'
            )
        ;
    }

    protected function missingParameterRequestSetUp()
    {
        $this->missingParameterRequest = new MissingMandatoryParametersRequest([
            'project' => function () {
                $projects = [];
                /* foreach($this->kernel->getProjects() as $project) { */
                /*     $projects[$project['id']] = $project['name']; */
                /* } */

                return $projects;
            },
            'board' => function (InputInterface $input) {
                $boards = [];
                foreach($this->kernel->getBoards($input->getOption('project')) as $board) {
                    $boards[$board['id']] = $board['name'];
                }

                return $boards;
            }
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        for ($i = 0; $i < 5; $i++) {
            try {
                $this->kernel = $this->kernelFactory->forProfile($input->getOption('profile'));
            } catch (MissingSettingsException $e) {
                $settingsWrapper = $e->settingsWrapper();
                $settingsWrapper->add($this->enrichSettings($input, $output));
                $settingsWrapper->store();
            }
        }

        $needAuth = !$this->kernel->isAuthenticated();
        for ($i = 0; $i < 5; $i++) {
            try {
                if ($needAuth) {
                    $this->authenticate($input, $output);
                }

                $this->missingParameterRequestSetUp();
                $this->missingParameterRequest->enrichInputs(
                    $input,
                    $output,
                    $this->getHelper('question')
                );

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

    protected function enrichSettings(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question(
            "Insert kanbanize api url: \n[ default = http://kanbanize.com/index.php/api/kanbanize/ ]\n",
            'http://kanbanize.com/index.php/api/kanbanize/'
        );
        $url = $helper->ask($input, $output, $question);

        return [
            'url' => $url,
        ];
    }
}
