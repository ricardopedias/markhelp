<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

use Exception;

class TreeBuilder
{
    /**
     * @param array<string> $pathsList
     * @return array<array>
     */
    public function treeFromPaths(array $pathsList): array
    {
        $flatList = $this->flatListFromPaths($pathsList);
        return $this->treeFromFlatList($flatList);
    }

    /**
     * @param array<array> $flatList
     * @param int $parent
     * @return array<array>
     */
    public function treeFromFlatList(array $flatList, int $parent = 0): array
    {
        $branch = [];

        foreach ($flatList as $item) {
            if ($this->isValidFlatItem($item) === false) {
                throw new Exception("The flat list is poorly formatted");
            }

            $itemId     = $item['id'];
            $itemParent = $item['parent'];

            if ($itemParent === $parent) {
                $item['children'] = $this->treeFromFlatList($flatList, $itemId);
                $branch[$itemId] = $item;
                continue;
            }
        }

        return $branch;
    }

    /**
     * @param array<int|string> $item
     */
    private function isValidFlatItem(array $item): bool
    {
        if (isset($item['id']) === false || is_int($item['id']) === false) {
            return false;
        }

        if (isset($item['parent']) === false || is_int($item['parent']) === false) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string> $pathsList
     * @return array<array>
     */
    public function flatListFromPaths(array $pathsList): array
    {
        // Separa a lista de caminhos em 3 niveis de diretórios (indices: 0,1,2)
        $levelsList = [
            0 => [],
            1 => [],
            2 => [],
        ];
        array_walk($pathsList, function ($path) use (&$levelsList) {
            if (is_string($path) === false) {
                throw new Exception("The paths list is poorly formatted");
            }
            $path   = trim($path, "/");
            $level  = substr_count($path, "/");
            // Apenas três niveis são permitidos na árvore
            if ($level >= 3) {
                return;
            }
            $levelsList[$level][] = $path;
        });

        // Gera a lista com seus relacionamentos ('id' e 'parent')
        // Cada item é indexado com a string do 'path',
        // Isso possibilita mais flexibilidade na ordenação dos itens
        $relationsList = [
            0 => [],
            1 => [],
            2 => [],
        ];
        array_walk($levelsList, function ($level, $index) use (&$relationsList) {

            array_walk($level, function ($path) use (&$relationsList, $index) {
                if (is_string($path) === false) {
                    throw new Exception("The paths list is poorly formatted");
                }

                // nível 1
                if ($index === 0) {
                    $relationsList[$index][$path] = [
                        'id'     => $path,
                        'parent' => '',
                        'path'   => $path
                    ];
                    return;
                }

                // nível 2
                $directory = (string)preg_replace('/^(.*)\/.*/', '$1', $path);
                if ($index === 1) {
                    $relationsList[$index][$directory] = [
                        'id'     => $directory,
                        'parent' => '',
                        'path'   => $directory
                    ];

                    $relationsList[$index][$path] = [
                        'id'     => $path,
                        'parent' => $directory,
                        'path'   => $path
                    ];
                    return;
                }
                
                // nível 3
                $parentDirectory = preg_replace('/^(.*)\/.*/', '$1', $directory);
                $relationsList[$index][$directory] = [
                    'id'     => $directory,
                    'parent' => $parentDirectory,
                    'path'   => $directory
                ];

                $relationsList[$index][$path] = [
                    'id'     => $path,
                    'parent' => $directory,
                    'path'   => $path
                ];
            });
        });

        // O nível 1 (indice 0) contém apenas arquivos
        $files = $relationsList[0];

        // Os níveis 2 e 3 (indices 1 e 2) contém diretórios e seus arquivos
        $directories = array_merge(
            $relationsList[1],
            $relationsList[2]
        );

        // Para o nível 1 ficar correto,
        // deve ser ordenado separadamente
        ksort($files);
        ksort($directories);

        // Monta a lista completa, ordenada corretamente
        // Adicionando como primeiro elemento, invormações vazias
        // que servirão para identificar o relacionamento '0'
        $list = array_merge(
            [['id' => '', 'parent' => '', 'path' => '']], // elemento id 0
            $files,
            $directories
        );

        // Mapeia cada item para um ID numérico começando por '0'
        $parentIds = array_values(array_map(function ($item) {
            return $item['id'];
        }, $list));
        $parentIds = array_flip($parentIds);

        // Gera uma lista relacionada por indices numéricos
        // ao invés de strings contendo os 'paths'
        $finalList = [];
        array_walk($list, function ($item) use (&$finalList, $parentIds) {
            $item['id']     = $parentIds[$item['id']];
            $item['parent'] = $parentIds[$item['parent']];
            $finalList[$item['id']] = $item;
        });

        // Remove o elemento 0, inútil a partir de agora
        array_shift($finalList);

        return $finalList;
    }

    /**
     * @param array<string> $pathsList
     * @param string $parent
     * @return array<array>
     */
    private function flatListProccessPaths(array $pathsList, string $parent = ''): array
    {
        $listFiles       = [];
        $listDirectories = [];

        $pathsList = array_map(function ($path) {
            return ltrim($path, "/");
        }, $pathsList);

        natsort($pathsList);

        foreach ($pathsList as $path) {
            if ($path === '') {
                continue;
            }

            $nodes = explode('/', $path);

            // se o caminho contiver diretórios
            if (count($nodes) === 1) {
                $url = $parent !== ''
                    ? $parent . "/" . $path
                    : $path;

                $itemId = $url;

                $listFiles[$itemId] = [
                    'parent' => $parent,
                    'id'     => $itemId,
                    'url'    => $url
                ];

                continue;
            }

            $directoryName = $this->flatListResolveDirectoryName($nodes);
            $itemId = $directoryName;
            $url = $parent !== ''
                ? $parent . "/" . $directoryName
                : $directoryName;

            $listDirectories[$itemId] = [
                'parent' => $parent,
                'id'     => $itemId,
                'url'    => $url
            ];

            $currentDirectoryPaths = $this->flatListFilter($directoryName, $pathsList);
            $listSubdirectories  = $this->flatListProccessPaths($currentDirectoryPaths, $directoryName);
            
            $listDirectories = array_merge(
                $listDirectories,
                $listSubdirectories
            );
        }

        $list = array_merge($listFiles, $listDirectories);

        return $list;
    }

    /**
     * @param string $directoryName
     * @param array<string> $pathsList
     * @return array<string>
     */
    private function flatListFilter(string $directoryName, array $pathsList): array
    {
        $directoryName = str_replace("/", "\/", $directoryName);
        
        $list = array_filter($pathsList, function ($path) use ($directoryName) {
            return preg_match("/{$directoryName}(.*)/", $path);
        });

        $list = array_map(function ($path) use ($directoryName) {
            $replace = (string)preg_replace("/{$directoryName}(.*)/", "$1", $path);
            return ltrim($replace, "/");
        }, $list);

        return $list;
    }

    /**
     * @param array<string> $pathsNodes
     */
    private function flatListResolveDirectoryName(array $pathsNodes): string
    {
        array_pop($pathsNodes);
        return implode('/', $pathsNodes);
    }
}
