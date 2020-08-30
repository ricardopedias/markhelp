<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use MarkHelp\Reader\Files\Asset;
use MarkHelp\Reader\Files\File;
use MarkHelp\Reader\Files\Image;
use MarkHelp\Reader\Files\Markdown;
use MarkHelp\Reader\Git\Catcher;

class Loader
{
    private Settings $settings;

    /** @var array<Release> */
    private array $releases = [];

    private Theme $theme;

    private ?File $menuConfig = null;

    private string $templatesPath;

    /**
     * Constrói um leitor de arquivos markdown.
     * @param string $projectPath Diretório contendo arquivos markdown
     */
    public function __construct()
    {
        $this->settings      = new Settings();
        $this->theme         = new Theme();
        $this->templatesPath = $this->pathTheme('templates');
    }

    /**
     * Seta um valor de configuração.
     * @param string $param
     * @param string|null $value
     * @return \MarkHelp\Reader\Loader
     */
    public function setConfig(string $param, ?string $value): Loader
    {
        $this->settings->setParam($param, $value);
        return $this;
    }

    /**
     * Obtém um valor de configuração.
     * @param string $param
     * @return string|null
    */
    public function config(string $param): ?string
    {
        return $this->settings->param($param);
    }

    /**
     * Carrega os arquivos a partir de um repositório GIT online
     * @param string $remoteUrl
     * @param string $workDirectory Diretório onde os arquivos serão trabalhados
     * @return \MarkHelp\Reader\Loader
     */
    public function fromRemoteUrl(string $remoteUrl, string $workDirectory): Loader
    {
        // Se o nome do repositório for 'ricardopedias/markhelp-test-repo.git'
        // o clone dos releases será feito em '$workDir/markhelp-test-repo'
        $gotcha = new Catcher($remoteUrl);
        $gotcha->cloneTo($workDirectory);

        $localDirectory = $workDirectory . DIRECTORY_SEPARATOR . $gotcha->repoName();
        return $this->fromLocalDirectory($localDirectory);
    }

    /**
     * Carrega os arquivos a partir de um diretório local
     * @param string $originDirectory
     * @return \MarkHelp\Reader\Loader
     */
    public function fromLocalDirectory(string $originDirectory)
    {
        $this->setConfig('path_project', $originDirectory); 
        $this->parseProject();
        $this->parseTheme();
        return $this;
    }

    /**
     * Devolve o caminho completo até o projeto.
     * @param string $appendPath Adiciona um sufixo ao caminho
     * @return string
     */
    public function pathProject(string $appendPath = ''): string
    {
        $appendPath = str_replace(DIRECTORY_SEPARATOR, '/', $appendPath);
        $appendPath = rtrim($appendPath, '/');
        return $this->config('path_project') . DIRECTORY_SEPARATOR . $appendPath;
    }

    /**
     * Devolve o caminho completo até o tema atual.
     * @param string $appendPath Adiciona um sufixo ao caminho
     * @return string
     */
    public function pathTheme(string $appendPath = ''): string
    {
        $themePath  = $this->config('path_theme') ?? '';
        $appendPath = str_replace(DIRECTORY_SEPARATOR, '/', $appendPath);
        $appendPath = rtrim($appendPath, '/');
        return $themePath . DIRECTORY_SEPARATOR . $appendPath;
    }

    /** 
     * Lê os releases localizados no diretório do projeto
     */
    private function parseProject(): void
    {
        $root = $this->pathProject();

        $project = reliability()->mountDirectory($root);

        // Verifica se o diretório contém releases (v1.0.0, v2.0.0 etc)
        $hasReleases  = true;
        $rootItems = $project->listContents("/");
        $releases = [];
        foreach ($rootItems as $item) {
            if ($item['type'] !== 'dir') {
                $hasReleases = false;
            }

            $releases[] = $item['basename'];
        }

        // Projetos locais não possuem releases clonados
        // Usa-se o diretório principal
        if ($hasReleases === false) {
            $root = rtrim($root, '/');
            $object = $this->parseRelease($root);
            $object->setName('_');
            $this->releases['_'] = $object;
            return;
        }

        foreach ($releases as $release) {
             $object = $this->parseRelease($this->pathProject($release));
             $object->setName($release);
             $this->releases[$release] = $object;
        }
    }

