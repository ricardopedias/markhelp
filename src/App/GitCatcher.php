<?php
declare(strict_types=1);

namespace MarkHelp\App;

use Exception;

class GitCatcher
{
    use Tools;

    private $rootPath = null;

    private $repositories = [];

    private $branchs = [];

    private $listCloneds = [];

    public function __construct()
    {
        $this->filesystem = new Filesystem;        
    }

    /**
     * Devolve uma lista com as informações de documentações clonadas.
     * 
     * @return array
     */
    public function cloneds() : array
    {
        return $this->listCloneds;
    }

    /**
     * Adiciona um repositório para ser clonado.
     * 
     * @param string $gitUrl 
     * @param string $docsDirectory
     * @param array $versionBranchs
     * @return GitCatcher
     */
    public function addRepo(string $gitUrl, string $docsDirectory = 'docs', array $versionBranchs = []) : GitCatcher
    {
        $this->repositories[$gitUrl] = $docsDirectory;
        $this->branchs[$gitUrl] = count($versionBranchs) === 0 ? ['master'] : $versionBranchs;
        return $this;
    }

    /**
     * Faz o clone dos repositórios no diretório especificado.
     * 
     * @param string $path
     * @return void
     */
    public function grabTo(string $path) : void
    {
        $this->rootPath = $path;

        $this->filesystem->mount('destination', $this->rootPath);

        $this->cleanup();

        foreach($this->repositories as $url => $copyDirectory) {

            if ($this->isRemoteUrlReadable($url) === false) {
                throw new Exception("The url {$url} is invalid");
            }

            $repoName = $this->extractRepositoryNameFromUrl($url);

            $this->extractRepository($repoName, $url, $copyDirectory);
        }
    }

    /**
     * Extrai os arquivos de documentos de um repositório.
     * 
     * @param string $repoName
     * @param string $url 
     * @param string $copyDirectory
     * @return void
     */
    protected function extractRepository(string $repoName, string $url, string $copyDirectory) : void
    {
        $cloneName = "{$repoName}-clone";
        $clonePath = $this->rootPath . DIRECTORY_SEPARATOR . $cloneName;

        $this->cloneRepository($url, $clonePath);

        $branchsRepo = $this->getBranches($clonePath);
        $branchsArgs = $this->branchs[$url];
        $branchsList = array_intersect($branchsArgs, $branchsRepo);

        foreach($branchsList as $branchName) {
            $this->extractBranch($branchName, $repoName, $copyDirectory, $cloneName, $clonePath);
        }

        $this->cleanup($cloneName);
    }

    public function extractBranch(string $branchName, string $repoName, string $copyDirectory, string $cloneName, string $clonePath) : void
    {
        $this->checkout($clonePath, $branchName);

        $extractDirectoryPath = "{$cloneName}/{$copyDirectory}";

        $list = $this->filesystem->listContents("destination://{$extractDirectoryPath}", true);
        foreach($list as $item) {

            if ($item['type'] === 'dir') {
                continue;
            }

            $file = ltrim(str_replace($extractDirectoryPath, '', $item['path']), "/");
            $this->filesystem->copy("destination://{$item['path']}", "destination://{$repoName}/{$branchName}/{$file}");
        }

        if (isset($this->listCloneds[$repoName]) === false) {
            $this->listCloneds[$repoName] = [];
        }

        $this->listCloneds[$repoName][$branchName] = [ 
            'path' => "{$repoName}/{$branchName}" 
        ];
    }

    /**
     * Remove o diretório especificado.
     * 
     * @return void
     */
    private function cleanup($path = null) : void
    {
        if ($path !== null) {
            $this->filesystem->deleteDir("destination://{$path}");
            return;
        }

        $cleanup = $this->filesystem->listContents('destination://');
        foreach($cleanup as $item) {
            if ($item['type'] === 'dir') {
                $this->filesystem->deleteDir("destination://{$item['path']}");
                continue;
            }
            $this->filesystem->delete("destination://{$item['path']}");
        }
    }

    /**
     * Executa um comando de terminal.
     * 
     * @param  string|array $command
     * @return GitCatcher
     * @throws Exception
     */
    protected function run($command) : GitCatcher
    {
        $arguments = func_get_args();
        $command = $this->processCommand($arguments);
        exec($command . ' 2>&1', $output, $returnCode);

        if($returnCode !== 0) {
            throw new Exception("Command '{$command}' failed (exit-code {$returnCode}).", $returnCode);
        }

        return $this;
    }

    /**
     * Processa um comando de terminal, validando suas entradas.
     * 
     * @param array $arguments
     * @return string
     */
    protected function processCommand(array $arguments)
    {
        $command = [];

        $programName = array_shift($arguments);

        foreach ($arguments as $arg) {

            if (is_array($arg)) {

                foreach($arg as $key => $value) {
                    $escapedCommand = '';
                    if(is_string($key)) {
                        $escapedCommand = "$key ";
                    }
                    $command[] = $escapedCommand . escapeshellarg($value);
                }

                continue;
            } 

            if ($arg !== null) {
                $command[] = escapeshellarg($arg);
            }
        }

        return "$programName " . implode(' ', $command);
    }

