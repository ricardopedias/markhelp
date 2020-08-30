<?php

declare(strict_types=1);

namespace MarkHelp\Reader\Git;

use Exception;

class Catcher
{
    private string $remoteUrl;

    private ?string $repoName = null;

    private string $docsDirectory = 'docs';

    /** @var array<string> */
    private array $releases = [];

    /** @var array<string> */
    private array $listCloneds = [];

    public function __construct(string $gitUrl)
    {
        $this->remoteUrl = $gitUrl;
    }

    /**
     * Devolve o nome do repositório especificado.
     * @return string
     */
    public function repoName(): string
    {
        return $this->repoName === null
            ? $this->extractCloneName($this->remoteUrl)
            : $this->repoName;
    }

    /**
     * Adiciona um release a ser clonado.
     * Ex: 2.0.1
     * @param string $release
     * @return \MarkHelp\Reader\Git\Catcher
     */
    public function addRelease(string $release): Catcher
    {
        $this->releases[] = $release;
        return $this;
    }

    /**
     * Faz o clone do repositório no diretório especificado.
     * Ex: Se o nome do repositório for 'ricardopedias/markhelp-test-repo.git'
     * e o parâmetro $path for '/home/ricardo/teste',
     * o clone dos releases será feito em '/home/ricardo/teste/markhelp-test-repo'
     * @param string $path
     * @return void
     */
    public function cloneTo(string $path): void
    {
        $commands = new Commands();
        if ($commands->isValidRemote($this->remoteUrl) === false) {
            throw new Exception("The url {$this->remoteUrl} is invalid");
        }

        $this->extractRepositoryTo($path);
    }

    /**
     * Extrai os arquivos de documentos de um repositório.
     * @param string $path
     * @return void
     */
    protected function extractRepositoryTo(string $path): void
    {
        $cloneName = $this->repoName() . "-clone";
        $clonePath = $path . DIRECTORY_SEPARATOR . $cloneName;

        // Caso o clone já tenha sido feito anteriormente, remove-o
        if (reliability()->isDirectory($clonePath) === true) {
            reliability()->removeDirectory($clonePath);
        }

        $commands = new Commands();
        $commands->clone($this->remoteUrl, $clonePath);

        $releasesRepo = $commands->getReleases($clonePath);
        $releasesList = $releasesRepo;

        if (count($this->releases) > 0) {
            $releasesConfig = $this->releases;
            $releasesList = array_intersect($releasesConfig, $releasesRepo);
        }

        // Caso uma extração já tenha sido feita anteriormente, remove-a
        $finalPath = $path . DIRECTORY_SEPARATOR . $this->repoName();
        if (reliability()->isDirectory($finalPath) === true) {
            reliability()->removeDirectory($finalPath);
        }

        // Extrai os releases dentro do diretório com o nome do repositório
        // Ex: markhelp-test-repo/v1.0.0, markhelp-test-repo/v2.0.0 etc
        foreach ($releasesList as $release) {
            $this->extractReleaseFiles($release, $clonePath);
        }

        // Remove o diretório clonado com todo o seu conteúdo
        reliability()->removeDirectory($clonePath);
    }

    /**
     * Devolve o nome do repositório a partir do URL fornecido.
     * @param  string $url /path/to/repo.git | host.xz:foo/.git | ...
     * @return string
     */
    private function extractCloneName(string $url): string
    {
        // host.xz:foo/.git => foo
        // /path/to/repo.git => repo
        $directory = rtrim($url, '/');
        if (substr($directory, -5) === '/.git') {
            $directory = substr($directory, 0, -5);
        }

        $directory = basename($directory, '.git');

        if (($pos = strrpos($directory, ':')) !== false) {
            $directory = substr($directory, $pos + 1);
        }

        return $directory;
    }

    public function extractReleaseFiles(string $release, string $clonePath): void
    {
        $cloneName       = reliability()->basename($clonePath);
        $destinationPath = reliability()->dirname($clonePath);
        $originDocsPath = "{$cloneName}/{$this->docsDirectory}";
        
        $commands = new Commands();
        $commands->checkoutRelease($clonePath, $release);

        $destination = reliability()->mountDirectory($destinationPath);
        $list = $destination->listContents("{$originDocsPath}", true);
        foreach ($list as $item) {
            if ($item['type'] === 'dir') {
                continue;
            }

            $file = ltrim(str_replace($originDocsPath, '', $item['path']), "/");
            $destination->copy("{$item['path']}", $this->repoName() . "/{$release}/{$file}");
        }

        $this->listCloneds[$release] = $this->repoName() . "/{$release}";
    }

    /**
     * Devolve uma lista com as informações de documentações clonadas.
     * @return array<string>
     */
    public function allCloneds(): array
    {
        return $this->listCloneds;
    }
}
