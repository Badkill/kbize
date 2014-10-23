<?php
namespace Kbize;

use Kbize\Sdk\HttpKbizeSdk;
use Kbize\StateUser;
use Kbize\Config\FilesystemConfigRepository;
use Kbize\Http\GuzzleClient;
use Kbize\Exception\MissingSettingsException;
use Kbize\Settings\SettingsWrapper;
use GuzzleHttp\Client;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class KbizeKernelFactory
{
    public function __construct($profileBasePath = null)
    {
        if (!$profileBasePath) {
            $this->profileBasePath = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . '.kbize';
        } else {
            $this->profileBasePath = $profileBasePath;
        }
    }

    public function forProfile($profile)
    {
        $settings = $this->settingsByProfile($profile);
        if (!$settings->isValid()) {
            throw new MissingSettingsException($settings);
        }

        return new \Kbize\RealKbizeKernel(
            new HttpKbizeSdk(
                new GuzzleClient(
                    new Client([
                        'base_url' => $settings['url'],
                    ])
                )
            ),
            new StateUser(
                new FilesystemConfigRepository(
                    $this->filePath($profile, StateUser::CONFIG_REPOSITORY_NAME),
                    new Parser(),
                    new Dumper()
                )
            )
        );
    }

    private function settingsByProfile($profile)
    {
        return new SettingsWrapper(
            new FilesystemConfigRepository(
                $this->filePath($profile, 'config.yml'),
                new Parser(),
                new Dumper()
            )
        );
    }

    private function filePath($profile, $file)
    {
        return $this->profileBasePath . DIRECTORY_SEPARATOR . $profile . DIRECTORY_SEPARATOR. $file;
    }
}
