<?php
declare(strict_types=1);

namespace MarkHelp\Console;

use MarkHelp\App\Tools;
use MarkHelp\Console\Command;
use Symfony\Component\Console\Application;

class Cli
{
    use Tools;

    private $consoleApplication = null;

    public function __construct($version)
    {
        $command = new Command;

        $this->consoleApplication = new Application('MarkHelp', $version);
        $this->consoleApplication->setAutoExit(false);
        $this->consoleApplication->add($command);
        $this->consoleApplication->setDefaultCommand($command->getName(), true);
    }

    /**
     * Captura as informações fornecidas pelo usuário,
     * executa o comando mais adequado e devolve o status de resolução.
     * 
     * @param Input $input
     * @param Output $output 
     * @return int 0 para sucesso, 1 para falhas
     */
    public function run(Input $input = null, Output $output = null)
    {
        $this->consoleApplication->run($input, $output);
    }
}