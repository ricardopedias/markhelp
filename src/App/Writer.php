<?php
declare(strict_types=1);

namespace MarkHelp\App;

use MarkHelp\Bags\File;

class Writer
{
    use Tools;

    private $reader = null;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Obtém a instancia do gerenciador de arquivos.
     * 
     * @return Filesystem
     */
    protected function filesystem() : Filesystem
    {
        return $this->reader->filesystem();
    }

    /**
     * Salva o projeto no diretório especificado.
     * 
     * @param string $path
     * @return void
     */
    public function saveTo(string $path) : void
    {
        $this->filesystem()->mount('destination', $path);

        $this->cleanup();

        $this->copyAssets();

        $this->generateFiles();
    }

    /**
     * Limpa o diretório de destino, 
     * onde a nova documentação será salva.
     * 
     * @return void
     */
    private function cleanup() : void
    {
        $cleanup = $this->filesystem()->listContents('destination://');
        foreach($cleanup as $item) {
            if ($item['type'] === 'dir') {
                $this->filesystem()->deleteDir("destination://{$item['path']}");
                continue;
            }
            $this->filesystem()->delete("destination://{$item['path']}");
        }
    }

    /**
     * Copia os arquivos de assets para o projeto.
     * 
     * @return void
     */
    private function copyAssets() : void
    {
        $list = $this->reader->assetsFiles();

        foreach($list as $item) {

            $mountPoint    = $item->param('mountPoint');
            $assetParam    = $item->param('assetParam');
            $assetPath     = $item->param('assetPath');
            $assetBasename = $item->param('assetBasename');

            $destinationPath = $assetParam === 'assets.images' ? 'images' : 'assets';
            $this->filesystem()->copy("{$mountPoint}://{$assetPath}", "destination://{$destinationPath}/{$assetBasename}");
        }
    }

    /**
     * Gera os arquivos html do projeto.
     * 
     * @return void
     */
    private function generateFiles() : void
    {
        $documentBag = $this->reader->supportFiles()['document'] ?? null;
        $menuBag = $this->reader->supportFiles()['menu'] ?? null;

        foreach($this->reader->markdownFiles() as $fileBag){

            $fileOrigin = $fileBag->param('pathSearch');
            $fileDestination = $fileBag->param('pathReplace');

            $contents = $this->filesystem()->read("origin://{$fileOrigin}");

            $render = new Converter($this->filesystem());
            if ($documentBag !== null) {
                $render->useDocument($documentBag);
            }
            if ($menuBag !== null) {
                $render->useMenu($menuBag);
            }

            $contents = $render->render($contents, $this->replaces($fileBag));

            $this->filesystem()->write("destination://{$fileDestination}", $contents);
        }
    }

    /**
     * Obtém a lista de parâmetros usados para substituição 
     * nos templates do projeto.
     * 
     * @param File $fileBag
     * @return array
     */
    private function replaces(File $fileBag) : array
    {
        $dotPrefix = $fileBag->param('assetsPrefix');
        
        $replaceStrings = [];
        foreach($this->reader->markdownFiles() as $item) {
            $fileOrigin = $item->param('pathSearch');
            $fileDestination = $dotPrefix . $item->param('pathReplace');
            $replaceStrings[$fileOrigin] = $fileDestination;
        }

        $assetsList = $this->reader->assetsFiles();
        foreach($assetsList as $assetBag) {

            $assetParam = $assetBag->param('assetParam');
            $assetFile = $assetBag->param('assetPath');
            $assetBasename = $assetBag->param('assetBasename');

            if($assetParam === 'assets.images') {
                $replaceStrings["images/$assetFile"] = "{$dotPrefix}images/{$assetFile}";
                continue;
            }

            $replaceStrings[$assetParam] = "{$dotPrefix}assets/{$assetFile}";
        }

        $replaceStrings['versions'] = $this->renderVersions();

        $homeUrl = $this->reader->config()->param('project.home');
        $homeUrl = (strpos($homeUrl, '{{project}}') !== false)
            ? $dotPrefix . substr($homeUrl, 12)
            : $homeUrl;
        $replaceStrings['home'] = $homeUrl;

        return array_merge($this->reader->config()->all(), $replaceStrings);
    }

    private function renderVersions()
    {
        $versions = $this->reader->versions();
        $current = $this->reader->currentVersion();

        $html = "";
        foreach($versions as $label => $version) {
            $selected = $version == $current ? 'selected' : '';
            $html.= "<option value='{$version}' {$selected}>{$label}</option>";
        }

        return $html;
    }
}
