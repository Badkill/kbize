<?php
namespace Kbize;

use Kbize\Sdk\HttpKbizeSdk;
use Kbize\StateUser;
use Kbize\Config\FilesystemConfigRepository;
use Kbize\Http\GuzzleClient;
use GuzzleHttp\Client;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class KbizeKernelFactory
{
    public function kernel(array $settings)
    {
        return new \Kbize\RealKbizeKernel(
            new HttpKbizeSdk(
                new GuzzleClient(
                    new Client([
                        'base_url' => 'http://localhost:8000',
                    ])
                )
            ),
            new StateUser(
                new FilesystemConfigRepository(
                    '/tmp/user.yml', //FIXME:!
                    new Parser(),
                    new Dumper()
                )
            )
        );
    }
}
