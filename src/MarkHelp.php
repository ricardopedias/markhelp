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
        $this->origin = $origin;
        $this->loader = new Loader();
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
        $this->loader->loadConfigFrom($configJson);
        return $this;
    }
}
