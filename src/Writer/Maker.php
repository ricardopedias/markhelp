<?php

declare(strict_types=1);

namespace MarkHelp\Writer;

use Exception;
use League\CommonMark\CommonMarkConverter;
use MarkHelp\Reader\Files\Asset;
use MarkHelp\Reader\Files\File;
use MarkHelp\Reader\Files\Image;
use MarkHelp\Reader\Files\Markdown;
use MarkHelp\Reader\Loader;
use MarkHelp\Reader\Release;
use MarkHelp\Reader\Theme;
use RuntimeException;
use Twig\Environment;

class Maker
{
    private Loader $loader;

    private ?string $destinationPath = null;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Salva o projeto no diretório especificado.
     * @param string $path
     * @return void
     */
    public function toDirectory(string $path): void
    {
        $this->destinationPath = $path;

        if (reliability()->isDirectory($path) === false) {
            throw new Exception("Directory {$path} is not exists");
        }
        reliability()->removeDirectory($path, true);

        if ($this->loader->hasReleases() === false) {
            $mainRelease = $this->loader->releases()['_'];
            $filesList   = $mainRelease->files();
            array_walk($filesList, function($file) use ($mainRelease) {
                $this->parseFile($mainRelease, $file);
            });
            $this->copyAssets($mainRelease, $this->loader->theme());
            return;
        }

        $releasesList = $this->loader->releases();
        array_walk($releasesList, function($release){
            $filesList = $release->files();
            array_walk($filesList, function($file) use ($release){
                $this->parseFile($release, $file);
            });
            $this->copyAssets($release, $this->loader->theme());
        });
    }

    private function parseFile(Release $release, File $file): void 
    {
        $pathPrefix = '';
        if ($release->name() !== '_') {
            $pathPrefix = $release->name();
        }
        
        if ($file->isInstanceOf(Markdown::class) === true) {
            $html = $this->fileToHtml($release, $file);
            $filePath = str_replace('.md', '.html', $file->path());
            var_dump($filePath);
            reliability()
                ->mountDirectory($this->destination($pathPrefix))
                ->write($filePath, $html);
        }

        if ($file->isInstanceOf(Image::class) === true) {
            reliability()->copyFile(
                $file->fullPath(), 
                $this->destination("{$pathPrefix}/" . $file->path())
            );
        }
    }

    private function copyAssets(Release $release, Theme $theme): void 
    {
        $pathPrefix = '';
        if ($release->name() !== '_') {
            $pathPrefix = $release->name();
        }

        $assetsList = $theme->files();

        array_walk($assetsList, function($file) use ($pathPrefix){
            $assetName = reliability()->basename($file->path());
            reliability()->copyFile(
                $file->fullPath(),
                $this->destination("{$pathPrefix}/assets/{$assetName}")
            );
        });
    }

    private function destination(string $path = ''): string
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        return $this->destinationPath . DIRECTORY_SEPARATOR . $path;
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

        return $twig; //echo $twig->render('index.html', ['name' => 'Fabien']);
    }

    private function fileToHtml(Release $release, File $file): string
    {
        $markdownString = reliability()
            ->mountDirectory($file->basePath())
            ->read($file->path());

        if ($markdownString === false) {
            throw new Exception($file->fullPath() . " file is not accessible");
        }

        $htmlString = (new CommonMarkConverter())->convertToHtml($markdownString);

        // TODO converter links para html
        // ...

        $params = [];
        foreach ($this->loader->params() as $param => $value) {
            $value = str_replace('{{theme}}/', '', $value);
            $params[str_replace('.', '_', $param)] = $value;
        }

        foreach ($release->params() as $param => $value) {
            $params[str_replace('.', '_', $param)] = $value;
        }

        $releasesList = [];
        foreach ($this->loader->releases() as $release) {
            $url = '../' . $release->name() . '/' . $release->home()->path();
            $releasesList[$release->name()] = $url;
        }

        $params['page_title']    = 'teste';
        $params['releases_list'] = $releasesList;
        $params['menu']          = $this->loader->menuConfig();
        $params['content']       = $htmlString;

        return $this->templateEngine()->render('page.html', $params);
    }






    /**
     * Gera os arquivos html do projeto.
     * @return void
     */
    private function generateFiles(): void
    {
        $documentBag = $this->reader->supportFiles()['document'] ?? null;
        $menuBag     = $this->reader->supportFiles()['menu'] ?? null;

        foreach ($this->reader->markdownFiles() as $fileBag) {
            $fileOrigin = $fileBag->param('pathSearch');
            $fileDestination = $fileBag->param('pathReplace');

            $contents = $this->reader->mountedDirectory('origin')->read("{$fileOrigin}");

            if ($contents === false) {
                throw new RuntimeException("{$fileOrigin} file is not accessible");
            }

            $render = new Render($this->reader);
            if ($documentBag !== null) {
                $render->useDocument($documentBag);
            }
            if ($menuBag !== null) {
                $render->useMenu($menuBag);
            }

            $contents = $render->render($contents, $this->replaces($fileBag));

            $this->reader->mountedDirectory('destination')->write("{$fileDestination}", $contents);
        }
    }

    /**
     * Obtém a lista de parâmetros usados para substituição
     * nos templates do projeto.
     * @param \Markhelp\Reader\Bags\File $fileBag
     * @return array<string|null>:
     */
    private function replaces(File $fileBag): array
    {
        $dotPrefix = $fileBag->param('assetsPrefix');
        
        $replaceStrings = [];
        foreach ($this->reader->markdownFiles() as $item) {
            $fileOrigin = $item->param('pathSearch');
            $fileDestination = $dotPrefix . $item->param('pathReplace');
            $replaceStrings[$fileOrigin] = $fileDestination;
        }

        $assetsList = $this->reader->assetsFiles();
        foreach ($assetsList as $assetBag) {
            $assetParam = $assetBag->param('assetParam');
            $assetFile = $assetBag->param('assetPath');
            $assetBasename = $assetBag->param('assetBasename');

            if ($assetParam === 'assets.images') {
                $replaceStrings["images/$assetFile"] = "{$dotPrefix}images/{$assetFile}";
                continue;
            }

            $replaceStrings[$assetParam] = "{$dotPrefix}assets/{$assetFile}";
        }

        $replaceStrings['versions'] = $this->renderVersions();

        $homeUrl = $this->reader->config()->param('project.home') ?? '';
        $homeUrl = (strpos($homeUrl, '{{project}}') !== false)
            ? $dotPrefix . substr($homeUrl, 12)
            : $homeUrl;
        $replaceStrings['home.url'] = $homeUrl;

        return array_merge($this->reader->config()->all(), $replaceStrings);
    }

    private function renderVersions(): string
    {
        $versions = $this->reader->versions();
        $current = $this->reader->currentVersion();

        $html = "";
        foreach ($versions as $label => $version) {
            $selected = $version == $current ? 'selected' : '';
            $html .= "<option value='{$version}' {$selected}>{$label}</option>";
        }

        return $html;
    }
}
