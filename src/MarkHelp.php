<?php

declare(strict_types=1);

namespace MarkHelp;

use Error;
use Exception;
use MarkHelp\Reader\Handlers\IHandle;
use MarkHelp\Reader\Handlers\LocalHandle;
use MarkHelp\Reader\Handlers\RepositoryHandle;
use Reliability\Reliability;

class MarkHelp
{
    private IHandle $handle;

    private bool $isRepository = false;

    /** @var array<string|null> */
    private array $configList = [];

    /**
     * Constrói um leitor de arquivos markdown.
     * O argumento $path pode ser um caminho real (/caminho/para/projeto)
     * ou um URL para repositório do git (http://meurepos.com/repo.git)
     * @param string $path Localização contendo arquivos markdown
     */
    public function __construct(string $path)
    {
        if ($this->canBeGitUrl($path) === true) {
            $this->isRepository = true;
            $this->handle = new RepositoryHandle();
            $this->handle->setOrigin($path);
            return;
        }

        $this->handle = new LocalHandle();
        $this->handle->setOrigin($path);
    }

    public function canBeGitUrl(string $url): bool
    {
        return substr($url, -4) === '.git';
    }

    public function saveTo(string $pathDestination): void
    {
        $path = $this->isRepository === true
            ? $pathDestination // a origem é o local que foram clonados
            : $this->handle->origin();

        $this->handle->setConfigList($this->configList);
        $this->handle->toDestination($path);
    }

    public function setConfig(string $param, string $value): MarkHelp
    {
        if ($value === "null") {
            $value = null;
        }

        $this->configList[$param] = $value;
        return $this;
    }

    public function config(string $param): ?string
    {
        return $this->configList[$param] ?? null;
    }

    public function loadConfigFrom(string $pathConfigJsonFile): MarkHelp
    {
        $reliability = new Reliability();
        $filesystem = $reliability->mountDirectory($reliability->dirname($pathConfigJsonFile));

        $jsonContent = $filesystem->read($reliability->basename($pathConfigJsonFile));

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
