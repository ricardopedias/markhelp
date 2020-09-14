<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

class Menu
{
    private string $html = '';

    /**
     * @param array<array> $itemsList
     */
    public function __construct(array $itemsList)
    {
        $this->html = $this->renderMenu($itemsList);
    }

    /**
     * @param array<array> $itemsList
     * @return string
     */
    private function renderMenu(array $itemsList): string
    {
        $html = "\n<ul>\n";

        foreach ($itemsList as $item) {
            if (isset($item['children']) === true) {
                $html .= "</ul>\n\n";
                $html .= "<h2>{$item['label']}</h2>\n\n";
                $html .= "<ul>\n";
                $html .= $this->renderMenuBlock($item['children']);
                continue;
            }

            $html .= $this->renderMenuItem($item['label'], $item['url']);
        }

        $html .= "</ul>\n";

        return $html;
    }

    /**
     * @param array<array> $itemsList
     * @return string
     */
    private function renderMenuBlock(array $itemsList): string
    {
        $html = '';
        foreach ($itemsList as $blockItem) {
            if (isset($blockItem['children']) === true) {
                $html .= $this->renderMenuItem($blockItem['label'], $blockItem['url'], $blockItem['children']);
                continue;
            }

            $html .= $this->renderMenuItem($blockItem['label'], $blockItem['url']);
        }

        return $html;
    }

    /**
     * @param array<array> $itemsList
     * @return string
     */
    private function renderMenuItem(string $label, string $url, array $itemsList = []): string
    {
        $submenu = [];
        foreach ($itemsList as $item) {
            $submenu[] = "        " . $this->renderMenuItem($item['label'], $item['url']);
        }

        if ($submenu !== []) {
            return "    <li>\n"
                 . "        <a href=\"{$url}\">{$label}</a>\n"
                 . "        <ul>\n"
                 . implode("", $submenu)
                 . "        </ul>\n"
                 . "    </li>\n";
        }

        return "    <li><a href=\"{$url}\">{$label}</a></li>\n";
    }


    public function toHtml(): string
    {
        return $this->html;
    }
}
