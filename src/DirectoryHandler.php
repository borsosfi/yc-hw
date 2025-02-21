<?php
declare(strict_types=1);

namespace YoCierge;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DirectoryHandler {
    public static function listFiles(string $directoryPath): array {
        $result = [];

        $dirIterator = new RecursiveDirectoryIterator($directoryPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);
        $sortedIterator = new SortedIterator($iterator);

        foreach($sortedIterator as $file) {
            $pathKey = $file->getPath();

            if ($file->isFile() && $file->isReadable()) {
                if (!array_key_exists($pathKey, $result)) {
                    $result[$pathKey] = [];
                }

                $result[$pathKey][] = $file->getFilename();
            }
        }

        ksort($result);

        return $result;
    }
}
