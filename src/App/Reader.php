<?php
declare(strict_types=1);

namespace MarkHelp\App;

use MarkHelp\Bags\Asset;
use MarkHelp\Bags\Bag;
use MarkHelp\Bags\Config;
use MarkHelp\Bags\File;
use MarkHelp\Bags\Support;

class Reader
{
    use Tools;

    private $config = null;

    private $filesystem = null;

    /**
     * Esse atributo controla os diretório montados
     * do sistema de arquivo, para evitar redundância.
     * @var array
     */
    private $mountedDirectories = [];

    private $files = null;

    private $support = null;

    private $assets = null;

    /**
     * Constrói um leitor de arquivos markdown.
     * 
     * @param string $path Diretório contendo arquivos markdown
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->filesystem = new Filesystem;
        $this->filesystem->mount('origin', $this->config()->param('path.root'));
        $this->filesystem->mount("theme", $this->config()->param('path.theme'));
    }

    /**
     * Obtém a instancia do objeto de configurações.
     * 
     * @return Config
     */
    public function config() : Config
    {
        return $this->config;
    }

    /**
     * Obtém a instancia do gerenciador de arquivos.
     * 
     * @return Filesystem
     */
    public function filesystem() : Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Obtém os arquivos markdown do projeto.
     * 
     * @return array
     */
    public function markdownFiles() : array
    {
        if ($this->files === null){
            $this->files = $this->loadMarkdownFiles();    
        }

        return $this->files;
    }

/**
     * Obtém os arquivos de suporte do projeto.
     * 
     * @return array
     */
    public function supportFiles() : array
    {
        if ($this->support === null){
            $this->support = $this->loadSupportFiles();    
        }

        return $this->support;
    }

    /**
     * Obtém os assets do projeto.
     * 
     * @return array
     */
    public function assetsFiles() : array
    {
        if ($this->assets === null){
            $this->assets = $this->loadAssetsFiles();    
        }

        return $this->assets;
    }

    /**
     * Lê os arquivos markdown contidos no diretório especificado na configuração
     * e retorna uma lista de informações pertinentes.
     * 
     * @return array
     */
    private function loadMarkdownFiles() : array
    {
        $files = [];

        $list = $this->filesystem()->listContents('origin://', true);

        foreach($list as $item) {

            if ($item['type'] === 'dir' || $item['extension'] !== 'md') {
                continue;
            }

            if ($item['path'] === 'index.md') {

                $bag = new File;
                $bag->setParam('type', 'index');
                $bag->setParam('assetsPrefix', './');
                $bag->setParam('pathSearch', "index.md");
                $bag->setParam('pathReplace', "index.html");

                $files[$item['filename']] = $bag;
                continue;
            }

            $prefixUrl = $this->generatePrefix($item['path']);
            $filebase  = mb_strtolower(str_replace([' ', '.md'], ['_', '.html'], $item['path']));

            $bag = new File;
            $bag->setParam('type', 'page');
            $bag->setParam('assetsPrefix', $prefixUrl);
            $bag->setParam('pathSearch', $item['path']);
            $bag->setParam('pathReplace', $this->safeFilename($filebase));

            $files[$item['filename']] = $bag;
        }

        return $files;
    }

    /**
     * Lê os arquivos de suporte contidos na configuração especificada
     * e retorna uma lista de informações pertinentes.
     * 
     * @return array
     */
    private function loadSupportFiles() : array
    {
        $support = [];

        $allowedSupport = [
            'document' => 'document.html',
            'menu'     => 'menu.json',
        ];

        foreach($allowedSupport as $supportParam => $filename){

            $supportFile = $this->config()->param("support.{$supportParam}");

            $bag = $this->retrieveThemeSupportFile($supportFile);
            if ($bag !== null) {
                $support[$supportParam] = $bag;
                continue;
            }

            if ($this->isThemeFile($supportFile) === true){
                continue;
            }

            $bag = $this->retrieveCustomSupportFile($supportFile);
            if ($bag !== null) {
                $support[$supportParam] = $bag;
                continue;
            }

            $bag = $this->retrieveProjectSupportFile($filename);
            if ($bag !== null) {
                $support[$supportParam] = $bag;
                continue;
            }
        }

        return $support;
    }

    private function isThemeFile(?string $filename) : bool
    {
        return $filename !== null && substr($filename, 0, 9) === '{{theme}}';
    }

    /**
     * Obtém as informações do arquivo especificado
     * com base na localização do tema atual.
     * 
     * @param string $supportFile
     * @return Bag
     */
    private function retrieveThemeSupportFile($supportFile) : ?Bag
    {
        if ($supportFile === null){
            return null;
        }

        $themeSupportFile = substr($supportFile, 10);
        if ($this->filesystem()->has("theme://{$themeSupportFile}") === false) {
            return null;
        }

        $bag = new Support;
        $bag->setParam('mountPoint', 'theme');
        $bag->setParam('supportPath', $themeSupportFile);
        return $bag;
    }

