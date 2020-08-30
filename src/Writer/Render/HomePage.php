<?php

declare(strict_types=1);

namespace MarkHelp\Writer\Render;

class HomePage extends Artifact
{
    public function saveTo(string $path): void
    {
        // transformar em html
        $this->fullPath();

        $path = rtrim($path, DIRECTORY_SEPARATOR);
        reliability()->copyFile(
            $this->fullPath(), 
            $path . DIRECTORY_SEPARATOR .$this->path()
        );
    }
}
