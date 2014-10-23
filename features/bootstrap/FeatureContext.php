<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Kbize\Console\Command\TaskListCommand;
use Kbize\KbizeKernelFactory;
use Kbize\Settings\SettingsWrapper;
use Kbize\Config\FilesystemConfigRepository;
use Kbize\StateUser;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->profile = 'behat';
        $this->profileBasePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kbizeTestProfile';
        $this->profilePath = $this->profileBasePath . DIRECTORY_SEPARATOR . $this->profile;
        $this->initializeSettings();

        $this->taskList = new TaskListCommand(new KbizeKernelFactory(
            $this->profileBasePath
        ));
        $this->application = new Application();
        $this->application->add($this->taskList);

        $this->userInputs = [];
    }

    /**
     * @Given /^I am an unauthenticated user$/
     */
    public function iAmAnUnauthenticatedUser()
    {
        @unlink($this->profilePath . DIRECTORY_SEPARATOR . StateUser::CONFIG_REPOSITORY_NAME);
    }

    /**
     * @Given /^I am an authenticated user$/
     */
    public function iAmAnAuthenticatedUser()
    {
        $this->stateUser()->update([
            "email"       => "name.surname@email.com",
            "username"    => "name.surname",
            "realname"    => "'Name Surname'",
            "companyname" => "Company",
            "timezone"    => "'0:0'",
            "apikey"      => "ERtVj8IJKn9jUkSyY0ml6HMK3c1N4tVZSjHSmQVy",
        ]);
    }

    /**
     * @When /^I want to view projects list$/
     */
    public function iWantToViewTaskList()
    {
        $this->command = $this->application->find('task:list');
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @Given /^I write "([^"]*)"$/
     * @Given /^I write [^:]*: "([^"]*)"$/
     */
    public function iWrite($userInput)
    {
        $this->userInputs[] = $userInput;
    }

    /**
     * @Then /^command is executed$/
     */
    public function commandIsExecuted()
    {
        $inputStream = $this->setupUserInputs();

        try {
            $this->commandTester->execute([
                'command' => $this->command->getName(),
                '--profile' => 'behat',
                '--board' => '2',
            ]);
        } catch (RuntimeException $e) {
            throw new RuntimeException("Missing input data\nCommand output is: \n" .
                $this->commandTester->getDisplay()
            );
        }

        $this->ensureNoMoreInputsData($inputStream);
    }

    /**
     * @Given /^I have an expired token$/
     */
    public function iHaveAnExpiredToken()
    {
        $this->user->update(array_merge($this->user->toArray(), [
            'apikey' => 'expired',
        ]));
    }


    /**
     * @Given /^my token is stored$/
     */
    public function myTokenIsStored()
    {

        $config = new FilesystemConfigRepository(
            $this->profilePath . DIRECTORY_SEPARATOR . 'user.yml',
            new Parser(),
            new Dumper()
        );

        assertArrayHasKey('apikey', $config->toArray(), var_export($config->toArray(), true));
    }

    private function initializeSettings()
    {
        $settingsWrapper = new SettingsWrapper(
            new FilesystemConfigRepository(
                $this->profilePath . DIRECTORY_SEPARATOR . 'config.yml',
                new Parser(),
                new Dumper()
            )
        );

        $settingsWrapper->add([
            'url' => 'http://localhost:8000',
        ]);
        $settingsWrapper->store();
    }

    private function stateUser()
    {
        $this->user = new StateUser(
            new FilesystemConfigRepository(
                $this->profilePath . DIRECTORY_SEPARATOR . StateUser::CONFIG_REPOSITORY_NAME,
                new Parser(),
                new Dumper()
            )
        );

        return $this->user;
    }

    private function setUpUserInputs()
    {
        $dialog = $this->command->getHelper('question');
        $inputStream = $this->getInputStream(implode("\n", $this->userInputs));
        $dialog->setInputStream($inputStream);

        return $inputStream;
    }

    private function ensureNoMoreInputsData($inputStream)
    {
        if ($inputStream) {
            if (!feof($inputStream)) {
                $tooMuchInputs = '';
                while (($buffer = fgets($inputStream, 4096)) !== false) {
                    $tooMuchInputs .= $buffer;
                }

                if ($tooMuchInputs) {
                    throw new RuntimeException(
                        "Too much input data, inputs in surplus are: \n`$tooMuchInputs`"
                    );
                }
            }
            fclose($inputStream);
        }
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}
