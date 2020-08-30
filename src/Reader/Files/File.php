<?php

declare(strict_types=1);

namespace MarkHelp\Reader\Files;

class File
{
    protected string $basePath;

    protected string $path;

    public function __construct(string $basePath, string $path)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
        $this->path     = ltrim($path, DIRECTORY_SEPARATOR);
        return $this;
    }

    public function fullPath(): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . $this->path;
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function isInstanceOf(string $class): bool
    {
        return $this instanceof $class;
    }
}
