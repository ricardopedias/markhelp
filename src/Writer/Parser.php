<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

use Exception;
use League\CommonMark\CommonMarkConverter;
use MarkHelp\Reader\Files\Asset;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Files\Image;
use MarkHelp\Reader\Files\Markdown;
use MarkHelp\Reader\Loader;
use MarkHelp\Reader\Release;
use MarkHelp\Reader\Theme;
use RuntimeException;
use Twig\Environment;

class Parser
{
    private string $markdownString;

    public function __construct(string $markdownString)
    {
        $this->markdownString = $markdownString;
    }

    public function extractTitle(): string
    {
        $title = '';

        $lines = explode("\n", $this->markdownString);
        foreach ($lines as $line) {
            $lineContent = trim($line);
            if ($lineContent[0] === '#') {
                $title = substr($lineContent, 1);
                break;
            }
        }
        
        return $title;
    }
}
