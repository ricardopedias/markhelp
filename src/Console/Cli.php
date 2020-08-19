<?php

declare(strict_types=1);

namespace MarkHelp\Console;

use MarkHelp\App\Filesystem;
use MarkHelp\App\Tools;
use MarkHelp\Console\Command;
use Symfony\Component\Console\Application;

class Cli
{
    use Tools;

    private $consoleApplication = null;

    private $version = null;

    public function loadVersionFrom($versionFile)
    {
        $directory = $this->dirname($versionFile);
        $basename = $this->basename($versionFile);

        $filesystem = new Filesystem();
        $filesystem->mount('root', $directory);
        $this->version = $filesystem->read("root://{$basename}");

        return $this;
    }

    /**
     * Captura as informações fornecidas pelo usuário,
     * executa o comando mais adequado e devolve o status de resolução.
     * @param Input $input
     * @param Output $output
     * @return int 0 para sucesso, 1 para falhas
     */
    public function run(Input $input = null, Output $output = null)
    {
        $command = new Command();

        $this->consoleApplication = new Application('MarkHelp', $this->version);
        $this->consoleApplication->setAutoExit(false);
        $this->consoleApplication->add($command);
        $this->consoleApplication->setDefaultCommand($command->getName(), true);
        $this->consoleApplication->run($input, $output);
    }
}
