<?php
namespace ReactFileWatcher\Utils;

class UtilsFunctions
{
    public static function PathsJoin(string ...$parts): string {
        $parts = array_map('trim', $parts);
        $path = [];

        foreach ($parts as $part) {
            if ($part !== '') {
                $path[] = $part;
            }
        }

        $path = implode(DIRECTORY_SEPARATOR, $path);

        return preg_replace(
            '#' . preg_quote(DIRECTORY_SEPARATOR) . '{2,}#',
            DIRECTORY_SEPARATOR,
            $path
        );
    }

}