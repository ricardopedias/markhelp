<?php
declare(strict_types=1);

namespace MarkHelp;

use League\CommonMark\CommonMarkConverter;
use RuntimeException;

class Engine extends Handle
{
    private $reader = null;

    private $theme = null;

    private $themeDefault = null;

    /**
     * Constrói um renderizador de arquivos html.
     * 
     * @param Read $reader
     */
    public function __construct(Reader $reader)
    {
        $this->setPathBase($reader->pathBase());

        $this->reader = $reader;
        
        $this->themeDefault = implode(DIRECTORY_SEPARATOR, [__DIR__, 'themes' , 'default']);
    }

    public function reader()
    {
        return $this->reader;
    }

    /**
     * Seta o tema a ser usado.
     * 
     * @return string Pode ser um nome ou um local
     */
    public function setTheme(string $theme) 
    {
        if ($this->directoryExists($theme)) {
            $this->theme = $theme;
            return $this;
        }

        $mainTheme = implode(DIRECTORY_SEPARATOR, [__DIR__, 'themes' , trim($theme, '/')]);
        if ($this->directoryExists($mainTheme)) {
            $this->theme = $mainTheme;
            return $this;
        }

        throw new RuntimeException('The specified theme is invalid or does not exist.');
    }

    /**
     * Devolve o css a ser usado.
     * 
     * @return string
     */
    public function logo() : string
    {
        $file = $this->reader()->info()['logo.src'];

        return $this->fileExists($file) 
            ? $file 
            : implode(DIRECTORY_SEPARATOR, [$this->themeDefault, 'assets' , 'logo.png']);
    }

    /**
     * Devolve o css a ser usado.
     * 
     * @return string
     */
    public function css() : string
    {
        $file = implode(DIRECTORY_SEPARATOR, [$this->theme, 'assets' , 'styles.css']);

        return $this->fileExists($file) 
            ? $file 
            : implode(DIRECTORY_SEPARATOR, [$this->themeDefault, 'assets' , 'styles.css']);
    }

    /**
     * Devolve o script a ser usado.
     * 
     * @return string
     */
    public function script() : string
    {
        $file = implode(DIRECTORY_SEPARATOR, [$this->theme, 'assets' , 'scripts.js']);
        
        return $this->fileExists($file) 
            ? $file 
            : implode(DIRECTORY_SEPARATOR, [$this->themeDefault, 'assets' , 'scripts.js']);
    }

    /**
     * Devolve o css a ser usado.
     * 
     * @return string
     */
    public function favicon() : string
    {
        $file = $this->reader()->info()['favicon'];

        return $this->fileExists($file) 
            ? $file 
            : implode(DIRECTORY_SEPARATOR, [$this->themeDefault, 'assets' , 'favicon.ico']);
    }

    /**
     * Devolve o css a ser usado.
     * 
     * @return string
     */
    public function appleTouchIcon() : string
    {
        $file = $this->reader()->info()['apple.touch.icon'];

        return $this->fileExists($file) 
            ? $file 
            : implode(DIRECTORY_SEPARATOR, [$this->themeDefault, 'assets' , 'apple-touch-icon-precomposed.png']);
    }

    /**
     * Devolve o tema a ser usado.
     * 
     * @return string
     */
    public function document() : string
    {
        $file = implode(DIRECTORY_SEPARATOR, [$this->theme, 'document.html']);
        
        return $this->fileExists($file) 
            ? $file 
            : implode(DIRECTORY_SEPARATOR, [$this->themeDefault, 'document.html']);
    }

    protected function replace($content, $replaces)
    {
        return preg_replace_callback("#{{\s*(?P<key>[a-zA-Z0-9_\.-]+?)\s*}}#", function($match) use($replaces){
            return isset($replaces[$match["key"]]) ? $replaces[$match["key"]] : $match[0];
        }, $content);
    }

    private function convertToHtml(string $markdownFile) : string
    {
        $markdown = $this->filesystem()->read($markdownFile);
        $markdown = $this->fixLinks($markdown);
        return (new CommonMarkConverter)->convertToHtml($markdown);
    }

    private function fixLinks(string $markdownString) : string
    {
        return str_replace('.md', '.html', $markdownString);
    }

    /**
     * Renderiza as páginas em html.
     * 
     * @return string
     */
    public function render()
    {
        $this->reader()->load();

        $html = $this->reader()->all();
        $html['index'] = $this->convertToHtml($html['index']);
        $html['menu'] = $this->convertToHtml($html['menu']);
        foreach($html['pages'] as $index => $page) {
            $html['pages'][$index] = $this->convertToHtml($page);
        }

        $replaces = array_merge([
            'sidemenu' => $html['menu'],
            'content'  => $html['index']
        ], $this->reader()->info());
        $replaces['logo.src']         = basename($replaces['logo.src']);
        $replaces['favicon']          = 'favicon.ico';
        $replaces['apple.touch.icon'] = basename($replaces['apple.touch.icon']);

        $document = file_get_contents($this->document());
        $this->structure['index'] = $this->replace($document, $replaces);
        foreach($html['pages'] as $index => $page) {
            $replaces['content'] = $page;
            $this->structure['pages'][$index] = $this->replace($document, $replaces);
        }

        return $this;
    }
}