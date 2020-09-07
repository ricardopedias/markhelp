<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

use Exception;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Release;

class Parser
{
    public function extractTitle(string $markdownString): string
    {
        $title = '';

        if ($markdownString === '') {
            return '';
        }

        $lines = explode("\n", $markdownString);
        foreach ($lines as $line) {
            $lineContent = trim($line);
            if ($lineContent === "") {
                continue;
            }

            if (preg_match('/^#(.*)$/', $lineContent) !== false) {
                $title = substr($lineContent, 1);
                break;
            }
        }
        
        return trim($title);
    }

    /**
     * @return array<int, array<string>>
     */
    public function extractMenuItems(Release $release, string $urlPrefix = ''): array
    {
        $tree = new TreeBuilder();
        $list = $tree->treeFromPaths($release->filesAsString(false));
        $basePath = $release->path() . DIRECTORY_SEPARATOR;

        $list = $this->prepareMenuItems($basePath, $list, $urlPrefix);
        return $list;
    }

    /**
     * @param string $basePath
     * @param array<array> $list
     * @param string $urlPrefix
     * @return array<array>
     */
    private function prepareMenuItems(string $basePath, array $list, string $urlPrefix = ''): array
    {
        $homePage = [];
        $menuList = [];

        $list = $this->filterInvalidMenuItems($list);

        foreach ($list as $item) {
            $item['children'] = $this->filterInvalidMenuItems($item['children']);

            $isDirectory = count($item['children']) > 0;
            if ($isDirectory === true) {
                $label = explode('/', $item['path']);
                $label = $label[count($label) - 1];
                $label = (string)preg_replace('/[0-9]*[_-](.*)/', '$1', $label);
                $label = str_replace(['_', '-'], ' ', $label);
                $menuItem = [
                    'label'    => $label,
                    'url'      => 'javascript:void(0);',
                    'children' => $this->prepareMenuItems($basePath, $item['children'], $urlPrefix)
                ];
                $menuList[] = $menuItem;
                continue;
            }

            $releaseDirectory = reliability()->mountDirectory($basePath);
            $markdownString   = $releaseDirectory->read($item['path']);
            if ($markdownString === false) {
                throw new Exception("{$item['path']} file is not accessible");
            }

            $menuItem = [
                'label' => $this->extractTitle($markdownString),
                'url'   => $urlPrefix . str_replace('.md', '.html', $item['path']),
            ];

            // A home page deve ser extraída da lista momentaneamente
            if ($item['path'] === 'index.md') {
                $homePage[] = $menuItem;
                continue;
            }
            
            $menuList[] = $menuItem;
        }

        $menuList = array_merge($homePage, $menuList);
        return $menuList;
    }

    /**
     * Um item válido deve ser um arquivo markdown
     * ou um diretório contendo arquivos markdown
     * @param array<array> $itemsList
     * @return array<array>
     */
    private function filterInvalidMenuItems(array $itemsList): array
    {
        $filteredList = array_filter($itemsList, function ($item) {

            $isMarkdown  = (bool)preg_match('/.*\.md/', $item['path']) === true;
            if ($isMarkdown === true) {
                return true;
            }

            $isDirectory = count($item['children']) > 0;
            $hasMarkdownChilds = false;
            foreach ($item['children'] as $child) {
                if ((bool)preg_match('/.*\.md/', $child['path']) === true) {
                    $hasMarkdownChilds = true;
                }
            }

            if ($isDirectory === true && $hasMarkdownChilds === true) {
                return true;
            }

            return false;
        });

        return $filteredList;
    }
}
