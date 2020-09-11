<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

use Error;
use Exception;
use League\CommonMark\CommonMarkConverter;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Loader;
use MarkHelp\Reader\Release;
use Twig\Environment;

class Menu
{
    private string $html = '';

    public function __construct(array $items)
    {
        $this->html = $this->renderMenu($items);
    }

    public function toHtml(): string
    {
        return $this->html;
    }

    private function renderMenu(array $menuItems): string
    {
        if (count($menuItems) === 0) {
            return '';
        }

        try {
            $menu = "";
            foreach ($menuItems as $item) {

                if (isset($item['children']) === false) {
                    $menu .= "<ul>";
                    $menu .= $this->renderMenuItem($item['label'], $item['url']);
                    continue;
                }

                $menu .= "</ul>";
                $menu .= "<h2>{$item['label']}</h2>";
                $menu .= $this->renderMenuSection($item['children']);
            }
        } catch (Error $e) {
            throw new Exception("The menu file format is invalid. " . $e->getMessage(), $e->getCode());
        }

        return $menu;
    }

    private function renderMenuSection(array $menuItems): string
    {
        $block = "<ul>";
        foreach ($menuItems as $item) {
            if (isset($item['children']) === false) {
                $block .= $this->renderMenuItem($item['label'], $item['url']);
                continue;
            }

            $block .= $this->renderMenuBlock($item['label'], $item['children']);
        }
        $block .= "</ul>";

        return $block;
    }

    private function renderMenuItem(string $label, string $url): string
    {
        return implode("\n", [
            "<li>",
            "<a href=\"{$url}\">{$label}</a>",
            "</li>"
        ]);
    }

    private function renderMenuBlock(string $label, array $items): string
    {
        return implode("\n", [
            "<li>",
                "<a href=\"javascript:void(0);\">{$label}</a>",
                $this->renderMenuSection($items),
            "</li>"
        ]);
    }
}
