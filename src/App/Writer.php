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

        $this->generateIndexPhp();

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
     * Se a configuração estiver ativada, gera um arquivo 
     * de índice PHP para rotear a documentação.
     * 
     * @return void
     */
    private function generateIndexPhp() : void
    {
        $generatePhpIndex = $this->reader->config()->param('generate.phpindex');
        if ($generatePhpIndex !== false) {

            $this->filesystem()->write("destination://index.php", "<?php
                if (php_sapi_name() == 'cli-server') {

                    \$info = parse_url(\$_SERVER['REQUEST_URI']);

                    if (is_file( \"./\" . urldecode(\$info['path']) )) {

                        \$extension = pathinfo(\$info['path'], PATHINFO_EXTENSION);
                        
                        switch(\$extension) {
                            case 'css':
                                header('Content-type: text/css');
                                break;
                            case 'js':
                                header('Content-type: application/javascript');
                                break;
                        }

                        include_once \"./\" . urldecode(\$info['path']);
                        return;
                    }

                    include_once 'index.html';
                }
            ");
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
            $assetPath     = $item->param('assetPath');
            $assetBasename = $item->param('assetBasename');

            $this->filesystem()->copy("{$mountPoint}://{$assetPath}", "destination://assets/{$assetBasename}");
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
        
        $replaceUrls = [];
        foreach($this->reader->markdownFiles() as $item) {
            $fileOrigin = $item->param('pathSearch');
            $fileDestination = $dotPrefix . $item->param('pathReplace');
            $replaceUrls[$fileOrigin] = $fileDestination;
        }

        $assetsList = $this->reader->assetsFiles();
        foreach($assetsList as $assetBag) {

            $assetParam = $assetBag->param('assetParam');
            $assetBasename = $assetBag->param('assetBasename');
            $this->reader->config()->setParam($assetParam, "{$dotPrefix}assets/{$assetBasename}");
        }

        $this->reader->config()->setParam('home', $dotPrefix . "index.html");

        return array_merge($this->reader->config()->all(), $replaceUrls);
    }
}