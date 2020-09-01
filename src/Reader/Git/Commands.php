<?php

declare(strict_types=1);

namespace MarkHelp\Reader\Git;

use Closure;
use Exception;

class Commands
{
    /**
     * Executa um comando de terminal.
     * @param array<string> $arguments
     * @return string
     * @throws Exception
     */
    public function run(array $arguments): string
    {
        $command = $this->processCommand($arguments);
        $output = [];
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception("Command '{$command}' failed (exit-code {$returnCode}).", $returnCode);
        }

        return implode("\n", $output);
    }

     /**
     * Processa um comando de terminal, validando suas entradas.
     * @param array<string> $arguments
     * @return string
     */
    protected function processCommand(array $arguments): string
    {
        $command = [];

        $programName = $arguments[0];
        unset($arguments[0]);

        foreach ($arguments as $arg) {
            // if (is_array($arg) === true) {
            //     foreach ($arg as $key => $value) {
            //         $escapedCommand = '';
            //         if (is_string($key)) {
            //             $escapedCommand = "$key ";
            //         }
            //         $command[] = $escapedCommand . escapeshellarg($value);
            //     }

            //     continue;
            // }
            $command[] = escapeshellarg($arg);
        }

        return "$programName " . implode(' ', $command);
    }

/**
     * Verifica se o URL é de um repositório válido.
     * @param  string $url
     * @param  array<string> $refs
     * @return bool
     */
    public function isValidRemote(string $url, array $refs = []): bool
    {
        $env = 'GIT_TERMINAL_PROMPT=0';

        if (DIRECTORY_SEPARATOR === '\\') {
            // Windows
            $env = 'set GIT_TERMINAL_PROMPT=0 &&';
        }

        $output = null;
        $returnCode = 1;

        $nodes = [
            $env . ' git ls-remote',
            '--heads',
            '--quiet',
            '--exit-code',
            $url
        ];
        $withRefs = array_merge($nodes, $refs);
        exec($this->processCommand($withRefs) . ' 2>&1', $output, $returnCode);

        return $returnCode === 0;
    }

    /**
     * Clona um repositório GIT a partir do URL fornecido.
     * @param  string $remoteUrl
     * @param  string $clonePath
     * @return bool
     * @throws Exception
     */
    public function clone(string $remoteUrl, string $clonePath): bool
    {
        if (reliability()->isDirectory("$clonePath/.git")) {
            throw new Exception("Repository already exists in $clonePath.");
        }

        $descriptorSpec = array(
            0 => array('pipe', 'r'), // stdin
            1 => array('pipe', 'w'), // stdout
            2 => array('pipe', 'w'), // stderr
        );

        $pipes = [];
        $command = $this->processCommand([
            'git clone',
            '-q',
            $remoteUrl,
            $clonePath
        ]);
        $process = proc_open($command, $descriptorSpec, $pipes);

        if ($process === false) {
            throw new Exception("Git clone failed to directory {$clonePath}.");
        }

        // Limpa a saída e os erros
        $stdout = '';
        $stderr = '';

        while (true) {
            // lê a saída padrão
            $output = fgets($pipes[1], 1024);
            if ($output) {
                $stdout .= $output;
            }

            // lê saída de erro
            $output_err = fgets($pipes[2], 1024);
            if ($output_err) {
                $stderr .= $output_err;
            }

            $executionFinished = (feof($pipes[1]) or $output === false)
                && (feof($pipes[2]) or $output_err === false);
            if ($executionFinished) {
                break;
            }
        }

        $returnCode = proc_close($process);
        if ($returnCode !== 0) {
            throw new Exception("Git clone failed (directory $clonePath)." . ($stderr !== '' ? ("\n$stderr") : ''));
        }

        return true;
    }

    /**
     * Devolve a lista de releases presentes no repositorio.
     * @param string $clonedPath
     * @return array<string>
     * @throws Exception
     */
    public function getReleases(string $clonedPath): array
    {
        $result = $this->extractFromCommand("cd {$clonedPath}; git tag -l", function ($value) {
            return trim($value);
        });
        return $result ?? [];
    }

    /**
     * Faz o chckout em um Release.
     * @param string $clonePath
     * @param string $release
     * @throws Exception
     * @return string
     */
    public function checkoutRelease(string $clonePath, string $release): string
    {
        $output = $this->run(["cd {$clonePath}; git checkout tags/{$release}"]);
        $lines = explode("\n", $output);
        return (string)$lines[array_key_last($lines)];
    }
    
/**
     * Devolve a lista de branchs presentes no repositorio.
     * @param string $clonedPath
     * @return array<string>
     * @throws Exception
     */
    public function getBranches(string $clonedPath): array
    {
        $list = $this->extractFromCommand("cd {$clonedPath}; git branch -r", function ($value) {
            $value = trim(substr($value, 1));
            $value = explode('/', $value)[1];
            return $value;
        });

        return array_filter($list ?? [], function ($value) {
            return strpos($value, 'HEAD') === false;
        });
    }

    /**
     * @param  string $command
     * @param  Closure $filter
     * @return array<string>
     * @throws \Exception
     */
    protected function extractFromCommand(string $command, ?Closure $filter = null): array
    {
        $output = [];
        $exitCode = null;

        exec("$command", $output, $exitCode);

        if ($exitCode !== 0 || is_array($output) === false) {
            throw new Exception("Command $command failed.");
        }

        if ($filter !== null) {
            $newArray = [];
            foreach ($output as $line) {
                $value = $filter($line);
                if ($value === false) {
                    continue;
                }
                $newArray[] = $value;
            }

            $output = $newArray;
        }

        if (!isset($output[0])) {
            // array vazio
            return [];
        }

        return $output;
    }
}
