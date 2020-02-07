<?php
declare(strict_types=1);

namespace MarkHelp;

/**
 * Esta classe contém as funcionalidades 
 * críticas do PHP com um tratamento especial.
 */
class Insurance
{
    public static function include($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return include($filename);
    }

    public static function getContents($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return file_get_contents($filename);
    }

    public static function isFile($filename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        return is_file($filename);
    }

    public static function isDirectory($path)
    {
        $path = filter_var($path, FILTER_SANITIZE_STRING);
        return is_dir($path);
    }

    public static function dirname($path, $levels = 1)
    {
        $path = filter_var($path, FILTER_SANITIZE_STRING);

        for($x=0; $x < $levels; $x++) {
            $path = dirname($path);
        }
        return $path;
    }

    public static function basename($filename)
    {
        return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    }

    public static function filename($filename)
    {
        $basename = self::basename($filename);
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $basename);
    }

    public static function copy($filename, $newFilename)
    {
        $filename = filter_var($filename, FILTER_SANITIZE_STRING);
        $newFilename = filter_var($newFilename, FILTER_SANITIZE_STRING);
        return copy($filename, $newFilename);
    }
}