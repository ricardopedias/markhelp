<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

use Exception;
use League\CommonMark\CommonMarkConverter;
use MarkHelp\Reader\File;
use MarkHelp\Reader\Loader;
use MarkHelp\Reader\Release;
use Twig\Environment;

class Page
{
    private Loader $loader;

    private string $releaseName;

    private File $markdown;

    public function __construct(Loader $loader, string $releaseName, File $markdown)
    {
        $this->loader = $loader;
        $this->releaseName = $releaseName;

        if ($markdown->type() !== File::TYPE_MARKDOWN) {
            throw new Exception('Only markdown files are allowed');
        }

        $this->markdown = $markdown;
    }

    public function toDirectory(string $filePath): void
    {
        $html = $this->generateHtml();
        $file = str_replace('.md', '.html', $this->markdown->path());

        reliability()
            ->mountDirectory($filePath)
            ->write($file, $html);
    }

    public function generateHtml(): string
    {
        $currentRelease = $this->currentRelease();

        $tags = [];

        $params = $this->loader->params();
        foreach ($params as $param => $value) {
            $tags[$param] = str_replace('{{theme}}/', '', $value);
        }

        $releasesList = [];
        foreach ($this->loader->releases() as $release) {
            if ($release->name() === '_') {
                continue;
            }
            
            $homePath = str_replace('.md', '.html', $release->home()->path());
            $url = '../' . $release->name() . '/' . $homePath;
            $releasesList[$release->name()] = $url;
        }

        $urlPrefix      = $this->resolveUrlPrefix();
        $markdownString = str_replace('.md)', '.html)', $this->fileContent());
        
        $htmlString = (new CommonMarkConverter())->convertToHtml($markdownString);
        $pageTitle  = (new Parser())->extractTitle($markdownString);
        $menuItems  = (new Parser())->extractMenuItems($currentRelease, $urlPrefix);

        $currentPage = $urlPrefix . str_replace('.md', '.html', $this->markdown->path());

        $tags['page_title']    = $pageTitle;
        $tags['release']       = $this->currentRelease()->name();
        $tags['releases_list'] = $releasesList;
        $tags['menu']          = (new Menu($menuItems, $currentPage))->toHtml();
        $tags['content']       = $htmlString;
        $tags['project_logo']  = $urlPrefix . $this->resolveProjectLogoUrl();

        foreach ($this->loader->theme()->filesAsString(false) as $assetPath) {
            $assetName = reliability()->filename($assetPath);
            $assetName = str_replace("-", "_", $assetName);
            $tags["asset_{$assetName}"] = $urlPrefix . $assetPath;
        }

        return $this->templateEngine()->render('page.html', $tags);
    }

    private function currentRelease(): Release
    {
        return $this->loader->releases()[$this->releaseName];
    }

    /**
     * Obtém o conteúdo do arquivo em Markdown
     * @return string
     */
    private function fileContent(): string
    {
        $markdownString = reliability()
            ->mountDirectory($this->markdown->basePath())
            ->read($this->markdown->path());

        if ($markdownString === false) {
            throw new Exception($this->markdown->fullPath() . " file is not accessible");
        }

        return $markdownString;
    }

    private function templateEngine(): Environment
    {
        $templatesPath = $this->loader->config('path_theme')
            . DIRECTORY_SEPARATOR
            . 'templates';
        $loader = new \Twig\Loader\FilesystemLoader($templatesPath);
        $twig = new Environment($loader, [
            'debug' => true
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        return $twig;
    }

    private function resolveUrlPrefix(): string
    {
        $currentRelease = $this->currentRelease();

        $file = str_replace($currentRelease->path(), '', $this->markdown->path());
        $dirname = reliability()->dirname($file);
        if ($dirname === '') {
            return './';
        }

        $nodes = explode('/', $dirname);
        return str_repeat('../', count($nodes));
    }

    private function resolveProjectLogoUrl(): string
    {
        $basePath = $this->loader->config('path_project');
        $logoPath = $this->loader->config('project_logo');

        $filePath = trim(str_replace([$basePath], '', $logoPath), DIRECTORY_SEPARATOR);
        $baseReleasePath = $basePath;
        if ($this->releaseName !== '_') {
            $baseReleasePath .= DIRECTORY_SEPARATOR . $this->releaseName;
        }
        $fileReleasedPath = $baseReleasePath . DIRECTORY_SEPARATOR . $filePath;

        $hasLogo = reliability()->isFile($fileReleasedPath);
        if ($hasLogo === true) {
            $filePath = str_replace(DIRECTORY_SEPARATOR, '/', $filePath);
            return $filePath;
        }

        $themePath = $this->loader->theme()->path();
        $logoPath = $themePath
            . DIRECTORY_SEPARATOR . 'assets'
            . DIRECTORY_SEPARATOR . 'logo.png';
        $hasLogo = reliability()->isFile($logoPath);
        if ($hasLogo === true) {
            return 'assets/logo.png';
        }

        return '';
    }
}
