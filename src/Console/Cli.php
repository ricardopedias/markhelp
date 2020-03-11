<?php
declare(strict_types=1);

namespace MarkHelp\Console;

use MarkHelp\App\Tools;
use MarkHelp\Console\Commands\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;

class Cli
{
    use Tools;

    private $consoleApplication = null;

    public function __construct($version)
    {
        $this->consoleApplication = new Application('MarkHelp', $version);
        $this->consoleApplication->setAutoExit(false);
    }

    /**
     * Registra o comando especificado na lista de comandos 
     * disponíveis no terminal
     * 
     * @param Command $command
     * @return void
     */
    public function registerCommand(Command $command) : void
    {
        $this->consoleApplication->add($command);
    }

    /**
     * Captura as informações fornecidas pelo usuário,
     * executa o comando mais adequado e devolve o status de resolução.
     * 
     * @param Input $input
     * @param Output $output 
     * @return int 0 para sucesso, 1 para falhas
     */
    public function run(Input $input, Output $output) : int
    {
        return $this->consoleApplication->run($input, $output);
    }

    /**
     * Finaliza o comando e devolve a saída de status para o terminal
     * 
     * @param Input $input 
     * @param int $status
     */
    public function output(Input $input, $status) : void
    {
        exit($status);
    }

    /**
     * Invoca um comando programaticamente.
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function callCommand(string $name, array $arguments = [])
    {
        $this->consoleApplication->setAutoExit(false);

        // // (optional) define the value of command arguments
        // 'fooArgument' => 'barValue',
        // // (optional) pass options to the command
        // '--message-limit' => $messages,

        $inputList = array_merge(['command' => $name], $arguments);
        $input     = new Input($inputList);
        $output    = new BufferedOutput();

        $this->consoleApplication->run($input, $output);

        return $output->fetch();
    }
}