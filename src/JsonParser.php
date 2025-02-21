<?php
declare(strict_types=1);

namespace YoCierge;

class JsonParser {
    /**
    * Load json file content as an associative array
    *
    * @param string $jsonFile
    * @return bool
    *
    * @throws Exception
    * @throws JsonException
    */
    public static function parseJsonFile(string $jsonFile): array {
        if (!file_exists($jsonFile)) {
            return false;
        }

        if (!is_readable($jsonFile)) {
            throw new Exception("People file is not readable: {$jsonFile}");
        }

        $fileContent = file_get_contents($jsonFile);

        if ($fileContent == '') {
            return false;
        }

        $dataArray = json_decode($fileContent, true, 1000, JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }

        return $dataArray;
    }
}