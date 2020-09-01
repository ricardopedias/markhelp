<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

use Exception;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Release;

class Menu
{
    private Release $release;

    public function __construct(Release $release)
    {
        $this->release = $release;
    }

    /**
     * @return array<int, array<string>>
     */
    public function extractItems(string $urlPrefix = ''): array
    {
        $items = [];

        $lastIndex = 0;
        $list = $this->release->files();
        foreach ($list as $item) {
            if ($item->type() !== File::TYPE_MARKDOWN) {
                continue;
            }

            $lastIndex++;
            
            $markdownString = $this->fileContent($item);
            $url   = $urlPrefix . str_replace('.md', '.html', $item->path());
            $label = (new Parser($markdownString))->extractTitle();
            $label = trim($label);
            $label = preg_replace('/^[0-9](.*)/i', '$1', $label);
            $label = trim((string)$label);

            $items[$lastIndex] = [];

            // $nodes = count(explode('/', $item->path()));
            // if ($nodes > 1) {
            //     $child = ['label' => $label, 'url'=> $url];
            //     $items[$lastIndex]['childs'][] = $child;
            //     continue;
            // }

            $items[$lastIndex]['label'] = $label;
            $items[$lastIndex]['url']   = $url;
        }

        sort($items);

        return $items;
    }

    /**
     * Obtém o conteúdo do arquivo em Markdown
     * @return string
     */
    private function fileContent(File $markdown): string
    {
        $markdownString = reliability()
            ->mountDirectory($markdown->basePath())
            ->read($markdown->path());

        if ($markdownString === false) {
            throw new Exception($markdown->fullPath() . " file is not accessible");
        }

        return $markdownString;
    }
}
