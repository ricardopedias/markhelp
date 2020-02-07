<?php
declare(strict_types=1);

namespace MarkHelp;

use League\Flysystem\Adapter;
use League\Flysystem\Filesystem;

abstract class Handle
{
    private $base = null;

    protected $structure = [
        'index' => null,
        'menu'  => null,
        'pages' => []
    ];

    /**
     * Seta o diretório base.
     * 
     * @return string
     */
    public function setPathBase(string $path) 
    {
        $this->base = $path;
    }

    /**
     * Devolve o diretório base.
     * 
     * @return string
     */
    public function pathBase() : string
    {
        return $this->base;
    }

    protected function filesystem()
    {
        $adapter = new Adapter\Local($this->pathBase());
        return new Filesystem($adapter);
    }

    /**
     * Devolve a estrutura de arquivos.
     * 
     * @return array
     */
    public function all() : array
    {
        return $this->structure;
    }

    protected function directoryExists($path)
    {
        return is_dir($path);
    }

    protected function fileExists($file)
    {
        return is_file($file);
    }
}