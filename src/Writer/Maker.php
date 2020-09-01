<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

use Exception;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Loader;
use MarkHelp\Reader\Release;
use MarkHelp\Reader\Theme;

class Maker
{
    private Loader $loader;

    private ?string $destinationPath = null;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Salva o projeto no diretório especificado.
     * @param string $path
     * @return void
     */
    public function toDirectory(string $path): void
    {
        $this->destinationPath = $path;

        if (reliability()->isDirectory($path) === false) {
            throw new Exception("Directory {$path} is not exists");
        }
        reliability()->removeDirectory($path, true);

        $this->convert();
    }

    /**
     * Converte os arquivos e gera a estrutura html.
     * @return void
     */
    private function convert(): void
    {
        $releasesList = $this->loader->releases();

        if ($this->loader->hasReleases() === false) {
            $this->convertRelease($releasesList['_']);
            return;
        }

        array_walk($releasesList, function ($release) {
            $this->convertRelease($release);
        });
    }

    /**
    * Converte os arquivos de um release específico.
    * @return void
    */
    private function convertRelease(Release $release): void
    {
        $filesList = $release->files();
        array_walk($filesList, function ($file) use ($release) {
            $this->handleFile($release, $file);
        });
        $this->copyAssets($release, $this->loader->theme());
    }

    private function handleFile(Release $release, File $file): void
    {
        $pathPrefix = '';
        if ($release->name() !== '_') {
            $pathPrefix = $release->name();
        }

        // Imagens e outros arquivos que por ventura existam na documentação
        if ($file->type() !== File::TYPE_MARKDOWN) {
            reliability()->copyFile(
                $file->fullPath(),
                $this->destination("{$pathPrefix}/" . $file->path())
            );
            return;
        }

        $page = new Page($this->loader, $release->name(), $file);
        $page->toDirectory($this->destination($pathPrefix));
    }

    private function copyAssets(Release $release, Theme $theme): void
    {
        $pathPrefix = '';
        if ($release->name() !== '_') {
            $pathPrefix = $release->name();
        }

        $assetsList = $theme->files();

        array_walk($assetsList, function ($file) use ($pathPrefix) {
            $assetName = reliability()->basename($file->path());
            reliability()->copyFile(
                $file->fullPath(),
                $this->destination("{$pathPrefix}/assets/{$assetName}")
            );
        });
    }

    private function destination(string $path = ''): string
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        return $this->destinationPath . DIRECTORY_SEPARATOR . $path;
    }
}
