<?php
namespace Kbize\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

class MissingMandatoryParametersRequest
{
    private $mandatoryParams;

    public function __construct(array $mandatoryParams = [])
    {
        $this->mandatoryParams = $mandatoryParams;
    }

    public function enrichInputs(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper
    )
    {
        foreach ($this->mandatoryParams as $mandatoryParam => $availableValuesCallback) {
            if (!$input->getOption($mandatoryParam)) {
                $availableValues = call_user_func($availableValuesCallback, $input);
                if (0 === count($availableValues)) {
                    continue;
                }

                if (1 === count($availableValues)) {
                    $value = key($availableValues);
                } else {
                    $question = (new ChoiceQuestion(
                        "Choose a $mandatoryParam",
                        $availableValues,
                        array_keys($availableValues)[0]
                    ))->setMaxAttempts(5);

                    $question->setErrorMessage("The $mandatoryParam `%s` is not valid.");

                    $valueLabel = $helper->ask($input, $output, $question);
                    $value = array_search($valueLabel, $availableValues);
                }

                $input->setOption($mandatoryParam, $value);
            }
        }
    }
}
