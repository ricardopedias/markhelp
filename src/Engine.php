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
        if (Insurance::isDirectory($theme)) {
            $this->theme = $theme;
            return $this;
        }

        $mainTheme = implode(DIRECTORY_SEPARATOR, [__DIR__, 'themes' , trim($theme, '/')]);
        if (Insurance::isDirectory($mainTheme)) {
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

        return Insurance::isFile($file) 
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

        return Insurance::isFile($file) 
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
        
        return Insurance::isFile($file) 
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

        return Insurance::isFile($file) 
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
        $file = $this->reader()->info()['apple.icon'];

        return Insurance::isFile($file) 
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
        
        return Insurance::isFile($file) 
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
        $markdown = str_replace('.md', '.html', $markdown);
        return (new CommonMarkConverter)->convertToHtml($markdown);
    }

    private function convertMenuToHtml(string $markdownFile, $prefix) : string
    {
        $markdown = $this->filesystem()->read($markdownFile);

        $markdown = str_replace("index.md", "{$prefix}index.md", $markdown);
        foreach($this->reader()->all()['pages'] as $linkPage) {
            $markdown = str_replace($linkPage, "{$prefix}{$linkPage}", $markdown);
        }

        $markdown = str_replace('.md', '.html', $markdown);

        return (new CommonMarkConverter)->convertToHtml($markdown);
    }

    /**
     * Renderiza as páginas em html.
     * 
     * @return string
     */
    public function render()
    {
        $this->reader()->load();

        $template = file_get_contents($this->document());
        $markdown = $this->reader()->all();
        $replaces = $this->reader()->info();

        $logoSrc      = Insurance::basename($replaces['logo.src']);
        $appleIconSrc = Insurance::basename($replaces['apple.icon']);

        $replaces['home']       = "./index.html";
        $replaces['sidemenu']   = $this->convertMenuToHtml($markdown['menu'], './');
        $replaces['content']    = $this->convertToHtml($markdown['index']);
        $replaces['logo.src']   = $logoSrc;
        $replaces['favicon']    = 'favicon.ico';
        $replaces['apple.icon'] = $appleIconSrc;
        $replaces['styles']     = "./styles.css";
        $replaces['scripts']    = "./scripts.js";

        $this->structure['index'] = $this->replace($template, $replaces);

        foreach($markdown['pages'] as $index => $page) {

            $prefix = $this->reader()->linkDots()['pages'][$index];

            $replaces['home']       = "{$prefix}index.html";
            $replaces['sidemenu']   = $this->convertMenuToHtml($markdown['menu'], $prefix);
            $replaces['content']    = $this->convertToHtml($page);
            $replaces['logo.src']   = "{$prefix}{$logoSrc}";
            $replaces['favicon']    = "{$prefix}favicon.ico";
            $replaces['apple.icon'] = "{$prefix}{$appleIconSrc}";
            $replaces['styles']     = "{$prefix}styles.css";
            $replaces['scripts']    = "{$prefix}scripts.js";

            $this->structure['pages'][$index] = $this->replace($template, $replaces);
        }

        return $this;
    }
}