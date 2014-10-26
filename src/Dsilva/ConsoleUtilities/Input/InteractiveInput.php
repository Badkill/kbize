<?php

namespace Dsilva\ConsoleUtilities\Input;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Dsilva\ConsoleUtilities\Input\LazyInputOption;

/**
 * @inheritdoc
 *
 * @author Danilo Silva <badkill82@gmail.com>
 */
class InteractiveInput implements InputInterface
{
    private $input;
    private $output;
    private $helper;
    private $optionEnricher;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->helper = $helper;
        $this->optionEnricher = new OptionsEnricher($this, $this->output, $this->helper);
    }

    public function getOption($name)
    {
        $value = $this->input->getOption($name);
        if (!is_null($value)) {
            return $value;
        }

        $option = $this->definition->getOption($name);
        if ($option instanceof LazyInputOption && $option->isMandatory()) {
            return $this->optionEnricher->enrich($option->getName(), $option->availableValues($this));
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function bind(InputDefinition $definition)
    {
        $this->definition = $definition;
        return $this->input->bind($definition);
    }

    /**
     * @inheritdoc
     */
    protected function parse()
    {
        return $this->input->parse();
    }

    /**
     * @inheritdoc
     */
    public function getFirstArgument()
    {
        return $this->input->getFirstArgument();
    }

    /**
     * @inheritdoc
     */
    public function hasParameterOption($values)
    {
        return $this->input->hasParameterOption($values);
    }

    /**
     * @inheritdoc
     */
    public function getParameterOption($values, $default = false)
    {
        return $this->input->getParameterOption($values, $default);
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return $this->input->validate();
    }

    public function getArguments()
    {
        return $this->input->getArguments();
    }

    public function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    public function setArgument($name, $value)
    {
        return $this->input->setArguments($name, $value);
    }

    public function hasArgument($name)
    {
        return $this->input->setArguments($name, $value);
    }

    public function getOptions()
    {
        return $this->input->getOptions();
    }

    public function setOption($name, $value)
    {
        return $this->input->setOption($name, $value);
    }

    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    public function setInteractive($interactive)
    {
        return $this->input->setInteractive($interactive);
    }
}
