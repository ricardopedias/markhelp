<?php

declare(strict_types=1);

namespace MarkHelp\Reader;

use Exception;

class Settings
{
    private const TYPE_PATH = 'path';

    private const TYPE_STRING = 'string';

    private const TYPE_FILE = 'file';

    private const TYPE_BOOL = 'bool';

    /** @var array<string> */
    protected array $params = [];

    /** @var array<string> */
    private array $types = [];

    public function __construct()
    {
        $pathTheme = \reliability()->dirname(__DIR__) . '/Themes/default';

        $this->setupParam(self::TYPE_PATH, 'path_project', '');
        $this->setupParam(self::TYPE_PATH, 'path_theme', $pathTheme);

        $this->setupParam(self::TYPE_STRING, 'clone_url', 'https://github.com/ricardopedias/markhelp');
        $this->setupParam(self::TYPE_STRING, 'clone_directory', 'docs');
        $this->setupParam(self::TYPE_STRING, 'clone_tags', 'dev-master');

        $this->setupParam(self::TYPE_STRING, 'copy_name', 'Ricardo Pereira');
        $this->setupParam(self::TYPE_STRING, 'copy_url', 'http://www.ricardopedias.com.br');

        $this->setupParam(self::TYPE_STRING, 'project_name', 'Mark Help');
        $this->setupParam(self::TYPE_STRING, 'project_slogan', 'Gerador de documentação');
        $this->setupParam(self::TYPE_BOOL, 'project_fork', 'true');
        $this->setupParam(self::TYPE_STRING, 'project_description', 'Gerador de documentação feito em PHP');
        $this->setupParam(self::TYPE_FILE, 'project_logo_status', 'enabled');
        $this->setupParam(self::TYPE_FILE, 'project_logo', '{{project}}/images/logo.png');
    }

    /**
     * Seta um valor de configuração.
     * @param string $name
     * @param string $value
     * @return \MarkHelp\Reader\Settings
     */
    public function setParam(string $name, string $value): Settings
    {
        if ($this->validadeParam($name) === false) {
            throw new Exception("Param {$name} is invalid");
        }

        if (in_array($this->types[$name], [self::TYPE_PATH, self::TYPE_FILE]) && $value !== '') {
            $value = rtrim($value, DIRECTORY_SEPARATOR);
            $value = str_replace(['{{ ',' }}'], ['{{','}}'], $value);
        }

        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Valida um parâmetro e seu valor.
     * @param string $name
     * @return bool
     */
    private function validadeParam(string $name): bool
    {
        if (isset($this->params[$name]) === false) {
            return false;
        }

        // Se path.root já tiver sido setado, não pode ser alterado
        if ($name === 'path_project' && $this->params[$name] !== "") {
            return false;
        }

        return true;
    }

    /**
     * Obtém um valor de configuração.
     * @param string $name
     * @return string
    */
    public function param(string $name): string
    {
        if (isset($this->params[$name]) === false) {
            throw new Exception("Param {$name} is not exists");
        }

        if (in_array($this->types[$name], [self::TYPE_PATH, self::TYPE_FILE]) === true) {
            return $this->parseTags($this->params[$name]);
        }

        return $this->params[$name];
    }

    /**
     * Devolve uma lista com todos os parâmetros existentes
     * @return array<string>
    */
    public function allParams(): array
    {
        return $this->params;
    }

    /**
     * Seta um valor de configuração padrão.
     * @param string $type
     * @param string $name
     * @param string $value
     * @return \MarkHelp\Reader\Settings
     */
    private function setupParam(string $type, string $name, string $value): Settings
    {
        $this->params[$name] = $value;
        $this->types[$name] = $type;
        return $this;
    }

    private function parseTags(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $projectPath = $this->params['path_project'] ?? '';
        if ($projectPath !== '') {
            $value = str_replace("{{project}}", $projectPath, $value);
        }

        $themePath = $this->params['path_theme'] ?? '';
        $value = str_replace("{{theme}}", $themePath, $value);

        return $value;
    }
}
