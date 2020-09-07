<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use MarkHelp\Reader\File;

class Theme
{
    /** @var array<\MarkHelp\Reader\File> */
    private array $files = [];

    private string $path;

    private string $templatesPath;

    public function __construct(string $path = '')
    {
        $this->path = rtrim($path, '/');
        
        // 0 atributo templatesPath é reconfigurado em parseTheme().
        $this->templatesPath = '';

        if ($this->path !== '') {
            $this->parseTheme($this->path);
        }
    }

    public function path(): string
    {
        return $this->path;
    }

    public function templatesPath(): string
    {
        return $this->templatesPath;
    }

    private function addFile(File $fileInstance): Theme
    {
        $this->files[] = $fileInstance;
        return $this;
    }

    /**
     * Lê os arquivos do tema
     */
    private function parseTheme(string $pathTheme): void
    {
        $themeDirectory = reliability()->mountDirectory($pathTheme);
        $themeItems = $themeDirectory->listContents("/", true);
        foreach ($themeItems as $item) {
            if ($item['type'] === 'dir' && $item['basename'] === 'templates') {
                $this->templatesPath = $pathTheme . DIRECTORY_SEPARATOR . $item['path'];
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
                $this->addFile($themeFile);
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

    /**
     * @return array<\MarkHelp\Reader\File>
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * @return array<string>
     */
    public function filesAsString(bool $generateFullPath = true): array
    {
        if ($generateFullPath === false) {
            return array_map(function ($item) {
                return $item->path();
            }, $this->files);
        }

        return array_map(function ($item) {
            return $item->basePath() . DIRECTORY_SEPARATOR . $item->path();
        }, $this->files);
    }
}
