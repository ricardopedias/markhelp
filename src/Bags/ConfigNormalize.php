<?php

declare(strict_types=1);

namespace MarkHelp\Bags;

use MarkHelp\App\Reliability;

class ConfigNormalize
{
    private string $projectPath;

    private string $themePath;

    public function __construct(string $projectPath, string $themePath)
    {
        $this->projectPath = $projectPath;
        $this->themePath   = $themePath;
    }

    public function normalize(string $name, ?string $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        $result = null;

        switch ($type) {
            case 'path':
                $result = $this->normalizePath($value, true) ?? null;
                break;
            case 'file':
                $result = $this->normalizeFile($value);
                break;
            case 'bool':
                $result = $this->normalizeBool($value);
                break;
            default:
                $result = $this->normalizeValue($value);
        }

        return $result;
    }

    public function normalizePath(string $value, bool $checkExist = false): ?string
    {
        // remove o separador de diretórios na extremidade direita
        $value = rtrim($value, DIRECTORY_SEPARATOR);
        $value = $this->parseTemplateTags($value) ?? '';
        
        if ($checkExist === true) {
            return (new Reliability())->isDirectory($value) ? $value : null;
        }
        
        return $value;
    }

    public function normalizeFile(string $value): ?string
    {
        $reliability = new Reliability();
        $value = $this->parseTemplateTags($value) ?? '';

        // busca no diretório do projeto
        $projectFileBasename = $reliability->basename($value);
        $projectFile = $this->projectPath . DIRECTORY_SEPARATOR . $projectFileBasename;
        if ($reliability->isFile($projectFile) === true) {
            return $projectFile;
        }

        if ($reliability->isFile($value) === true) {
            return $value;
        }
        
        return null;
    }

    public function normalizeBool(string $value): ?string
    {
        if ($value === "" || $value === 'false') {
            return '0';
        }

        return (string)(bool)$value;
    }

    public function normalizeValue(string $value): ?string
    {
        // caminhos padrões dinâmicos, contendo variáveis {{theme|project}} devem ser reprocessados
        $tags = strpos((string) $value, "{{theme}}") !== false
             || strpos((string) $value, "{{ theme }}") !== false
             || strpos((string) $value, "{{project}}") !== false
             || strpos((string) $value, "{{ project }}") !== false;

        if ($tags === true) {
            $value = $this->parseTemplateTags($value);
        }

        return $value;
    }

    public function parseTemplateTags(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // remove o separador de diretórios na extremidade direita
        $themePath   = rtrim($this->themePath, DIRECTORY_SEPARATOR);
        $projectPath = rtrim($this->projectPath, DIRECTORY_SEPARATOR);

        $value = str_replace(["{{theme}}", "{{ theme }}"], $themePath, $value);
        $value = str_replace(["{{project}}", "{{ project }}"], $projectPath, $value);
        return $value;
    }
}
