<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use MarkHelp\Reader\File;
use MarkHelp\Reader\Git\Catcher;

class Loader
{
    private Settings $settings;

    /** @var array<Release> */
    private array $releases = [];

    private Theme $theme;

    private string $templatesPath;

    /**
     * Constrói um leitor de arquivos markdown.
     */
    public function __construct()
    {
        $this->settings      = new Settings();
        $this->theme         = new Theme($this->pathTheme());
        $this->templatesPath = $this->pathTheme('templates');
    }

    /**
     * Seta um valor de configuração.
     * @param string $param
     * @param string $value
     * @return \MarkHelp\Reader\Loader
     */
    public function setConfig(string $param, string $value): Loader
    {
        $this->settings->setParam($param, $value);
        return $this;
    }

    /**
     * Obtém um valor de configuração.
     * @param string $param
     * @return string
    */
    public function config(string $param): string
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
            $object = $this->parseRelease('_', $root);
            $this->releases['_'] = $object;
            return;
        }

        foreach ($releases as $release) {
             $object = $this->parseRelease($release, $this->pathProject($release));
             $this->releases[$release] = $object;
        }
    }

    /**
     * Lê o conteúdo de um release
     */
    private function parseRelease(string $name, string $pathRelease): Release
    {
        $homeSetted = false;

        $release = new Release($name, $pathRelease);
        $releaseDirectory = reliability()->mountDirectory($pathRelease);
        $rootItems = $releaseDirectory->listContents("/", true);
        foreach ($rootItems as $item) {
            if ($item['type'] !== 'file') {
                continue;
            }

            $allowed = ['md','jpg','jpeg','png','gif','webm'];
            if (in_array($item['extension'], $allowed) === false) {
                continue;
            }

            $releaseFile = $this->parseReleaseFile($pathRelease, $item['path'], $item['extension']);
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

    private function parseReleaseFile(string $basePath, string $path, string $extension): File
    {
        $file = new File($basePath, $path);

        switch ($extension) {
            case 'md':
                $file->setType(File::TYPE_MARKDOWN);
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webm':
                $file->setType(File::TYPE_IMAGE);
        }

        return $file;
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

            if (preg_match('/node_modules|resources|templates/', $item['path'])) {
                continue;
            }

            if ($item['type'] === 'dir') {
                continue;
            }

            $allowed = ['ico','jpg','jpeg','png','gif','webm', 'js', 'css'];
            if (in_array($item['extension'], $allowed) === false) {
                continue;
            }

            $isAsset = (bool)preg_match('/assets/', $item['path']);
            if ($item['extension'] === 'js' && $isAsset === false) {
                continue;
            }

            $themeFile = $this->parseThemeFile($pathTheme, $item['path'], $item['basename'], $item['extension']);
            if ($themeFile !== null) {
                $this->theme->addFile($themeFile);
            }
        }
    }

    private function parseThemeFile(string $basePath, string $path, string $basename, string $extension): File
    {
        $file = new File($basePath, $path);

        if ($basename === '.gitignore') {
            return $file;
        }

        if ($basename === 'package.json') {
            return $file;
        }

        if ($basename === 'webpack.config.js') {
            return $file;
        }

        // Diretório resources contém assets do mix
        $isResource = (int)preg_match('/.*(resources\/js).*/', $path);
        if ($isResource > 0) {
            return $file;
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
                $file->setType(File::TYPE_ASSET);
        }

        return $file;
    }

    public function hasReleases(): bool
    {
        return isset($this->releases['_']) === false
            && count($this->releases) > 1;
    }

    /**
     * @return array<\MarkHelp\Reader\Release>
     */
    public function releases(): array
    {
        return $this->releases;
    }

    public function theme(): Theme
    {
        return $this->theme;
    }

    /**
     * @return array<string>
     */
    public function params(): array
    {
        return $this->settings->allParams();
    }
    
    public function templatesPath(): string
    {
        return $this->templatesPath;
    }
}
