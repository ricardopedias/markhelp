<?php
declare(strict_types=1);

namespace MarkHelp;

use Error;
use Exception;
use MarkHelp\App\Filesystem;
use MarkHelp\App\Reader;
use MarkHelp\App\Tools;
use MarkHelp\App\Writer;
use MarkHelp\Bags\Config;

class MarkHelp
{
    use Tools;

    private $config = null;

    /**
     * Constrói um leitor de arquivos markdown.
     * 
     * @param string $path Diretório contendo arquivos markdown
     */
    public function __construct(string $path)
    {
        $this->config = new Config($path);
    }

    public function setConfig(string $param, $value)
    {
        $this->config->setParam($param, $value);
        return $this;
    }

    public function config(string $param)
    {
        return $this->config->param($param);
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
                $this->config->setParam($param, $value);
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

    public function saveTo(string $pathDestination)
    {
        $reader = new Reader($this->config);
        $writer = new Writer($reader);
        $writer->saveTo($pathDestination);
    }
}