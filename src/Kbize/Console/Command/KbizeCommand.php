<?php
namespace Kbize\Console\Command;

use LazyOptionCommand\Command\Command;
use LazyOptionCommand\Input\LazyInputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Kbize\KbizeKernelFactory;
use Kbize\Exception\ForbiddenException;
use Kbize\Exception\MissingSettingsException;

abstract class KbizeCommand extends Command
{
    protected $kernel;
    protected $kernelFactory;
    protected $settings;

    public function __construct(KbizeKernelFactory $kernelFactory, array $settings = [])
    {
        parent::__construct();
        $this->kernelFactory = $kernelFactory;
        $this->settings      = array_merge([
            'env' => 'prod'
        ], $settings);
    }

    protected function options()
    {
        return [
            new InputOption(
                'profile',
                'i',
                InputOption::VALUE_REQUIRED,
                'You can use different configuration for different profile',
                'default'
            ),
            new LazyInputOption(
                'project',
                'p',
                InputOption::VALUE_REQUIRED,
                'The ID of the project',
                null,
                function (InputInterface $input) {
                    $projects = [];
                    foreach($this->kernel($input)->getProjects() as $project) {
                        $projects[$project['id']] = $project['name'];
                    }
                    return $projects;
                }
            ),
            new LazyInputOption(
                'board',
                'b',
                InputOption::VALUE_REQUIRED,
                'The ID of the board whose structure you want to get.',
                null,
                function (InputInterface $input) {
                    $boards = [];
                    foreach($this->kernel($input)->getBoards($input->getOption('project')) as $board) {
                        $boards[$board['id']] = $board['name'];
                    }
                    return $boards;
                }
            ),
            /* [ */
            /*     'name'        => 'no-cache', */
            /*     'shortcut'    => 'x', */
            /*     'mode'        => InputOption::VALUE_NONE, */
            /*     'description' => 'Do not use cached data' */
            /* ], */
        ];
    }

    protected function configure()
    {
        foreach ($this->options() as $option) {
            $this->addRawOption($option);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //FIXME:! it needed only for behat!
        if ('test' === $this->settings['env']) {
            $input->setInteractive(true);
        }

        for ($i = 0; $i < 5; $i++) {
            try {
                $this->kernel = $this->kernelFactory->forProfile($input->getOption('profile'));
                break;
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
        if ('test' !== $this->settings['env']) {
            //FIXME:! it brokes behat with cli
            $question->setHidden(true);
            $question->setHiddenFallback(false);
        }
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

    private function kernel(InputInterface $input)
    {
        if (!$this->kernel) {
            $this->kernel = $this->kernelFactory->forProfile($input->getOption('profile'));
        }

        return $this->kernel;
    }
}