    /**
     * Devolve o nome do repositório a partir da url fornecida.
     * 
     * @param  string $url /path/to/repo.git | host.xz:foo/.git | ...
     * @return string
     */
    public function extractRepositoryNameFromUrl(string $url) : string
    {
        // host.xz:foo/.git => foo
        // /path/to/repo.git => repo
        $directory = rtrim($url, '/');
        if (substr($directory, -5) === '/.git') {
            $directory = substr($directory, 0, -5);
        }

        $directory = basename($directory, '.git');

        if(($pos = strrpos($directory, ':')) !== false) {
            $directory = substr($directory, $pos + 1);
        }

        return $directory;
    }

    /**
     * Verifica se o URL é de um repositório válido.
     * 
     * @param  string $url
     * @param  array|null $refs
     * @return bool
     */
    public function isRemoteUrlReadable(string $url, array $refs = null) : bool
    {
        $env = 'GIT_TERMINAL_PROMPT=0';

        if (DIRECTORY_SEPARATOR === '\\') { 
            // Windows
            $env = 'set GIT_TERMINAL_PROMPT=0 &&';
        }

        $output = null;
        $returnCode = 1;

        exec($this->processCommand([
            $env . ' git ls-remote',
            '--heads',
            '--quiet',
            '--exit-code',
            $url,
            $refs,
        ]) . ' 2>&1', $output, $returnCode);

        return $returnCode === 0;
    }

    /**
     * Clona um repositório GIT a partir da url fornecida.
     * 
     * @param  string $url
     * @param  string $directory
     * @param  array|null $params
     * @return bool
     * @throws Exception
     */
    public function cloneRepository(string $url, string $directory, ?array $params = null) : bool
    {
        if ($this->isDirectory("$directory/.git")) {
            throw new Exception("Repository already exists in $directory.");
        }

        if ($params === null) {
            $params = '-q';
        }

        $descriptorSpec = array(
            0 => array('pipe', 'r'), // stdin
            1 => array('pipe', 'w'), // stdout
            2 => array('pipe', 'w'), // stderr
        );

        $pipes = [];
        $command = self::processCommand(array(
            'git clone',
            $params,
            $url,
            $directory
        ));
        $process = proc_open($command, $descriptorSpec, $pipes);

        if ($process === false) {
            throw new Exception("Git clone failed to directory {$directory}.");
        }

        // Limpa a saída e os erros
        $stdout = '';
        $stderr = '';

        while (true) {

            // lê a saída padrão
            $output = fgets($pipes[1], 1024);
            if ($output) {
                $stdout .= $output;
            }

            // lê saída de erro
            $output_err = fgets($pipes[2], 1024);
            if ($output_err) {
                $stderr .= $output_err;
            }

            // execução do comando finalizada
            if ((feof($pipes[1]) OR $output === false) 
             && (feof($pipes[2]) OR $output_err === false)
            ) {
                break;
            }
        }

        $returnCode = proc_close($process);
        if ($returnCode !== 0) {
            throw new Exception("Git clone failed (directory $directory)." . ($stderr !== '' ? ("\n$stderr") : ''));
        }

        return true;
    }

    /**
     * Devolve a lista de branchs presentes no repositorio.
     * 
     * @return array
     * @throws Exception
     */
    public function getBranches(string $path) : array
    {
        $list = $this->extractFromCommand("cd {$path}; git branch -r", function($value) {
            $value = trim(substr($value, 1));
            $value = explode('/', $value)[1];
            return $value;
        });

        return array_filter($list, function($value){
            return strpos($value, 'HEAD') === false;
        });
    }

    /**
     * Faz o chckout em um Branch.
     * 
     * @param string $path
     * @param string $branch
     * @throws Exception
     * @return self
     */
    public function checkout(string $path, string $branch)
    {
        return $this->run("cd {$path}; git checkout", $branch);
    }
    
    /**
     * Returns list of tags in repo.
     * 
     * @return string[]|NULL  NULL => no tags
     * @throws GitException
     */
    public function getTags()
    {
        return $this->extractFromCommand('git tag', 'trim');
    }

    /**
     * @param  string $command
     * @param  callback|null $filter
     * @return array|null
     * @throws Exception
     */
    protected function extractFromCommand(string $command, $filter = null)
    {
        $output = [];
        $exitCode = null;

        exec("$command", $output, $exitCode);

        if ($exitCode !== 0 || !is_array($output)) {
            throw new Exception("Command $command failed.");
        }

        if ($filter !== null){

            $newArray = [];
            foreach ($output as $line) {

                $value = $filter($line);
                if($value === false){
                    continue;
                }
                $newArray[] = $value;
            }

            $output = $newArray;
        }

        if(!isset($output[0])){
            // array vazio
            return null;
        }

        return $output;
    }
}