<?php 
declare(strict_types=1);
namespace MarkHelp\Console\Commands;

use MarkHelp\Console\Commands\Command;
use MarkHelp\MarkHelp;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MainCommand extends Command
{
    protected static $defaultName = 'make';

    protected function configure()
    {
        $this->setDescription('Create an HTML site based on the markdown files');
        $this->setHelp('This command scans a directory containing markdown files and generates browsable HTML pages in the form of documentation');
        // $this->addArgument('file', InputArgument::OPTIONAL, 'The specific file to be tested');
        // $this->addArgument('file', InputArgument::OPTIONAL, 'The specific file to be tested');

        $this->addOption(
            'input',
            'i',
            InputOption::VALUE_OPTIONAL,
            'Specifies the location of the markdown files',
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $origin      = $input->getOption('input');
        $repository  = $input->getOption('repository');
        $destination = $input->getOption('output');
        $config      = $input->getOption('config');

        $currentDir = str_replace("\n", "", shell_exec('pwd'));

        if ($origin === NULL && $repository === NULL) {
            $origin = $currentDir;
            $output->writeln("Using current directory: {$origin}");
        }

        if ($config !== null && is_file($currentDir . DIRECTORY_SEPARATOR . 'config.json')) {
            $config = $currentDir . DIRECTORY_SEPARATOR . 'config.json';
        }

        // TODO: identificar se é um repositório do git
        // baixar o repo e usar como origem

        $app = new MarkHelp($origin);

        if ($config !== null) {
            $app->loadConfigFrom($config);
            $output->writeln("Load config.json");
        }
        
        $app->saveTo($destination);
        $output->writeln("Documentation site successfully generated in {$destination}");


        // $helper = $this->getHelper('question');
        // $question = new Question('Please enter the name of the bundle: ', 'AcmeDemoBundle');
        // $bundleName = $helper->ask($input, $output, $question);
        
        // $output->writeln($bundleName);

        // $file = $input->getArgument('file');
        // $output->writeln('xxxxx ' . $file);

        // $section1 = $output->section();
        // $section2 = $output->section();
        // $section1->writeln('Hello');
        // $section2->writeln('World!');
        // // Output displays "Hello\nWorld!\n"

        // // overwrite() replaces all the existing section contents with the given content
        // sleep(2);
        // $section1->overwrite('Goodbye');
        return 0;
    }
}






