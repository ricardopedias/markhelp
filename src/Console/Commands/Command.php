<?php 

namespace MarkHelp\Console\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|iterable $messages The message as an iterable of strings or a single string
     * @param int             $options  A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    protected function writeExtended(OutputInterface $output, $message, string $color = '\033[0m', $options = 0, $lineBreak = true)
    {
        $message = (array) $message;
        $message = array_map(function($value) use ($color){
            return "{$color}{$value}" .'\033[0m';
        }, $message);

        return $lineBreak === true 
            ? $output->writeln($message, $options) 
            : $output->write($message, $options);
    }

    protected function error(OutputInterface $output, $message, $options = 0, $lineBreak = true)
    {
        return $this->writeExtended($output, '\033[0;31m', $message, $options, $lineBreak);
    }

    protected function warning(OutputInterface $output, $message, $options = 0, $lineBreak = true)
    {
        return $this->writeExtended($output, '\e[33m', $message, $options, $lineBreak);
    }

    protected function success(OutputInterface $output, $message, $options = 0, $lineBreak = true)
    {
        return $this->writeExtended($output, '\033[0;32m', $message, $options, $lineBreak);
    }

    protected function info(OutputInterface $output, $message, $options = 0, $lineBreak = true)
    {
        return $this->writeExtended($output, '\e[34m', $message, $options, $lineBreak);
    }

    protected function message(OutputInterface $output, $message, $options = 0, $lineBreak = true)
    {
        return $this->writeExtended($output, '\033[0m', $message, $options, $lineBreak);
    }
}