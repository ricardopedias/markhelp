<?php
declare(strict_types=1);

namespace MarkHelp;

use League\Flysystem\Adapter;
use League\Flysystem\Filesystem;

class Writer extends Handle
{
    private $engine = null;

    /**
     * Constrói um renderizador de arquivos html.
     * 
     * @param Read $reader
     */
    public function __construct(Reader $reader)
    {
        $this->engine = new Engine($reader);
        $this->engine->render();
        $this->setPathBase($reader->pathBase());
    }

    protected function engine()
    {
        return $this->engine;
    }

    protected function reader()
    {
        return $this->engine()->reader();
    }

    public function saveTo($path)
    {
        $adapter = new Adapter\Local($path);
        $filesystem = new Filesystem($adapter);

        $paths = $this->reader()->all();
        $list  = $this->engine()->all();

        $old = $filesystem->listContents('./');
        foreach($old as $item) {
            if ($item['type'] === 'dir') {
                $filesystem->deleteDir($item['path']);
                continue;
            }
            $filesystem->delete($item['path']);
        }

        $logo           = $this->engine()->logo();
        $favicon        = $this->engine()->favicon();
        $appleTouchIcon = $this->engine()->appleTouchIcon();
        $css            = $this->engine()->css();
        $script         = $this->engine()->script();
        
        Insurance::copy($logo, $path . DIRECTORY_SEPARATOR . Insurance::basename($logo));
        Insurance::copy($favicon, $path . DIRECTORY_SEPARATOR . 'favicon.ico');
        Insurance::copy($appleTouchIcon, $path . DIRECTORY_SEPARATOR . Insurance::basename($appleTouchIcon));
        Insurance::copy($css, $path . DIRECTORY_SEPARATOR . 'styles.css');
        Insurance::copy($script, $path . DIRECTORY_SEPARATOR . 'scripts.js');

        $filesystem->write('index.html', $list['index']);
        foreach($list['pages'] as $index => $page) {
            $paths['pages'][$index] = str_replace('.md', '.html', $paths['pages'][$index]);
            $filesystem->write($paths['pages'][$index], $page);
        }
    }
}