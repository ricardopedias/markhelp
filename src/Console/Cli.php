<?php

declare(strict_types=1);

namespace MarkHelp\Console;

use MarkHelp\Console\Command;
use Reliability\Reliability;
use Symfony\Component\Console\Application;

class Cli
{
    private ?Application $consoleApplication = null;

    private string $version;

    public function loadVersionFrom(string $versionFile): Cli
    {
        $reliability = new Reliability();
        $directory = $reliability->dirname($versionFile);
        $basename = $reliability->basename($versionFile);

        $filesystem = $reliability->mountDirectory($directory);
        $version = $filesystem->read("{$basename}");

        if ($version !== false) {
            $this->version = '';
        }

        return $this;
    }

    /**
     * Captura as informações fornecidas pelo usuário,
     * executa o comando mais adequado e devolve o status de resolução.
     * @param Input $input
     * @param Output $output
     * @return int 0 para sucesso, 1 para falhas
     */
    public function run(Input $input = null, Output $output = null): int
    {
        $name = 'MarkHelp';
        $command = new Command();

        $this->consoleApplication = new Application($name, $this->version);
        $this->consoleApplication->setAutoExit(false);
        $this->consoleApplication->add($command);
        $this->consoleApplication->setDefaultCommand($name, true);
        return $this->consoleApplication->run($input, $output);
    }
}
