<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use MarkHelp\Reader\Files\File;

class Theme
{
    /** @var array<\MarkHelp\Reader\Files\File> */
    private array $files = [];

    public function addFile(File $fileInstance): Theme
    {
        $this->files[] = $fileInstance;
        return $this;
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
}
