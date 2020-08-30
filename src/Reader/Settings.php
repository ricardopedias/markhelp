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

    /** @var array<string|null> */
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
        $this->setupParam(self::TYPE_PATH, 'project_images', '{{project}}/images');
        $this->setupParam(self::TYPE_STRING, 'project_home', '{{project}}/home.html');
        $this->setupParam(self::TYPE_FILE, 'project_logo', '{{project}}/images/logo.png');
        $this->setupParam(self::TYPE_FILE, 'project_menu', '{{project}}/menu.json');

        $this->setupParam(self::TYPE_FILE, 'assets_styles', '{{theme}}/assets/styles.css');
        $this->setupParam(self::TYPE_FILE, 'assets_scripts', '{{theme}}/assets/scripts.js');
        $this->setupParam(self::TYPE_FILE, 'assets_icon_favicon', '{{theme}}/assets/favicon.ico');
        $this->setupParam(self::TYPE_FILE, 'assets_icon_apple', '{{theme}}/assets/apple-touch-icon-precomposed.png');
    }

    /**
     * Seta um valor de configuração.
     * @param string $name
     * @param string|null $value
     * @return \MarkHelp\Reader\Settings
     */
    public function setParam(string $name, ?string $value): Settings
    {
        if ($this->validadeParam($name) === false) {
            throw new Exception("Param {$name} is invalid");
        }

        if ($this->types[$name] === self::TYPE_PATH && $value !== null) {
            $value = rtrim($value, DIRECTORY_SEPARATOR);
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
     * @return string|null
    */
    public function param(string $name): ?string
    {
        if (isset($this->params[$name]) === false) {
            throw new Exception("Param {$name} is not exists");
        }

        if ($this->types[$name] === self::TYPE_PATH) {
            return $this->parseTags($this->params[$name]);
        }

        return $this->params[$name];
    }

    /**
     * Devolve uma lista com todos os parâmetros existentes
     * @return array<string|null>
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

    private function parseTags(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $projectPath = $this->params['path_project'] ?? '';
        if ($projectPath !== "") {
            $value = str_replace(["{{project}}", "{{ project }}"], $projectPath, $value);
        }

        $themePath = $this->params['path_theme'] ?? '';
        $value = str_replace(["{{theme}}", "{{ theme }}"], $themePath, $value);

        return $value;
    }
}