    /**
     * Obtém as informações do arquivo especificado
     * com base em uma url absoluta.
     * 
     * @param string $supportFile
     * @return Bag
     */
    private function retrieveCustomSupportFile(?string $supportFile) : ?Bag
    {
        if ($supportFile === null){
            return null;
        }

        $directory = $this->dirname($supportFile);
        $basename  = $this->basename($supportFile);
        $point     = 'mount_' . md5($directory);

        if (! isset($this->mountedDirectories[$point])) {
            $this->filesystem->mount($point, $directory);
            $mountedDirectories[$point] = true;
        }

        if ($this->filesystem->has("{$point}://{$basename}") === false){
            return null;
        }

        $bag = new Support;
        $bag->setParam('mountPoint', $point);
        $bag->setParam('supportPath', $basename);
        return $bag;
    }

    /**
     * Obtém as informações do arquivo especificado
     * com base na localização do projeto.
     * 
     * @param string $supportFile
     * @return Bag
     */
    private function retrieveProjectSupportFile(?string $supportFile) : ?Bag
    {
        if ($supportFile === null) {
            return null;
        }

        $exists = $this->filesystem()->has("origin://{$supportFile}");
        if ($exists === false) {
            return null;
        }

        $bag = new Support;
        $bag->setParam('mountPoint', 'origin');
        $bag->setParam('supportPath', $supportFile);
        return $bag;
    }

    /**
     * Lê os arquivos de assets contidos na configuração especificada
     * e retorna uma lista de informações pertinentes.
     * 
     * @return array
     */
    private function loadAssetsFiles() : array
    {
        $assets = [];

        $allowedAssets = [
            'assets.styles',
            'assets.scripts',
            'assets.logo.src',
            'assets.icon.favicon',
            'assets.icon.apple'
        ];

        foreach($allowedAssets as $assetParam){

            $assetFile = $this->config()->param($assetParam);

            $bag = $this->retrieveThemeAssetFile($assetParam, $assetFile);
            if ($bag !== null) {
                $assets[$assetParam] = $bag;
                continue;
            }

            if ($this->isThemeFile($assetFile) === true){
                continue;
            }

            $bag = $this->retrieveCustomAssetFile($assetParam, $assetFile);
            if ($bag !== null) {
                $assets[$assetParam] = $bag;
                continue;
            }
        }

        return $assets;
    }

    /**
     * Obtém as informações do arquivo especificado
     * com base na localização do tema atual.
     * 
     * @param string $assetParam
     * @param string $assetFile
     * @return Bag
     */
    private function retrieveThemeAssetFile(string $assetParam, ?string $assetFile) : ?Bag
    {
        if ($assetFile === null){
            return null;
        }
        
        $themeAssetFile = substr($assetFile, 10);
        if ($this->filesystem()->has("theme://{$themeAssetFile}") === false) {
            return null;
        }

        $bag = new Asset;
        $bag->setParam('assetType', "builtin");
        $bag->setParam('mountPoint', 'theme');
        $bag->setParam('assetParam', $assetParam);
        $bag->setParam('assetPath', $themeAssetFile);
        $bag->setParam('assetBasename', $this->basename($assetFile));
        return $bag;
    }

    /**
     * Obtém as informações do arquivo especificado
     * com base em uma url absoluta.
     * 
     * @param string $assetParam
     * @param string $assetFile
     * @return Bag
     */
    private function retrieveCustomAssetFile(string $assetParam, ?string $assetFile) : ?Bag
    {
        if ($assetFile === null){
            return null;
        }

        $directory = $this->dirname($assetFile);
        $basename  = $this->basename($assetFile);
        $point     = 'mount_' . md5($directory);

        if (! isset($this->mountedDirectories[$point])) {
            $this->filesystem->mount($point, $directory);
            $mountedDirectories[$point] = true;
        }

        if ($this->filesystem->has("{$point}://{$basename}") === false){
            return null;
        }

        $bag = new Asset;
        $bag->setParam('assetType', "custom");
        $bag->setParam('mountPoint', $point);
        $bag->setParam('assetParam', $assetParam);
        $bag->setParam('assetPath', $basename);
        $bag->setParam('assetBasename', $this->basename($assetFile));
        return $bag;
    }

    /**
     * Gera o prefixo para URLs relativas.
     * 
     * @param string $url
     * @return string
     */
    private function generatePrefix(string $url) : string
    {
        $levels = substr_count ($url, "/");
        return "./" . str_repeat("../", $levels);
    }

    /**
     * Devolve um nome seguro para um arquivo a ser salvo.
     * 
     * @param string $name
     * @return string
     */
    private function safeFilename(string $name) : string
    {
        $except = array('\\', ':', '*', '?', '"', '<', '>', '|');
        return str_replace($except, '', $name);
    }
}