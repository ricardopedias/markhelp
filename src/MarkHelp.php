<?php

declare(strict_types=1);

namespace MarkHelp;

use Error;
use Exception;
use MarkHelp\Reader\Loader;
use MarkHelp\Writer\Maker;

class MarkHelp
{
    private Loader $loader;

    private string $origin;

    /**
     * O argumento $origin pode ser um caminho real (/caminho/para/projeto)
     * ou um URL para repositório do git (http://meurepos.com/repo.git)
     * @param string $origin Localização contendo arquivos markdown
     */
    public function __construct(string $origin)
    {
        $this->loader = new Loader();
        $this->origin = $origin;
    }

    protected function canBeGitUrl(): bool
    {
        $url = $this->origin;
        return substr($url, -4) === '.git';
    }

    public function setConfig(string $param, string $value): MarkHelp
    {
        $this->loader->setConfig($param, $value);
        return $this;
    }

    public function config(string $param): string
    {
        return $this->loader->config($param);
    }

    public function saveTo(string $pathDestination): void
    {
        $pathDestination = rtrim($pathDestination, DIRECTORY_SEPARATOR);

        if ($this->canBeGitUrl() === true) {
            $pathClone = $pathDestination . DIRECTORY_SEPARATOR . 'clone';
            $this->loader->fromRemoteUrl($this->origin, $pathClone);
            $this->process($pathDestination);
            reliability()->removeDirectory($pathClone);
            return;
        }

        $this->loader->fromLocalDirectory($this->origin);
        $this->process($pathDestination);
    }

    private function process(string $pathDestination): void
    {
        $maker = new Maker($this->loader);
        $maker->toDirectory($pathDestination);
    }

    public function loadConfigFrom(string $configJson): MarkHelp
    {
        $directory  = reliability()->dirname($configJson);
        $file       = reliability()->basename($configJson);
        $filesystem = reliability()->mountDirectory($directory);

        $jsonContent = $filesystem->read($file);

        if ($jsonContent === false) {
            throw new Exception("The config file does not contain a valid json");
        }

        $configList = @json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("The config file does not contain a json: " . \json_last_error_msg());
        }

        try {
            foreach ($configList as $param => $value) {
                if (is_array($value) === true) {
                    throw new Exception("Parameter {$param} does not contain a valid value");
                }

                $this->setConfig($param, (string)$value);
            }
        } catch (Error $e) {
            throw new Exception("The config file format is invalid. " . $e->getMessage(), $e->getCode());
        }
        
        return $this;
    }
}
