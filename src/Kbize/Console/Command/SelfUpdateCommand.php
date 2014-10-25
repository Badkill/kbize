<?php
namespace Kbize\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;

class SelfUpdateCommand extends Command
{
    const MANIFEST_FILE = 'http://silvadanilo.github.io/kbize/manifest.json';

    protected function configure()
    {
        $this
            ->setName('self:update')
            ->setAliases(['selfupdate', 'self-update'])
            ->setDescription('Updates kbize.phar to the latest version')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($this->getApplication()->getVersion(), true);
    }
}
