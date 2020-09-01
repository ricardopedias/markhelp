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

    public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setPath(string $path): Release
    {
        $this->path = $path;
        return $this;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function addFile(File $fileInstance): Release
    {
        $this->files[] = $fileInstance;
        return $this;
    }

    public function setCurrentFileAsHome(): Release
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
    public function files(bool $flaten = false): array
    {
        if ($flaten === false) {
            return $this->files;
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
}
