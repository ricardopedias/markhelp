<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use MarkHelp\Reader\Files\File;

class Release
{
    /** @var array<\MarkHelp\Reader\Files\File> */
    private array $files = [];

    private ?string $name = null;

    private int $homeIndex = 0;

    public function setName(string $name): Release
    {
        $this->name = $name;
        return $this;
    }

    public function name(): ?string
    {
        return $this->name;
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

    public function files(bool $flaten = false): array
    {
        if ($flaten === false) {
            return $this->files;
        }
        
        return array_map(function($item){
            return $item->basePath() . DIRECTORY_SEPARATOR . $item->path();
        }, $this->files);
    }

    public function params(): array
    {
        return [
            'release' => $this->name(),
            'index'   => $this->home()->path(),
        ];
    }
}
