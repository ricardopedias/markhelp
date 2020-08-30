<?php

declare(strict_types=1);

namespace MarkHelp\Console;

use MarkHelp\MarkHelp;
use Reliability\Reliability;
use RuntimeException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TypeError;

class Command extends SymfonyCommand
{
    private const EXIT_SUCCESS = 0;

    private const EXIT_FAIL = 1;

    protected static $defaultName = 'make';

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
            'repository',
            'r',
            InputOption::VALUE_OPTIONAL,
            'Specifies a repository for obtaining the markdown files',
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
        $reliability = new Reliability();
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

        $config      = $this->stringOrNullOption($input, 'config');
        $repository  = $this->stringOrNullOption($input, 'repository');
        $source      = $this->stringOrNullOption($input, 'input');
        $destination = $this->stringOrNullOption($input, 'output');

        if ($destination === null) {
            $output->writeln('<fg=red>A destination for compiled pages is required</>');
            return self::EXIT_FAIL;
        }

        if ($this->isGitUrl($source)) {
            // clonar o repo
            // ...
            // $helper = $this->getHelper('question');
            // $question = new Question('Please enter the name of the bundle: ', 'AcmeDemoBundle');
            // $bundleName = $helper->ask($input, $output, $question);
            // $output->writeln($bundleName);
        }

        try {
            $source = $source !== null ? $reliability->absolutePath($source) : null;
        } catch (TypeError $e) {
            $output->writeln('<fg=red>The specified source is not a valid path</>');
            return self::EXIT_FAIL;
        }

        if ($source === null) {
            $output->writeln('<fg=red>The specified source is not a valid path</>');
            return self::EXIT_FAIL;
        }

        try {
            $destination = $reliability->absolutePath($destination);
        } catch (TypeError $e) {
            $output->writeln('<fg=red>The specified destination is not a valid path</>');
            return self::EXIT_FAIL;
        }

        if ($destination === null) {
            $output->writeln('<fg=red>The specified destination is not a valid path</>');
            return self::EXIT_FAIL;
        }
        
        $source      = rtrim($source, '/') . "/";
        $destination = rtrim($destination, '/') . "/";

        $output->writeln("<fg=blue>Reading from: {$source}</>");
        $output->writeln("<fg=blue>Saving in {$destination}</>");
        
        if ($config === null && $reliability->isFile($currentDir . DIRECTORY_SEPARATOR . 'config.json')) {
            $config = $currentDir . DIRECTORY_SEPARATOR . 'config.json';
            $output->writeln("<fg=blue>Load config.js from current directory</>");
        }

        if ($config === null && $reliability->isFile($source . DIRECTORY_SEPARATOR . 'config.json')) {
            $config = $source . 'config.json';
            $output->writeln("<fg=blue>Load config from {$config}</>");
        }

        $app = new MarkHelp($source);

        if ($config !== null) {
            $app->loadConfigFrom($config);
        }

        $templateDocument = $app->config('support.document');
        $output->writeln("<fg=blue>Load template from {$templateDocument}</>");

        $app->saveTo($destination);
        $output->writeln("<fg=green>Documentation site successfully generated</>");

        return self::EXIT_SUCCESS;
    }

    private function isGitUrl(?string $url): bool
    {
        return $url !== null && strpos($url, ".git") !== false;
    }

    private function stringOrNullOption(InputInterface $input, string $option): ?string
    {
        $value = $input->getOption('output');

        if (is_array($value) === true) {
            return (string) $value[0];
        }

        if ($value === false) {
            return null;
        }

        return (string)$value;
    }
}
