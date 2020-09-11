<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use Error;
use Exception;
use MarkHelp\Reader\Git\Catcher;

class Loader
{
    private Settings $settings;

    /** @var array<Release> */
    private array $releases = [];

    private Theme $theme;

    private string $defaultTemplatesPath;

    private string $configFile = '';

    /**
     * Constrói um leitor de arquivos markdown.
     */
    public function __construct()
    {
        $this->settings = new Settings();
        $this->defaultTemplatesPath = $this->pathTheme('templates');

        // 0 atributo theme é reconfigurado em fromLocalDirectory().
        $this->theme = new Theme();
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
        $this->theme = new Theme($this->pathTheme());

        if ($this->configFile !== '') {
            $this->loadConfigFrom($originDirectory . DIRECTORY_SEPARATOR . $this->configFile);
        }
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
            $release = new Release('_', $root);
            $this->checkConfigFile($release);
            $this->releases['_'] = $release;
            return;
        }

        foreach ($releases as $directory) {
            $release = new Release(
                $directory,
                $this->pathProject($directory)
            );
            $this->checkConfigFile($release);
            $this->releases[$directory] = $release;
        }
    }

    private function checkConfigFile(Release $release): void
    {
        if ($this->configFile !== '' || $release->configFile() === '') {
            return;
        }

        if ($release->name() === '_') {
            $this->configFile = $release->configFile();
            return;
        }

        $this->configFile = $release->name() . DIRECTORY_SEPARATOR . $release->configFile();
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
        $path = $this->theme->templatesPath();
        if ($path === '') {
            $path = $this->defaultTemplatesPath;
        }
        return $path;
    }

    public function loadConfigFrom(string $configJson): Loader
    {
        if (reliability()->isFile($configJson) === false) {
            throw new Exception("The specified configuration file {$configJson} does not exist");
        }

        $directory  = reliability()->dirname($configJson);
        $file       = reliability()->basename($configJson);
        $filesystem = reliability()->mountDirectory($directory);

        $jsonContent = $filesystem->read($file);

        if ($jsonContent === false) {
            throw new Exception("The config file does not contain a valid json");
        }

        $configList = @json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("The config file does not contain a json: " . \json_last_error_msg());
        }

        try {
            foreach ($configList as $param => $value) {
                if (is_array($value) === true) {
                    throw new Exception("Parameter {$param} does not contain a valid value");
                }

                $this->setConfig($param, (string)$value);
            }
        } catch (Error $e) {
            throw new Exception("The config file format is invalid. " . $e->getMessage(), $e->getCode());
        }
        
        return $this;
    }
}
