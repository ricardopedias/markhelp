<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use MarkHelp\Reader\File;

class Theme
{
    /** @var array<\MarkHelp\Reader\File> */
    private array $files = [];

    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function addFile(File $fileInstance): Theme
    {
        $this->files[] = $fileInstance;
        return $this;
    }

    /**
     * @return array<\MarkHelp\Reader\File>
     */
    public function files(bool $flaten = false): array
    {
        if ($flaten === false) {
            return $this->files;
        }
        
        return array_map(function ($item) {
            return $item->basePath() . DIRECTORY_SEPARATOR . $item->path();
        }, $this->files);
    }
}
