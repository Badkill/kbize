<?php
namespace Kbize\Integration\Tester;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class CliCommandTester
{
    private $command;
    private $env;

    public function __construct($command, array $env = array())
    {
        $this->command = $command;
        $this->env = $env;
    }

    /**
     * Executes the command.
     *
     * Available options:
     *
     *  * interactive: Sets the input interactive flag
     *  * decorated:   Sets the output decorated flag
     *  * verbosity:   Sets the output verbosity flag
     *
     * @param array $input   An array of arguments and options
     * @param array $options An array of options
     *
     * @return int     The command exit code
     */
    public function execute(array $input = array(), array $options = array())
    {
        $commandLine = "php run.php $this->command";

        foreach ($options as $option => $value) {
            if ('-' == substr($option, 0, 1)) {
                $commandLine .= ' ' . $option . ' ' . $value;
            } else {
                $commandLine .= ' ' . $value;
            }
        }

        $this->process = new Process($commandLine);
        $this->process->setEnv(array_merge($_SERVER, $this->env));

        if (!empty($input)) {
            $this->process->setInput(implode("\n", $input));
        }
        $this->process->run();

        if (!$this->process->isSuccessful()) {
            throw new \RuntimeException($this->process->getErrorOutput());
        }

        return $this->statusCode = $this->process->getExitCode();
    }

    /**
     * Gets the display returned by the last execution of the command.
     *
     * @param bool    $normalize Whether to normalize end of lines to \n or not
     *
     * @return string The display
     */
    public function getDisplay($normalize = false)
    {
        $display = $this->process->getOutput() . PHP_EOL . $this->process->getErrorOutput();

        if ($normalize) {
            $display = str_replace(PHP_EOL, "\n", $display);
        }

        return $display;
    }

    /**
     * Gets the input instance used by the last execution of the command.
     *
     * @return InputInterface The current input instance
     */
    public function getInput()
    {
        return $this->process->getInput();
    }

    /**
     * Gets the output instance used by the last execution of the command.
     *
     * @return OutputInterface The current output instance
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Gets the status code returned by the last execution of the application.
     *
     * @return int     The status code
     */
    public function getStatusCode()
    {
        return $this->process->getExitCode();
    }
}
