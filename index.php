<?php
declare(strict_types=1);

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);
set_time_limit(0);

require(__DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');

use YoCierge\DirectoryHandler;
use YoCierge\DirectoryQrCodeHandler;
use YoCierge\Helper;
use YoCierge\JsonParser;
use YoCierge\PdfGenerator;
use YoCierge\Person;

$dataPath = __DIR__.DIRECTORY_SEPARATOR.'data';

$faxesPath = $dataPath.DIRECTORY_SEPARATOR.'faxes';
$pdfPath = $dataPath.DIRECTORY_SEPARATOR.'pdf';

$faxesStateFilePath = $dataPath.DIRECTORY_SEPARATOR.'faxes.json';
$peopleJsonFilePath = $dataPath.DIRECTORY_SEPARATOR.'people.json';

ob_implicit_flush(true); // send content immediately to the browser
ob_end_flush(); // flush output buffer and outputs all of its contents

print('<h1>Read fax directories and files</h1>');

$faxFiles = DirectoryHandler::listFiles($faxesPath);
Helper::dumpDirectoriesAndFiles($faxFiles);

$faxQrFiles = array_map(function($key) {
    return realpath("{$key}/1.png");
}, array_keys($faxFiles));

print('<h1>Decode UUIDs from fax directories QR coded image</h1>');
$faxesAndPersonIds = DirectoryQrCodeHandler::parseDirectoryQrFiles($faxQrFiles, $faxesStateFilePath);

print('<h1>Read people from JSON file</h1>');
$people = JsonParser::parseJsonFile($peopleJsonFilePath);
Helper::dumpPeople($people);

print('<h1>People list with fax combined</h1>');
$people = Helper::combinePersonAndFaxFiles($people, $faxFiles, $faxesAndPersonIds, $faxesPath);
Helper::dumpPeople($people);

print('<h1>Filtered people list (having fax)</h1>');
$peopleWithFax = Helper::filterPeopleWithFax($people);
Helper::dumpPeople($peopleWithFax);

print('<h1>Generate all pdfs based on people list (having fax)</h1>');
PdfGenerator::generateAllPersonPdfs($peopleWithFax, $faxesPath, $pdfPath);

print("<br><hr><h3>To force rescan QR codes from fax directories, please delete <code>{$faxesStateFilePath}</code> file!</h3>");
