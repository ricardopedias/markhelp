<?php
declare(strict_types=1);

namespace MarkHelp;

use Error;
use Exception;
use MarkHelp\App\Filesystem;
use MarkHelp\App\Tools;
use MarkHelp\Bags\Config;
use MarkHelp\Handlers\LocalHandle;
use MarkHelp\Handlers\RepositoryHandle;

class MarkHelp
{
    use Tools;

    private $origin = null;

    private $configList = [];

    private $handle = null;

    private $isRepository = false;

    /**
     * Constrói um leitor de arquivos markdown.
     * 
     * @param string $path Localização contendo arquivos markdown
     */
    public function __construct(string $path)
    {
        $this->origin = $path;
        
        if ($this->canBeGitUrl($this->origin) === true) {
            $this->isRepository = true;
            $this->handle = new RepositoryHandle($this->origin);
            return;
        }

        $this->handle = new LocalHandle;
    }

    public function canBeGitUrl($url)
    {
        return substr($url, -4) === '.git';
    }

    public function saveTo(string $pathDestination)
    {
        $path = $this->isRepository === true ? $pathDestination : $this->origin;

        $this->config = new Config($path);
        $this->config->addParams($this->configList);

        $this->handle->setConfig($this->config);
        $this->handle->toDestination($pathDestination);
    }

    public function setConfig(string $param, $value)
    {
        $this->configList[$param] = $value;
        return $this;
    }

    public function config(string $param)
    {
        return $this->configList[$param] ?? null;
    }

    public function loadConfigFrom(string $pathConfigJsonFile)
    {
        $filesystem = new Filesystem;
        $filesystem->mount('jsonfile', $this->dirname($pathConfigJsonFile));
        $jsonContent = $filesystem->read("jsonfile://" . $this->basename($pathConfigJsonFile));

        $configList = @json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("The config file does not contain a json: " . \json_last_error_msg());
        }

        try {

            foreach($configList as $param => $value){

                if (is_array($value) === true) {
                    throw new Exception("Parameter {$param} does not contain a valid value");
                }

                $value = $this->normalizeValue($value);
                $this->setConfig($param, $value);
            }

        } catch(Error $e) {
            throw new Exception("The config file format is invalid. " . $e->getMessage(), $e->getCode());
        }
        
        return $this;
    }

    public function normalizeValue($value)
    {
        if ($value === "null") {
            return null;
        }

        if (in_array($value, ["true", "false"]) === true) {
            return (bool) $value;
        }

        return $value;
    }
}