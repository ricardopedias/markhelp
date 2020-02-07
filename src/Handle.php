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

    // Funções seguras

    public function include($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return include($filename);
    }

    public function getContents($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return file_get_contents($filename);
    }

    public function isFile($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return is_file($filename);
    }

    public function isDirectory($path)
    {
        $path = filter_var($path, FILTER_SANITIZE_STRING);
        return is_dir($path);
    }

    public function dirname($path, $levels = 1)
    {
        $path = filter_var($path, FILTER_SANITIZE_STRING);

        for($x=0; $x < $levels; $x++) {
            $path = dirname($path);
        }
        return $path;
    }

    public function basename($filename)
    {
        return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    }

    public function filename($filename)
    {
        $basename = self::basename($filename);
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $basename);
    }

    public function copy($filename, $newFilename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        $newFilename = filter_var($newFilename, FILTER_SANITIZE_STRING);
        return copy($filename, $newFilename);
    }
}