<?php

declare(strict_types=1);

namespace MarkHelp\Bags;

use Exception;
use MarkHelp\App\Tools;

class Config extends Bag
{
    use Tools;

    private $defaults = [];

    private $themeDefault = null;

    public function __construct(string $pathRoot)
    {
        $pathRoot = $this->requireValue('path_root', $pathRoot);

        // verificar se é uma url GIT
        $this->isDirectoryOrException($pathRoot);
        parent::setParam('path.root', $pathRoot);

        $this->themeDefault = $this->dirname(__DIR__) . '/Themes/default';
        $pathTheme = $this->makePath($this->themeDefault);

        $this->defaults = [
            'path.theme'          => $pathTheme,
            'logo.status'         => $this->makeBoolean(true),
            'project.name'        => $this->makeString('Mark Help'),
            'project.slogan'      => $this->makeString('Gerador de documentação'),
            'project.description' => $this->makeString('Gerador de documentação feito em PHP'),
            'project.images'      => $this->makePath('{{project}}/images'),
            'project.home'        => $this->makeString('{{project}}/home.html'),
            'current.page'        => $this->makeString(''),
            'git.url'             => $this->makeString('https://github.com/ricardopedias/markhelp'),
            'git.fork'            => $this->makeBoolean(true),
            'copy.name'           => $this->makeString('Ricardo Pereira'),
            'copy.url'            => $this->makeString('http://www.ricardopdias.com.br'),
            'support.menu'        => $this->makeFile(null),
            'support.document'    => $this->makeFile('{{theme}}/support/document.html'),
            'assets.styles'       => $this->makeFile('{{theme}}/assets/styles.css'),
            'assets.scripts'      => $this->makeFile('{{theme}}/assets/scripts.js'),
            'assets.logo.src'     => $this->makeFile('{{theme}}/assets/logo.png'),
            'assets.icon.favicon' => $this->makeFile('{{theme}}/assets/favicon.ico'),
            'assets.icon.apple'   => $this->makeFile('{{theme}}/assets/apple-touch-icon-precomposed.png'),
            'clone.directory'     => $this->makeString('docs'),
            'clone.branchs'       => $this->makeString('master'),
        ];
        
        $this->addParams(array_map(function ($v) {
            return $v->value;
        }, $this->defaults));
    }

    private function makeParam(string $type, $value, bool $required = false)
    {
        return (object) [
            'type'     => $type,
            'value'    => $value,
            'required' => $required
        ];
    }

    private function makeFile($value, bool $required = false)
    {
        return $this->makeParam('file', $value, $required);
    }

    private function makePath($value, bool $required = false)
    {
        return $this->makeParam('path', $value, $required);
    }

    private function makeString($value, bool $required = false)
    {
        return $this->makeParam('string', $value, $required);
    }

    private function makeBoolean($value, bool $required = false)
    {
        return $this->makeParam('bool', $value, $required);
    }

    public function setParam(string $name, $value)
    {
        $info = $this->defaults[$name] ?? null;
        if ($info === null) {
            throw new Exception("The configuration parameter {$name} is invalid");
        }
        
        return parent::setParam($name, $this->resolveValue($name, $value, $info));
    }

    protected function resolveValue(string $name, $value, object $info)
    {
        $value = $this->castValue($info, $value);

        if ($info->type === 'path') {
            $value = $this->normalizePath($value, true);
        }

        if ($info->type === 'file') {
            $support = [
                'support.menu'     => 'menu.json',
                'support.document' => 'document.html',
            ];

            if (isset($support[$name]) && $value === "") {
                $value = $support[$name];
            }
            
            $value = $this->normalizeFile($value);
        }

        if ($info->required === true) {
            $value = $this->requireValue($name, $value);
        }
        
        $value = $this->normalizeValue($name, $value);

        return $value;
    }

    public function requireValue($name, $value)
    {
        if ($value === "" || $value === null) {
            throw new Exception("The {$name} parameter is mandatory");
        }

        return $value;
    }

    private function castValue($info, $value)
    {
        switch ($info->type) {
            case 'bool':
                $value = (bool) $value;
                break;
            case 'int':
                $value = (int) $value;
                break;
            case 'string':
            case 'path':
            case 'file':
                $value = (string) $value;
                break;
        }

        return $value;
    }

    public function normalizePath(string $value, bool $checkExist)
    {
        if ($this->param('path.theme') === null) {
            $this->params['path.theme'] = $this->themeDefault;
        }

        $value     = rtrim($value, DIRECTORY_SEPARATOR);
        $themePath = rtrim($this->param('path.theme'), DIRECTORY_SEPARATOR);
        $value     = str_replace(["{{theme}}", "{{ theme }}"], $themePath, $value);

        $rootPath = rtrim($this->param('path.root'), DIRECTORY_SEPARATOR);
        $value    = str_replace(["{{project}}", "{{ project }}"], $rootPath, $value);

        if ($checkExist === true) {
            return $this->isDirectory($value) ? $value : null;
        }
        
        return $value;
    }

    public function normalizeFile(string $value)
    {
        $value = $this->normalizePath($value, false);

        // busca no diretório do projeto
        $projectFileBasename = $this->basename($value);
        $projectFile = $this->param('path.root') . DIRECTORY_SEPARATOR . $projectFileBasename;
        if ($this->isFile($projectFile) === true) {
            return $projectFile;
        }

        if ($this->isFile($value) === true) {
            return $value;
        }
        
        return null;
    }

    public function normalizeValue(string $name, $value)
    {
        $changed = false;

        if ($value === "" || $value === null) {
            // volta o valor inválido para o padrão
            $value = $this->defaults[$name]->value;
            $changed = true;
        }

        if ($changed === false) {
            return $value;
        }

        if ($value !== $this->defaults[$name]->value) {
            return $value;
        }

        // caminhos padrões dinâmicos, contendo variáveis {{theme|project}} devem ser reprocessados
        $tags = strpos((string) $value, "{{theme}}") !== false
             || strpos((string) $value, "{{ theme }}") !== false
             || strpos((string) $value, "{{project}}") !== false
             || strpos((string) $value, "{{ project }}") !== false;

        if ($tags === true) {
            $value = $this->normalizeFile($value);
        }

        return $value;
    }
}
