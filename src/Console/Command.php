<?php

declare(strict_types=1);

namespace MarkHelp\Console;

use Exception;
use MarkHelp\MarkHelp;
use Reliability\Reliability;
use RuntimeException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TypeError;

class Command extends SymfonyCommand
{
    private const EXIT_SUCCESS = 0;

    private const EXIT_FAIL = 1;

    protected static $defaultName = 'markhelp';

    protected function configure(): void
    {
        $this->setDescription('Create an HTML site based on the markdown files');
        $this->setHelp('This command scans a directory containing markdown files '
            . 'and generates browsable HTML pages in the form of documentation');

        $this->addOption(
            'input',
            'i',
            InputOption::VALUE_OPTIONAL,
            'Specifies the location of the markdown files',
            null
        );

        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_REQUIRED,
            'Specifies a location for the site after compilation',
            null
        );

        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Specifies a file containing settings',
            null
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();

        if ($application === null) {
            throw new RuntimeException("Unable to get the symfony application");
        }

        $version = $application->getVersion();
        $output->writeln([
            '<fg=green>---------------------------------------------------</>',
            '<fg=green>MarkHelp ' . $version . '</>',
            '<fg=green>---------------------------------------------------</>',
        ]);
        
        $currentDir = (string)shell_exec('pwd');
        $currentDir = str_replace("\n", "", $currentDir);

        $config      = $this->parseOption($input, 'config');
        $source      = $this->parseOption($input, 'input');
        $destination = $this->parseOption($input, 'output');

        // O único parâmetro obrigatório é o destino da renderização
        if ($destination !== '') {
            return $this->routine($output, $source, $destination, $config);
        }

        $this->outputHelp($input, $output);

        return self::EXIT_SUCCESS;
    }

    /**
     * Para exibir a ajuda programaticamente.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function outputHelp(InputInterface $input, OutputInterface $output)
    {
        $help = new HelpCommand();
        $help->setCommand($this);
        return $help->run($input, $output);
    }

    private function routine(OutputInterface $output, string $source, string $destination, string $config): int
    {
        $source = reliability()->absolutePath($source) ?? '';
        if (reliability()->isDirectory($source) === false) {
            $output->writeln('<fg=red>The specified source is not a valid path</>');
            return self::EXIT_FAIL;
        }
        
        $destination = reliability()->absolutePath($destination) ?? '';
        if (reliability()->isDirectory($destination) === false) {
            $output->writeln('<fg=red>The specified destination is not a valid path</>');
            return self::EXIT_FAIL;
        }
        
        if ($config !== '') {
            $config = reliability()->absolutePath($config) ?? '';
        }

        if ($config !== '' && reliability()->isFile($config) === false) {
            $output->writeln('<fg=red>The specified configuration file not exists</>');
            return self::EXIT_FAIL;
        }

        $source      = rtrim($source, '/') . "/";
        $destination = rtrim($destination, '/') . "/";
        $output->writeln("<fg=blue>Reading from: {$source}</>");
        $output->writeln("<fg=blue>Saving in {$destination}</>");
        
        try {
            $markHelp = new MarkHelp($source);
            if ($config !== '') {
                $markHelp->loadConfigFrom($config);
                $output->writeln("<fg=blue>Load config from {$config}</>");
            }
            $markHelp->saveTo($destination);
        } catch (Exception $e) {
            $output->writeln("<fg=red>{$e->getMessage()}</>");
            return self::EXIT_FAIL;
        }

        $output->writeln("<fg=green>Documentation site successfully generated</>");

        return self::EXIT_SUCCESS;
    }

    private function parseOption(InputInterface $input, string $option): string
    {
        $value = $input->getOption($option);

        if (is_array($value) === true) {
            return (string) $value[0];
        }

        if ($value === false) {
            return '';
        }

        return (string)$value;
    }
}
