<?php
namespace Kbize\Exception;

use Kbize\Settings\SettingsWrapper;

class MissingSettingsException extends KbizeException
{
    private $settingsWrapper;

    public function __construct(SettingsWrapper $settingsWrapper, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->settingsWrapper = $settingsWrapper;
    }

    public function settingsWrapper()
    {
        return $this->settingsWrapper;
    }
}
