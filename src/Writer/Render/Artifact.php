<?php

declare(strict_types=1);

namespace MarkHelp\Writer\Render;

use Error;
use Exception;
use League\CommonMark\CommonMarkConverter;
use MarkHelp\Reader\Files\File;
use MarkHelp\Reader\Loader;

class Artifact
{
    private Loader $loader;

    private File $file;

    public function __construct(Loader $loader, File $file)
    {
        $this->loader = $loader;
        $this->file   = $file;
    }

    protected function loader(): Loader
    {
        return $this->loader;
    }

    protected function path(): string
    {
        return $this->file->path();
    }

    protected function fullPath(): string
    {
        return $this->file->fullPath();
    }

    protected function basePath(): string
    {
        return $this->file->basePath();
    }

    

    public function generate()
    {
        
    }

    public function saveTo(string $path): void
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        reliability()->copyFile(
            $this->file->fullPath(), 
            $path . DIRECTORY_SEPARATOR .$this->file->path()
        );
    }


    
    public function mountedDirectory(string $mountPoint): Filesystem
    {
        return $this->reader->mountedDirectory($mountPoint);
    }

    public function useDocument(Support $documentBag): void
    {
        $mountPoint  = $documentBag->param('mountPoint');
        $supportPath = $documentBag->param('supportPath');

        if ($mountPoint === null || $supportPath === null) {
            return;
        }

        try {
            $filesystem = $this->mountedDirectory($mountPoint);
            $content = $filesystem->read("{$supportPath}");
            if ($content !== false) {
                $this->documentContent = $content;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function useMenu(Support $menuBag): void
    {
        $mountPoint = $menuBag->param('mountPoint');
        $supportPath = $menuBag->param('supportPath');

        try {
            $this->menuContent = $this->mountedDirectory($mountPoint)->read("{$supportPath}");
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    private function document()
    {
        return $this->documentContent === null
            ? (
               "<main>"
             . "<menu>{{ sidemenu }}</menu>"
             . "<article>{{ content }}</article>"
             . "</main>"
              )
            : $this->documentContent;
    }

    private function menuItems(): array
    {
        if ($this->menuItems === [] && $this->menuContent !== null) {
            $menuItems =  @json_decode($this->menuContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("The menu file does not contain a json: " . \json_last_error_msg());
            }
            $this->menuItems = $menuItems;
        }

        return $this->menuItems;
    }

    /**
     * Redesenha uma string contendo formatação em markdown,
     * tranformando-a em HTML e substituindo os parâmetros passados.
     * @param string $contents
     * @param array $replaces
     * @return string
     */
    public function render(string $contents, array $replaces = []): string
    {
        $sidemenu = $this->renderMenu($this->menuItems(), $replaces);
        $sidemenu = $this->replace($sidemenu, $replaces);
        
        $contents = $this->replace($contents, $replaces);
        $contents = $this->toHtml($contents);

        $mainReplaces = [
            'content'  => $contents,
            'sidemenu' => $sidemenu
        ];

        return $this->replace(
            $this->document(),
            array_merge($replaces, $mainReplaces)
        );
    }

    /**
     * Converte um arquivo markdown para HTML
     * @param string $markdownString
     * @return string
     */
    private function toHtml(string $markdownString): string
    {
        return (new CommonMarkConverter())->convertToHtml($markdownString);
    }

    /**
     * Substitui os parâmetros passados dentro de uma string.
     * @param string $content
     * @param array $replaces
     * @return string
     */
    public function replace(string $content, array $replaces): string
    {
        // tags: {{ minhatag  }}
        $content = preg_replace_callback("/{{\s*(?P<key>[a-zA-Z0-9_\.-]+?)\s*}}/", function ($match) use ($replaces) {
            return isset($replaces[$match["key"]]) ? $replaces[$match["key"]] : $match[0];
        }, $content);

        // links markdown: (minha/url.ext)
        foreach ($replaces as $search => $replace) {
            $search = ["($search", "( $search"];
            $replace = "($replace";
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    private function renderMenu(array $menuItems, array $replaces): ?string
    {
        if (count($menuItems) === 0) {
            return '';
        }

        try {
            $menu = "";
            foreach ($menuItems as $label => $url) {
                if (is_array($url) === false) {
                    $menu .= "<ul>";
                    $menu .= $this->renderMenuItem($label, $url, $replaces);
                    continue;
                }

                $menu .= "</ul>";
                $menu .= "<h2>{$label}</h2>";
                $menu .= $this->renderMenuSection($url, $replaces);
            }
        } catch (Error $e) {
            throw new Exception("The menu file format is invalid. " . $e->getMessage(), $e->getCode());
        }

        return $menu;
    }

    private function renderMenuSection(array $menuItems, array $replaces): ?string
    {
        $block = "<ul>";
        foreach ($menuItems as $label => $url) {
            if (is_array($url) === false) {
                $block .= $this->renderMenuItem($label, $url, $replaces);
                continue;
            }

            $block .= $this->renderMenuBlock($label, $url, $replaces);
        }
        $block .= "</ul>";

        return $block;
    }

    private function renderMenuItem(string $label, string $url, array $replaces): string
    {
        $url = $this->replaceMenuItemUrl($url, $replaces);

        return implode("\n", [
            "<li>",
            "<a href=\"{$url}\">{$label}</a>",
            "</li>"
        ]);
    }

    private function renderMenuBlock(string $label, array $items, array $replaces): string
    {
        return implode("\n", [
            "<li>",
                "<a href=\"javascript:void(0);\">{$label}</a>",
                $this->renderMenuSection($items, $replaces),
            "</li>"
        ]);
    }

    private function replaceMenuItemUrl(string $url, array $replaces): string
    {
        $search  = array_keys($replaces);
        $replace = array_values($replaces);
        return str_replace($search, $replace, $url);
    }
}
