<?php
declare(strict_types=1);

namespace YoCierge;

use TCPDF;
use YoCierge\Person;

class PdfGenerator {
    public static function generateAllPersonPdfs(array $people, string $faxesPath, string $destionationPdfPath): void {
        $index = 0;
        foreach($people as $personIndex => $person) {
            $newline = ($index++ === 0) ? '' : '<br>';
            $indexKey = str_pad((string)$index, 3, '0', STR_PAD_LEFT);

            self::generatePersonPdf($person, $faxesPath, $destionationPdfPath.DIRECTORY_SEPARATOR."{$person->id}.pdf");
            print("{$indexKey}: {$person->name}'s pdf generated to {$destionationPdfPath}".DIRECTORY_SEPARATOR."{$person->id}.pdf<br>");
        }
    }

    public static function generatePersonPdf(Person $person, string $faxesPath, string $destinationPdfFile): bool {
        $pdfOutputMode = 'F'; // for reference: https://tcpdf.org/docs/srcdoc/TCPDF/classes-TCPDF/#method_Output

        $faxCount = count($person->faxes);
        $html = "<p align=\"center\" style=\"color: blue; font-size: 16px;\"><br>Name: {$person->name}<br>Date of birth: {$person->dateOfBirth}<br>Fax count: {$faxCount}</p>";

        $pdf = new TCPDF();

        // add first cover page for the pdf document
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        foreach($person->faxes as $faxDirName => $faxDirFiles) {
            $dateParts = str_split((string)$faxDirName, 2); //split fax dir name as date to two character parts, for example: 202405261552 -> 20 24 05 26 15 52
            $preformattedDate = "{$dateParts[0]}{$dateParts[1]}-{$dateParts[2]}-{$dateParts[3]} {$dateParts[4]}:{$dateParts[5]}:00";

            $dateTimeOfFax = strtotime($preformattedDate); // formatted as: 2024-05-26 15:52:00
            $formattedDate = date('m/d/Y H:i', $dateTimeOfFax);

            // add a cover page before every fax
            $pdf->AddPage();
            $html = "<p align=\"center\" style=\"color: blue; font-size: 15px;\"><br>Fax @ {$formattedDate} [{$faxDirName}]</p>";
            $pdf->writeHTML($html, true, false, true, false, '');

            $pdfImageHeight = 0; // auto
            $pdfImageWidth = 0; // auto
            $pdfImageType = ''; // auto, inferred from the file extension
            $pdfImageLink = '';
            $pdfImageAlign = 'M';
            $pdfImagePosX = null;
            $pdfImagePosY = null;
            $pdfImageResize = true;
            $pdfImageDpi =  150;
            $pdfImagePAlign = 'C';
            $pdfImageIsMask = false;
            $pdfImageMask = false;
            $pdfImageBorder = 1;
            $pdfImageFixbox = false;
            $pdfImageHidden = false;
            $pdfImageFitOnPage = true;

            // insert every fax image
            foreach($faxDirFiles as $faxImageIndex => $faxImage) {
                $faxImagePath = $faxesPath.DIRECTORY_SEPARATOR.$faxDirName.DIRECTORY_SEPARATOR.$faxImage;

                $pdf->AddPage();

                // for reference:  https://tcpdf.org/docs/srcdoc/TCPDF/classes-TCPDF/#method_Image
                $pdf->Image($faxImagePath, $pdfImagePosX, $pdfImagePosY, $pdfImageWidth, $pdfImageHeight, $pdfImageType, $pdfImageLink,
                    $pdfImageAlign, $pdfImageResize, $pdfImageDpi, $pdfImagePAlign, $pdfImageIsMask, $pdfImageMask, $pdfImageBorder,
                    $pdfImageFixbox, $pdfImageHidden, $pdfImageFitOnPage);
            }
        }

        $pdf->Output($destinationPdfFile, $pdfOutputMode);

        return true;
    }
}