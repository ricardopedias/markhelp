<?php 
declare(strict_types=1);
namespace MarkHelp\Console;

use MarkHelp\MarkHelp;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TypeError;

class Command extends SymfonyCommand
{
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
        }

        try {
            $source = $source !== null ? realpath($source) : null;
        } catch(TypeError $e) {
            $output->writeln('<fg=red>The specified source is not a valid path</>');
            return 1;
        }

        if ($source === false) {
            $output->writeln('<fg=red>The specified source is not a valid path</>');
            return 1;
        }

        try {
            $destination = $this->getAbsolutePath($destination);
        } catch(TypeError $e) {
            $output->writeln('<fg=red>The specified destination is not a valid path</>');
            return 1;
        }

        if ($destination === false) {
            $output->writeln('<fg=red>The specified destination is not a valid path</>');
        }
        
        $source      = rtrim($source, '/') . "/";
        $destination = rtrim($destination, '/') . "/";

        $sourceMessage = "Reading from: {$source}";
        if ($source === null && $repository === NULL) {
            $source = $currentDir;
            $sourceMessage = "Reading from current directory";
        }
        $output->writeln("<fg=blue>{$sourceMessage}</>");
        $output->writeln("<fg=blue>Saving in {$destination}</>");
        
        if ($config === null && is_file($currentDir . DIRECTORY_SEPARATOR . 'config.json')) {
            $config = $currentDir . DIRECTORY_SEPARATOR . 'config.json';
            $output->writeln("<fg=blue>Load config.js from current directory</>");
        }

        if ($config === null && is_file($source . DIRECTORY_SEPARATOR . 'config.json')) {
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

        // // $helper = $this->getHelper('question');
        // // $question = new Question('Please enter the name of the bundle: ', 'AcmeDemoBundle');
        // // $bundleName = $helper->ask($input, $output, $question);
        
        // // $output->writeln($bundleName);

        // // $file = $input->getArgument('file');
        // // $output->writeln('xxxxx ' . $file);

        // // $section1 = $output->section();
        // // $section2 = $output->section();
        // // $section1->writeln('Hello');
        // // $section2->writeln('World!');
        // // // Output displays "Hello\nWorld!\n"

        // // // overwrite() replaces all the existing section contents with the given content
        // // sleep(2);
        // // $section1->overwrite('Goodbye');
        // return 0;
    }

    private function isGitUrl($path)
    {
        return false;
    }

    /**
     * @see https://www.php.net/manual/en/function.realpath.php
     */
    private function getAbsolutePath($path)
    {
        if(DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }
        $search = explode('/', $path);
        $search = array_filter($search, function($part) {
            return $part !== '.';
        });
        $append = array();
        $match = false;
        while(count($search) > 0) {
            $match = realpath(implode('/', $search));
            if($match !== false) {
                break;
            }
            array_unshift($append, array_pop($search));
        };
        if($match === false) {
            $match = getcwd();
        }
        if(count($append) > 0) {
            $match .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $append);
        }
        return $match;
    }
}






