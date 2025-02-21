<?php
declare(strict_types=1);

namespace YoCierge;

use Exception;
use JsonException;

class StateFileHandler {
    private array $states = [];
    private string $stateFilename = '';

    public function __construct(string $stateFilename) {
        $this->stateFilename = $stateFilename;
        $this->states = [];
    }

    public function delete(string $key): bool {
        if (!array_key_exists($key, $this->states)) {
            return false;
        }

        unset($states[$key]);
        return true;
    }

    public function exists(string $key): bool {
        return array_key_exists($key, $this->states);
    }

    public function get(string $key): mixed {
        if (!array_key_exists($key, $this->states)) {
            return false;
        }

        return $this->states[$key];
    }

    public function getAll(): array {
        return $this->states;
    }

    /**
    * Load states from file
    *
    * @param string $stateFilename
    * @return bool
    *
    * @throws Exception
    * @throws JsonException
    */
    public function load($stateFilename = null): bool {
        if (is_null($stateFilename)) {
            $stateFilename = $this->stateFilename;
        }

        if (!file_exists($stateFilename)) {
            $this->states = [];
            return false;
        }

        if (!is_readable($stateFilename)) {
            throw new Exception("State file is not readable: {$stateFilename}");
        }

        $fileContent = file_get_contents($stateFilename);

        if ($fileContent == '') {
            return false;
        }

        $dataArray = json_decode($fileContent, true, 1000, JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }

        $this->states = $dataArray;

        return true;
    }

    /**
    * Save states to file
    *
    * @param string $stateFilename
    * @return bool
    * @throws JsonException
    */
    public function save(string $stateFilename = null): bool {
        if (is_null($stateFilename)) {
            $stateFilename = $this->stateFilename;
        }

        $jsonContent = json_encode($this->states, JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException(json_last_error_msg(), json_last_error());
        }

        return file_put_contents($stateFilename, $jsonContent) !== false;
    }

    public function set(string $key, mixed $value) {
        $this->states[$key] = $value;
    }
}
