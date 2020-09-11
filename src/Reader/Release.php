<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use MarkHelp\Reader\File;

class Release
{
    /** @var array<\MarkHelp\Reader\File> */
    private array $files = [];

    private string $name;

    private string $path;

    private int $homeIndex = 0;

    private string $configFile = '';

    public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = rtrim($path, '/');
        $this->parseRelease($this->path);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function configFile(): string
    {
        return $this->configFile;
    }

    private function addFile(File $fileInstance): Release
    {
        $this->files[] = $fileInstance;
        return $this;
    }

    private function setCurrentFileAsHome(): Release
    {
        $this->homeIndex = count($this->files) - 1;
        return $this;
    }

    public function home(): File
    {
        return $this->files[$this->homeIndex];
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

    /**
     * @return array<string, string>
     */
    public function params(): array
    {
        return [
            'release' => $this->name(),
            'index'   => $this->home()->path(),
        ];
    }

    private function parseRelease(string $pathRelease): void
    {
        $homeSetted = false;

        $releaseDirectory = reliability()->mountDirectory($pathRelease);
        $rootItems = $releaseDirectory->listContents("/", true);
        foreach ($rootItems as $item) {
            if ($item['type'] !== 'file') {
                continue;
            }

            if ($item['path'] === 'config.json') {
                $this->configFile = $item['path'];
            }

            $allowed = ['md','jpg','jpeg','png','gif','webm'];
            if (in_array($item['extension'], $allowed) === false) {
                continue;
            }

            $releaseFile = $this->parseReleaseFile($pathRelease, $item['path'], $item['extension']);
            if ($releaseFile !== null) {
                $this->addFile($releaseFile);
            }

            // O primeiro arquivo markdown deve ser um fallback
            // para caso a home não exista
            if ($item['extension'] === 'md' && $homeSetted === false) {
                $this->setCurrentFileAsHome();
                $homeSetted = true;
            }

            // A página home real
            if ($item['basename'] === 'index.md') {
                $this->setCurrentFileAsHome();
            }
        }
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
}