    /** 
     * Lê os arquivos do tema
     */
    private function parseTheme(): void
    {
        $pathTheme = $this->pathTheme();

        $themeDirectory = reliability()->mountDirectory($pathTheme);
        $themeItems = $themeDirectory->listContents("/", true);
        foreach ($themeItems as $item) {

            if ($item['type'] === 'dir' && $item['basename'] === 'templates') {
                $this->templatesPath = $this->pathTheme($item['path']);
                continue;
            }

            if ($item['type'] !== 'file') {
                continue;
            }

            $themeFile = $this->parseThemeFile($pathTheme, $item['path'], $item['basename'], $item['extension']);
            if ($themeFile !== null) {
                $this->theme->addFile($themeFile);
            }
        }
    }

    /** 
     * Lê o conteúdo de um release
     */
    private function parseRelease(?string $pathRelease): Release
    {
        $homeSetted = false;

        $release = new Release();
        $releaseDirectory = reliability()->mountDirectory($pathRelease);
        $rootItems = $releaseDirectory->listContents("/", true);
        foreach ($rootItems as $item) {
            if ($item['type'] !== 'file') {
                continue;
            }

            $releaseFile = $this->parseReleaseFile($pathRelease, $item['path'], $item['basename'], $item['extension']);
            if ($releaseFile !== null) {
                $release->addFile($releaseFile);
            }

            // O primeiro arquivo markdown deve ser um fallback 
            // para caso a home não exista
            if ($item['extension'] === 'md' && $homeSetted === false) {
                $release->setCurrentFileAsHome();
                $homeSetted = true;
            }

            // A página home real
            if ($item['basename'] === 'index.md') {
                $release->setCurrentFileAsHome();
            }
        }

        return $release;
    }

    private function parseReleaseFile(string $basePath, string $path, string $basename, string $extension): ?File
    {
        if ($basename === 'menu.json') {
            $this->menuConfig = new File($basePath, $path);
            return null;
        }

        switch ($extension) {
            case 'md':
                return new Markdown($basePath, $path);
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webm':
                return new Image($basePath, $path);
        }

        return null;
    }

    private function parseThemeFile(string $basePath, string $path, string $basename, string $extension): ?File
    {
        if ($basename === '.gitignore') {
            return null;
        }

        if ($basename === 'package.json') {
            return null;
        }

        if ($basename === 'webpack.mix.js') {
            return null;
        }

        // Diretório resources contém assets do mix
        $isResource = (int)preg_match('/.*(resources\/js).*/', $path);
        if ($isResource > 0) {
            return null;
        }

        switch ($extension) {
            case 'js':
            case 'css':
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webm':
            case 'ico':
                return new Asset($basePath, $path);
            default:
        }

        return null;
    }

    public function hasReleases(): bool
    {
        return isset($this->releases['_']) === false
            && count($this->releases) > 1;
    }

    public function releases(): array
    {
        return $this->releases;
    }

    public function theme(): Theme
    {
        return $this->theme;
    }

    public function params(): array
    {
        return $this->settings->allParams();
    }
    
    public function menuConfig(): ?File
    {
        return $this->menuConfig;
    }

    public function templatesPath(): string
    {
        return $this->templatesPath;
    }
    
//     /**
//      * Devolve um nome seguro para um arquivo a ser salvo.
//      * @param string $name
//      * @return string
//      */
//     private function safeFilename(string $name): string
//     {
//         $except = array('\\', ':', '*', '?', '"', '<', '>', '|');
//         return str_replace($except, '', $name);
//     }
}
