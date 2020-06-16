<?php

namespace Neodynamic\SDK\Web;

use ZipArchive;


/**
 * Specifies information about a group of ClientPrintJob objects to be processed at the client side.
 */
class ClientPrintJobGroup
{

    /**
     * Gets or sets an array of ClientPrintJob objects to be processed at the client side. Default is NULL.
     * @var array 
     */
    public $clientPrintJobGroup = null;

    /**
     * Sends this ClientPrintJobGroup object to the client for further processing.
     * The ClientPrintJobGroup object will be processed by the WCPP installed at the client machine.
     * @return string A string representing a ClientPrintJobGroup object.
     */
    public function sendToClient()
    {

        if (isset($this->clientPrintJobGroup)) {
            $groups = count($this->clientPrintJobGroup);

            $dataPartIndexes = Utils::intToArray($groups);

            $cpjgHeader = chr(99) . chr(112) . chr(106) . chr(103) . chr(2);

            $buffer = '';

            $cpjBytesCount = 0;

            foreach ($this->clientPrintJobGroup as $cpj) {
                $cpjBuffer = '';

                if (!Utils::isNullOrEmptyString($cpj->printerCommands)) {
                    if ($cpj->printerCommandsCopies > 1) {
                        $cpjBuffer .= 'PCC=' . $cpj->printerCommandsCopies . Utils::SER_SEP;
                    }
                    if ($cpj->formatHexValues) {
                        $cpjBuffer .= Utils::formatHexValues($cpj->printerCommands);
                    } else {
                        $cpjBuffer .= $cpj->printerCommands;
                    }
                } else if (isset($cpj->printFile)) {
                    $cpjBuffer = $cpj->printFile->serialize();
                } else if (isset($cpj->printFileGroup)) {
                    $cpjBuffer = 'wcpPFG:';
                    $zip = new ZipArchive;
                    $cacheFileName = (Utils::strEndsWith(WebClientPrint::$wcpCacheFolder, '/') ? WebClientPrint::$wcpCacheFolder : WebClientPrint::$wcpCacheFolder . '/') . 'PFG' . uniqid() . '.zip';
                    $res = $zip->open($cacheFileName, ZipArchive::CREATE);
                    if ($res === TRUE) {
                        foreach ($cpj->printFileGroup as $printFile) {
                            $file = $printFile->fileName;
                            if ($printFile->copies > 1) {
                                $pfc = 'PFC=' . $printFile->copies;
                                $file = substr($file, 0, strrpos($file, '.')) . $pfc . substr($file, strrpos($file, '.'));
                            }
                            if (is_a($printFile, 'PrintFilePDF')) $file .= '.wpdf';
                            if (is_a($printFile, 'PrintFileTXT')) $file .= '.wtxt';

                            $zip->addFromString($file, $printFile->getFileContent());
                        }
                        $zip->close();
                        $handle = fopen($cacheFileName, 'rb');
                        $cpjBuffer .= fread($handle, filesize($cacheFileName));
                        fclose($handle);
                        unlink($cacheFileName);
                    } else {
                        $cpjBuffer = 'Creating PrintFileGroup failed. Cannot create zip file.';
                    }
                }

                $arrIdx1 = Utils::intToArray(strlen($cpjBuffer));

                if (!isset($cpj->clientPrinter)) {
                    $cpj->clientPrinter = new UserSelectedPrinter();
                }

                $cpjBuffer .= $cpj->clientPrinter->serialize();

                $cpjBytesCount += strlen($arrIdx1 . $cpjBuffer);

                $dataPartIndexes .= Utils::intToArray($cpjBytesCount);

                $buffer .= $arrIdx1 . $cpjBuffer;
            }


            $lo = '';
            if (Utils::isNullOrEmptyString(WebClientPrint::$licenseOwner)) {
                $lo = substr(uniqid(), 0, 8);
            } else {
                $lo = 'php>' . base64_encode(WebClientPrint::$licenseOwner);
            }
            $lk = '';
            if (Utils::isNullOrEmptyString(WebClientPrint::$licenseKey)) {
                $lk = substr(uniqid(), 0, 8);
            } else {
                $lk = WebClientPrint::$licenseKey;
            }
            $buffer .= $lo . chr(124) . $lk;

            return $cpjgHeader . $dataPartIndexes . $buffer;
        } else {

            return NULL;
        }
    }
}