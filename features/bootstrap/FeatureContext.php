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
use Kbize\Integration\Tester\CliCommandTester;

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

        /* $this->taskList = new TaskListCommand(new KbizeKernelFactory( */
        /*     $this->profileBasePath */
        /* )); */
        /* $this->application = new Application(); */
        /* $this->application->add($this->taskList); */

        $this->userInputs = [];
        $this->options = [
            '--profile' => 'behat',
        ];
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
     * @When /^I want to launch "([^"]*)" command$/
     */
    public function iWantToViewTaskList($command)
    {
        /* $this->command = $this->application->find('task:list'); */
        /* $this->commandTester = new CommandTester($this->command); */
        $this->commandTester = new CliCommandTester($command, [
            'KBIZE_PROFILE_PATH' => $this->profileBasePath,
            'KBIZE_ENV' => 'test',
        ]);
    }

    /**
     * @Given /^I use the "([^"]*)" argument "([^"]*)"$/
     */
    public function iUseTheArgument($argumentName, $argumentValue)
    {
        $this->options[$argumentName] = $argumentValue;
    }

    /**
     * @Given /^I insert[^"]* "([^"]*)" as.* input$/
     */
    public function iInsertInput($userInput)
    {
        $this->userInputs[] = $userInput;
    }

    /**
     * @Given /^I use the "([^"]*)" option with value "([^"]*)"$/
     * @Given /^I use the "([^"]*)" option$/
     */
    public function iUseTheOptionWithValue($optionName, $optionValue = '')
    {
        $this->options['--' . $optionName] = $optionValue;
    }


    /**
     * @Then /^command is executed$/
     */
    public function commandIsExecuted()
    {
        try {
            $this->commandTester->execute($this->userInputs, $this->options);
            $this->output = $this->commandTester->getDisplay();
        } catch (RuntimeException $e) {
            throw new RuntimeException("Missing input data\nCommand output is: \n" .
                $this->commandTester->getDisplay()
            );
        }

        //FIXME:! Should be check that no more data is present on input stream!
        /* $this->ensureNoMoreInputsData($inputStream); */


    }
    /* public function commandIsExecuted() */
    /* { */
    /*     $this->commandTester->execute(array_merge($this->options, [ */
    /*         'command' => $this->command->getName(), */
    /*     ])); */

    /*     $inputStream = $this->setupUserInputs(); */

    /*     try { */
    /*         $this->commandTester->execute(array_merge($this->options, [ */
    /*             'command' => $this->command->getName(), */
    /*         ])); */

    /*         $this->output = $this->commandTester->getDisplay(); */
    /*     } catch (RuntimeException $e) { */
    /*         throw new RuntimeException("Missing input data\nCommand output is: \n" . */
    /*             $this->commandTester->getDisplay() */
    /*         ); */
    /*     } */

    /*     $this->ensureNoMoreInputsData($inputStream); */
    /* } */

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
        assertNotEquals('expired', $config->toArray()['apikey'], 'the api key is not updated');
    }

    /**
     * @Then /^no more inputs are requested$/
     */
    public function noInputsAreRequested()
    {
        //FIXME:!
    }

    /**
     * @Given /^the story "([^"]*)" of "([^"]*)" exists$/
     */
    public function theStoryTitleOfUserExists($title, $assignee)
    {
        //FIXME:! to be implemented
    }

    /**
     * @Then /^the output contains "([^"]*)"$/
     */
    public function theOutputContains($wantedOutput)
    {
        assertRegexp("/$wantedOutput/", $this->output);
    }

    /**
     * @Then /^the output does not contains "([^"]*)"$/
     */
    public function theOutputDoesNotContains($wantedOutput)
    {
        assertNotRegexp("/$wantedOutput/", $this->output);
    }

    /**
     * @Then /^show the output$/
     */
    public function showTheOutput()
    {
        echo $this->output;
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

    /* private function setUpUserInputs() */
    /* { */
    /*     $dialog = $this->command->getHelper('question'); */
    /*     $inputStream = $this->getInputStream(implode("\n", $this->userInputs)); */
    /*     $dialog->setInputStream($inputStream); */

    /*     return $inputStream; */
    /* } */

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

    /* protected function getInputStream($input) */
    /* { */
    /*     $stream = fopen('php://memory', 'r+', false); */
    /*     stream_set_blocking($stream, 0); */
    /*     stream_set_timeout($stream, 1); */
    /*     fputs($stream, $input); */
    /*     rewind($stream); */

    /*     return $stream; */
    /* } */
}
