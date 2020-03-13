<?php 
declare(strict_types=1);
namespace MarkHelp\Console;

use MarkHelp\App\Tools;
use MarkHelp\MarkHelp;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TypeError;

class Command extends SymfonyCommand
{
    use Tools;

    protected static $defaultName = 'make';

    protected function configure()
    {
        $this->setDescription('Create an HTML site based on the markdown files');
        $this->setHelp('This command scans a directory containing markdown files and generates browsable HTML pages in the form of documentation');

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
        $version = $this->getApplication()->getVersion();
        $output->writeln([
            '<fg=green>---------------------------------------------------</>',
            "<fg=green>MarkHelp {$version}</>",
            '<fg=green>---------------------------------------------------</>',
        ]);
        

        $currentDir = str_replace("\n", "", shell_exec('pwd'));

        $config      = $input->getOption('config');
        $repository  = $input->getOption('repository');
        $source      = $input->getOption('input');
        $destination = $input->getOption('output');

        if ($destination === null) {
            $output->writeln('<fg=red>A destination for compiled pages is required</>');
            return 1;
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
            $source = $source !== null ? $this->absolutePath($source) : null;
        } catch(TypeError $e) {
            $output->writeln('<fg=red>The specified source is not a valid path</>');
            return 1;
        }

        if ($source === false) {
            $output->writeln('<fg=red>The specified source is not a valid path</>');
            return 1;
        }

        try {
            $destination = $this->absolutePath($destination);
        } catch(TypeError $e) {
            $output->writeln('<fg=red>The specified destination is not a valid path</>');
            return 1;
        }

        if ($destination === false) {
            $output->writeln('<fg=red>The specified destination is not a valid path</>');
        }
        
        $source      = $source !== null ? rtrim($source, '/') . "/" : null;
        $destination = rtrim($destination, '/') . "/";

        $sourceMessage = "Reading from: {$source}";
        if ($source === null && $repository === NULL) {
            $source = $currentDir;
            $sourceMessage = "Reading from current directory";
        }
        $output->writeln("<fg=blue>{$sourceMessage}</>");
        $output->writeln("<fg=blue>Saving in {$destination}</>");
        
        if ($config === null && $this->isFile($currentDir . DIRECTORY_SEPARATOR . 'config.json')) {
            $config = $currentDir . DIRECTORY_SEPARATOR . 'config.json';
            $output->writeln("<fg=blue>Load config.js from current directory</>");
        }

        if ($config === null && $this->isFile($source . DIRECTORY_SEPARATOR . 'config.json')) {
            $config = $source . 'config.json';
            $output->writeln("<fg=blue>Load config from {$config}</>");
        }

        $app = new MarkHelp($source);

        if ($config !== null) {
            $app->loadConfigFrom($config);
        }

        $app->saveTo($destination);
        $output->writeln("<fg=green>Documentation site successfully generated</>");

        return 0;
    }

    private function isGitUrl($path)
    {
        return false;
    }
}






