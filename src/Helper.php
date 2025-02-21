<?php
declare(strict_types=1);

namespace YoCierge;

use YoCierge\Person;

class Helper {
    /**
    * Combine together a list of person with list of faxes, files and link faxes to the corresponding person
    *
    * @param array $people
    * @param array $faxFiles
    * @param array $faxesAndPersonIds: person id as key, array of faxes directories as value
    * @param string $faxesPath path to fax files
    * @return array of person with id, name, date of birth, fax directories and files
    */
    public static function combinePersonAndFaxFiles(array $people, array $faxFiles, array $faxesAndPersonIds, string $faxesPath): array {
        $result = [];

        foreach($people as $index => $person) {
            $personId = $person['id'];
            $personFaxesDirectories = $faxesAndPersonIds[$personId]['faxes'] ?? [];
            $personFaxesDirectoriesAndFiles = [];

            if (count($personFaxesDirectories) > 0) {
                foreach($personFaxesDirectories as $personFaxDirectoryIndex => $personFaxDirectory) {
                    $fullFaxPath = $faxesPath.DIRECTORY_SEPARATOR.$personFaxDirectory;
                    $personFaxesDirectoriesAndFiles[$personFaxDirectory] = $faxFiles[$fullFaxPath];
                }
            }

            $result[$personId] = new Person($person['id'], $person['name'], $person['date_of_birth'], $personFaxesDirectoriesAndFiles);
        }

        return $result;
    }

    public static function filterPeopleWithFax(array $people): array {
        return array_filter($people, function(Person $value, string $key) {
            return !empty($value->faxes);
            }, ARRAY_FILTER_USE_BOTH);
    }

    public static function dumpDirectoriesAndFiles(array $dirsAndFiles): void {
        $index = 0;
        foreach($dirsAndFiles as $dirIndex => $files) {
            $newline = ($index++ === 0) ? '' : '<br>';
            $indexKey = str_pad((string)$index, 3, '0', STR_PAD_LEFT);

            print("{$newline}{$indexKey}: [ {$dirIndex} ]");
            foreach($files as $fileIndex => $file) {
                print(" / {$file}");
            }
        }
    }

    public static function dumpPeople(array $people): void {
        $index = 0;
        foreach($people as $personId => $person) {
            $newline = ($index++ === 0) ? '' : '<br>';
            $indexKey = str_pad((string)$index, 3, '0', STR_PAD_LEFT);

            $faxes = '/ Faxes: 0';

            if (is_array($person)) {
                print("{$newline}{$indexKey}: [ ID: {$person['id']} / Name: {$person['name']} / Date of birth: {$person['date_of_birth']} ]");
            } else {
                if (!empty($person->faxes)) {
                    $faxes = '| Faxes: '.implode(', ', array_keys($person->faxes));
                }
                print("{$newline}{$indexKey}: [ ID: {$person->id} / Name: {$person->name} / Date of birth: {$person->dateOfBirth} {$faxes} ]");
            }
        }
    }
}