<?php
declare(strict_types=1);

namespace YoCierge;

use chillerlan\QRCode\{QRCode, QROptions};

class DirectoryQrCodeHandler {
    public static function parseDirectoryQrFiles(array $directoryQrFiles, string $stateFile): array {
        $result = [];

        // stateHandler is used for ensure every directory QR code decoded only at first time
        $stateHandler = new StateFileHandler($stateFile);
        $stateHandler->load($stateFile);

        $options = new QROptions();
        $options->readerUseImagickIfAvailable = false;
        $options->readerGrayscale = false;
        $options->readerIncreaseContrast = false;
        $qrCodeReader = new QRCode($options);

        foreach($directoryQrFiles as $index => $directoryQrFile) {
            try {
                $pathKey = self::getPathKey($directoryQrFile);

                $indexKey = str_pad((string)($index + 1), 3, '0', STR_PAD_LEFT);
                // if directory QR code is not decoded earlier
                if (!$stateHandler->exists($pathKey)) {
                    $qrCodeResult = $qrCodeReader->readFromFile($directoryQrFile);
                    $decodedUuid = (string)$qrCodeResult;

                    if (self::isValidUuid($decodedUuid)) {
                        if (!array_key_exists($decodedUuid, $result)) {
                            $result[$decodedUuid] = [];
                        }

                        $stateHandler->set($pathKey, $decodedUuid);
                        print("{$indexKey}: [{$pathKey}] directory QR decoded UUID: {$decodedUuid}<br>");
                    } else {
                        print("QR decoding failed for file: {$directoryQrFile}<br>");
                        continue;
                    }
                } else {
                    $decodedUuid = $stateHandler->get($pathKey);
                    print("{$indexKey}: [{$pathKey}] directory cached QR decoded UUID: {$decodedUuid}<br>");
                }

                $result[$decodedUuid]['faxes'][] = $pathKey;

                $stateHandler->save($stateFile);
            } catch (Exception $ex) {
                print("Exception occured: {$ex->message}@{$directoryQrFile}<br>");
            }
        }

        return $result;
    }

    public static function getPathKey(string $directoryQrFile): string {
        $pathParts = explode(DIRECTORY_SEPARATOR, dirname($directoryQrFile));
        return array_pop($pathParts);
    }

    public static function isValidUuid(mixed $uuid): bool {
        return (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) ? false : true;
    }
}