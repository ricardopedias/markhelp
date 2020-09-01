<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

class File
{
    public const TYPE_REGULAR = 'regular';

    public const TYPE_MARKDOWN = 'markdown';

    public const TYPE_IMAGE = 'image';

    public const TYPE_ASSET = 'asset';

    protected string $basePath;

    protected string $path;

    protected string $type = self::TYPE_REGULAR;

    public function __construct(string $basePath, string $path)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
        $this->path     = ltrim($path, DIRECTORY_SEPARATOR);
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

    public function setType(string $type = self::TYPE_REGULAR): File
    {
        $this->type = $type;
        return $this;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isInstanceOf(string $class): bool
    {
        return $this instanceof $class;
    }
}
