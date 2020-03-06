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
    private function basename($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    }

    /**
     * Obtém o nome de um arquivo especificado.
     * Ex: /dir/meu-arquivo.md -> meu-arquivo
     */
    private function filename($filename)
    {
        $basename = $this->basename($filename);
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $basename);
    }

    /**
     * Obtém o nome de um diretório com base no caminho especificado.
     * Ex: /dir/meu-arquivo.md -> /dir
     */
    private function dirname($filename)
    {
        $basename = $this->basename($filename);
        return rtrim(str_replace("/" . $basename, '', $filename), '/');
    }
}