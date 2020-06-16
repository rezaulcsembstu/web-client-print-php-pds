<?php

namespace Neodynamic\SDK\Web;

use ZipArchive;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\WebClientPrint;
use Neodynamic\SDK\Web\UserSelectedPrinter;

/**
 * Specifies information about the print job to be processed at the client side.
 */
class ClientPrintJob
{

    /**
     * Gets or sets the ClientPrinter object. Default is NULL.
     * The ClientPrinter object refers to the kind of printer that the client machine has attached or can reach.
     * - Use a DefaultPrinter object for using the default printer installed in the client machine.
     * - Use a InstalledPrinter object for using a printer installed in the client machine with an associated Windows driver.
     * - Use a ParallelPortPrinter object for using a printer which is connected through a parallel port in the client machine.
     * - Use a SerialPortPrinter object for using a printer which is connected through a serial port in the client machine.
     * - Use a NetworkPrinter object for using a Network IP/Ethernet printer which can be reached from the client machine.
     * @var ClientPrinter 
     */
    public $clientPrinter = null;
    /**
     * Gets or sets the printer's commands in text plain format. Default is an empty string.
     * @var string 
     */
    public $printerCommands = '';
    /**
     * Gets or sets the num of copies for Printer Commands. Default is 1.
     * Most Printer Command Languages already provide commands for printing copies. 
     * Always use that command instead of this property. 
     * Refer to the printer command language manual or specification for further details.
     * @var integer 
     */
    public $printerCommandsCopies = 1;
    /**
     * Gets or sets whether the printer commands have chars expressed in hexadecimal notation. Default is false.
     * The string set to the $printerCommands property can contain chars expressed in hexadecimal notation.
     * Many printer languages have commands which are represented by non-printable chars and to express these commands 
     * in a string could require many concatenations and hence be not so readable.
     * By using hex notation, you can make it simple and elegant. Here is an example: if you need to encode ASCII 27 (escape), 
     * then you can represent it as 0x27.        
     * @var boolean 
     */
    public $formatHexValues = false;
    /**
     * Gets or sets the PrintFile object to be printed at the client side. Default is NULL.
     * @var PrintFile 
     */
    public $printFile = null;
    /**
     * Gets or sets an array of PrintFile objects to be printed at the client side. Default is NULL.
     * @var array 
     */
    public $printFileGroup = null;


    /**
     * Sends this ClientPrintJob object to the client for further processing.
     * The ClientPrintJob object will be processed by the WCPP installed at the client machine.
     * @return string A string representing a ClientPrintJob object.
     */
    public function sendToClient()
    {

        $cpjHeader = chr(99) . chr(112) . chr(106) . chr(2);

        $buffer = '';

        if (!Utils::isNullOrEmptyString($this->printerCommands)) {
            if ($this->printerCommandsCopies > 1) {
                $buffer .= 'PCC=' . $this->printerCommandsCopies . Utils::SER_SEP;
            }
            if ($this->formatHexValues) {
                $buffer .= Utils::formatHexValues($this->printerCommands);
            } else {
                $buffer .= $this->printerCommands;
            }
        } else if (isset($this->printFile)) {
            $buffer = $this->printFile->serialize();
        } else if (isset($this->printFileGroup)) {
            $buffer = 'wcpPFG:';
            $zip = new ZipArchive;
            $cacheFileName = (Utils::strEndsWith(WebClientPrint::$wcpCacheFolder, '/') ? WebClientPrint::$wcpCacheFolder : WebClientPrint::$wcpCacheFolder . '/') . 'PFG' . uniqid() . '.zip';
            $res = $zip->open($cacheFileName, ZipArchive::CREATE);
            if ($res === TRUE) {
                foreach ($this->printFileGroup as $printFile) {
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
                $buffer .= fread($handle, filesize($cacheFileName));
                fclose($handle);
                unlink($cacheFileName);
            } else {
                $buffer = 'Creating PrintFileGroup failed. Cannot create zip file.';
            }
        }

        $arrIdx1 = Utils::intToArray(strlen($buffer));

        if (!isset($this->clientPrinter)) {
            $this->clientPrinter = new UserSelectedPrinter();
        }

        $buffer .= $this->clientPrinter->serialize();

        $arrIdx2 = Utils::intToArray(strlen($buffer));

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

        return $cpjHeader . $arrIdx1 . $arrIdx2 . $buffer;
    }
}