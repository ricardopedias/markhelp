<?php

declare(strict_types=1);

namespace MarkHelp\Console;

use Exception;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Console\Application;

class Cli
{
    private ?Application $consoleApplication = null;

    private string $version = '';

    public function loadVersionFrom(string $versionFile): Cli
    {
        $directory = reliability()->dirname($versionFile);
        $basename = reliability()->basename($versionFile);

        $filesystem = reliability()->mountDirectory($directory);

        try {
            $version = $filesystem->read("{$basename}");
        } catch (FileNotFoundException $e) {
            throw new Exception("The file {$versionFile} does not exist");
        }

        if ($version !== false) {
            $this->version = trim($version);
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

        $this->consoleApplication = new Application($name, $this->version);
        $this->consoleApplication->setAutoExit(false);
        $this->consoleApplication->add(new Command());
        $this->consoleApplication->setDefaultCommand('markhelp', true);

        return $this->consoleApplication->run($input, $output);
    }
}
