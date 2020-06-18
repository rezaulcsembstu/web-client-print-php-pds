<?php

namespace Neodynamic\SDK\Web;

use Exception;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\SecUtils;

/**
 * It represents a DOC file in the server that will be printed at the client side.
 */
class PrintFileDOC extends PrintFile
{

    /**
     * Gets or sets a subset of pages to print. It can be individual page numbers, a range, or a combination. For example: 1, 5-10, 25, 50. Default is an empty string which means print all pages.
     * @var string
     */
    public $pagesRange = '';

    /**
     * Gets or sets whether pages are printed in reverse order. Default is False.
     * @var boolean
     */
    public $printInReverseOrder = false;

    /**
     * Gets or sets the password for this PDF file.
     * @var string
     */
    public $password = '';

    /**
     * Gets or sets whether to perform manual duplex printing. Default is False. Manual duplex lets you print on both sides of a sheet by ordering the print job so that after the first half of the print job has been printed, the job can be flipped over for the second side printing.
     * @var boolean
     */
    public $duplexPrinting = false;

    /**
     * Gets or sets the dialog message to prompt to the user to flip pages after first half of print job has been printed. Default is an empty string.
     * @var string
     */
    public $duplexPrintingDialogMessage = '';


    public function serialize()
    {
        $this->fileExtension = '.wdoc';

        return parent::serialize();
    }

    public function getFileContent()
    {

        $pr = urldecode($this->pagesRange);
        if (!Utils::isNullOrEmptyString($pr)) {
            if (preg_match('/^(?!([ \d]*-){2})\d+(?: *[-,] *\d+)*$/', $pr)) {
                //validate range
                $ranges = explode(',', str_replace(' ', '', $pr)); //remove any space chars

                for ($i = 0; $i < count($ranges); $i++) {
                    if (strpos($ranges[$i], '-') > 0) {
                        $pages = explode('-', $ranges[$i]);
                        if (intval($pages[0]) > intval($pages[1])) {
                            throw new Exception("The specified PageRange is not valid.");
                        }
                    }
                }
            } else
                throw new Exception("The specified PageRange is not valid.");
        }

        $metadata = (Utils::isNullOrEmptyString($pr) ? 'A' : $pr);
        $metadata .= Utils::SER_SEP . ($this->printInReverseOrder ? '1' : '0');
        $metadata .= Utils::SER_SEP;

        $this->fileIsPasswordProtected = !Utils::isNullOrEmptyString($this->password);

        if ($this->fileIsPasswordProtected == false) {
            $metadata .= 'N';
        } else {
            if (Utils::isNullOrEmptyString($this->encryptMetadata->publicKeyBase64) == false) {
                $metadata .= base64_encode(SecUtils::rsaVerifyAndEncrypt($this->encryptMetadata->publicKeyBase64, $this->encryptMetadata->publicKeySignatureBase64, $this->password));
            } else {
                $metadata .= base64_encode($this->password);
            }
        }

        $metadata .= Utils::SER_SEP . ($this->duplexPrinting ? '1' : '0');
        $metadata .= Utils::SER_SEP . (Utils::isNullOrEmptyString($this->duplexPrintingDialogMessage) ? 'D' : base64_encode($this->duplexPrintingDialogMessage));

        $metadataLength = strlen($metadata);
        $metadata .= Utils::SER_SEP;
        $metadataLength++;
        $metadataLength += strlen(strval($metadataLength));
        $metadata .= strval($metadataLength);


        if (!Utils::isNullOrEmptyString($this->filePath)) {
            $handle = fopen($this->filePath, 'rb');
            $content = fread($handle, filesize($this->filePath));
            fclose($handle);
        } else {
            $content = $this->fileBinaryContent;
        }

        return $content . $metadata;
    }
}