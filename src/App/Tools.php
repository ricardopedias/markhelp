<?php
declare(strict_types=1);

namespace MarkHelp\App;

use League\CommonMark\CommonMarkConverter;

trait Tools
{
    /**
     * Obtém o nome + extensão de um arquivo especificado.
     * Ex: /dir/meu-arquivo.md -> meu-arquivo.md
     */
    public function basename($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    }

    /**
     * Obtém o nome de um arquivo especificado.
     * Ex: /dir/meu-arquivo.md -> meu-arquivo
     */
    public function filename($filename)
    {
        $basename = $this->basename($filename);
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $basename);
    }

    /**
     * Obtém o nome de um diretório com base no caminho especificado.
     * Ex: /dir/meu-arquivo.md -> /dir
     */
    public function dirname($filename)
    {
        $basename = $this->basename($filename);
        return rtrim(str_replace("/" . $basename, '', $filename), '/');
    }

    /**
     * Obtém o caminho absoluto do caminho relativo informado.
     * @see https://www.php.net/manual/en/function.realpath.php
     */
    public function absolutePath($path)
    {
        if(DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }
        $search = explode('/', $path);
        $search = array_filter($search, function($part) {
            return $part !== '.';
        });
        $append = array();
        $match = false;
        while(count($search) > 0) {
            $match = realpath(implode('/', $search));
            if($match !== false) {
                break;
            }
            array_unshift($append, array_pop($search));
        };
        if($match === false) {
            $match = getcwd();
        }
        if(count($append) > 0) {
            $match .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $append);
        }
        return $match;
    }

    public function isFile($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return is_file($filename);
    }
    
}